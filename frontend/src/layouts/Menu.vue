<script setup lang="ts">
import { useAuthStore } from '@/store/auth';
import { computed } from 'vue';
import LogoutButton from '@/components/ui/LogoutButton.vue';

const auth = useAuthStore();

const username = computed(() => auth.user?.username || '');

</script>

<template>
  <nav class="bg-gray-800 p-4 text-white">
    <ul class="flex flex-row space-x-4 w-full justify-between">
      <ul class="flex flex-row gap-2">
        <li><router-link to="/" class="hover:text-gray-300">Home</router-link></li>
        <li><router-link to="/login" class="hover:text-gray-300">Login</router-link></li>
        <li><router-link to="/register" class="hover:text-gray-300">Register</router-link></li>
        <li><router-link to="/ui" class="hover:text-gray-300">page UI</router-link></li>
      </ul>
      <ul v-if="auth.user" class="flex flex-row gap-2">
        <li><router-link to="/profile" class="text-blue-700 hover:underline"> {{ username }} </router-link></li>
        <li class="ml-auto"><LogoutButton /></li>
      </ul>
    </ul>
  </nav>
</template>
