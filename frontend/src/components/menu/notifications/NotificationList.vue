<script setup lang="ts">
import FriendRequestNotification from './FriendRequestNotification.vue';
import GameInviteNotification from './GameInviteNotification.vue';
import GenericNotification from './GenericNotification.vue';
import type { Notification } from '@/types';
import { NotificationType, type NotificationTypeValue } from '@/constants/notificationType';
import Title from '@/components/ui/atoms/Title.vue';

defineProps<{
  notifications: Notification[];
}>();

const emit = defineEmits<{
  (e: 'accept', notificationId: string, notificationType: NotificationTypeValue): void;
  (e: 'deny', notificationId: string, notificationType: NotificationTypeValue): void;
  (e: 'delete', notificationId: string, notificationType: NotificationTypeValue): void;
}>();
</script>

<template>
  <Title :level="3" class="text-notification-text border-b border-notification-border p-4">Notifications</Title>
  <ul v-if="notifications.length > 0" class="px-4 max-h-96 overflow-y-auto">
    <template v-for="notification in notifications" :key="notification.id">
      <FriendRequestNotification
        v-if="notification.type === NotificationType.FRIEND_REQUEST"
        :notification="notification"
        @accept="emit('accept', $event, notification.type)"
        @deny="emit('deny', $event, notification.type)"
        @delete="emit('delete', $event, notification.type)"
      />
      <GameInviteNotification
        v-else-if="notification.type === NotificationType.INVITATION"
        :notification="notification"
        @accept="emit('accept', $event, notification.type)"
        @deny="emit('deny', $event, notification.type)"
        @delete="emit('delete', $event, notification.type)"
      />
      <GenericNotification
        v-else-if="notification.type === NotificationType.OTHER"
        :notification="notification"
        @delete="emit('delete', $event, notification.type)"
      />
    </template>
  </ul>
  <p v-else class="text-sm text-notification-text p-4">No notifications (｡•́︿•̀｡)</p>
</template>
