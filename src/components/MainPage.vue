<template>
  <div>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Diplomski</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <div class="w-100 d-flex justify-content-between align-items-center">
            
            <ul class="navbar-nav mx-auto">

              <li class="nav-item" v-if="userRole === 'student' || userRole === 'professor' || userRole === 'admin'">
                <a class="nav-link" href="#prijavi">KREIRANA PITANJA</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="#kontakt">KONTAKT</a></li>
            </ul>
            <ul class="navbar-nav">
              <li class="nav-item">
                <button class="nav-link btn btn-link p-0" @click="logout" style="border: none; background: none;">Logout</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
    <Login v-if="!userRole" />

    <br><br>

    <section class="uvodni-tekst container container-custom mt-5 pt-5 py-5 bg-light rounded shadow">
      <div class="row align-items-center">
        
        <div class="col-md-6 px-5 text-dark">
          <h2 class="mb-3 fw-bold text-primary">Dobrodošli na online test!</h2>
          <p class="lead">
            <strong> Dobrodošli na Online Test Platformu za Studente</strong><br><br>
            Naša platforma omogućava jednostavno i interaktivno rješavanje testova direktno putem interneta.
            Bez obzira da li učite od kuće, iz studentskog doma ili biblioteke — sve što vam je potrebno je uređaj s internetom i vaš studentski nalog.
          </p>
          <a href="#testrjesavanje" class="btn btn-primary btn-lg mt-3">✏️ Riješi test</a>
        </div>

        <div class="col-md-6 text-center">
          <img src="/fotografije/sum.png" alt="Tehnologije"
            style="max-height: 220px;" />
        </div>

      </div>
    </section>


    
    <section id="prijavi" class="container mt-5" v-if="userRole === 'student' || userRole === 'professor' || userRole === 'admin'">
  <PRIJAVI :userRole="userRole" />
</section>

    <section id="prijavi" class="container mt-5" v-if="userRole === 'student' || userRole === 'professor' || userRole === 'admin'">
  <RokoviPrijave :userRole="userRole" />
</section>


    <section id="testrjesavanje" class="container mt-5" v-if="userRole === 'student' || userRole === 'professor' || userRole === 'admin'">
  <TestRjesavanje :userRole="userRole" />
</section>


    <section id="prijavi" class="container mt-5" v-if="userRole === 'student' || userRole === 'professor' || userRole === 'admin'">
  <PregledRezultata :userRole="userRole" />
</section>


    
    <section class="container text-center mt-5">
      <h2 class="mb-4 gradient-text">Statistika</h2>
      <div class="row mt-4">
        <div class="col-md-4">
          <div class="stat-box">
            <h3 class="counter gradient-text">37+</h3>
            <p class="gradient-text">Testova</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-box">
            <h3 class="counter gradient-text">21</h3>
            <p class="gradient-text">Aktivnih učenika</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-box">
            <h3 class="counter gradient-text">9</h3>
            <p class="gradient-text">Profesora</p>
          </div>
        </div>
      </div>
    </section>

    
    <section id="kontakt" class="container mt-5 mb-5">
      <h2 class="text-center mb-4">Kontaktirajte profesora ili admina</h2>
      <form @submit.prevent="posaljiKontakt" novalidate>
        <div class="mb-3">
          <label for="ime" class="form-label">Ime i prezime</label>
          <input id="ime" type="text" v-model.trim="kontakt.ime" class="form-control"
            :class="{ 'is-invalid': validacija && !kontakt.ime }" required />
          <div class="invalid-feedback">Molimo unesite ime.</div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email" v-model.trim="kontakt.email" class="form-control"
            :class="{ 'is-invalid': validacija && !validEmail(kontakt.email) }" required />
          <div class="invalid-feedback">Unesite ispravan email.</div>
        </div>

        <div class="mb-3">
          <label for="poruka" class="form-label">Poruka</label>
          <textarea id="poruka" rows="4" v-model.trim="kontakt.poruka" class="form-control"
            :class="{ 'is-invalid': validacija && !kontakt.poruka }" required></textarea>
          <div class="invalid-feedback">Poruka ne može biti prazna.</div>
        </div>

        <button type="submit" class="btn btn-primary">Pošalji</button>
      </form>
      <div v-if="feedbackPoruka" class="alert alert-success mt-3" role="alert">
        {{ feedbackPoruka }}
      </div>
    </section>
    <div v-if="(userRole === 'professor' || userRole === 'admin')" class="poruke-wrapper mt-5">
  <h4>Poruke učenika:</h4>
  <div class="poruke-container">
    <div
      v-for="msg in poruke"
      :key="msg.id"
      class="card shadow-sm"
      style="width: 350px;"
    >
      <div class="card-body">
        <h5 class="card-title"><strong>Ime i Prezime učenika: </strong>{{ msg.ime }}</h5>
        <h6 class="card-subtitle mb-2 text-muted"><strong>Email: </strong>{{ msg.email }}</h6>
        <p class="card-text"><strong>Poruka: </strong>{{ msg.poruka }}</p>
        <button class="btn btn-danger btn-sm" @click="obrisiPoruku(msg.id)">Obriši</button>
      </div>
    </div>
  </div>
</div>



    
    <footer class="custom-footer bg-dark text-white text-center py-4 mt-5">
      <div class="container">
        <p class="mb-2 fs-5 footer-title">Online testovi</p>
        <small class="d-block mb-3">Izradio: Safet Srna, 2025.</small>

        
        <div class="footer-icons d-flex justify-content-center gap-3">
          <a href="https://www.youtube.com/@universityofmostartv" target="_blank" class="footer-icon" title="YouTube">
            <i class="fab fa-youtube"></i>
          </a>
          <a href="https://www.instagram.com/fpmoz.mostar/" target="_blank" class="footer-icon" title="Instagram">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="https://mail.google.com/mail/u/3/#inbox?compose=CllgCJlGVGPWXsjDqRDKRKWCGcqfgkNlWFBxVHfXwXPxXxGRQpVgqMLKvDTkjWrsTPtmTZclWJV" class="footer-icon" title="Pošalji mail">
            <i class="fas fa-envelope"></i>
          </a>
          <a href="https://fpmoz.sum.ba/" target="_blank" class="footer-icon" title="Fakultet">
            <i class="fas fa-university"></i>
          </a>
        </div>
      </div>
    </footer>


    
    <button @click="scrollToTop" class="btn btn-primary position-fixed bottom-0 end-0 m-4" aria-label="Scroll to top">
      ⬆️
    </button>
  </div>
</template>

<script>
import PRIJAVI from './PRIJAVI.vue'
import LoginForm from './LoginForm.vue'
import RokoviPrijave from './RokoviPrijave.vue'
import TestRjesavanje from './TestRjesavanje.vue'
import PregledRezultata from './PregledRezultata.vue'

export default {
  name: 'App',
  components: { PRIJAVI, LoginForm, RokoviPrijave, TestRjesavanje, PregledRezultata },

  data() {
    return {
      prikaziLogin: false,
      userRole: null,

      kontakt: {
        ime: '',
        email: '',
        poruka: '',
      },

      validacija: false,
      feedbackPoruka: '',

      poruke: [], 
    }
  },

  created() {
    const user = localStorage.getItem("user");
    console.log('user iz localStorage:', user);
    if (!user) {
      this.$router.push('/');
    } else {
      const parsedUser = JSON.parse(user);
      console.log('Parsed user:', parsedUser);
      this.userRole = parsedUser.user.role;
      console.log('userRole:', this.userRole);

      if (this.userRole === 'professor' || this.userRole === 'admin') {
        this.ucitajPoruke();
      }
    }
  },

  methods: {
    validEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },

    async posaljiKontakt() {
      this.validacija = true;

      if (
        !this.kontakt.ime ||
        !this.validEmail(this.kontakt.email) ||
        !this.kontakt.poruka
      ) {
        this.feedbackPoruka = "Molimo popunite sva polja ispravno.";
        setTimeout(() => {
          this.feedbackPoruka = "";
        }, 5000);
        return;
      }

      try {
        const res = await fetch("http://localhost/my_project/kontakt.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(this.kontakt)
        });

        const data = await res.json();

        if (res.ok) {
          this.feedbackPoruka = data.poruka || "Poruka je uspješno poslata!";
          this.kontakt = { ime: '', email: '', poruka: '' };
          this.validacija = false;

          
          if (this.userRole === 'professor' || this.userRole === 'admin') {
            this.ucitajPoruke();
          }
        } else {
          this.feedbackPoruka = data.poruka || "Greška prilikom slanja poruke.";
        }
      } catch (error) {
        console.error("Greška:", error);
        this.feedbackPoruka = "Došlo je do greške prilikom slanja.";
      }
    },

    async ucitajPoruke() {
      try {
        const res = await fetch("http://localhost/my_project/getMessages.php");
        if (!res.ok) throw new Error("Greška pri dohvatu poruka.");
        this.poruke = await res.json();
      } catch (error) {
        console.error(error);
        this.poruke = [];
      }
    },

    async obrisiPoruku(id) {
      if (!confirm("Jeste li sigurni da želite obrisati ovu poruku?")) return;

      try {
        const res = await fetch("http://localhost/my_project/deleteMessage.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id }),
        });
        const data = await res.json();

        if (res.ok) {
          this.poruke = this.poruke.filter(p => p.id !== id);
        } else {
          alert(data.poruka || "Greška prilikom brisanja poruke.");
        }
      } catch (error) {
        console.error(error);
        alert("Greška pri brisanju poruke.");
      }
    },

    formatirajDatum(datum) {
      return new Date(datum).toLocaleString();
    },

    otvoriLogin() {
      this.prikaziLogin = true;
    },

    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    logout() {
      localStorage.removeItem("user");
      this.$router.push('/');
    },
  }
}
</script>


<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');


:root {
  --primary: #1e90ff;
  --primary-dark: #0056b3;
  --secondary: #ff6b81;
  --bg-light: #f9f9f9;
  --bg-dark: #1f1f1f;
  --shadow-soft: 0 6px 20px rgba(0,0,0,0.1);
}


.navbar {
  background: linear-gradient(90deg, #0d0d0d, #1a1a1a);
  box-shadow: 0 4px 12px rgba(0,0,0,0.5);
}

.navbar-brand {
  font-family: 'Racing Sans One', sans-serif;
  font-size: 1.8rem;
  color: var(--primary);
  text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
  transition: all 0.4s ease;
}

.navbar-brand:hover {
  color: var(--primary-dark);
  transform: scale(1.05);
}

.nav-link {
  color: #cccccc;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.nav-link:hover, .nav-link:focus {
  color: var(--primary);
  background-color: rgba(30, 144, 255, 0.15);
  text-shadow: 0 0 8px var(--primary);
  transform: translateY(-2px);
}

.nav-link.active {
  color: var(--primary);
  font-weight: 700;
  border-bottom: 2px solid var(--primary);
}

.navbar-toggler {
  border-color: var(--primary);
}

.navbar-toggler-icon {
  filter: invert(60%) sepia(90%) saturate(500%) hue-rotate(180deg) brightness(90%) contrast(85%);
}


.uvodni-tekst {
  font-family: 'Poppins', sans-serif;
  line-height: 1.6;
  color: #333333;
}

.uvodni-tekst h2 {
  font-weight: 600;
  color: var(--primary);
}

.uvodni-tekst p.lead {
  font-weight: 500;
  font-size: 1.125rem;
  color: #555555;
}

.uvodni-tekst p {
  font-size: 1rem;
  color: #666666;
}

.images-container {
  position: absolute;
  top: 0;
  right: 0;
  width: 300px;
  height: 300px;
  overflow: hidden;
}

.image-right img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.image-right img:hover {
  transform: scale(1.05) rotate(1deg);
}

.tech-card {
  width: 100%;
  max-width: 300px;
  height: auto;
  background: linear-gradient(145deg, #2b2b2b, #3a3a3a);
  border-radius: 14px;
  box-shadow: var(--shadow-soft);
  transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

.tech-card:hover {
  background-color: rgb(120, 122, 124);
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(30, 144, 255, 0.3);
}


.video-wrapper {
  background-color: #f4f6fa;
  border: 2px solid #2a5298;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 8px 18px rgba(42, 82, 152, 0.2);
  width: 100%;
  max-width: 500px;
  margin-bottom: 2rem;
}


.stat-box {
  background-color: rgb(199, 237, 241);
  padding: 30px;
  border-radius: 15px;
  box-shadow: var(--shadow-soft);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-box:hover {
  transform: scale(1.07);
  box-shadow: 0 12px 28px rgba(30,144,255,0.3);
}

.counter {
  font-size: 2.5rem;
  font-weight: bold;
  animation: countUp 1.5s ease-out;
}

@keyframes countUp {
  0% { transform: scale(0); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

.gradient-text {
  background: linear-gradient(45deg,rgb(78, 78, 175),rgb(101, 160, 223));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  color: transparent;
}


.custom-footer {
  background-color: #000;
  font-family: 'Poppins', sans-serif;
  border-top: 2px solid #444;
}

.footer-title {
  font-weight: 600;
  letter-spacing: 0.5px;
  color: #cfd8dc;
}

.footer-icons .footer-icon {
  color: #b0bec5;
  font-size: 1.5rem;
  transition: all 0.4s ease;
}

.footer-icons .footer-icon:hover {
  color: var(--primary);
  transform: rotate(10deg) scale(1.2);
  text-shadow: 0 0 12px var(--primary);
}


.card {
  border: none;
  border-radius: 12px;
  background: linear-gradient(145deg, #1c1c1c, #2e2e2e);
  color: #e0e0e0;
  box-shadow: 0 8px 18px rgba(30,144,255,0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(30,144,255,0.35);
}


.btn-danger {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  background-color: #ff4d4d;
  border: none;
  padding: 0.4rem 1rem;
  border-radius: 8px;
  transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-danger:hover {
  background-color: #cc0000;
  transform: scale(1.07);
  box-shadow: 0 6px 12px rgba(255,77,77,0.3);
}

.poruke-wrapper {
  max-width: 1500px;
  margin: 0 auto;
  padding: 1rem;
}

.poruke-wrapper h4 {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: var(--primary);
  text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
  text-align: center;
}

.poruke-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem;
}

.page-frame {
  max-width: 100%;
  padding: 1rem;
  box-sizing: border-box;
}
.container-custom {
  max-width: 1275px;
  margin: 0 auto;
}
</style>
