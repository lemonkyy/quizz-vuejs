<script setup lang="ts">
import { computed } from 'vue';
import CrossButton from '../../ui/molecules/buttons/CrossButton.vue';
import type { Notification } from '@/types';
import { formatTimestamp } from '@/utils/formatTimestamp';

const props = defineProps<{
  notification: Notification;
}>();

const emit = defineEmits<{
  (e: 'delete', notificationId: string): void;
}>();

const formattedTimestamp = computed(() => {
  return formatTimestamp(props.notification.sentAt);
});
</script>

<template>
  <li class="pr-2 pl-20 py-6 border-b border-notification-item-border last:border-b-0 flex items-center justify-between">
    <div>
      <p class="text-sm text-notification-text">{{ props.notification.data.message }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ formattedTimestamp }}</p>
    </div>
    <CrossButton @click="emit('delete', props.notification.id)" :size="1.5" />
  </li>
</template>
