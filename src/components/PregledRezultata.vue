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
      <button class="btn btn-secondary mt-2" @click="zatvoriRezultate">Nazad</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      apiUrl: "http://localhost/my_project",
      testovi: [],
      odabraniTest: null,
      rezultati: [],
      testIntervalId: null,
      rezultatiIntervalId: null
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
        this.loadRezultati(testId);
      }, 10000);
    },
    async loadRezultati(testId) {
      try {
        const res = await fetch(`${this.apiUrl}/get-rezultati.php?test_id=${testId}`);
        const json = await res.json();
        if (json.success) this.rezultati = json.rezultati;
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
    },
    formatDatum(d) {
      return new Date(d).toLocaleString();
    }
  },
  mounted() {
    this.ucitajTestove();
    this.testIntervalId = setInterval(this.ucitajTestove, 5000);
  },
  beforeUnmount() {
    if (this.testIntervalId) clearInterval(this.testIntervalId);
    if (this.rezultatiIntervalId) clearInterval(this.rezultatiIntervalId);
  }
};
</script>
