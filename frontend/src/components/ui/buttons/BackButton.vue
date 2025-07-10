<script setup lang="ts">
import Button from '../atoms/Button.vue';
</script>

<template>
  <Button
    withoutBorder
    transparent
    class="p-2 rounded-full hover:bg-gray-200 cursor-pointer"
    aria-label="Go back"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
  </Button>
</template>
