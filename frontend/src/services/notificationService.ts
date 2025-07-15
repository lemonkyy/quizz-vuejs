import type { Notification } from "@/types";
import axios from "@/plugins/axios";

export async function listNotifications(params: {page?: number, limit?: number}): Promise<{code: string, notifications?: Notification[],  hasMore?: boolean}> {
  try {
    const response = await axios.get('/user/notifications', { params });
    return {
      code: 'SUCCESS',
      notifications: response.data.member
    };
  } catch (error) {
    throw error;
  }
}

export async function countNotifications(): Promise<{code: string, notificationCount?: number}> {
  try {
    const response = await axios.get('/user/notifications/count');
    return {
      code: 'SUCCESS',
      notificationCount: response.data.notificationCount
    };
  } catch (error) {
    throw error;
  }
}
