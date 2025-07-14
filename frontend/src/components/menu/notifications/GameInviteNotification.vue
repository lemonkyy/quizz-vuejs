<script setup lang="ts">
import { computed } from 'vue';
import UserIcon from '../../profile/UserIcon.vue';
import Button from '../../ui/atoms/Button.vue';
import CrossButton from '../../ui/molecules/buttons/CrossButton.vue';
import type { Notification } from '@/types';
import { formatTimestamp } from '@/utils/formatTimestamp';

const props = defineProps<{
  notification: Notification;
}>();

const emit = defineEmits<{
  (e: 'accept', notificationId: string): void;
  (e: 'deny', notificationId: string): void;
  (e: 'delete', notificationId: string): void;
}>();

const formattedTimestamp = computed(() => {
  return formatTimestamp(props.notification.timestamp);
});
</script>

<template>
  <li class="px-2 py-4 border-b border-notification-item-border last:border-b-0 flex items-center">
    <UserIcon v-if="props.notification.data.senderId" :src="props.notification.data.senderProfilePicture" :size="3" class="mx-2" />
    <div class="flex-grow px-2">
      <p class="text-sm text-notification-text">{{ props.notification.data.senderUsername }} invited you to a game.</p>
      <p class="text-xs text-gray-500 mt-1">{{ formattedTimestamp }}</p>
      <div class="mt-2 flex flex-row gap-3">
        <Button size="sm" @click="emit('accept', props.notification.id)" transparent>Join</Button>
        <Button  size="sm" @click="emit('deny', props.notification.id)" transparent>Refuse</Button>
      </div>
    </div>
    <CrossButton @click="emit('delete', props.notification.id)" :size="1.5" />
  </li>
</template>
