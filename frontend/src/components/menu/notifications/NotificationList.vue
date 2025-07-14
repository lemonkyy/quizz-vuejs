<script setup lang="ts">
import FriendRequestNotification from './FriendRequestNotification.vue';
import GameInviteNotification from './GameInviteNotification.vue';
import GenericNotification from './GenericNotification.vue';
import type { Notification } from '@/types';
import Title from '@/components/ui/atoms/Title.vue';

defineProps<{
  notifications: Notification[];
}>();

const emit = defineEmits<{
  (e: 'accept', notificationId: string): void;
  (e: 'deny', notificationId: string): void;
  (e: 'delete', notificationId: string): void;
}>();
</script>

<template>
  <Title :level="3" class="text-notification-text border-b border-notification-border p-4">Notifications</Title>
  <ul v-if="notifications.length > 0" class="px-4 max-h-96 overflow-y-auto">
    <template v-for="notification in notifications" :key="notification.id">
      <FriendRequestNotification
        v-if="notification.type === 'friend_request'"
        :notification="notification"
        @accept="emit('accept', $event)"
        @deny="emit('deny', $event)"
        @delete="emit('delete', $event)"
      />
      <GameInviteNotification
        v-else-if="notification.type === 'game_invite'"
        :notification="notification"
        @accept="emit('accept', $event)"
        @deny="emit('deny', $event)"
        @delete="emit('delete', $event)"
      />
      <GenericNotification
        v-else-if="notification.type === 'other'"
        :notification="notification"
        @delete="emit('delete', $event)"
      />
    </template>
  </ul>
  <p v-else class="text-sm text-notification-text p-4">No notifications (｡•́︿•̀｡)</p>
</template>
