import axios from '@/plugins/axios';
import type { PublicUser } from '@/types';

export async function sendFriendRequest(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/friend-request/${id}/send`);

    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function acceptFriendRequest(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/friend-request/${id}/accept`);

    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function refuseFriendRequest(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/friend-request/${id}/refuse`);

    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function cancelFriendRequest(id: string): Promise<{code: string}> {
  try {
    await axios.post(`/friend-request/${id}/cancel`);

    return {
      code: 'SUCCESS'
    };
  } catch (error) {
    throw error;
  }
}

export async function listSentFriendRequests(): Promise<{code: string, friendRequests?: any[]}> {
  try {
    const response = await axios.get('/friend-request/sent');

    return {
      code: 'SUCCESS',
      friendRequests: response.data.member,
    };
  } catch (error) {
    throw error;
  }
}

export async function listReceivedFriendRequests(): Promise<{code: string, friendRequests?: any[]}> {
  try {
    const response = await axios.get('/friend-request/received');
    
    return {
      code: 'SUCCESS',
      friendRequests: response.data.member,
    };
  } catch (error) {
    throw error;
  }
}

export async function listFriends(params: {username?: string, page?: number, limit?: number}): Promise<{code: string, friends?: PublicUser[], hasMore?: boolean}> {
  try {
    const response = await axios.get('/user/friends', { params } );
    
    return {
      code: 'SUCCESS',
      friends: response.data.member,
      hasMore: response.data.hasMore
    };
  } catch (error) {
    throw error;
  }
}

export async function removeFriend(id: string): Promise<{code: string}> {
  try {
    await axios.delete(`/user/friends/${id}`);

    return {
      code: 'SUCCESS'
    };  
  } catch (error) {
    throw error;
  }
}

