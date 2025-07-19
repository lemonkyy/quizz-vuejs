import axios from '@/api/axios';
import type { User, PublicUser } from '@/types';

export async function login(params: {email: string, password: string}): Promise<{code: string, message?: string, tempToken?: string, error?: string}> {
  try {
    console.log(import.meta.env.VITE_PUBLIC_API_URL + '/login');
    const response = await axios.post('/login', params);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function logout(): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post('/user/logout');
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function register(params: {email: string, password: string, tosAgreedTo: boolean, username?: string}): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post('/register', params);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function loginVerify(params: {totpCode: string, tempToken: string}): Promise<{code: string, tempToken?: string, error?: string}> {
  try {
    const response = await axios.post('/login-verify', params);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function generateTotpSecret(): Promise<{code: string, totpSecret?: string, error?: string}> {
  try {
    const response = await axios.post('/user/totp/secret');
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function updateUser(params: {newUsername?: string, newProfilePictureId?: string, clearTotpSecret?: boolean}): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.put('/user', params);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function getUserByUsername(params: {username: string}): Promise<{code: string, user?: PublicUser, error?: string}> {
  try {
    const response = await axios.get('/user/get-by-username', { params: params });
    return {
      code: 'SUCCESS',
      user: response.data
    };
  } catch (error) {
    throw error;
  }
}

export async function searchUsers(username: string, page?: number, limit?: number): Promise<{code: string, users?: PublicUser[]}> {
  try {
    const response = await axios.get('/user/search', { params: { username, page, limit } });
    return {
      code: 'SUCCESS',
      users: response.data.member
    };
  } catch (error) {
    throw error;
  }
}

export async function getMe(): Promise<{code: string, user?: User}> {
  try {
    const response = await axios.get('/user/me');
    return {
      code: 'SUCCESS',
      user: response.data
    };
  } catch (error) {
    throw error;
  }
}
