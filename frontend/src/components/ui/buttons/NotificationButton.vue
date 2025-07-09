<script setup lang="ts">
import { ref } from 'vue';
import Button from '../atoms/Button.vue';
import Notification from '@/assets/images/icons/notification.svg';
import { useAuthStore } from '@/store/auth';
import Image from '../atoms/Image.vue';

const auth = useAuthStore();
</script>

<template>
  <Button
    type="button"
    theme="secondary"
  >
    <Image :src="Notification" alt="notification" />
  </Button>
</template>
