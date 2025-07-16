import { defineStore } from 'pinia';
import { onMounted, ref } from 'vue';
import type { Notification } from '@/types';
import { useToast } from "vue-toastification";
import { listNotifications as listNotificationsService, countNotifications as countNotificationsService } from '@/services/notificationService';
import { acceptFriendRequest as acceptFriendRequestService, refuseFriendRequest as refuseFriendRequestService } from '@/services/friendService';
import { acceptInvitation as acceptInvitationService, denyInvitation as denyInvitationService } from '@/services/invitationService';
import { useAuthStore } from './auth';

export const useNotificationStore = defineStore("notification", () => {
  //notificationCount split from notifications for pagination
  const notifications = ref<Notification[]>([]);
  const notificationCount = ref(0);

  const toast = useToast();

  const currentPage = ref(1);
  const itemsPerPage = ref(10);
  const hasMoreNotifications = ref(true);

  const auth = useAuthStore();

  const listNotifications = async (page: number = 1, limit: number = itemsPerPage.value) => {
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
      toast.error("Error while fetching notifications.");
      throw error;
    }
  };

  const loadMoreNotifications = async () => {
    if (hasMoreNotifications.value) {
      await listNotifications(currentPage.value + 1, itemsPerPage.value);
    }
  };

  const countNotifications = async () => {
    try {
      const response = await countNotificationsService();
      if (response.code === 'SUCCESS' && response.notificationCount) {
        notificationCount.value = response.notificationCount;
      }
    } catch (error) {
      throw error;
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
      if (response.code === 'SUCCESS') {
        notificationCount.value--;
        toast.success('Room joined.');
      }
    } catch (error: any) {
      if (error.response && error.response.data && error.response.data.code === 'ERR_INVITATION_EXPIRED') {
        toast.error('The invitation expired.');
      } else {
        toast.error('Error accepting invitation.');
      }
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
    countNotifications();
    setupMercureListener();
  });

  const setupMercureListener = () => {
    if (!auth.user) {
      console.warn('Mercure listener not set up: User not authenticated.');
      return;
    }

    const mercureUrl = import.meta.env.VITE_MERCURE_PUBLIC_URL;
    console.log('Mercure URL from env:', mercureUrl);
    const url = new URL(mercureUrl);
    url.searchParams.append('topic', `/notifications/${auth.user.id}`);
    const eventSource = new EventSource(url, { withCredentials: true });

    eventSource.onmessage = (event) => {
      console.log('Mercure update received:', event.data);
      notificationCount.value++;
      listNotifications(1, itemsPerPage.value);
    };

    eventSource.onerror = (error) => {
      console.error('Mercure EventSource error:', error);
      eventSource.close();
    };

    console.log('Mercure listener set up for user:', auth.user.id);
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
  };
});
