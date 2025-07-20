import { defineStore } from 'pinia';
import { onMounted, ref } from 'vue';
import type { Notification } from '@/types';
import { useToast } from "vue-toastification";
import { listNotifications as listNotificationsService, countNotifications as countNotificationsService } from '@/services/notificationService';
import { acceptFriendRequest as acceptFriendRequestService, refuseFriendRequest as refuseFriendRequestService } from '@/services/friendService';
import { acceptInvitation as acceptInvitationService, denyInvitation as denyInvitationService } from '@/services/invitationService';
import { useAuthStore } from './auth';
import { useRoomStore } from './room';
import router from '@/router';

export const useNotificationStore = defineStore("notification", () => {
  //notificationCount split from notifications for pagination
  const notifications = ref<Notification[]>([]);
  const notificationCount = ref(0);

  const toast = useToast();

  const currentPage = ref(1);
  const itemsPerPage = ref(10);
  const hasMoreNotifications = ref(true);

  const auth = useAuthStore();
  const roomStore = useRoomStore();

  const listNotifications = async (page: number = 1, limit: number = itemsPerPage.value) => {
    if (!auth.user) {
      return;
    }
    try {
      if (!hasMoreNotifications.value && page > 1) {
        return;
      }

      const response = await listNotificationsService({ page, limit });
      if (response.code === 'SUCCESS' && response.notifications) {
        if (page === 1) {
          notifications.value = response.notifications;
        } else {
          notifications.value = [...notifications.value, ...response.notifications];
        }
        hasMoreNotifications.value = response.hasMore || false;
        currentPage.value = page;
      }
    } catch (error) {
      console.warn("Error while fetching notifications:", error);
    }
  };

  const loadMoreNotifications = async () => {
    if (hasMoreNotifications.value) {
      await listNotifications(currentPage.value + 1, itemsPerPage.value);
    }
  };

  const countNotifications = async () => {
    if (!auth.user) {
      return;
    }
    try {
      const response = await countNotificationsService();
      if (response.code === 'SUCCESS' && response.notificationCount) {
        notificationCount.value = response.notificationCount;
      }
    } catch (error) {
      console.warn('Error counting notifications:', error);
    }
  }

  const acceptFriendRequest = async (id: string, username: string = "user") => {
    try {
      const response = await acceptFriendRequestService(id);
      if (response.code === 'SUCCESS') {
        notificationCount.value--;
        toast.success(`You and ${username} are now friends.`);
      }
    } catch (error) {
      toast.error('Error accepting friend request.');
      throw error;
    }
  };

  const denyFriendRequest = async (id: string) => {
    try {
      const response = await refuseFriendRequestService(id);
      if (response.code === 'SUCCESS') {
        notificationCount.value--;
      }
    } catch (error) {
      toast.error('Error denying friend request.');
      throw error;
    }
  };

  const acceptInvitation = async (id: string) => {
    try {
      const response = await acceptInvitationService(id);
      if (response.code === 'SUCCESS' && response.room) {
        notificationCount.value--;
        roomStore.currentRoom = response.room;
        router.push({ path: `/room/` });
      }
    } catch (error: any) {
      if (error.response && error.response.data && error.response.data.code === 'ERR_INVITATION_EXPIRED') {
        toast.error('The invitation expired.');
        notificationCount.value--;
      } else if (error.response && error.response.data && error.response.data.code === 'ERR_ROOM_DELETED') {
        toast.error('The room you tried to join has been deleted.');
        await denyInvitation(id);
        notificationCount.value--;
      } 
      // No generic error toast here, as roomStore.joinRoom handles its own errors
      throw error;
    }
  };

  const denyInvitation = async (id: string) => {
    try {
      const response = await denyInvitationService(id);
      if (response.code === 'SUCCESS') {
        notificationCount.value--;
      }
    } catch (error) {
      toast.error('Error denying invitation.');
      throw error;
    }
  };

  onMounted(() => {
    if (auth.user) {
      countNotifications();
      setupMercureListener();
    }
  });

  //mercure stuff for live notifications
  const setupMercureListener = () => {
    if (!auth.user) {
      return;
    }

    const mercureUrl = import.meta.env.VITE_MERCURE_PUBLIC_URL;
    const url = new URL(mercureUrl);
    url.searchParams.append('topic', `/notifications/${auth.user.id}`);

    const eventSource = new EventSource(url, { withCredentials: true });

    eventSource.onmessage = () => {
      notificationCount.value++;
      listNotifications(1, itemsPerPage.value);
    };

    eventSource.onerror = () => {
      eventSource.close();
    };
  };

  const initNotifications = () => {
    if (auth.user) {
      countNotifications();
      setupMercureListener();
    }
  };

  return {
    notifications,
    notificationCount,
    hasMoreNotifications,
    listNotifications,
    loadMoreNotifications,
    countNotifications,
    acceptFriendRequest,
    denyFriendRequest,
    acceptInvitation,
    denyInvitation,
    initNotifications,
  };
});
