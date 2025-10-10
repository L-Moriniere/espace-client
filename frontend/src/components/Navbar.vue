<template>
  <nav class="navbar">
    <div class="navbar-left">
      <h1>Bonjour<span v-if="userEmail">, {{ userEmail }}</span></h1>
    </div>
    <div class="navbar-right">
      <button @click="logout">Se déconnecter</button>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const userEmail = ref('')

// Récupération de l'email de l'utilisateur connecté
onMounted(async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    router.push('/login')
    return
  }
})

// Déconnexion
function logout() {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #222;
  color: #fff;
  padding: 1rem 2rem;
}

.navbar h1 {
  font-size: 1.2rem;
  font-weight: 400;
}

.navbar button {
  background: #e74c3c;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  color: white;
  cursor: pointer;
  transition: background 0.2s;
}

.navbar button:hover {
  background: #c0392b;
}
</style>
