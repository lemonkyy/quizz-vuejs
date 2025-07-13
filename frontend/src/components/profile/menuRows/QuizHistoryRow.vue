<script setup lang="ts">
import QuizHistoryButton from '../../ui/molecules/buttons/QuizHistoryButton.vue';
</script>

<template>
  <router-link to="/user/quiz-history" class="flex items-center gap-3 py-2 sm:px-8 rounded-lg hover:bg-gray-100 cursor-pointer">
    <QuizHistoryButton />
    <span class="font-semibold">Quiz History</span>
  </router-link>
</template>
