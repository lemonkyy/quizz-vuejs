<script setup lang="ts">
import Modal from '../../atoms/Modal.vue';
import FriendRequestNotification from '@/components/menu/notifications/FriendRequestNotification.vue';
import GameInviteNotification from '@/components/menu/notifications/GameInviteNotification.vue';
import GenericNotification from '@/components/menu/notifications/GenericNotification.vue';
import type { Notification } from '@/types';
import { NotificationType, type NotificationTypeValue } from '@/constants/notificationType';
import Title from '@/components/ui/atoms/Title.vue';

const modelValue = defineModel({required: true, default: false});

const props = defineProps<{
  notifications: Notification[];
}>();

const emit = defineEmits<{
  (e: 'accept', notificationId: string, notificationType: NotificationTypeValue): void;
  (e: 'deny', notificationId: string, notificationType: NotificationTypeValue): void;
  (e: 'delete', notificationId: string, notificationType: NotificationTypeValue): void;
}>();
</script>

<template>
  <Modal v-model="modelValue">
    <template #header>
      <Title :level="3" class="text-notification-text p-4">Notifications</Title>
    </template>
    <template #default>
      <div>
        <ul v-if="props.notifications.length > 0" class="px-4 max-h-96 overflow-y-auto">
          <template v-for="notification in props.notifications" :key="notification.id">
            <FriendRequestNotification
              v-if="notification.type === NotificationType.FRIEND_REQUEST"
              :notification="notification as Notification"
              @accept="emit('accept', $event, notification.type)"
              @deny="emit('deny', $event, notification.type)"
              @delete="emit('delete', $event, notification.type)"
            />
            <GameInviteNotification
              v-else-if="notification.type === NotificationType.INVITATION"
              :notification="notification as Notification"
              @accept="emit('accept', $event, notification.type)"
              @deny="emit('deny', $event, notification.type)"
              @delete="emit('delete', $event, notification.type)"
            />
            <GenericNotification
              v-else-if="notification.type === 'other'"
              :notification="notification as Notification"
              @delete="emit('delete', $event, notification.type)"
            />
          </template>
        </ul>
        <p v-else class="text-sm text-notification-text p-4">No notifications (｡•́︿•̀｡)</p>
      </div>
    </template>
  </Modal>
</template>
