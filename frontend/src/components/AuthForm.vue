<template>
  <div class="form-container">
    <h1>{{ title }}</h1>
    <form @submit.prevent="onSubmit">
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Mot de passe" required />

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

      <button type="submit" :disabled="validatePassword && !isPasswordValid || isLoading">
        <span v-if="!isLoading">{{ buttonText }}</span>
        <span v-else class="spinner"></span>
      </button>
    </form>

    <ul v-if="Array.isArray(error) && error.length" class="login-error-list">
      <li v-for="(err, i) in error" :key="i" class="login-error">{{ err }}</li>
    </ul>
    <p v-else-if="error" class="login-error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue'

const props = defineProps({
  title: { type: String, default: 'Connexion' },
  buttonText: { type: String, default: 'Se connecter' },
  error: { type: [String, Array], default: '' },
  validatePassword: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const email = ref('')
const password = ref('')
const isLoading = ref(false)

const isPasswordValid = computed(() => {
  return (
    password.value.length >= 8 &&
    /[A-Z]/.test(password.value) &&
    /\d/.test(password.value) &&
    /[\W_]/.test(password.value)
  )
})

function onSubmit() {
  if (props.validatePassword && !isPasswordValid.value) return

  isLoading.value = true
  emit('submit', { email: email.value, password: password.value })

  // Pour tester le spinner, on simule un délai de 2 secondes
  setTimeout(() => {
    isLoading.value = false
  }, 2000)
}
</script>

