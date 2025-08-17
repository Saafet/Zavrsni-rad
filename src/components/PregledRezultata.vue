<template>
  <div class="container my-5">
    <div v-if="toast.show" :class="['toast-notification', toast.type]">
      {{ toast.message }}
    </div>
    <h3>Pregled rezultata testova</h3>
    <div v-if="!odabraniTest" class="mb-3">
      <input v-model="search" placeholder="Pretraži testove..." class="form-control" />
    </div>

    <div v-if="!odabraniTest">
      <ul class="list-group">
        <li
          v-for="test in paginiraniTestovi"
          :key="test.id"
          class="list-group-item d-flex justify-content-between align-items-center"
        >
          <span>{{ test.naziv }} ({{ formatDatum(test.datum_kreiranja) }})</span>
          <button class="btn btn-sm btn-primary" @click="ucitajRezultate(test.id)">
            Pregledaj rezultate
          </button>
        </li>
      </ul>

      <nav v-if="totalStranica > 1" class="mt-2">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: trenutnaStranica === 1 }">
            <button class="page-link" @click="prethodnaStranica">Prethodno</button>
          </li>

          <li
            v-for="page in totalStranica"
            :key="page"
            class="page-item"
            :class="{ active: page === trenutnaStranica }"
          >
            <button class="page-link" @click="trenutnaStranica = page">{{ page }}</button>
          </li>

          <li class="page-item" :class="{ disabled: trenutnaStranica === totalStranica }">
            <button class="page-link" @click="sljedecaStranica">Sljedeće</button>
          </li>
        </ul>
      </nav>
    </div>

    <div v-else>
      <h4>Rezultati testa #{{ odabraniTest }}</h4>
      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th>Učenik</th>
            <th>Točno</th>
            <th>Ukupno</th>
            <th>Postotak</th>
            <th>Ocjena</th>
            <th>Datum</th>
            <th>Uredi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in rezultati" :key="r.id">
            <td>{{ r.ime_ucenika }} {{ r.prezime_ucenika }}</td>
            <td>{{ r.tocno }}</td>
            <td>{{ r.ukupno }}</td>
            <td>{{ r.procenat }}%</td>
            <td>
              <input type="number" v-model.number="r.ocjena" min="1" max="5" step="0.1"
                     class="form-control" style="width:80px;">
            </td>
            <td>{{ formatDatum(r.datum) }}</td>
            <td>
              <button class="btn btn-sm btn-success" @click="spremiOcjenu(r.id, r.ocjena)">
                Spremi
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="summary mt-2">
        <p><strong>Ukupno učenika:</strong> {{ totalUcenika }}</p>
        <p><strong>Prosjek ocjena:</strong> {{ prosjekOcjena }}</p>
        <p><strong>Najčešća ocjena:</strong> {{ najcescaOcjena }}</p>
      </div>
      <div class="mt-2 chart-container">
        <canvas id="analizaChart"></canvas>
      </div>

      <button class="btn btn-secondary mt-2" @click="zatvoriRezultate">Nazad</button>
    </div>
  </div>
</template>

<script>
import { Chart } from "chart.js/auto";
import { nextTick, watch } from "vue";

export default {
  data() {
    return {
      apiUrl: "http://localhost/my_project",
      testovi: [],
      odabraniTest: null,
      rezultati: [],
      chart: null,
      prosjekOcjena: 0,
      totalUcenika: 0,
      najcescaOcjena: 0,
      toast: { show: false, message: "", type: "success" },
      ocjeneBoje: [
        "rgba(255, 99, 132, 0.7)",
        "rgba(255, 159, 64, 0.7)",
        "rgba(255, 205, 86, 0.7)",
        "rgba(75, 192, 192, 0.7)",
        "rgba(54, 162, 235, 0.7)",
      ],
      search: "",
      trenutnaStranica: 1,
      perPage: 10
    };
  },

  computed: {
    filtriraniTestovi() {
      if (!this.search) return this.testovi;
      return this.testovi.filter(t =>
        t.naziv.toLowerCase().includes(this.search.toLowerCase())
      );
    },
    totalStranica() {
      return Math.ceil(this.filtriraniTestovi.length / this.perPage);
    },
    paginiraniTestovi() {
      const start = (this.trenutnaStranica - 1) * this.perPage;
      return this.filtriraniTestovi.slice(start, start + this.perPage);
    }
  },

  watch: {
    search() {
      this.trenutnaStranica = 1; 
    }
  },

  methods: {
    showToast(message, type = "success") {
      this.toast.message = message;
      this.toast.type = type;
      this.toast.show = true;
      setTimeout(() => (this.toast.show = false), 3000);
    },

    async ucitajTestove() {
      try {
        const res = await fetch(`${this.apiUrl}/get-testovi.php`);
        const json = await res.json();
        if (json.success) {
          this.testovi = json.testovi;
        }
      } catch (err) {
        console.error(err);
      }
    },

    async ucitajRezultate(testId) {
      await this.loadRezultati(testId);

      if (!this.rezultati.length) {
        this.showToast("Nema rezultata za ovaj test.", "warning");
        return;
      }

      this.odabraniTest = testId;
      await nextTick();
      this.napraviGrafikon();
    },

    async loadRezultati(testId) {
      try {
        const res = await fetch(
          `${this.apiUrl}/get-rezultati.php?test_id=${testId}`
        );
        const json = await res.json();
        if (json.success) {
          this.rezultati = json.rezultati;
        }
      } catch (err) {
        console.error(err);
      }
    },

    async spremiOcjenu(rezultatId, novaOcjena) {
      try {
        const res = await fetch(`${this.apiUrl}/update-ocjena.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            rezultat_id: rezultatId,
            nova_ocjena: novaOcjena,
          }),
        });
        const json = await res.json();

        if (json.success) {
          this.showToast(json.message || "Ocjena spremljena!", "success");
          const r = this.rezultati.find((r) => r.id === rezultatId);
          if (r) r.ocjena = novaOcjena;
          this.updateGrafikon();
        } else {
          this.showToast(json.message || "Greška pri spremanju!", "error");
        }
      } catch (err) {
        console.error(err);
        this.showToast("Greška pri spremanju!", "error");
      }
    },

    zatvoriRezultate() {
      this.odabraniTest = null;
      if (this.chart) this.chart.destroy();
      this.chart = null;
      this.totalUcenika = 0;
      this.prosjekOcjena = 0;
      this.najcescaOcjena = 0;
      this.rezultati = [];
    },

    formatDatum(d) {
      return new Date(d).toLocaleString();
    },

    napraviGrafikon() {
      if (!this.rezultati.length) return;

      this.izracunajStatistiku();

      const ctx = document.getElementById("analizaChart").getContext("2d");
      this.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ["1","2","3","4","5"],
          datasets: [
            {
              label: "Broj učenika",
              data: this.raspodjelaOcjena,
              backgroundColor: this.ocjeneBoje
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: `Broj učenika po ocjeni`,
              font: { size: 14 }
            },
            tooltip: {
              callbacks: {
                label: (context) => {
                  const idx = context.dataIndex;
                  const value = context.dataset.data[idx];
                  const postotak = ((value/this.totalUcenika)*100).toFixed(1);
                  return `${value} učenika (${postotak}%)`;
                }
              }
            },
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'Učenici' },
              ticks: { stepSize: 1 }
            },
            x: {
              title: { display: true, text: "Ocjena" }
            }
          }
        }
      });
    },

    updateGrafikon() {
      if (this.chart) {
        this.chart.destroy();
        this.chart = null;
      }
      this.napraviGrafikon();
    },

    izracunajStatistiku() {
      const ocjene = this.rezultati
        .map((r) => parseFloat(r.ocjena))
        .filter((o) => !isNaN(o) && o > 0);

      if (!ocjene.length) return;

      this.totalUcenika = ocjene.length;
      const avg = ocjene.reduce((a, b) => a + b, 0) / ocjene.length;
      this.prosjekOcjena = avg.toFixed(2);

      const count = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };
      ocjene.forEach((o) => {
        const round = Math.round(o);
        if (count[round] != null) count[round]++;
      });
      this.raspodjelaOcjena = Object.values(count);

      const naj = Object.entries(count).reduce(
        (a, b) => (b[1] > a[1] ? b : a),
        [0, 0]
      )[0];
      this.najcescaOcjena = naj;
    },

    prethodnaStranica() {
      if (this.trenutnaStranica > 1) this.trenutnaStranica--;
    },
    sljedecaStranica() {
      if (this.trenutnaStranica < this.totalStranica) this.trenutnaStranica++;
    }
  },

  mounted() {
    this.ucitajTestove();
  },
};
</script>

<style scoped>
.chart-container {
  width: 100%;
  max-width: 600px;
  height: 300px;
  margin: auto;
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

.toast-notification.warning {
  background-color: #ffc107; 
  color: black;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
