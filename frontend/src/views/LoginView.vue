<template>
  <FlashMessage
    :message="flashMessage"
    type="success"
  />

  <div class="form-bg">
    <div class="form-wrapper">
      <AuthForm
        title="Connexion"
        buttonText="Se connecter"
        :error="loginError"
        @submit="handleLogin"
      />
      <div class="form-links">
        <router-link to="/register">S'inscrire</router-link>
        <router-link to="/reset-password">Mot de passe oublié ?</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import AuthForm from '@/components/AuthForm.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import '@/assets/main.css';

const router = useRouter();
const route = useRoute();

const loginError = ref('');       // Nom de variable plus explicite
const flashMessage = ref('');    // Nom de variable plus explicite

// Fonction pour gérer le flash message
function handleFlashMessage() {
  if (route.query.flash) {
    flashMessage.value = route.query.flash;
    // Netoyer l'URL après affichage
    history.replaceState(null, '', route.path);
  }
}

// Fonction pour gérer la connexion
async function handleLogin({ email, password }) {
  try {
    const response = await fetch('http://localhost:8000/api/login_check', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password }),
    });

    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.message || 'Une erreur inattendue est survenue.');
    }

    localStorage.setItem('token', data.token); // Stockage du token
    router.push('/home');                      // Redirection
  } catch (err) {
    loginError.value = err.message;           // Gestion de l'erreur
  }
}

onMounted(handleFlashMessage); // Appeler la fonction dans onMounted
</script>
