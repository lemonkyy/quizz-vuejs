<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import NotificationButton from '../../ui/molecules/buttons/NotificationButton.vue';
import NotificationList from './NotificationList.vue';
import NotificationModal from '../../ui/molecules/modals/NotificationModal.vue';
import type { Notification } from '@/types';
import { useMediaQuery } from '@/composables/useMediaQuery';

const isSmallScreen = useMediaQuery('(max-width: 768px)'); // Example breakpoint for small screens

const notificationsCount = ref(3);
const showNotifications = ref(false);
const notificationContainerRef = ref<HTMLElement | null>(null);

const mockNotifications: Notification[] = [
  { id: '1', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user123', senderUsername: 'User123', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture1.png' } },
  { id: '2', type: 'game_invite', sentAt: new Date().toISOString(), data: { senderId: 'playerone', senderUsername: 'PlayerOne', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture1.png' } },
  { id: '3', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user456', senderUsername: 'User456', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture1.png' } },
  { id: '4', type: 'other', sentAt: new Date().toISOString(), data: { message: 'Your game has started!' } },
  { id: '5', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user789', senderUsername: 'User789', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture2.png' } },
  { id: '6', type: 'game_invite', sentAt: new Date().toISOString(), data: { senderId: 'playertwo', senderUsername: 'PlayerTwo', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture2.png' } },
  { id: '7', type: 'other', sentAt: new Date().toISOString(), data: { message: 'New message in chat!' } },
  { id: '8', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user101', senderUsername: 'User101', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture3.png' } },
  { id: '9', type: 'game_invite', sentAt: new Date().toISOString(), data: { senderId: 'playerthree', senderUsername: 'PlayerThree', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture3.png' } },
  { id: '10', type: 'other', sentAt: new Date().toISOString(), data: { message: 'Your turn in quiz!' } },
  { id: '11', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user112', senderUsername: 'User112', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture4.png' } },
  { id: '12', type: 'game_invite', sentAt: new Date().toISOString(), data: { senderId: 'playerfour', senderUsername: 'PlayerFour', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture4.png' } },
  { id: '13', type: 'other', sentAt: new Date().toISOString(), data: { message: 'Achievement unlocked!' } },
  { id: '14', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user131', senderUsername: 'User131', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture5.png' } },
  { id: '15', type: 'game_invite', sentAt: new Date().toISOString(), data: { senderId: 'playerfive', senderUsername: 'PlayerFive', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture5.png' } },
  { id: '16', type: 'other', sentAt: new Date().toISOString(), data: { message: 'Daily reward available!' } },
  { id: '17', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user141', senderUsername: 'User141', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture6.png' } },
  { id: '18', type: 'game_invite', sentAt: new Date().toISOString(), data: { senderId: 'playersix', senderUsername: 'PlayerSix', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture6.png' } },
  { id: '19', type: 'other', sentAt: new Date().toISOString(), data: { message: 'New event started!' } },
  { id: '20', type: 'friend_request', sentAt: new Date().toISOString(), data: { senderId: 'user151', senderUsername: 'User151', senderProfilePicture: 'http://localhost:8000/images/profile_pictures/profile_picture7.png' } },
];

function toggleNotifications() {
  showNotifications.value = !showNotifications.value;
}

function handleAccept(notificationId: string) {
  console.log(`Accepted notification: ${notificationId}`);
  notificationsCount.value--;
  const index = mockNotifications.findIndex(n => n.id === notificationId);
  if (index !== -1) {
    mockNotifications.splice(index, 1);
  }
}

function handleDeny(notificationId: string) {
  console.log(`Denied notification: ${notificationId}`);
  notificationsCount.value--;
  const index = mockNotifications.findIndex(n => n.id === notificationId);
  if (index !== -1) {
    mockNotifications.splice(index, 1);
  }
}

function handleDelete(notificationId: string) {
  console.log(`Deleted notification: ${notificationId}`);
  notificationsCount.value--;
  const index = mockNotifications.findIndex(n => n.id === notificationId);
  if (index !== -1) {
    mockNotifications.splice(index, 1);
  }
}

const handleClickOutside = (event: MouseEvent) => {
  if (!isSmallScreen.value && notificationContainerRef.value && !notificationContainerRef.value.contains(event.target as Node)) {
    showNotifications.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="notificationContainerRef">
    <NotificationButton
      :notificationsCount="notificationsCount"
      @click="toggleNotifications"
    />

    <NotificationModal
      v-if="isSmallScreen"
      v-model="showNotifications"
      :notifications="mockNotifications"
      @accept="handleAccept"
      @deny="handleDeny"
      @delete="handleDelete"
    />

    <div
      v-else-if="showNotifications"
      class="absolute right-0 mt-2 w-md bg-notification-background rounded-lg shadow-lg z-20"
    >
      <NotificationList
        :notifications="mockNotifications"
        @accept="handleAccept"
        @deny="handleDeny"
        @delete="handleDelete"
      />
    </div>
  </div>
</template>

<style scoped>
/* Add any specific styles here if needed */
</style>