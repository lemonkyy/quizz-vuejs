import { defineStore } from 'pinia';
import type { User } from '@/types';
import { ref } from 'vue';
import {login as loginService, register as registerService, logout as logoutService} from '@/services/userService';

export const useAuthStore = defineStore("auth",  () => {

  const user = ref<User |null>(null);
  const token = ref<string | null>(null);
  
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
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  };

  const logout = async () => {
    try {
      const response = await logoutService({ token: token.value || '' });
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
