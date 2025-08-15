<template>
  <div class="container my-5">
    <div v-if="toast.show" class="toast-notification" :class="toast.type">
      {{ toast.message }}
    </div>

    <div v-if="userRole === 'professor' || userRole === 'admin'" class="mb-5 border rounded p-3 bg-light">
      <h3>Dodaj novo pitanje</h3>
      <form @submit.prevent="dodajPitanje" class="row g-2 align-items-center">
        <div class="col-12 mb-2">
          <input v-model="novoPitanje" type="text" class="form-control" placeholder="Unesite novo pitanje" required />
        </div>

        <div class="col-12 mb-2">
          <select v-model="tipPitanja" class="form-select" required>
            <option disabled value="">Odaberite tip odgovora</option>
            <option value="text">Tekstualni odgovor</option>
            <option value="truefalse">Da/Ne</option>
          </select>
        </div>

        <div class="col-12 mb-2" v-if="tipPitanja === 'text'">
          <input v-model="tocanOdgovorText" type="text" class="form-control" placeholder="Unesite tačan odgovor" required />
        </div>
        <div class="col-12 mb-2" v-if="tipPitanja === 'truefalse'">
          <select v-model="tocanOdgovorTF" class="form-select" required>
            <option disabled value="">Odaberite odgovor</option>
            <option :value="1">Da</option>
            <option :value="0">Ne</option>
          </select>
        </div>

        <div class="col-auto">
          <button type="submit" class="btn btn-success">Dodaj pitanje</button>
        </div>
      </form>
    </div>
    <div v-if="userRole === 'professor' || userRole === 'admin'" class="mb-5 border rounded p-3 bg-light">
      <h3>Kreiraj test</h3>
      <form @submit.prevent="kreirajTest" class="row g-2">
        <div class="col-12 mb-2">
          <input v-model="noviTest.naziv" type="text" class="form-control" placeholder="Naziv testa" required />
        </div>
        <div class="col-12">
          <p><strong>Odaberite pitanja:</strong></p>
          <button type="button" class="btn btn-sm btn-outline-primary mb-2" @click="toggleSelectAll">
            {{ svaPitanja.length > 0 && noviTest.odabranaPitanja.length === svaPitanja.length ? 'Odznači sva pitanja' : 'Označi sva pitanja' }}
          </button>
          <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">
            <div v-for="pitanje in svaPitanja" :key="pitanje.id" class="form-check">
              <input class="form-check-input" type="checkbox" :value="pitanje.id" v-model="noviTest.odabranaPitanja" />
              <label class="form-check-label">
                {{ pitanje.naziv }} ({{ isTextType(pitanje.tip) ? 'Tekst' : 'Da/Ne' }})
              </label>
            </div>
          </div>
        </div>
        <div class="col-12 mt-3">
          <button type="submit" class="btn btn-primary">Spremi test</button>
        </div>
      </form>
    </div>
    <div class="mb-5">
      <h3>Vaša kreirana pitanja</h3>
      <div class="row row-cols-1 row-cols-md-4 g-1" style="max-height: 500px; overflow-y: auto;">
        <div class="col" v-for="pitanje in prikazanaPitanja" :key="pitanje.id">
          <div class="card h-100 shadow">
            <div class="card-body">
              <h5 class="card-title"><strong>Pitanje:</strong> {{ pitanje.naziv }}</h5>
              <p class="card-text">Tip odgovora: {{ isTextType(pitanje.tip) ? 'Tekstualni ' : 'Da/Ne' }}</p>
              <p v-if="isTextType(pitanje.tip)">Odgovor: {{ pitanje.tocan_odgovor }}</p>
              <p v-else>Odgovor: {{ Number(pitanje.tocan_odgovor) === 1 ? 'Da' : 'Ne' }}</p>
              <div class="mt-3">
                <button v-if="userRole === 'professor' || userRole === 'admin'" class="btn btn-sm btn-warning me-2" @click="pokreniUrediPitanje(pitanje)">Uredi</button>
                <button v-if="userRole === 'professor' || userRole === 'admin'" class="btn btn-sm btn-danger" @click="obrisiPitanje(pitanje.id)">Obriši</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="urediPitanje.id" class="modal-backdrop">
      <div class="modal d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Uredi pitanje</h5>
              <button type="button" class="btn-close" @click="urediPitanje = {}"></button>
            </div>
            <div class="modal-body">
              <input v-model="urediPitanje.naziv" type="text" class="form-control mb-2" placeholder="Novi naziv" />
              <select v-model="urediPitanje.tip" class="form-select mb-2">
                <option value="text">Tekstualni odgovor</option>
                <option value="truefalse">Da/Ne</option>
              </select>
              <input v-if="isTextType(urediPitanje.tip)" v-model="urediPitanje.tocan_odgovor" type="text" class="form-control" placeholder="Unesite tačan odgovor" />
              <select v-else v-model.number="urediPitanje.tocan_odgovor" class="form-select">
                <option :value="1">Da</option>
                <option :value="0">Ne</option>
              </select>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="urediPitanje = {}">Zatvori</button>
              <button class="btn btn-primary" @click="spremiUrediPitanje">Spremi</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: "PRIJAVI",
  props: { userRole: { type: String, required: true } },
  data() {
    return {
      apiUrl: "http://localhost/my_project",
      novoPitanje: "",
      tipPitanja: "",
      tocanOdgovorText: "",
      tocanOdgovorTF: null,
      svaPitanja: [],
      urediPitanje: {},
      noviTest: { naziv: "", odabranaPitanja: [] },
      toast: { show: false, message: "", type: "success" }
    };
  },
  computed: {
    prikazanaPitanja() {
      return this.svaPitanja.slice(0, 10);
    }
  },
  methods: {
    toggleSelectAll() {
      if (this.noviTest.odabranaPitanja.length === this.svaPitanja.length) {
        this.noviTest.odabranaPitanja = [];
      } else {
        this.noviTest.odabranaPitanja = this.svaPitanja.map(p => p.id);
      }
    },
    showToast(message, type = "success") {
      this.toast.message = message;
      this.toast.type = type;
      this.toast.show = true;
      setTimeout(() => { this.toast.show = false; }, 3000);
    },
    isTextType(tip) {
      return tip && tip.toString().trim().toLowerCase() === 'text';
    },
    async dodajPitanje() {
      let payload = {
        naziv: this.novoPitanje.trim(),
        tip: this.tipPitanja,
        tocan_odgovor: this.tipPitanja === 'text' ? this.tocanOdgovorText.trim() : this.tocanOdgovorTF
      };
      const res = await fetch(`${this.apiUrl}/dodaj-pitanje.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      this.showToast(json.message || (json.success ? "Pitanje dodano!" : "Greška!"), json.success ? "success" : "error");
      if (json.success) {
        this.novoPitanje = "";
        this.tipPitanja = "";
        this.tocanOdgovorText = "";
        this.tocanOdgovorTF = null;
        this.ucitajPitanja();
      }
    },
    async kreirajTest() {
      let payload = {
        naziv: this.noviTest.naziv.trim(),
        odabranaPitanja: this.noviTest.odabranaPitanja
      };
      const res = await fetch(`${this.apiUrl}/kreiraj-test.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      this.showToast(json.message || (json.success ? "Test kreiran!" : "Greška!"), json.success ? "success" : "error");
      if (json.success) {
        this.noviTest = { naziv: "", odabranaPitanja: [] };
      }
    },
    async ucitajPitanja() {
      const res = await fetch(`${this.apiUrl}/dohvati-pitanja.php`);
      const json = await res.json();
      if (json.success) {
        this.svaPitanja = json.pitanja.map(p => ({
          ...p,
          tip: p.tip ? p.tip.toString().trim().toLowerCase() : ''
        }));
      }
    },
    pokreniUrediPitanje(pitanje) {
      this.urediPitanje = { ...pitanje };
    },
    async spremiUrediPitanje() {
      if (this.urediPitanje.tip === 'truefalse') {
        this.urediPitanje.tocan_odgovor = Number(this.urediPitanje.tocan_odgovor);
      }
      const res = await fetch(`${this.apiUrl}/uredi-pitanje.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(this.urediPitanje),
      });
      const json = await res.json();
      this.showToast(json.message || (json.success ? "Pitanje uređeno!" : "Greška!"), json.success ? "success" : "error");
      if (json.success) {
        this.ucitajPitanja();
        this.urediPitanje = {};
      }
    },
    async obrisiPitanje(id) {
      const res = await fetch(`${this.apiUrl}/obrisi-pitanje.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id }),
      });
      const json = await res.json();
      this.showToast(json.message || (json.success ? "Pitanje obrisano!" : "Greška!"), json.success ? "success" : "error");
      if (json.success) {
        this.ucitajPitanja();
      }
    }
  },
  mounted() { this.ucitajPitanja(); },
};
</script>

<style>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 1050;
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
.toast-notification.success {
  background-color: #28a745;
}
.toast-notification.error {
  background-color: #dc3545;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
