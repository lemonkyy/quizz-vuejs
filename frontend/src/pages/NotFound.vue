<script setup lang="ts">
import Button from '@/components/ui/atoms/Button.vue';
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-gray-800">
    <h1 class="text-6xl font-bold text-red-600">404</h1>
    <p class="text-2xl mt-4">Page Not Found</p>
    <p class="text-lg my-2 text-center">The page you are looking for does not exist.</p>
    <Button>
      <router-link to="/">
        Go to Home
      </router-link>
    </Button>
  </div>
</template>