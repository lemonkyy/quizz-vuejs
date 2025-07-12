import axios from '@/plugins/axios';
import type { User } from '@/types';

export async function sendFriendRequest(receiverId: string): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post(`/friend-requests/${receiverId}`);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function acceptFriendRequest(id: string): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post(`/friend-requests/${id}/accept`);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function refuseFriendRequest(id: string): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post(`/friend-requests/${id}/refuse`);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function cancelFriendRequest(id: string): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post(`/friend-requests/${id}/cancel`);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function listSentFriendRequests(): Promise<{code: string, friendRequests?: any[], error?: string}> {
  try {
    const response = await axios.get('/friend-requests/sent');
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function listReceivedFriendRequests(): Promise<{code: string, friendRequests?: any[], error?: string}> {
  try {
    const response = await axios.get('/friend-requests/received');
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function listFriends(): Promise<{code: string, friends?: User[], error?: string}> {
  try {
    const response = await axios.get('/user/friends', {withCredentials: true});
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function searchUsers(username: string, page?: number, limit?: number): Promise<{code: string, users?: User[], error?: string}> {
  try {
    const response = await axios.post('/user/search', { username, page, limit }, {withCredentials: true});
    return response.data;
  } catch (error) {
    throw error;
  }
}
