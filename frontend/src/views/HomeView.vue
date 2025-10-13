<template>
  <div class="form-container">
    <div class="logout">
      <a @click="logout">Se déconnecter</a>
    </div>
    <h1 class="home-title">Contact</h1>

    <form @submit.prevent="onSubmit" class="contact-form">
      <label class="contact-label">
        Sujet :
        <input
          type="text"
          v-model="subject"
          required
          placeholder="Sujet de votre message"
          class="contact-input"
        />
      </label>

      <label class="contact-label">
        Message :
        <textarea
          v-model="message"
          required
          placeholder="Votre message"
          class="contact-textarea"
        ></textarea>
      </label>

      <label class="contact-label">
        Pièce jointe (PDF, max 2 Mo) :
        <input
          type="file"
          @change="handleFileChange"
          accept="application/pdf"
          class="contact-file"
        />
        <span v-if="fileError" class="contact-error">{{ fileError }}</span>
      </label>

      <button type="submit" :disabled="isSubmitting" class="contact-button">Envoyer</button>

      <p v-if="successMessage" class="contact-success">{{ successMessage }}</p>
      <p v-if="error" class="login-error">{{ error }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/api'
import '@/assets/mgp.css' // Chemin vers votre fichier CSS
import { useRouter } from 'vue-router'

const subject = ref('')
const message = ref('')
const file = ref(null)
const fileError = ref('')
const successMessage = ref('')
const error = ref('')
const isSubmitting = ref(false)
const router = useRouter()

function handleFileChange(event) {
  fileError.value = ''
  const selectedFile = event.target.files[0]

  if (selectedFile) {
    if (selectedFile.type !== 'application/pdf') {
      fileError.value = 'Seuls les fichiers PDF sont autorisés.'
      file.value = null
    } else if (selectedFile.size > 2 * 1024 * 1024) {
      fileError.value = 'Le fichier ne doit pas dépasser 2 Mo.'
      file.value = null
    } else {
      file.value = selectedFile
    }
  }
}

async function onSubmit() {
  error.value = ''
  successMessage.value = ''

  if (!subject.value || !message.value) return
  if (fileError.value) return

  const formData = new FormData()
  formData.append('subject', subject.value)
  formData.append('message', message.value)
  if (file.value) {
    formData.append('attachment', file.value)
  }

  isSubmitting.value = true

  try {
    await api.post('/send-message', formData)

    successMessage.value = 'Message envoyé avec succès !'
    subject.value = ''
    message.value = ''
    file.value = null
    document.querySelector('input[type=file]').value = ''
  } catch (err) {
    if (err.response && err.response.data) {
      // Affiche uniquement le message d'erreur texte
      error.value =
        err.response.data.message || err.response.data.error || 'Une erreur est survenue.'
    } else {
      error.value = 'Erreur réseau ou serveur.'
    }
  } finally {
    isSubmitting.value = false
  }
}

function logout() {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>
