<template>
  <div class="form-container">
    <h1>{{ title }}</h1>
    <form @submit.prevent="onSubmit">
      <input
        v-model="email"
        type="email"
        placeholder="Email"
        required
      />

      <input
        v-model="password"
        type="password"
        placeholder="Mot de passe"
        required
      />

      <!-- ✅ Affiche les règles de mot de passe uniquement en mode inscription -->
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

      <button
        type="submit"
        :disabled="validatePassword && !isPasswordValid"
      >
        {{ buttonText }}
      </button>
    </form>

    <ul v-if="Array.isArray(error) && error.length" class="login-error-list">
      <li v-for="(err, i) in error" :key="i" class="login-error">{{ err }}</li>
    </ul>
    <p v-else-if="error" class="login-error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue';

const props = defineProps({
  title: { type: String, default: 'Connexion' },
  buttonText: { type: String, default: 'Se connecter' },
  error: { type: [String, Array], default: '' },
  validatePassword: { type: Boolean, default: false } // 👈 active ou non la validation
});

const emit = defineEmits(['submit']);

const email = ref('');
const password = ref('');

const isPasswordValid = computed(() => {
  return (
    password.value.length >= 8 &&
    /[A-Z]/.test(password.value) &&
    /\d/.test(password.value) &&
    /[\W_]/.test(password.value)
  );
});

function onSubmit() {
  if (props.validatePassword && !isPasswordValid.value) return;
  emit('submit', { email: email.value, password: password.value });
}
</script>

<style scoped>
.login-error {
  color: #ef4444;
  text-align: left;
  font-size: 0.95rem;
  font-weight: 500;
  margin-top: 0.5rem;
}
.login-error-list {
  margin: 0.5rem 0 0 0;
  padding: 0;
  list-style: none;
}
.password-rules {
  list-style: none;
  padding: 0;
  margin: 0.5rem 0 1rem;
  font-size: 0.9rem;
  text-align: left;
}
.password-rules li {
  margin: 0.25rem 0;
  color: #ef4444;
}
.password-rules li.valid {
  color: #22c55e;
  font-weight: 500;
}
button[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
