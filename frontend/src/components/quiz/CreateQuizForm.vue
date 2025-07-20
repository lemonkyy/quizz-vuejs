<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import Button from '@/components/ui/atoms/Button.vue';
import Input from '@/components/ui/atoms/Input.vue';
import Select from '@/components/ui/atoms/Select.vue';
import ActiveTimerInput from '@/components/ui/molecules/inputs/ActiveTimerinput.vue';
import { useAuthStore } from '@/store/auth';
import { useRoomStore } from '@/store/room';
import { useToast } from 'vue-toastification';
import { useMatomo } from '@/composables/useMatomo';


const router = useRouter()
const authStore = useAuthStore()
const roomStore = useRoomStore()
const toast = useToast()
const { trackEvent } = useMatomo()

const prompt = ref('')
const count = ref(10)
const isLoading = ref(false)
const minutes = ref(0)
const seconds = ref(30)
const invite = ref('')
const timePerQuestion = computed(() => minutes.value * 60 + seconds.value)

const createQuiz = async () => {
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
      isPublic: true
    })

    const room = roomResponse?.data
    console.log("Room created:", room)

    roomStore.currentRoom = room;

    localStorage.setItem('currentQuizTopic', prompt.value);

    toast.success(`Room créée avec le code: ${room.code}`);

    // Track successful room creation
    trackEvent('Room', 'Create Success', cleanPrompt, count.value);
    trackEvent('Quiz', 'Generation Started', cleanPrompt, count.value);

    router.push('/room');

    createQuizInBackground(cleanPrompt, count.value, timePerQuestion.value);

  } catch (error) {
    console.error("Erreur lors de la création de la room:", error)
    trackEvent('Room', 'Create Failed', cleanPrompt, 0);
    toast.error('Erreur lors de la création de la room');
  } finally {
    isLoading.value = false
  }
}

const createQuizInBackground = async (cleanPrompt: string, countValue: number, timePerQuestionValue: number) => {
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
      trackEvent('Quiz', 'Generation Skipped', 'Already Exists', 1);
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
      const quizzes = quizzesArray.filter(q =>
        typeof q.title === 'string' &&
        q.title.trim().toLowerCase() === cleanPrompt &&
        q.ready == true
      )

      if (quizzes.length > 0) {
        const lastQuiz = quizzes.reduce((max, q) => q.id > max.id ? q : max, quizzes[0])
        console.log("Quiz ready and found:", lastQuiz)
        trackEvent('Quiz', 'Generation Complete', cleanPrompt, countValue);
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
    trackEvent('Quiz', 'Generation Failed', cleanPrompt, 0);
  }
}
</script>

<template>
  <div class="mx-auto p-6 bg-white w-100">
    <h2 class="text-xl font-bold mb-4">Créer un Quiz</h2>

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
        {{ isLoading ? 'Création...' : 'Créer le Quiz' }}
      </Button>
    </form>
  </div>
</template>
