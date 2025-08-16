<template>
  <div class="container my-5">
    <h3>Pregled rezultata testova</h3>

    <div v-if="!odabraniTest">
      <ul class="list-group">
        <li
          v-for="test in testovi"
          :key="test.id"
          class="list-group-item d-flex justify-content-between align-items-center"
        >
          <span>{{ test.naziv }} ({{ formatDatum(test.datum_kreiranja) }})</span>
          <button class="btn btn-sm btn-primary" @click="ucitajRezultate(test.id)">
            Pregledaj rezultate
          </button>
        </li>
      </ul>
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
              <input
                type="number"
                v-model.number="r.ocjena"
                min="1"
                max="5"
                step="0.1"
                class="form-control"
                style="width:80px;"
              >
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

export default {
  data() {
    return {
      apiUrl: "http://localhost/my_project",
      testovi: [],
      odabraniTest: null,
      rezultati: [],
      testIntervalId: null,
      rezultatiIntervalId: null,
      chart: null,
      prosjekOcjena: 0,
      totalUcenika: 0,
      najcescaOcjena: 0,
      ocjeneBoje: [
        "rgba(255, 99, 132, 0.7)",   // 1
        "rgba(255, 159, 64, 0.7)",   // 2
        "rgba(255, 205, 86, 0.7)",   // 3
        "rgba(75, 192, 192, 0.7)",   // 4
        "rgba(54, 162, 235, 0.7)"    // 5
      ]
    };
  },
  methods: {
    async ucitajTestove() {
      try {
        const res = await fetch(`${this.apiUrl}/get-testovi.php`);
        const json = await res.json();
        if (json.success) this.testovi = json.testovi;
      } catch (e) {
        console.error(e);
      }
    },
    async ucitajRezultate(testId) {
      this.odabraniTest = testId;
      await this.loadRezultati(testId);
      if (this.rezultatiIntervalId) clearInterval(this.rezultatiIntervalId);
      this.rezultatiIntervalId = setInterval(() => {
        this.loadRezultati(testId, true); 
      }, 10000);
    },
    async loadRezultati(testId, update=false) {
      try {
        const res = await fetch(`${this.apiUrl}/get-rezultati.php?test_id=${testId}`);
        const json = await res.json();
        if (json.success) {
          this.rezultati = json.rezultati;
          if(update && this.chart){
            this.updateGrafikon();
          } else {
            this.napraviGrafikon();
          }
        }
      } catch (e) {
        console.error(e);
      }
    },
    async spremiOcjenu(rezultatId, novaOcjena) {
      try {
        const res = await fetch(`${this.apiUrl}/update-ocjena.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ rezultat_id: rezultatId, nova_ocjena: novaOcjena })
        });
        const json = await res.json();
        alert(json.message);
      } catch (e) {
        console.error(e);
      }
    },
    zatvoriRezultate() {
      this.odabraniTest = null;
      if (this.rezultatiIntervalId) {
        clearInterval(this.rezultatiIntervalId);
        this.rezultatiIntervalId = null;
      }
      if (this.chart) this.chart.destroy();
      this.prosjekOcjena = 0;
      this.totalUcenika = 0;
      this.najcescaOcjena = 0;
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
      if (!this.chart) return;
      this.izracunajStatistiku();
      this.chart.data.datasets[0].data = this.raspodjelaOcjena;
      this.chart.update();
    },

    izracunajStatistiku() {
      const ocjene = this.rezultati
        .map(r => parseFloat(r.ocjena))
        .filter(o => !isNaN(o) && o > 0);

      if (!ocjene.length) return;

      this.totalUcenika = ocjene.length;
      const prosjek = ocjene.reduce((a,b)=>a+b,0)/ocjene.length;
      this.prosjekOcjena = prosjek.toFixed(2);


      const rasp = {1:0,2:0,3:0,4:0,5:0};
      ocjene.forEach(o=>{
        const zaok = Math.round(o);
        if(rasp[zaok]!==undefined) rasp[zaok]++;
      });
      this.raspodjelaOcjena = Object.values(rasp);


      const najcesca = Object.entries(rasp).reduce((a,b)=> b[1] > a[1]? b : a, [0,0])[0];
      this.najcescaOcjena = najcesca;
    }
  },
  mounted() {
    this.ucitajTestove();
    this.testIntervalId = setInterval(this.ucitajTestove, 5000);
  },
  beforeUnmount() {
    if (this.testIntervalId) clearInterval(this.testIntervalId);
    if (this.rezultatiIntervalId) clearInterval(this.rezultatiIntervalId);
    if (this.chart) this.chart.destroy();
  }
};
</script>

<style scoped>
.chart-container {
  width: 100%;
  max-width: 600px;
  height: 300px;
  margin: auto;
}

.summary p {
  margin: 2px 0;
}
</style>
