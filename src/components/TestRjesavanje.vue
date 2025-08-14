<template>
  <div class="container mt-5">
    <h1 class="mb-4">Dostupni testovi</h1>

    <!-- Unos imena i prezimena -->
    <div v-if="!imePrezimePotvrđeno" class="mb-4">
      <input v-model="ime" placeholder="Ime" class="form-control mb-2" />
      <input v-model="prezime" placeholder="Prezime" class="form-control mb-2" />
      <button class="btn btn-primary" :disabled="!ime || !prezime" @click="potvrdiImePrezime">
        Počni
      </button>
    </div>

    <!-- Lista testova -->
    <div v-if="imePrezimePotvrđeno">
      <div v-if="loading" class="alert alert-info">Učitavanje testova...</div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <div v-for="test in testovi" :key="test.id" class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">{{ test.naziv }}</h5>
          <p class="card-text">Kreirano: {{ formatDate(test.datum_kreiranja) }}</p>
          <button class="btn btn-primary me-2" @click="otvoriTest(test.id)">Riješi test</button>
          <button class="btn btn-secondary" @click="loadResults(test.id)">Rezultati</button>
        </div>
      </div>
    </div>

    <!-- Modal za rješavanje testa -->
    <div v-if="currentTest" class="modal-backdrop">
      <div class="modal-content p-4">
        <h3>{{ currentTest.naziv }}</h3>
        <form @submit.prevent="submitTest">
          <div v-for="(pitanje, index) in currentTest.pitanja" :key="pitanje.id" class="mb-3">
            <label class="form-label">{{ index + 1 }}. {{ pitanje.tekst }}</label>

            <!-- Višestruki izbor -->
            <div v-if="pitanje.tip === 'multiple'">
              <div v-for="opt in pitanje.odgovori" :key="opt.id">
                <input type="radio" :name="'q' + pitanje.id" :value="opt.id" v-model="answers[pitanje.id]" /> {{ opt.tekst }}
              </div>
            </div>

            <!-- Tačno / Netačno -->
            <div v-if="pitanje.tip === 'boolean'">
              <div>
                <input type="radio" :name="'q' + pitanje.id" value="true" v-model="answers[pitanje.id]" /> Tačno
              </div>
              <div>
                <input type="radio" :name="'q' + pitanje.id" value="false" v-model="answers[pitanje.id]" /> Netačno
              </div>
            </div>

            <!-- Kratki odgovor -->
            <div v-if="pitanje.tip === 'short'">
              <input type="text" class="form-control" v-model="answers[pitanje.id]" />
            </div>
          </div>

          <button type="submit" class="btn btn-success">Predaj test</button>
          <button type="button" class="btn btn-secondary ms-2" @click="closeTest">Zatvori</button>
        </form>
      </div>
    </div>

    <!-- Rezultati -->
    <div v-if="results.length" class="mt-5">
      <h3>Rezultati</h3>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Učenik</th>
            <th>Točno</th>
            <th>Ukupno</th>
            <th>Procenat</th>
            <th>Ocjena</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in results" :key="r.id">
            <td>{{ r.ime_ucenika || r.ime }} {{ r.prezime_ucenika || r.prezime }}</td>
            <td>{{ r.tocno }}</td>
            <td>{{ r.ukupno }}</td>
            <td>{{ r.procenat }}%</td>
            <td>{{ r.ocjena }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Poruke -->
    <div v-if="message.text" :class="'alert mt-3 alert-' + message.type">
      {{ message.text }}
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  setup() {
    const testovi = ref([]);
    const loading = ref(true);
    const error = ref("");
    const currentTest = ref(null);
    const answers = ref({});
    const results = ref([]);
    const message = ref({ text: "", type: "info" });

    const ime = ref("");
    const prezime = ref("");
    const imePrezimePotvrđeno = ref(false);

    const fetchTests = async () => {
      try {
        const res = await fetch("http://localhost/my_project/get-testovi.php");
        const data = await res.json();
        if (data.success) testovi.value = data.testovi;
      } catch {
        error.value = "Greška pri učitavanju testova.";
      } finally {
        loading.value = false;
      }
    };

    const potvrdiImePrezime = () => {
      imePrezimePotvrđeno.value = true;
      message.value = { text: `Dobrodošli, ${ime.value} ${prezime.value}!`, type: "success" };
    };

    const otvoriTest = async (testId) => {
      try {
        const res = await fetch(`http://localhost/my_project/dohvati-test.php?test_id=${testId}`);
        const data = await res.json();
        if (data.success) {
          currentTest.value = {
            id: testId,
            naziv: data.naziv || "Test",
            pitanja: data.pitanja.map(p => ({
              id: p.pitanje_id,
              tekst: p.naziv,
              tip: p.tip || "multiple",
              odgovori: p.odgovori.map(o => ({ id: o.odgovor_id, tekst: o.tekst }))
            }))
          };
          answers.value = {};
        } else {
          message.value = { text: data.message, type: "danger" };
        }
      } catch {
        message.value = { text: "Greška pri otvaranju testa", type: "danger" };
      }
    };

    const closeTest = () => { currentTest.value = null; };

    const submitTest = async () => {
      try {
        const odgovoriArray = Object.entries(answers.value).map(([pitanjeId, odgovor]) => {
          const pitanje = currentTest.value.pitanja.find(p => p.id === parseInt(pitanjeId));
          let odgovor_id = odgovor;
          if (pitanje.tip === 'boolean') {
            odgovor_id = odgovor === 'true' ? 1 : 0;
          }
          return { pitanje_id: parseInt(pitanjeId), odgovor_id };
        });

        const res = await fetch("http://localhost/my_project/predaj-test.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            test_id: currentTest.value.id,
            ime: ime.value,
            prezime: prezime.value,
            odgovori: odgovoriArray
          })
        });
        const data = await res.json();

        if (data.status === "success") {
          message.value = {
            text: `Test predan! Točno: ${data.tocno}/${data.ukupno} (${data.procenat}%), Ocjena: ${data.ocjena}`,
            type: "success"
          };
          closeTest();
        } else {
          message.value = { text: data.message, type: "danger" };
        }
      } catch (e) {
        message.value = { text: "Greška pri predaji testa", type: "danger" };
      }
    };

    const loadResults = async (testId) => {
      try {
        const res = await fetch(`http://localhost/my_project/get-rezultati.php?test_id=${testId}`);
        const data = await res.json();
        if (data.success) results.value = data.rezultati;
        else message.value = { text: data.message, type: "danger" };
      } catch {
        message.value = { text: "Greška pri učitavanju rezultata", type: "danger" };
      }
    };

    const formatDate = (dateStr) => {
      const d = new Date(dateStr);
      return d.toLocaleDateString() + " " + d.toLocaleTimeString();
    };

    onMounted(fetchTests);

    return {
      testovi, loading, error, currentTest, answers, results, message,
      ime, prezime, imePrezimePotvrđeno,
      potvrdiImePrezime, otvoriTest, closeTest, submitTest, loadResults, formatDate
    };
  }
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top:0; left:0; width:100%; height:100%;
  background-color: rgba(0,0,0,0.6);
  display: flex; justify-content:center; align-items:center; z-index:1050;
}
.modal-content {
  background-color: #fff;
  border-radius: 8px;
  width: 700px;
  max-width: 95%;
  max-height: 90%;
  overflow-y: auto;
  padding: 20px;
}
</style>
