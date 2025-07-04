<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TheMenu',
});
</script>

<template>
  <nav class="bg-gray-800 p-4 text-white">
    <ul class="flex space-x-4">
      <li><router-link to="/" class="hover:text-gray-300">Home</router-link></li>
      <li><router-link to="/login" class="hover:text-gray-300">Login</router-link></li>
      <li><router-link to="/register" class="hover:text-gray-300">Register</router-link></li> 
      <li><router-link to="/ui" class="hover:text-gray-300">page UI</router-link></li> 
    </ul>
  </nav>
</template>

