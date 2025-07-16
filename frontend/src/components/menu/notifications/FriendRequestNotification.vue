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
  return formatTimestamp(props.notification.sentAt);
});

const userProfilePictureUrl = computed(() => {
  return props.notification?.data?.sender?.profilePicture ? import.meta.env.VITE_PUBLIC_PFP_URL + '/' + props.notification.data.sender.profilePicture : "";
})

</script>

<template>
  <li class="px-2 py-4 border-b border-notification-item-border last:border-b-0 flex items-center">
    <UserIcon :src="userProfilePictureUrl" :size="3" class="mx-2" />
    <div class="flex-grow px-2">
      <p class="text-sm text-notification-text">{{ props.notification?.data?.sender?.username }} sent you a friend request.</p>
      <p class="text-xs text-gray-500 mt-1">{{ formattedTimestamp }}</p>
      <div class="mt-2 flex flex-row gap-3">
        <Button size="sm" @click="emit('accept', props.notification.id)" transparent>Accept</Button>
        <Button size="sm" @click="emit('deny', props.notification.id)" transparent>Deny</Button>
      </div>
    </div>
    <CrossButton @click="emit('delete', props.notification.id)" :size="1.5" />
  </li>
</template>
