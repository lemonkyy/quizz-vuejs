import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { Notification } from '@/types';
import { useToast } from "vue-toastification";
import { listNotifications as listNotificationsService, countNotifications as countNotificationsService } from '@/services/notificationService';

export const useNotificationStore = defineStore("notification", () => {
  //notificationCount split from notifications for pagination
  const notifications = ref<Notification[]>([]);
  const notificationCount = ref(0);

  const toast = useToast();

  const currentPage = ref(1);
  const itemsPerPage = ref(10);
  const hasMoreNotifications = ref(true);

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

  return {
    notifications,
    notificationCount,
    hasMoreNotifications,
    listNotifications,
    loadMoreNotifications,
    countNotifications,
  };
});
