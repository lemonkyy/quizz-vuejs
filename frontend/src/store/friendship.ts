import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useToast } from "vue-toastification";
import type { PublicUser } from '@/types';
import {
  sendFriendRequest as sendFriendRequestService,
  acceptFriendRequest as acceptFriendRequestService,
  refuseFriendRequest as refuseFriendRequestService,
  cancelFriendRequest as cancelFriendRequestService,
  listFriends as listFriendsService,
  removeFriend as removeFriendService
} from '@/services/friendService';
import { getUserByUsername as getUserByUsernameService } from '@/services/userService';

export const useFriendStore = defineStore("friend", () => {

  const friends = ref<PublicUser[]>([]);

  const toast = useToast();

  const currentPage = ref(1);
  const itemsPerPage = ref(10);
  const hasMoreFriends = ref(true);

  const currentUsernameFilter = ref<string | undefined>(undefined);

  const listFriends = async (username?: string, page: number = 1, limit: number = itemsPerPage.value) => {
    try {
      if (username !== undefined && username !== currentUsernameFilter.value) {
        friends.value = [];
        currentPage.value = 1;
        hasMoreFriends.value = true;
      }
      currentUsernameFilter.value = username;

      if (!hasMoreFriends.value && page > 1) {
        return;
      }

      const response = await listFriendsService({username, page, limit});
      
      if (response.code === 'SUCCESS' && response.friends) {
        if (page === 1) {
          friends.value = response.friends;
        } else {
          friends.value = [...friends.value, ...response.friends];
        }
        hasMoreFriends.value = response.hasMore || false;
        currentPage.value = page;
      }
    } catch (error) {
      console.error('Error listing friends:', error);
      throw error;
    }
  }

  const loadMoreFriends = async () => {
    if (hasMoreFriends.value) {
      await listFriends(currentUsernameFilter.value, currentPage.value + 1, itemsPerPage.value);
    }
  };

  const removeFriend = async (id: string) => {
    try {
      await removeFriendService(id);
      toast.success('Friend removed.');
      await listFriends(currentUsernameFilter.value, 1);
    } catch (error) {
      toast.error('Error removing friend.');
      throw error;
    }
  };

  const sendFriendRequest = async (id: string) => {
    try {
      await sendFriendRequestService(id);
      toast.success('Friend request sent!');
    } catch (error) {
      throw error;
    }
  };

  const sendFriendRequestByUsername = async (username: string) => {
    try {
      const response = await getUserByUsernameService({ username });
      if (!response.user) {
        toast.error('No user matching the username found.');
        return;
      }
      await sendFriendRequest(response.user.id);
    } catch (error) {
      throw error;
    }
  };

  const acceptFriendRequest = async (id: string) => {
    try {
      await acceptFriendRequestService(id);
      toast.success('Friend added!');
      await listFriends();
    } catch (error) {
      toast.error('Error accepting friend request.');
      throw error;
    }
  };

  const refuseFriendRequest = async (id: string) => {
    try {
      await refuseFriendRequestService(id);
      toast.success('Friend request refused.');
    } catch (error) {
      toast.error('Error refusing friend request.');
      throw error;
    }
  };

  const cancelFriendRequest = async (id: string) => {
    try {
      await cancelFriendRequestService(id);
      toast.success('Friend request cancelled.');
    } catch (error) {
      toast.error('Error cancelling friend request.');
      throw error;
    }
  };

  const clearFriends = () => {
    friends.value = [];
    currentPage.value = 1;
    hasMoreFriends.value = true;
    currentUsernameFilter.value = undefined;
  };

  return {
    friends,
    hasMoreFriends,
    listFriends,
    loadMoreFriends,
    sendFriendRequest,
    acceptFriendRequest,
    refuseFriendRequest,
    cancelFriendRequest,
    sendFriendRequestByUsername,
    removeFriend,
    clearFriends
  };
});
