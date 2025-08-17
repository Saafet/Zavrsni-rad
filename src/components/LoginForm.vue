<template>
  <div class="auth-container">
    <div class="auth-card" :class="{ flipped: currentForm === 'register' }">
      
      <div class="form-side front">
        <h2>Prijava</h2>
        <form @submit.prevent="handleLogin">
          <input type="text" v-model="username" placeholder="Korisničko ime" required />
          <input type="password" v-model="password" placeholder="Šifra" required />
          <button type="submit">Prijava</button>
        </form>
        <p @click="switchForm('register')">Nemate nalog? Registrujte se</p>
      </div>

      <div class="form-side back">
        <h2>Registracija</h2>
        <form @submit.prevent="handleRegister">
          <input type="text" v-model="username" placeholder="Korisničko ime" required />
          <input type="email" v-model="email" placeholder="Email" required />
          <input type="password" v-model="password" placeholder="Šifra" required />
          <button type="submit">Registruj se</button>
        </form>
        <p @click="switchForm('login')">Već imate nalog? Prijavite se</p>
      </div>

    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      currentForm: 'login',
      username: '',
      password: '',
      email: ''
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post("http://localhost/my_project/login.php", {
          username: this.username,
          password: this.password
        });

        if (response.data.status === "success") {
          localStorage.setItem("user", JSON.stringify(response.data));
          this.$router.push('/main');
        } else {
          alert("Neispravno korisničko ime ili šifra.");
        }
      } catch (error) {
        alert("Greška pri prijavi.");
      }
    },

    async handleRegister() {
      try {
        const response = await axios.post("http://localhost/my_project/register.php", {
          username: this.username,
          email: this.email,
          password: this.password
        });

        if (response.data.status === "success") {
          alert("Registracija uspešna!");
          this.switchForm('login');
        } else {
          alert("Registracija nije uspela.");
        }
      } catch (error) {
        alert("Greška pri registraciji.");
      }
    },

    switchForm(form) {
      this.currentForm = form;
    }
  }
};
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #53565a, #909192);
  perspective: 1200px;
}

.auth-card {
  width: 100%;
  max-width: 400px;
  height: 480px;
  position: relative;
  transform-style: preserve-3d;
  transition: transform 0.8s ease;
}

.auth-card.flipped {
  transform: rotateY(180deg);
}

.form-side {
  position: absolute;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(10px);
  border-radius: 10px;
  padding: 40px 30px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  backface-visibility: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.front {
  transform: rotateY(0deg);
}

.back {
  transform: rotateY(180deg);
}

.form-side h2 {
  text-align: center;
  color: #000000;
  margin-bottom: 25px;
}

.form-side input {
  width: 100%;
  padding: 12px 16px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
  background: #f9f9f9;
  transition: 0.3s ease;
}

.form-side input:focus {
  outline: none;
  background: #eef6ff;
  border-color: #515152;
}

.form-side button {
  width: 100%;
  padding: 12px;
  background-color: #303131;
  color: white;
  border: none;
  font-weight: bold;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
  transition: transform 0.2s ease-in-out, background-color 0.3s ease;
}

.form-side button:hover {
  transform: scale(1.04);
  background-color: #0056b3;
}

.form-side p {
  margin-top: 15px;
  text-align: center;
  color: #007BFF;
  cursor: pointer;
  transition: 0.3s;
}
.form-side p:hover {
  text-decoration: underline;
}

@media (max-width: 420px) {
  .auth-card {
    width: 90%;
    height: auto;
  }

  .form-side {
    padding: 30px 20px;
  }
}
</style>
