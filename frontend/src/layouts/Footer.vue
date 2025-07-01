<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TheFooter',
  setup() {
    const currentYear = new Date().getFullYear();
    return { currentYear };
  },
});
</script>

<template>
  <footer class="bg-gray-800 p-4 text-white text-center mt-auto">
    <p>&copy; {{ currentYear }} Quiz-It. All rights reserved.</p>
  </footer>
</template>

