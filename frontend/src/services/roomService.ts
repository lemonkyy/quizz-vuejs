import type { Room, CreateRoomDto } from "@/types";
import axios from "@/api/axios";

export async function createRoom(roomData: CreateRoomDto): Promise<{code: string, room?: Room}> {
  try {
    const response = await axios.post('/room/create', roomData);
    return {
      code: 'SUCCESS',
      room: response.data
    };
  } catch (error) {
    throw error;
  }
}

export async function joinRoom(roomId: string): Promise<{code: string, room?: Room}> {
  try {
    const response = await axios.post(`/room/${roomId}/join`);
    return {
      code: 'SUCCESS',
      room: response.data
    };
  } catch (error) {
    throw error;
  }
}

export async function joinRoomByCode(code: string): Promise<{code: string, room?: Room}> {
  try {
    const response = await axios.post(`/room/${code}/join`);
    return {
      code: 'SUCCESS',
      room: response.data
    };
  } catch (error) {
    throw error;
  }
}

export async function getCurrentRoom(): Promise<{code: string, room?: Room}> {
  try {
    const response = await axios.get('/room/current');
    return {
      code: 'SUCCESS',
      room: response.data
    };
  } catch (error) {
    throw error;
  }
}

export async function leaveRoom(): Promise<{code: string}> {
  try {
    await axios.post('/room/leave');
    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function deleteRoom(): Promise<{code: string}> {
  try {
    await axios.delete('/room/delete');
    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function kickUser(userId: string): Promise<{code: string}> {
  try {
    await axios.post(`/room/${userId}/kick`);
    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function listPublicRooms(params?: { page?: number, itemsPerPage?: number }): Promise<{code: string, rooms?: Room[]}> {
  try {
    const response = await axios.get('/room/public', { params });
    return {
      code: 'SUCCESS',
      rooms: response.data.member
    };
  } catch (error) {
    throw error;
  }
}
