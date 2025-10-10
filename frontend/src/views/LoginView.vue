<template>
  <div class="form-bg">
    <div class="form-wrapper">
      <AuthForm
        title="Connexion"
        buttonText="Se connecter"
        :error="error"
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AuthForm from '@/components/AuthForm.vue';
import '@/assets/main.css'

const router = useRouter();
const error = ref('');

async function handleLogin({ email, password }) {
  try {
    const response = await fetch('http://localhost:8000/api/login_check', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password }), // <-- pas de .value
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Erreur de connexion')
    }

    localStorage.setItem('token', data.token)
    router.push('/home')
  } catch (err) {
    error.value = err.message
  }
}

</script>

