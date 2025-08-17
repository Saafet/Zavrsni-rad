<template>
  <div class="container mt-5">
    <div v-if="toast.show" :class="['toast-notification', toast.type]">
      {{ toast.message }}
    </div>

    <h1 class="mb-4">Dostupni testovi</h1>

    <div v-if="!imePrezimePotvrđeno" class="mb-4">
      <input v-model="ime" placeholder="Ime" class="form-control mb-2" />
      <input v-model="prezime" placeholder="Prezime" class="form-control mb-2" />
      <button class="btn btn-primary" :disabled="!ime || !prezime" @click="potvrdiImePrezime">
        Počni
      </button>
    </div>

    <div v-if="imePrezimePotvrđeno">
      <div class="mb-3">
        <input v-model="search" placeholder="Pretraži testove..." class="form-control" />
      </div>

      <div v-if="loading" class="alert alert-info">Učitavanje testova...</div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <div v-for="test in paginatedTests" :key="test.id" class="card mb-2">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-1">{{ test.naziv }}</h5>
            <small class="text-muted">Kreirano: {{ formatDate(test.datum_kreiranja) }}</small>
          </div>
          <div>
            <button class="btn btn-primary me-2" @click="otvoriTest(test.id)">Riješi test</button>
            <button class="btn btn-secondary" @click="otvoriRezultate(test.id)">Rezultati</button>
          </div>
        </div>
      </div>

      <nav v-if="totalPages > 1" class="mt-3">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" @click="currentPage--">Prethodno</button>
          </li>
          <li
            class="page-item"
            v-for="page in totalPages"
            :key="page"
            :class="{ active: page === currentPage }"
          >
            <button class="page-link" @click="currentPage = page">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button class="page-link" @click="currentPage++">Sljedeće</button>
          </li>
        </ul>
      </nav>
    </div>

    <div v-if="currentTest" class="modal-backdrop">
      <div class="modal-content p-4">
        <h3>{{ currentTest.naziv }}</h3>
        <form @submit.prevent="submitTest">
          <div v-for="(pitanje, index) in currentTest.pitanja" :key="pitanje.id" class="mb-3">
            <label class="form-label">{{ index + 1 }}. {{ pitanje.naziv }}</label>

            <div v-if="pitanje.tip === 'truefalse'">
              <div class="form-check">
                <input class="form-check-input" type="radio" :name="'q' + pitanje.id" value="1" v-model="answers[pitanje.id]" />
                <label class="form-check-label">Da</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" :name="'q' + pitanje.id" value="0" v-model="answers[pitanje.id]" />
                <label class="form-check-label">Ne</label>
              </div>
            </div>

            <div v-else-if="pitanje.tip === 'text'">
              <input type="text" class="form-control" v-model="answers[pitanje.id]" placeholder="Unesi odgovor..." />
            </div>

            <div v-else class="text-muted">Nepoznat tip pitanja.</div>
          </div>

          <button type="submit" class="btn btn-success">Predaj test</button>
          <button type="button" class="btn btn-secondary ms-2" @click="closeTest">Zatvori</button>
        </form>
      </div>
    </div>

    <div v-if="currentResults.length" class="modal-backdrop">
      <div class="modal-content p-4">
        <h3>Rezultati testa</h3>
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
            <tr v-for="r in currentResults" :key="r.id">
              <td>{{ r.ime_ucenika || r.ime }} {{ r.prezime_ucenika || r.prezime }}</td>
              <td>{{ r.tocno }}</td>
              <td>{{ r.ukupno }}</td>
              <td>{{ r.procenat }}%</td>
              <td>{{ r.ocjena }}</td>
            </tr>
          </tbody>
        </table>
        <button class="btn btn-secondary mt-2" @click="currentResults = []">Zatvori rezultate</button>
      </div>
    </div>

    <div v-if="message.text" :class="'alert mt-3 alert-' + message.type">
      {{ message.text }}
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from "vue";

export default {
  setup() {
    const testovi = ref([]);
    const loading = ref(true);
    const error = ref("");
    const currentTest = ref(null);
    const currentResults = ref([]);
    const answers = ref({});
    const message = ref({ text: "", type: "info" });

    const toast = ref({ show: false, message: "", type: "success" });
    const showToast = (msg, type = "success") => {
      toast.value = { show: true, message: msg, type };
      setTimeout(() => { toast.value.show = false }, 3000);
    };

    const ime = ref("");
    const prezime = ref("");
    const imePrezimePotvrđeno = ref(false);

    const search = ref("");
    const currentPage = ref(1);
    const perPage = 5;

    const API = "http://localhost/my_project";
    let intervalId = null;

    const fetchTests = async () => {
      try {
        const res = await fetch(`${API}/get-testovi.php`);
        const data = await res.json();
        if (data.success) testovi.value = data.testovi;
        else error.value = data.message || "Greška pri učitavanju testova.";
      } catch {
        error.value = "Greška pri učitavanju testova.";
      } finally {
        loading.value = false;
      }
    };

    const potvrdiImePrezime = () => {
      imePrezimePotvrđeno.value = true;
      message.value = { text: `Dobrodošli, ${ime.value} ${prezime.value}!`, type: "success" };
      fetchTests();
      intervalId = setInterval(fetchTests, 5000);
    };

    const otvoriTest = async (testId) => {
      try {
        const res = await fetch(`${API}/dohvati-test.php?test_id=${testId}`);
        const data = await res.json();
        if (data.success) {
          currentTest.value = {
            id: testId,
            naziv: data.naziv,
            pitanja: data.pitanja
              .map(p => ({
                id: p.id,
                naziv: p.naziv,
                tip: p.tip ? p.tip.toString().trim().toLowerCase() : ''
              }))
              .sort((a, b) => {
                if (a.tip === 'text' && b.tip !== 'text') return -1;
                if (a.tip !== 'text' && b.tip === 'text') return 1;
                return 0;
              })
          };
          answers.value = {};
        } else {
          message.value = { text: data.message || "Greška pri otvaranju testa", type: "danger" };
        }
      } catch {
        message.value = { text: "Greška pri otvaranju testa", type: "danger" };
      }
    };

    const closeTest = () => { currentTest.value = null; };

    const submitTest = async () => {
      try {
        const odgovoriArray = Object.entries(answers.value).map(([pitanjeId, odgovor]) => ({
          pitanje_id: parseInt(pitanjeId, 10),
          odgovor
        }));

        const res = await fetch(`${API}/predaj-test.php`, {
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
          fetchTests();
        } else {
          message.value = { text: data.message || "Greška pri predaji testa", type: "danger" };
        }
      } catch {
        message.value = { text: "Greška pri predaji testa", type: "danger" };
      }
    };

    const otvoriRezultate = async (testId) => {
      try {
        const res = await fetch(`${API}/get-rezultati.php?test_id=${testId}`);
        const data = await res.json();
        if (data.success) {
          if (data.rezultati && data.rezultati.length > 0) {
            currentResults.value = data.rezultati;
          } else {
            showToast("Nema rezultata za ovaj test.", "warning");
            currentResults.value = [];
          }
        } else {
          showToast(data.message || "Greška pri učitavanju rezultata", "error");
        }
      } catch {
        showToast("Greška pri učitavanju rezultata", "error");
      }
    };

    const formatDate = (dateStr) => {
      const d = new Date(dateStr);
      return d.toLocaleDateString() + " " + d.toLocaleTimeString();
    };

    const filteredTests = computed(() =>
      testovi.value.filter(t => t.naziv.toLowerCase().includes(search.value.toLowerCase()))
    );

    const totalPages = computed(() => Math.ceil(filteredTests.value.length / perPage));
    const paginatedTests = computed(() => {
      const start = (currentPage.value - 1) * perPage;
      return filteredTests.value.slice(start, start + perPage);
    });

    onMounted(fetchTests);
    onUnmounted(() => { if (intervalId) clearInterval(intervalId); });

    return {
      testovi, loading, error, currentTest, currentResults, answers, message,
      ime, prezime, imePrezimePotvrđeno, search, currentPage, totalPages, paginatedTests,
      potvrdiImePrezime, otvoriTest, closeTest, submitTest, otvoriRezultate,
      formatDate, toast  
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
  background-color: #888;
  border-radius: 8px;
  width: 700px;
  max-width: 95%;
  max-height: 90%;
  overflow-y: auto;
  padding: 20px;
}

.toast-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 10px 15px;
  border-radius: 5px;
  color: white;
  font-weight: bold;
  z-index: 9999;
  animation: fadeIn 0.3s ease-out;
}
.toast-notification.success { background: #28a745; }
.toast-notification.error   { background: #dc3545; }
.toast-notification.warning { background: #ffc107; color: #000; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
