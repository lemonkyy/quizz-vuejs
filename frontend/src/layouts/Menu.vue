<script setup lang="ts">
import { useAuthStore } from '@/store/auth';
import { computed } from 'vue';

const auth = useAuthStore();

const userName = computed(() => auth.user?.username || '');

</script>

<template>
  <nav class="bg-gray-800 p-4 text-white">
    <ul class="flex space-x-4">
      <li><router-link to="/" class="hover:text-gray-300">Home</router-link></li>
      <li><router-link to="/login" class="hover:text-gray-300">Login</router-link></li>
      <li><router-link to="/register" class="hover:text-gray-300">Register</router-link></li> 
      <li><router-link to="/ui" class="hover:text-gray-300">page UI</router-link></li> 
      <li v-if="auth.user" class="ml-auto">Bienvenue {{ userName }}</li>
    </ul>
  </nav>
</template>
