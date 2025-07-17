<script setup lang="ts">
const currentYear = new Date().getFullYear();
</script>

<template>
  <footer class="bg-gray-800 p-4 text-white text-center mt-auto">
    <p>&copy; {{ currentYear }} QuizUp. All rights reserved.</p>
  </footer>
</template>

