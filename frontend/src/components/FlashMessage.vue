<template>
  <transition name="fade">
    <div
      v-if="visible"
      :class="['flash-message', typeClass]"
    >
      <p>{{ message }}</p>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue'

const props = defineProps({
  message: { type: String, required: true },
  type: { type: String, default: 'success' },
  duration: { type: Number, default: 3000 } // durée avant disparition
})

const visible = ref(false)
const typeClass = computed(() => 'flash-success')

// Disparition après la durée définie
onMounted(() => {
  setTimeout(() => {
    visible.value = false
  }, props.duration)
})

// Si le message change, on réaffiche
watch(() => props.message, () => {
  visible.value = true
  setTimeout(() => (visible.value = false), props.duration)
})
</script>

