<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import Button from '@/components/ui/atoms/Button.vue';
import Input from '@/components/ui/atoms/Input.vue';
import ActiveTimerInput from '../ui/molecules/inputs/ActiveTimerInput.vue';
import { useAuthStore } from '@/store/auth';
import { useRoomStore } from '@/store/room';
import { useToast } from 'vue-toastification';
import Checkbox from '../ui/atoms/Checkbox.vue';
import Error from '../ui/atoms/Error.vue';
import { badWords } from '@/utils/profanity';

const router = useRouter()
const authStore = useAuthStore()
const roomStore = useRoomStore()
const toast = useToast()

const formError = ref<string | null>(null);
const isPublic = ref(false)
const prompt = ref(router.currentRoute.value.query.prompt as string || '')
const count = ref(10)
const isLoading = ref(false)
const minutes = ref(0)
const seconds = ref(30)
//const invite = ref('')
const timePerQuestion = computed(() => minutes.value * 60 + seconds.value)

const createQuiz = async () => {

  if (!prompt.value.trim()) {
    formError.value = 'Please enter a prompt for the quiz.';
    return;
  }

  const containsBadWord = badWords.some(word => prompt.value.toLowerCase().includes(word));
  if (containsBadWord) {
    formError.value = 'Please refrain from using inappropriate language.';
    return;
  }

  if (!authStore.user) {
    console.error('User must be authenticated to create a quiz/room');
    router.push('/login');
    return;
  }

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
    const roomResponse = await api.post('/room/create', {
      isPublic: isPublic.value,
    })

    const room = roomResponse?.data
    console.log("Room created:", room)

    roomStore.currentRoom = room;

    localStorage.setItem('currentQuizTopic', prompt.value);

    toast.success(`Room créée avec le code: ${room.code}`);

    router.push('/room');

    createQuizInBackground(cleanPrompt, count.value, timePerQuestion.value);

  } catch (error) {
    console.error("Erreur lors de la création de la room:", error)
  } finally {
    isLoading.value = false
  }
}

const createQuizInBackground = async (cleanPrompt: string, countValue: number, timePerQuestionValue: number) => {
  try {
    console.log("Checking if quiz already exists...")
    const initialResponse = await api.get(`/quizzes?title=${encodeURIComponent(cleanPrompt)}`)
    const quizzesArray = initialResponse?.data?.['member']
    const existingQuizzes = quizzesArray.filter((q: any) =>
      typeof q.title === 'string' &&
      q.title.trim().toLowerCase() === cleanPrompt &&
      q.ready == true
    )

    if (existingQuizzes.length > 0) {
      const lastQuiz = existingQuizzes.reduce((max: any, q: any) => q.id > max.id ? q : max, existingQuizzes[0])
      console.log("Existing ready quiz found:", lastQuiz)
      return
    }

    const payload = {
      prompt: cleanPrompt,
      count: countValue,
      timePerQuestion: timePerQuestionValue
    }
    console.log("Sending payload to create quiz:", payload)
    await api.post('/quizz', payload)

    const waitForQuiz = async (): Promise<void> => {
      const response = await api.get(`/quizzes?title=${encodeURIComponent(cleanPrompt)}`)
      const quizzesArray = response?.data?.['member']
      const quizzes = quizzesArray.filter((q: any) =>
        typeof q.title === 'string' &&
        q.title.trim().toLowerCase() === cleanPrompt &&
        q.ready == true
      )

      if (quizzes.length > 0) {
        const lastQuiz = quizzes.reduce((max: any, q: any) => q.id > max.id ? q : max, quizzes[0])
        console.log("Quiz ready and found:", lastQuiz)
        return
      } else {
        console.log("Quiz en cours de génération...")
        await new Promise(resolve => setTimeout(resolve, 1000))
        return waitForQuiz()
      }
    }

    await waitForQuiz()

  } catch (error) {
    console.error("Erreur lors de la création ou vérification du quizz:", error)
  }
}
</script>

<template>
  <div class="mx-auto p-6 w-100">
    <h2 class="text-xl font-bold mb-4">Quiz creation</h2>

    <form @submit.prevent="createQuiz">
      <div class="mb-4">
        <Input
          id="prompt"
          v-model="prompt"
          type="text"
          theme="secondary"
          placeholder="Ex: Pokémon"
          rounded="sm"
        />
      </div>

      <div class="mb-4">
        <Input
          id="count"
          v-model="count"
          type="number"
          theme="secondary"
          placeholder="Number of Questions"
          rounded="sm"
          min="1"
        />
      </div>

      <Checkbox 
        id="room-is-public"
        v-model="isPublic"
        label="Make this room public"
        class="mb-4"
        checkbox-left
      />

      <!-- Timer placé visuellement dans le form, mais structurellement en dehors -->
      <!-- On le place juste après le champ "count", mais toujours avant le bouton -->
    </form>

    <!-- Timer EN DEHORS du form, mais au bon endroit visuellement -->
    <div class="mb-6">
      <ActiveTimerInput v-model:minutes="minutes" v-model:seconds="seconds" />
    </div>

    <!-- Bouton toujours dans le form -->
    <form @submit.prevent="createQuiz" class="mt-0">
      <Button type="submit" theme="primary" class="primary" rounded="sm" :disabled="isLoading">
        {{ isLoading ? 'Creation in progress...' : 'Create Quiz' }}
      </Button>
    </form>
    <Error v-if="formError">
      <p>{{ formError }}</p>
    </Error>
  </div>
</template>
