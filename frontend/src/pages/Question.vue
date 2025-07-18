<template>

    

    <div class="max-w-2xl mx-auto p-6" v-if="currentQuestion">
      <ProgressBar :current="currentIndex + 1" :total="questions.length" class="mb-6" />

      <QuestionTitle :question="currentQuestion.questionText" />
  
      <OptionsList
        :options="currentQuestion.options"
        v-model="selected"
      />

      <div class="w-full mb-4">
      <CountdownTimer 
        class="w-full" 
        :duration="timePerQuestion" 
        :key="currentIndex" 
        @timeExpired="handleTimeExpired"
        :isActive="isTimerActive"
      />
    </div>
  
      <ActionButtons
        @submit="handleSubmit"
        @skip="handleSkip"
      />
  
      <div v-if="showResult" class="mt-4 text-center font-bold">
        <span v-if="isCorrect" class="text-green-600">Correct!</span>
        <span v-else class="text-red-600">Incorrect! La bonne réponse était: {{ currentQuestion.correctAnswer }}</span>
      </div>
    </div>
  
    <div v-else class="text-center p-6">
      Loading quiz...
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useRoute } from 'vue-router'
  import axios from '/src/api/axios'
  import QuestionTitle from '../components/quiz/QuestionTitle.vue'
  import OptionsList from '../components/quiz/OptionsList.vue'
  import ActionButtons from '../components/quiz/ActionButtons.vue'
  import ProgressBar from '../components/quiz/ProgressBar.vue'
  import CountdownTimer from '../components/ui/molecules/inputs/CountdownTimer.vue'

  
  const route = useRoute()
  const quizId = route.params.id
  
  const questions = ref([])
  const currentIndex = ref(0)
  const selected = ref('')
  const showResult = ref(false)
  const isCorrect = ref(false)
  const timePerQuestion = ref(30)
  const isTimerActive = ref(true)
  
  const currentQuestion = computed(() => questions.value[currentIndex.value])
  
  onMounted(async () => {
    try {
        const quizResponse = await axios.get(`/quizzes/${quizId}`)
        timePerQuestion.value = quizResponse.data.timePerQuestion || 30

        const response = await axios.get(`/quizzes/${quizId}/questions`)
        console.log(response.data)
        questions.value = response.data.map(q => ({
        id: q.id,
        questionText: q.questionText,
        options: typeof q.options === 'string' ? JSON.parse(q.options) : q.options,
        correctAnswer: q.correctAnswer
        }))
        console.log("Questions chargées :", questions.value)
        console.log("Time per question:", timePerQuestion.value)
    } catch (error) {
        console.error("Erreur lors du chargement des questions:", error)
    }
    })

  
  function handleSubmit() {
    if (!selected.value) return
    isTimerActive.value = false
    isCorrect.value = selected.value === currentQuestion.value.correctAnswer
    showResult.value = true
  
    setTimeout(() => {
      nextQuestion()
    }, 1200)
  }
  
  function handleSkip() {
    isTimerActive.value = false
    nextQuestion()
  }

  function handleTimeExpired() {
    if (showResult.value) return
    isTimerActive.value = false
    isCorrect.value = false
    showResult.value = true
    
    setTimeout(() => {
      nextQuestion()
    }, 1200)
  }
  
  function nextQuestion() {
    showResult.value = false
    selected.value = ''
    isTimerActive.value = true
    if (currentIndex.value < questions.value.length - 1) {
      currentIndex.value++
    } else {
      console.log("Quiz terminé !")
    }
  }
  </script>
  