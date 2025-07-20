<script setup lang="ts">
const currentYear = new Date().getFullYear();
</script>

<template>
  <footer class="bg-menu-background  py-4 text-black text-center mt-auto border border-menu-border border-t-menu-border w-full">
    <p>&copy; {{ currentYear }} QuizUp. All rights reserved.</p>
  </footer>
</template>

