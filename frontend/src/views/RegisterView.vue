<template>
  <div class="form-bg">
    <div class="form-wrapper">
      <AuthForm
        title="Inscription"
        buttonText="S'inscrire"
        :error="error"
        :validatePassword="true"
        @submit="handleRegister"
      />

      <div class="form-links">
        <router-link to="/login">Se connecter</router-link>
        <router-link to="/reset-password">Mot de passe oublié ?</router-link>
      </div>
      <ul v-if="Array.isArray(error) && error.length" class="form-error-list">
        <li v-for="(err, i) in error" :key="i" class="form-error">{{ err }}</li>
      </ul>
      <p v-else-if="error" class="form-error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import AuthForm from '@/components/AuthForm.vue'
import '@/assets/main.css'

const router = useRouter()
const error = ref('')

async function handleRegister({ email, password }) {
  error.value = ''
  try {
    const response = await api.post('/register', {
      email,
      password,
    })

    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
      router.push('/dashboard')
    } else {
      router.push('/login')
    }
  } catch (err) {
    if (err.response && err.response.data) {
      // On récupère le message d'erreur précis de l'API
      const apiError = err.response.data.message || err.response.data.error || 'Erreur lors de l’inscription.'
      error.value = [apiError]
    } else {
      error.value = ['Erreur réseau ou serveur.']
    }
  }
}

</script>
