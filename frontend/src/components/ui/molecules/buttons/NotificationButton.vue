<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Notification from '@/assets/images/icons/notification.svg';
import Image from '../../atoms/Image.vue';

defineProps({
  notificationsCount: { type: Number, default: 0 },
});

const emit = defineEmits(['click']);
</script>

<template>
  <Button
    type="button"
    theme="monochrome"
    @click="emit('click')"
  >
    <Image :src="Notification" alt="notification" :width="1.4"/>
    <span
      v-if="notificationsCount > 0"
      class="absolute top-0 right-0 inline-flex items-center justify-center px-3 py-2 font-bold leading-none bg-notification-badge-background text-notification-badge-text rounded-full transform translate-x-1/2 -translate-y-1/2"
    >
      {{ notificationsCount }}
    </span>
  </Button>
</template>

<style scoped>
/* Add any specific styles here if needed */
</style>

