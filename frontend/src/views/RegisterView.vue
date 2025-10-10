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
  //appel api pour l'inscription
  try {
    const response = await api.post('/register', {
      email,
      password,
    })
    //Si réponse OK
    if (response.status === 201) {
      // Redirige vers la page de connexion avec message flash
      await router.push({
        path: '/login',
        query: {flash: 'Inscription réussie !'},
      })
    }
  } catch (err) {
    error.value = err.response.data.message
  }
}
</script>
