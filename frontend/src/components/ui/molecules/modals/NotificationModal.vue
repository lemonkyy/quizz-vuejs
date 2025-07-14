<script setup lang="ts">
import Modal from '../../atoms/Modal.vue';
import FriendRequestNotification from '@/components/menu/notifications/FriendRequestNotification.vue';
import GameInviteNotification from '@/components/menu/notifications/GameInviteNotification.vue';
import GenericNotification from '@/components/menu/notifications/GenericNotification.vue';
import type { Notification } from '@/types';
import Title from '@/components/ui/atoms/Title.vue';

const modelValue = defineModel({required: true, default: false});

const props = defineProps<{
  notifications: Notification[];
}>();

const emit = defineEmits<{
  (e: 'accept', notificationId: string): void;
  (e: 'deny', notificationId: string): void;
  (e: 'delete', notificationId: string): void;
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
              v-if="notification.type === 'friend_request'"
              :notification="notification as Notification"
              @accept="emit('accept', $event)"
              @deny="emit('deny', $event)"
              @delete="emit('delete', $event)"
            />
            <GameInviteNotification
              v-else-if="notification.type === 'game_invite'"
              :notification="notification as Notification"
              @accept="emit('accept', $event)"
              @deny="emit('deny', $event)"
              @delete="emit('delete', $event)"
            />
            <GenericNotification
              v-else-if="notification.type === 'other'"
              :notification="notification as Notification"
              @delete="emit('delete', $event)"
            />
          </template>
        </ul>
        <p v-else class="text-sm text-notification-text p-4">No new notifications.</p>
      </div>
    </template>
  </Modal>
</template>
