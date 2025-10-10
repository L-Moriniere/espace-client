<template>
  <div class="form-bg">
    <div class="form-wrapper">

        <form @submit.prevent="onSubmit">
          <!-- Étape 1 : Email -->
          <div class="form-container" v-if="!emailValidated">
            <h1>Vérifier adresse email</h1>

            <input v-model="email" type="email" placeholder="Email" required />
            <button type="submit" :disabled="loading">
              {{ loading ? 'Vérification...' : 'Vérifier email' }}
            </button>
            <!-- Affichage des erreurs -->
            <ul v-if="Array.isArray(error) && error.length" class="login-error-list">
              <li v-for="(err, i) in error" :key="i" class="login-error">{{ err }}</li>
            </ul>
            <p v-else-if="error" class="login-error">{{ error }}</p>
          </div>

          <!-- Étape 2 : Nouveau mot de passe -->
          <div class="form-container" v-else>
            <h1>Réinitialiser mot de passe</h1>

            <input v-model="password" type="password" placeholder="Mot de passe" required />

            <!-- Règles de mot de passe -->
            <ul v-if="validatePassword" class="password-rules">
              <li :class="{ valid: password.length >= 8 }">
                {{ password.length >= 8 ? '✅' : '❌' }} Au moins 8 caractères
              </li>
              <li :class="{ valid: /[A-Z]/.test(password) }">
                {{ /[A-Z]/.test(password) ? '✅' : '❌' }} Une majuscule
              </li>
              <li :class="{ valid: /\d/.test(password) }">
                {{ /\d/.test(password) ? '✅' : '❌' }} Un chiffre
              </li>
              <li :class="{ valid: /[\W_]/.test(password) }">
                {{ /[\W_]/.test(password) ? '✅' : '❌' }} Un caractère spécial
              </li>
            </ul>

            <button type="submit" :disabled="(validatePassword && !isPasswordValid) || loading">
              {{ loading ? 'Réinitialisation...' : 'Réinitialiser' }}
            </button>
            <!-- Affichage des erreurs -->
            <ul v-if="Array.isArray(error) && error.length" class="login-error-list">
              <li v-for="(err, i) in error" :key="i" class="login-error">{{ err }}</li>
            </ul>
            <p v-else-if="error" class="login-error">{{ error }}</p>
          </div>

        </form>



      <!-- Liens -->
      <div class="form-links">
        <router-link to="/login">Se connecter</router-link>
        <router-link to="/register">S'inscrire</router-link>
      </div>
      </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router';

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const emailValidated = ref(false)
const validatePassword = ref(false)

const router = useRouter();

const isPasswordValid = computed(() => {
  return (
    password.value.length >= 8 &&
    /[A-Z]/.test(password.value) &&
    /\d/.test(password.value) &&
    /[\W_]/.test(password.value)
  )
})

async function onSubmit() {
  error.value = ''
  loading.value = true

  try {
    if (!emailValidated.value) {
      // Étape 1 : Vérifier email
      try {
        const res = await fetch('http://localhost:8000/api/request-reset-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email.value }),
        })
        const data = await res.json()
        if (!res.ok) {
          error.value = data.message
          return
        }
        emailValidated.value = true
        validatePassword.value = true
      } catch (err) {
        error.value = err.message
      }
    } else {
      const res = await fetch('http://localhost:8000/api/reset-password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email.value, password: password.value }),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.message || 'Erreur lors de la réinitialisation')
      router.push('/login')
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>
