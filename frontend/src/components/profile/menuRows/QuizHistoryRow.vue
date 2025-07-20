<script setup lang="ts">
import QuizHistoryButton from '../../ui/molecules/buttons/QuizHistoryButton.vue';
import { useToast } from "vue-toastification";

const toast = useToast();

const showComingSoon = () => {
  toast.info("Coming soon");
};
</script>

<template>
  <div @click="showComingSoon" class="flex items-center gap-3 py-2 sm:px-8 rounded-lg hover:bg-gray-100 cursor-pointer">
    <QuizHistoryButton />
    <span class="font-semibold">Quiz History</span>
  </div>
</template>
