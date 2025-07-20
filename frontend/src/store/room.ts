import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Room, CreateRoomDto } from '@/types';
import { useToast } from "vue-toastification";
import { 
  createRoom as createRoomService,
  joinRoom as joinRoomService,
  joinRoomByCode as joinRoomByCodeService,
  getCurrentRoom as getCurrentRoomService,
  leaveRoom as leaveRoomService,
  deleteRoom as deleteRoomService,
  kickUser as kickUserService,
  listPublicRooms as listPublicRoomsService
} from '@/services/roomService';

export const useRoomStore = defineStore("room", () => {
  const currentRoom = ref<Room | null>(null);
  const publicRooms = ref<Room[]>([]);
  const isLoading = ref(false);

  const toast = useToast();

  const createRoom = async (roomData: CreateRoomDto) => {
    try {
      isLoading.value = true;
      const response = await createRoomService(roomData);
      if (response.code === 'SUCCESS' && response.room) {
        currentRoom.value = response.room;
        toast.success('Room created successfully!');
        return response.room;
      }
    } catch (error: any) {
      toast.error('Error creating room');
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const joinRoom = async (roomId: string) => {
    try {
      isLoading.value = true;
      const response = await joinRoomService(roomId);
      if (response.code === 'SUCCESS' && response.room) {
        currentRoom.value = response.room;
        toast.success('Joined room successfully!');
        return response.room;
      }
    } catch (error: any) {
      if (error.response?.status === 404) {
        toast.error('Room not found');
      } else if (error.response?.status === 400) {
        toast.error('Cannot join this room');
      } else {
        toast.error('Error joining room');
      }
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const joinRoomByCode = async (code: string) => {
    try {
      isLoading.value = true;
      const response = await joinRoomByCodeService(code);
      if (response.code === 'SUCCESS' && response.room) {
        currentRoom.value = response.room;
        toast.success('Joined room successfully!');
        return response.room;
      }
    } catch (error: any) {
      if (error.response?.status === 404) {
        toast.error('Room not found with this code');
      } else if (error.response?.status === 400) {
        toast.error('Cannot join this room');
      } else {
        toast.error('Error joining room');
      }
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const getCurrentRoom = async () => {
    try {
      const response = await getCurrentRoomService();
      if (response.code === 'SUCCESS' && response.room) {
        currentRoom.value = response.room;
        return response.room;
      }
    } catch (error: any) {
      if (error.response?.status !== 404) {
        console.warn('Error getting current room:', error);
      }
    }
  };

  const leaveRoom = async () => {
    try {
      isLoading.value = true;
      const response = await leaveRoomService();
      if (response.code === 'SUCCESS') {
        currentRoom.value = null;
        toast.success('Left room successfully');
      }
    } catch (error: any) {
      toast.error('Error leaving room');
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const deleteRoom = async () => {
    try {
      isLoading.value = true;
      const response = await deleteRoomService();
      if (response.code === 'SUCCESS') {
        currentRoom.value = null;
        toast.success('Room deleted successfully');
      }
    } catch (error: any) {
      toast.error('Error deleting room');
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const kickUser = async (userId: string) => {
    try {
      const response = await kickUserService(userId);
      if (response.code === 'SUCCESS') {
        toast.success('User kicked successfully');
        await getCurrentRoom();
      }
    } catch (error: any) {
      toast.error('Error kicking user');
      throw error;
    }
  };

  const listPublicRooms = async (page?: number, itemsPerPage?: number) => {
    try {
      isLoading.value = true;
      const response = await listPublicRoomsService({ page, itemsPerPage });
      if (response.code === 'SUCCESS' && response.rooms) {
        publicRooms.value = response.rooms;
        return response.rooms;
      }
    } catch (error: any) {
      toast.error('Error loading public rooms');
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const userInRoom = computed(() => currentRoom.value !== null);

  return {
    currentRoom,
    publicRooms,
    isLoading,
    createRoom,
    joinRoom,
    joinRoomByCode,
    getCurrentRoom,
    leaveRoom,
    deleteRoom,
    kickUser,
    listPublicRooms,
    userInRoom,
  };
});