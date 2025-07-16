<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/store/auth';
import { useFriendStore } from '@/store/friendship';
import { useRouter } from 'vue-router';
import Button from '../../atoms/Button.vue';

const friendStore = useFriendStore();
const auth = useAuthStore();
const router = useRouter();
const isLoading = ref(false);

const handleLogout = async () => {
  isLoading.value = true;
  try {
    friendStore.clearFriends();
    await auth.logout();
    router.push('/');
  } catch (error) {
    console.error('Logout failed:', error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <Button
    type="button"
    theme="monochrome"
    @click="handleLogout"
    :loading="isLoading"
  >
    Log Out
  </Button>
</template>
