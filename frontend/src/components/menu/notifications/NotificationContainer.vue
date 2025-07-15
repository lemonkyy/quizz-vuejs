<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useNotificationStore } from '@/store/notification';
import NotificationButton from '../../ui/molecules/buttons/NotificationButton.vue';
import NotificationList from './NotificationList.vue';
import NotificationModal from '../../ui/molecules/modals/NotificationModal.vue';
import { useMediaQuery } from '@/composables/useMediaQuery';
import { BREAKPOINT_SMALL_SCREEN } from '@/constants/breakpoints';
import { NotificationType } from '@/constants/notificationType';

const isSmallScreen = useMediaQuery(BREAKPOINT_SMALL_SCREEN);

const notificationStore = useNotificationStore();

const showNotifications = ref(false);
const notificationContainerRef = ref<HTMLElement | null>(null);

function toggleNotifications() {
  showNotifications.value = !showNotifications.value;
}

function handleAccept(notificationId: string, notificationType: typeof NotificationType[keyof typeof NotificationType]) {

  console.log(`Accepted notification: ${notificationId} ${notificationType}`);
  //getting the notification here to have its name for .acceptFriendRequest and make a better toast message
  const notification = notificationStore.notifications.find(n => n.id === notificationId);

  if (notificationType === NotificationType.FRIEND_REQUEST && notification?.data?.sender?.username) {
    notificationStore.acceptFriendRequest(notificationId, notification.data.sender.username);
  } else if (notificationType === NotificationType.INVITATION) {
    notificationStore.acceptInvitation(notificationId);
  } else {
    //other unused as of now
    console.log("Accepter other notification");
  }

  const index = notificationStore.notifications.findIndex(n => n.id === notificationId);

  if (index !== -1) {
    notificationStore.notifications.splice(index, 1);
  }
}

function handleDeny(notificationId: string, notificationType: typeof NotificationType[keyof typeof NotificationType]) {

  console.log(`Denied notification: ${notificationId} ${notificationType}`);
  if (notificationType === NotificationType.FRIEND_REQUEST) {
    notificationStore.denyFriendRequest(notificationId);
  } else if (notificationType === NotificationType.INVITATION) {
    notificationStore.denyInvitation(notificationId);
  } else {
    //other unused as of now
    console.log("Denied other notification");
  }

  const index = notificationStore.notifications.findIndex(n => n.id === notificationId);

  if (index !== -1) {
    notificationStore.notifications.splice(index, 1);
  }
}

const handleClickOutside = (event: MouseEvent) => {
  if (!isSmallScreen.value && notificationContainerRef.value && !notificationContainerRef.value.contains(event.target as Node)) {
    showNotifications.value = false;
  }
};

onMounted(() => {
  notificationStore.listNotifications();
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="notificationContainerRef">
    <NotificationButton
      :notificationsCount="notificationStore.notificationCount"
      @click="toggleNotifications"
    />

    <NotificationModal
      v-if="isSmallScreen"
      v-model="showNotifications"
      :notifications="notificationStore.notifications"
      @accept="(id, type) => handleAccept(id, type)"
      @deny="(id, type) => handleDeny(id, type)"
      @delete="(id, type) => handleDeny(id, type)"
    />

    <div
      v-else-if="showNotifications"
      class="absolute right-0 mt-2 w-md bg-notification-background rounded-lg shadow-lg z-20"
    >
      <NotificationList
        :notifications="notificationStore.notifications"
        @accept="(id, type) => handleAccept(id, type)"
        @deny="(id, type) => handleDeny(id, type)"
        @delete="(id, type) => handleDeny(id, type)"
      />
    </div>
  </div>
</template>
