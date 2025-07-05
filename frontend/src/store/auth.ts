import { defineStore } from 'pinia';
import type { User, JWTUserPayload } from '@/types';
import { ref, watch } from 'vue';
import {login as loginService, register as registerService, logout as logoutService} from '@/services/userService';
import { jwtDecode } from 'jwt-decode';

export const useAuthStore = defineStore("auth",  () => {

  const user = ref<User |null>(null);
  const token = ref<string | null>(null);

  if (localStorage.getItem('user')) {
    user.value = JSON.parse(localStorage.getItem('user') || '');
  }

  watch(user, (userVal) => {
    localStorage.setItem('user', JSON.stringify(userVal));
  }, {deep: true})

  const register = async (email: string, password: string, passwordConfirmation: string) => {
      try {
          const response = await registerService({ email, password, passwordConfirmation });
          console.log(response.message);
        } catch (error) {
            console.error('Registration failed:', error);
            throw error;
        }
    };

  const login = async (email: string, password: string) => {
  try {
    const response = await loginService({ email, password });

    token.value = response.token;
    const userData = jwtDecode<JWTUserPayload>(response.token);

    user.value = {
      id: userData.jti,
      username: userData.username,
      email: userData.email,
      roles: userData.roles,
    };

    console.log('Login successful');

  } catch (error) {
    console.error('Login failed:', error);
    throw error;
  }
};

  const logout = async () => {
    try {
      await logoutService({ token: token.value || '' });
      token.value = null;
      user.value = null;
    } catch (error) {
      console.error('Logout failed:', error);
      throw error;
    }
  }

  return {
    user,
    token,
    register,
    login,
    logout
  }

})
