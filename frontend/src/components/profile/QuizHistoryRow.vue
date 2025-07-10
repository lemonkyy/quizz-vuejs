<script setup lang="ts">
import Image from '../ui/atoms/Image.vue';
import Button from '../ui/atoms/Button.vue';
import Clock from '@/assets/images/icons/clock.svg'
</script>

<template>
  <router-link to="/user/quiz-history" class="flex items-center gap-3 py-2 sm:px-8 rounded-lg hover:bg-gray-100 cursor-pointer">
    <Button theme="monochrome" class="p-2">
      <Image :src="Clock" alt="clock"/>
    </Button>
    <span class="font-semibold">Quiz History</span>
  </router-link>
</template>
