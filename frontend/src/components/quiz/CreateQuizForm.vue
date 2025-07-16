<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '/src/api/axios.ts'

const router = useRouter()

const prompt = ref('')
const count = ref(10)
const timePerQuestion = ref(30)
const isLoading = ref(false)

const createQuiz = async () => {
  const normalizePrompt = (str: string) =>
  str
    .trim()
    .toLowerCase()
    .normalize('NFD') // décompose les caractères accentués
    .replace(/[\u0300-\u036f]/g, '') // supprime les diacritiques (accents)
    .replace(/\s+/g, ' ') // unifie les espaces multiples

  const cleanPrompt = normalizePrompt(prompt.value)
  isLoading.value = true

  try {
    console.log("Checking if quiz already exists...")
    const initialResponse = await api.get(`/quizzes?title=${encodeURIComponent(cleanPrompt)}`)
    const quizzesArray = initialResponse?.data?.['member']
    const existingQuizzes = quizzesArray.filter(q =>
      typeof q.title === 'string' &&
      q.title.trim().toLowerCase() === cleanPrompt &&
      q.ready == true
    )

    if (existingQuizzes.length > 0) {
      const lastQuiz = existingQuizzes.reduce((max, q) => q.id > max.id ? q : max, existingQuizzes[0])
      console.log("Existing ready quiz found:", lastQuiz)
      return router.push({ name: 'Question', params: { id: lastQuiz.id } })
    }

    const payload = {
      prompt: cleanPrompt,
      count: count.value,
      timePerQuestion: timePerQuestion.value
    }
    console.log("Sending payload to create quiz:", payload)
    await api.post('/quizz', payload)

    const waitForQuiz = async (): Promise<void> => {
      const response = await api.get(`/quizzes?title=${encodeURIComponent(cleanPrompt)}`)
      const quizzesArray = response?.data?.['member']
      const quizzes = quizzesArray.filter(q =>
        typeof q.title === 'string' &&
        q.title.trim().toLowerCase() === cleanPrompt &&
        q.ready == true
      )

      if (quizzes.length > 0) {
        const lastQuiz = quizzes.reduce((max, q) => q.id > max.id ? q : max, quizzes[0])
        console.log("Quiz ready and found:", lastQuiz)
        await router.push({ name: 'Question', params: { id: lastQuiz.id } })
      } else {
        console.log("Quiz en cours de génération...")
        await new Promise(resolve => setTimeout(resolve, 1000))
        return waitForQuiz()
      }
    }

    await waitForQuiz()

  } catch (error) {
    console.error("Erreur lors de la création ou vérification du quizz:", error)
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
        <input v-model="prompt" type="text" class="w-full border rounded px-3 py-2" placeholder="Ex: Vue.js" />
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
