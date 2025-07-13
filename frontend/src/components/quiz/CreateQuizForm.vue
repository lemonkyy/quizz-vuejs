<script setup lang="ts">
import { ref } from 'vue'
import api from '/src/api/axios.ts'

const prompt = ref('')
const count = ref(10)
const timePerQuestion = ref(30)
const isLoading = ref(false)

const createQuiz = async () => {
  isLoading.value = true
  try {
    const payload = {
      prompt: prompt.value,
      count: count.value,
      timePerQuestion: timePerQuestion.value
    }
    console.log("Sending payload:", payload)

    const response = await api.post('/quizz', payload)

    console.log("Quiz created:", response.data)
    // TODO: rediriger vers la page de quiz, ou charger les questions

  } catch (error) {
    console.error("Erreur lors de la création du quizz:", error)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Créer un Quiz</h2>
    <form @submit.prevent="createQuiz">
      <div class="mb-4">
        <label class="block mb-1 font-semibold">Topic (Prompt)</label>
        <input v-model="prompt" type="text" class="w-full border rounded px-3 py-2" placeholder="Ex: Star Wars" />
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-semibold">Nombre de questions</label>
        <input v-model="count" type="number" class="w-full border rounded px-3 py-2" min="1" />
      </div>
      <div class="mb-6">
        <label class="block mb-1 font-semibold">Temps par question (s)</label>
        <input v-model="timePerQuestion" type="number" class="w-full border rounded px-3 py-2" min="5" />
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" :disabled="isLoading">
        {{ isLoading ? 'Création...' : 'Créer le Quiz' }}
      </button>
    </form>
  </div>
</template>
