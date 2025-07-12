import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useToast } from "vue-toastification";
import type { User } from '@/types';
import {
  sendFriendRequest as sendFriendRequestService,
  acceptFriendRequest as acceptFriendRequestService,
  refuseFriendRequest as refuseFriendRequestService,
  cancelFriendRequest as cancelFriendRequestService,
  listSentFriendRequests as listSentFriendRequestsService,
  listReceivedFriendRequests as listReceivedFriendRequestsService,
  listFriends as listFriendsService,
  searchUsers as searchUsersService
} from '@/services/friendService';

export const useFriendStore = defineStore("friend", () => {

  const friends = ref<User[]>([]);
  const sentRequests = ref<any[]>([]);
  const receivedRequests = ref<any[]>([]);
  const toast = useToast();

  const listFriends = async () => {
    try {
      const response = await listFriendsService();
      if (response.code === 'SUCCESS' && response.friends) {
        friends.value = response.friends;
      }
    } catch (error) {
      console.error('Error listing friends:', error);
      throw error;
    }
  }

  const searchUsers = async (username: string, page?: number, limit?: number) => {
    try {
      const response = await searchUsersService(username, page, limit);
      if (response.code === 'SUCCESS' && response.users) {
        return response.users;
      }
    } catch (error) {
      toast.error('Error while fetching users.');
      throw error;
    }
  }

  const sendFriendRequest = async (receiverId: string) => {
    try {
      await sendFriendRequestService(receiverId);
      toast.success('Friend request sent!');
      await listSentFriendRequests();
    } catch (error) {
      toast.error('Error sending friend request.');
      throw error;
    }
  };

  const acceptFriendRequest = async (id: string) => {
    try {
      await acceptFriendRequestService(id);
      toast.success('Friend request accepted!');
      await listReceivedFriendRequests();
      await listFriends();
    } catch (error) {
      toast.error('Error accepting friend request.');
      throw error;
    }
  };

  const refuseFriendRequest = async (id: string) => {
    try {
      await refuseFriendRequestService(id);
      toast.success('Friend request refused!');
      await listReceivedFriendRequests();
    } catch (error) {
      toast.error('Error refusing friend request.');
      throw error;
    }
  };

  const cancelFriendRequest = async (id: string) => {
    try {
      await cancelFriendRequestService(id);
      toast.success('Friend request cancelled!');
      await listSentFriendRequests();
    } catch (error) {
      toast.error('Error cancelling friend request.');
      throw error;
    }
  };

  const listSentFriendRequests = async () => {
    try {
      const response = await listSentFriendRequestsService();
      if (response.code === 'SUCCESS' && response.friendRequests) {
        sentRequests.value = response.friendRequests;
      }
    } catch (error) {
      console.error('Error listing sent friend requests:', error);
      throw error;
    }
  };

  const listReceivedFriendRequests = async () => {
    try {
      const response = await listReceivedFriendRequestsService();
      if (response.code === 'SUCCESS' && response.friendRequests) {
        receivedRequests.value = response.friendRequests;
      }
    } catch (error) {
      console.error('Error listing received friend requests:', error);
      throw error;
    }
  };

  return {
    friends,
    sentRequests,
    receivedRequests,
    listFriends,
    searchUsers,
    sendFriendRequest,
    acceptFriendRequest,
    refuseFriendRequest,
    cancelFriendRequest,
    listSentFriendRequests,
    listReceivedFriendRequests
  };
});