<template>
  <div class="auth-container">
    <div class="auth-form">
      <div v-if="currentForm === 'login'" class="form-container">
        <h2>Prijava</h2>
        <form @submit.prevent="handleLogin">
          <input type="text" v-model="username" placeholder="Korisničko ime" required />
          <input type="password" v-model="password" placeholder="Šifra" required />
          <button type="submit">Prijava</button>
        </form>
        <p @click="switchForm('register')">Nemate nalog? Registrujte se</p>
      </div>

      <div v-if="currentForm === 'register'" class="form-container">
        <h2>Registracija</h2>
        <form @submit.prevent="handleRegister">
          <input type="text" v-model="username" placeholder="Korisničko ime" required />
          <input type="email" v-model="email" placeholder="E-mail" required />
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
          alert("Invalid username or password!");
        }
      } catch (error) {
        console.error("Login failed:", error);
        alert("An error occurred. Please try again.");
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
          alert("Registration successful! Please log in.");
          this.switchForm('login');
        } else {
          alert("Registration failed. Please try again.");
        }
      } catch (error) {
        console.error("Registration failed:", error);
        alert("An error occurred. Please try again.");
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
  background-size: cover;
}

.auth-form {
  background: rgba(255, 255, 255, 0.8);
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 100%;
  animation: fadeIn 0.5s ease-in-out;
}

.auth-form input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.auth-form button {
  width: 100%;
  padding: 10px;
  background-color: #007BFF;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.auth-form button:hover {
  background-color: #0056b3;
}

.auth-form p {
  text-align: center;
  cursor: pointer;
  color: #007BFF;
  margin-top: 15px;
}

.auth-form p:hover {
  text-decoration: underline;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
