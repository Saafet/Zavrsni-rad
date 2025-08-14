<template>
  <div class="container my-5">

    <!-- Dodavanje novog pitanja (samo za profesore i admina) -->
    <div v-if="userRole === 'professor' || userRole === 'admin'" class="mb-5 border rounded p-3 bg-light">
      <h3>Dodaj novo pitanje</h3>
      <form @submit.prevent="dodajPitanje" class="row g-2 align-items-center">
        <div class="col-auto flex-grow-1">
          <input
            v-model="novoPitanje"
            type="text"
            class="form-control"
            placeholder="Unesite novo pitanje"
            required
          />
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-success">Dodaj pitanje</button>
        </div>
      </form>
    </div>

    <!-- Kreiranje testa (samo za profesore i admina) -->
    <div v-if="userRole === 'professor' || userRole === 'admin'" class="mb-5 border rounded p-3 bg-light">
      <h3>Kreiraj test</h3>
      <form @submit.prevent="kreirajTest" class="row g-2">
        <div class="col-12 mb-2">
          <input
            v-model="noviTest.naziv"
            type="text"
            class="form-control"
            placeholder="Naziv testa"
            required
          />
        </div>

        <div class="col-12">
          <p><strong>Odaberite pitanja:</strong></p>
          <div v-for="pitanje in svaPitanja" :key="pitanje.id" class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              :value="pitanje.id"
              v-model="noviTest.odabranaPitanja"
            />
            <label class="form-check-label">
              {{ pitanje.naziv }}
            </label>
          </div>
        </div>

        <div class="col-12 mt-3">
          <button type="submit" class="btn btn-primary">Spremi test</button>
        </div>
      </form>
    </div>

    <!-- Lista svih pitanja -->
    <div class="mb-5">
      <h3>Kreirana pitanja</h3>
      <div class="row row-cols-1 row-cols-md-4 g-1">
        <div class="col" v-for="pitanje in svaPitanja" :key="pitanje.id">
          <div class="card h-100 shadow">
            <div class="card-body">
              <h5 class="card-title">{{ pitanje.naziv }}</h5>
              <div class="mt-3">
                <button v-if="userRole === 'professor' || userRole === 'admin'" class="btn btn-sm btn-warning me-2" @click="pokreniUrediPitanje(pitanje)">Uredi</button>
                <button v-if="userRole === 'professor' || userRole === 'admin'" class="btn btn-sm btn-danger" @click="obrisiPitanje(pitanje.id)">Obriši</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal za uređivanje pitanja -->
    <div v-if="urediPitanje.id" class="modal-backdrop">
      <div class="modal d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Uredi pitanje</h5>
              <button type="button" class="btn-close" @click="urediPitanje = {}"></button>
            </div>
            <div class="modal-body">
              <input
                v-model="urediPitanje.naziv"
                type="text"
                class="form-control"
                placeholder="Novi naziv"
              />
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
  props: {
    userRole: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      apiUrl: "http://localhost/my_project",
      forma: { ime: "", prezime: "", email: "", datum: "", pitanje_id: "" },
      novoPitanje: "",
      svaPitanja: [],
      urediPitanje: {},
      noviTest: { naziv: "", odabranaPitanja: [] } // Dodano za kreiranje testa
    };
  },
  methods: {
    async dodajPitanje() {
      if (!this.novoPitanje.trim()) {
        alert("Unesite naziv pitanja.");
        return;
      }
      try {
        const res = await fetch(`${this.apiUrl}/dodaj-pitanje.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ naziv: this.novoPitanje.trim() }),
        });
        const json = await res.json();
        if (json.success) {
          alert(json.message);
          this.novoPitanje = "";
          this.ucitajPitanja();
        } else {
          alert(json.message || "Greška pri dodavanju pitanja.");
        }
      } catch (e) {
        alert("Greška kod dodavanja pitanja.");
        console.error(e);
      }
    },

    async ucitajPitanja() {
      try {
        const res = await fetch(`${this.apiUrl}/dohvati-pitanja.php`);
        const json = await res.json();
        if (json.success) {
          this.svaPitanja = json.pitanja;
        } else {
          alert(json.message || "Greška kod dohvaćanja pitanja.");
        }
      } catch (e) {
        console.error("Greška kod dohvaćanja pitanja:", e);
      }
    },

    async kreirajTest() {
      if (!this.noviTest.naziv.trim()) {
        alert("Unesite naziv testa.");
        return;
      }
      if (this.noviTest.odabranaPitanja.length === 0) {
        alert("Odaberite barem jedno pitanje.");
        return;
      }
      try {
        const res = await fetch(`${this.apiUrl}/kreiraj-test.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.noviTest),
        });
        const json = await res.json();
        if (json.success) {
          alert("Test uspješno kreiran!");
          this.noviTest = { naziv: "", odabranaPitanja: [] };
        } else {
          alert(json.message || "Greška pri kreiranju testa.");
        }
      } catch (e) {
        console.error("Greška pri kreiranju testa:", e);
        alert("Greška pri kreiranju testa.");
      }
    },

    pokreniUrediPitanje(pitanje) {
      this.urediPitanje = { ...pitanje };
    },
    async spremiUrediPitanje() {
      try {
        const res = await fetch(`${this.apiUrl}/uredi-pitanje.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.urediPitanje),
        });
        const json = await res.json();
        if (json.success) {
          alert("Pitanje uspješno uređeno.");
          await this.ucitajPitanja();
          this.urediPitanje = {};
        } else {
          alert(json.message || "Greška pri uređivanju.");
        }
      } catch (e) {
        console.error("Greška:", e);
      }
    },
    async obrisiPitanje(id) {
      try {
        let res = await fetch(`${this.apiUrl}/obrisi-pitanje.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id }),
        });
        let json = await res.json();

        if (json.success) {
          alert(json.message);
          this.ucitajPitanja();
        } else if (json.occupied) {
          if (confirm(json.message)) {
            res = await fetch(`${this.apiUrl}/obrisi-pitanje.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ id, force: true }),
            });
            json = await res.json();

            if (json.success) {
              alert(json.message);
              this.ucitajPitanja();
            } else {
              alert(json.message || "Greška pri brisanju pitanja.");
            }
          }
        } else {
          alert(json.message || "Greška pri brisanju pitanja.");
        }
      } catch (e) {
        console.error("Greška:", e);
        alert("Greška pri brisanju pitanja.");
      }
    },
  },
  mounted() {
    this.ucitajPitanja();
  },
};
</script>

<style>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 1050;
}
</style>
