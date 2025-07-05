<script setup lang="ts">
import { useAuthStore } from '@/store/auth';
import { useRouter } from 'vue-router';
import Button from './ui/Button.vue';

const auth = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  try {
    await auth.logout();
    router.push('/');
  } catch (error) {
    console.error('Logout failed:', error);
  }
};
</script>

<template>
  <Button
    type="button"
    @click="handleLogout"
  >
    Déconnexion
  </Button>
</template>
