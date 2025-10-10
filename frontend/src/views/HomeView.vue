<template>
  <Navbar />
  <div class="contact-container">
    <h1 class="home-title">Contact</h1>

    <form @submit.prevent="handleSubmit" class="contact-form">
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

      <button
        type="submit"
        :disabled="isSubmitting"
        class="contact-button"
      >
        Envoyer
      </button>

      <p v-if="successMessage" class="contact-success">{{ successMessage }}</p>
      <p v-if="submitError" class="contact-error">{{ submitError }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import '@/assets/mgp.css' // Chemin vers votre fichier CSS
import Navbar from '@/components/Navbar.vue'

const subject = ref('');
const message = ref('');
const file = ref(null);
const fileError = ref('');
const successMessage = ref('');
const submitError = ref('');
const isSubmitting = ref(false);

function handleFileChange(event) {
  fileError.value = '';
  const selectedFile = event.target.files[0];

  if (selectedFile) {
    if (selectedFile.type !== 'application/pdf') {
      fileError.value = 'Seuls les fichiers PDF sont autorisés.';
      file.value = null;
    } else if (selectedFile.size > 2 * 1024 * 1024) {
      fileError.value = 'Le fichier ne doit pas dépasser 2 Mo.';
      file.value = null;
    } else {
      file.value = selectedFile;
    }
  }
}

async function handleSubmit() {
  submitError.value = '';
  successMessage.value = '';

  if (!subject.value || !message.value) return;

  if (fileError.value) return;

  const formData = new FormData();
  formData.append('subject', subject.value);
  formData.append('message', message.value);
  if (file.value) {
    formData.append('file', file.value);
  }

  isSubmitting.value = true;

  try {
    const response = await api.post('/send-message', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    successMessage.value = 'Message envoyé avec succès !';
    subject.value = '';
    message.value = '';
    file.value = null;
    document.querySelector('input[type=file]').value = '';
  } catch (err) {
    submitError.value = 'Erreur lors de l’envoi du message.';
  } finally {
    isSubmitting.value = false;
  }
}
</script>

