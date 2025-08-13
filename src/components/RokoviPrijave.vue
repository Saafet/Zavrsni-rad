<template>
  <div  class="rokovi-container">
    <h2 v-if="userRole === 'professor' || userRole === 'admin'">Rokovi za prijavu testova</h2>

    <div v-if="userRole === 'professor' || userRole === 'admin'" class="dodaj-rok">
      <h3>Dodaj novi rok</h3>
      <form @submit.prevent="dodajRok">
        <label>
          Datum:
          <input type="date" v-model="noviRok.datum" required />
        </label>
        <label>
          Vrijeme:
          <input type="time" v-model="noviRok.vrijeme" required />
        </label>
        <label>
          Napomena:
          <input type="text" v-model="noviRok.napomena" />
        </label>
        <button type="submit">Dodaj rok</button>
      </form>
    </div>

    <h3>Popis rokova</h3>
    <ul class="rok-lista">
  <li v-for="rok in rokovi" :key="rok.id" class="rok-item">
    <div class="rok-info">
      <div class="datum-vrijeme">
        <span>{{ rok.datum }} {{ rok.vrijeme }}</span>
      </div>
      <div class="napomena" v-if="rok.napomena">
        - {{ rok.napomena }}
      </div>
    </div>

        <div class="akcije">
          
<button
  v-if="(userRole === 'student' || userRole === 'admin') && !prijavljeniRokoviIds.includes(rok.id)"
  @click="prijaviNaRok(rok.id)"
>
  Prijavi se
</button>


<button
  v-else-if="(userRole === 'student' || userRole === 'admin') && prijavljeniRokoviIds.includes(rok.id)"
  @click="odjaviSaRoka(rok.id)"
>
  Odjavi se
</button>


          
          <button v-if="userRole === 'professor' || userRole === 'admin'" @click="pripremiZaUredjivanje(rok)">Uredi</button>
          <button v-if="userRole === 'professor' || userRole === 'admin'" @click="obrisiRok(rok.id)">Obriši</button>
        </div>
      </li>
    </ul>

    
    <div v-if="urediRok.id" class="dodaj-rok">
      <h3>Uredi rok</h3>
      <form @submit.prevent="urediPostojeciRok">
        <label>
          Datum:
          <input type="date" v-model="urediRok.datum" required />
        </label>
        <label>
          Vrijeme:
          <input type="time" v-model="urediRok.vrijeme" required />
        </label>
        <label>
          Napomena:
          <input type="text" v-model="urediRok.napomena" />
        </label>
        <button type="submit">Spremi promjene</button>
        <button type="button" @click="urediRok = {}">Otkaži</button>
      </form>
    </div>

    <p v-if="poruka" :class="{'error-msg': porukaTip === 'error', 'success-msg': porukaTip === 'success'}">
      {{ poruka }}
    </p>
  </div>
</template>

<script>
export default {
  name: "RokPrijava",
  props: {
    userRole: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      rokovi: [],
      prijavljeniRokoviIds: [],
      noviRok: { datum: "", vrijeme: "", napomena: "" },
      urediRok: {},
      userId: 1,
      poruka: "",
      porukaTip: ""
    };
  },

  mounted() {
    this.dohvatiRokove();
    this.dohvatiPrijave();
  },
  methods: {
    async dohvatiRokove() {
      try {
        const res = await fetch("http://localhost/my_project/rokovi.php");
        const data = await res.json();
        if (data.success) this.rokovi = data.data;
        else this.prikaziPoruku(data.message || "Greška pri dohvatu rokova", "error");
      } catch {
        this.prikaziPoruku("Greška pri dohvatu rokova", "error");
      }
    },
    async dohvatiPrijave() {
      try {
        const res = await fetch(`http://localhost/my_project/prijave.php?user_id=${this.userId}`);
        const data = await res.json();
        if (data.success) {
          this.prijavljeniRokoviIds = data.data.map(p => p.rok_id);
        } else {
          this.prikaziPoruku(data.message || "Greška pri dohvatu prijava", "error");
        }
      } catch {
        this.prikaziPoruku("Greška pri dohvatu prijava", "error");
      }
    },
    async dodajRok() {
      try {
        const res = await fetch("http://localhost/my_project/rokovi.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.noviRok)
        });
        const data = await res.json();
        if (data.success) {
          this.prikaziPoruku("Rok uspješno dodan", "success");
          this.noviRok = { datum: "", vrijeme: "", napomena: "" };
          this.dohvatiRokove();
        } else {
          this.prikaziPoruku(data.message || "Greška pri dodavanju roka", "error");
        }
      } catch {
        this.prikaziPoruku("Greška pri dodavanju roka", "error");
      }
    },
    async prijaviNaRok(rokId) {
  try {
    const res = await fetch("http://localhost/my_project/prijave.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: this.userId, rok_id: rokId }),
    });
    const data = await res.json();
    if (data.success) {
      this.prikaziPoruku("Prijava uspješna", "success");

      
      if (!this.prijavljeniRokoviIds.includes(rokId)) {
        this.prijavljeniRokoviIds.push(rokId);
      }

      
      
    } else {
      this.prikaziPoruku(data.message || "Greška pri prijavi", "error");
      if (data.error) console.error("Detalji greške:", data.error);
    }
  } catch {
    this.prikaziPoruku("Greška pri prijavi", "error");
  }
},
    async odjaviSaRoka(rokId) {
      try {
        const res = await fetch("http://localhost/my_project/prijave.php", {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ user_id: this.userId, rok_id: rokId })
        });
        const data = await res.json();
        if (data.success) {
          this.prikaziPoruku("Odjavljeno sa roka", "success");
          this.dohvatiPrijave();
        } else {
          this.prikaziPoruku(data.message || "Greška pri odjavi", "error");
        }
      } catch {
        this.prikaziPoruku("Greška pri odjavi", "error");
      }
    },
    async obrisiRok(rokId) {
      if (!confirm("Jesi siguran da želiš obrisati ovaj rok?")) return;
      try {
        const res = await fetch(`http://localhost/my_project/rokovi.php?id=${rokId}`, {
          method: "DELETE"
        });
        const data = await res.json();
        if (data.success) {
          this.prikaziPoruku("Rok obrisan", "success");
          this.dohvatiRokove();
        } else {
          this.prikaziPoruku(data.message || "Greška pri brisanju", "error");
        }
      } catch {
        this.prikaziPoruku("Greška pri brisanju", "error");
      }
    },
    pripremiZaUredjivanje(rok) {
      this.urediRok = { ...rok };
    },
    async urediPostojeciRok() {
      try {
        const res = await fetch(`http://localhost/my_project/rokovi.php?id=${this.urediRok.id}`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.urediRok)
        });
        const data = await res.json();
        if (data.success) {
          this.prikaziPoruku("Rok ažuriran", "success");
          this.urediRok = {};
          this.dohvatiRokove();
        } else {
          this.prikaziPoruku(data.message || "Greška pri ažuriranju", "error");
        }
      } catch {
        this.prikaziPoruku("Greška pri ažuriranju", "error");
      }
    },
    
    prikaziPoruku(msg, tip) {
      this.poruka = msg;
      this.porukaTip = tip;
      setTimeout(() => {
        this.poruka = "";
        this.porukaTip = "";
      }, 4000);
    }
  }
};
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

:root {
  --primary: #e3f0ee; /* Darkly primary */
  --primary-dark: #e6f5f1; /* hover shade */
  --bg-glass: rgba(255, 255, 255, 0.05); /* transparent glass for dark bg */
  --border-glass: rgba(255, 255, 255, 0.15);
  --shadow-soft: 0 6px 20px rgba(0,0,0,0.4);
}

.rokovi-container {
  max-width: 1290px;
  margin: auto;
  font-family: 'Poppins', sans-serif;
  padding: 2rem;
  background: #2a2f3a; /* Darkly background */
  border-radius: 16px;
  color: #dee2e6; /* Light text from Darkly */
  box-shadow: var(--shadow-soft);
}

.dodaj-rok {
  background: var(--bg-glass);
  backdrop-filter: blur(8px);
  border: 1px solid var(--border-glass);
  padding: 1.2rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-soft);
  transition: transform 0.3s ease;
}

.dodaj-rok:hover {
  transform: translateY(-3px);
}

label {
  display: block;
  margin-bottom: 0.4rem;
  font-weight: 600;
  color: #fff;
}

input {
  display: block;
  margin-bottom: 1rem;
  width: 100%;
  padding: 0.6rem;
  border-radius: 8px;
  border: 1px solid var(--border-glass);
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  transition: all 0.3s ease;
}

input:focus {
  outline: none;
  border-color: var(--primary);
  background: rgba(255, 255, 255, 0.15);
}

button {
  background: var(--primary);
  color: white;
  border: none;
  padding: 0.6rem 1.2rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(234, 241, 240, 0.3);
}

button:hover {
  background: var(--primary-dark);
  transform: scale(1.05);
}

.rok-lista {
  list-style: none;
  padding: 0;
}

.rok-item {
  padding: 0.8rem 1rem;
  background: var(--bg-glass);
  backdrop-filter: blur(6px);
  border-radius: 10px;
  border: 1px solid var(--border-glass);
  margin-bottom: 0.6rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  color: #dee2e6;
}

.rok-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(232, 240, 239, 0.2);
}

.akcije {
  margin-left: 1rem;  
}

.error-msg {
  color: #e74c3c; /* Darkly danger */
  margin-top: 1rem;
  font-weight: 600;
}

.success-msg {
  color: #dfe9e7; /* Darkly success */
  margin-top: 1rem;
  font-weight: 600;
}
</style>
