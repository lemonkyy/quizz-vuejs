import { defineStore } from 'pinia';
import type { User, JWTUserPayload } from '@/types';
import { onMounted, ref } from 'vue';
import {login as loginService, register as registerService, logout as logoutService, loginVerify as loginVerifyService} from '@/services/userService';
import { jwtDecode } from 'jwt-decode';
import router from '@/router';

export const useAuthStore = defineStore("auth",  () => {

  const user = ref<User |null>(null);

  //get the cookie containing user info
  const getJwtHpFromCookie = (): string | null => {
    const match = document.cookie.match(/(?:^|; )jwt_hp=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : null;
  };

  //init user info from the hp cookie
  const initUserFromCookie = () => {

    const jwtHp = getJwtHpFromCookie();

    console.log("jwt: " + jwtHp)
    if (jwtHp) {
      try {
        const userData = jwtDecode<JWTUserPayload>(jwtHp);
        user.value = {
          username: userData.username,
          email: userData.email,
          roles: userData.roles,
        };
        console.log(user.value);
      } catch (error) {
        console.warn('Invalid JWT in cookie:', error);
      }
    }
  };
  onMounted(initUserFromCookie);

  const register = async (email: string, password: string, passwordConfirmation: string) => {
      try {
          const response = await registerService({ email, password, passwordConfirmation });
          console.log(response.message);
        } catch (error) {
          console.error('Registration failed:', error);
          throw error;
        }
    };

  const login = async (email: string, password: string): Promise<{code: string, tempToken?: string}> => {
    try {
      const response = await loginService({ email, password });

      if (response.code === 'TOTP_REQUIRED') {
        return {
          code: 'TOTP_REQUIRED',
          tempToken: response.tempToken,
        };

      } else {
        initUserFromCookie();
        router.push('/');
        return { code: 'SUCCESS' };
      }

    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  };

  const loginVerify = async (totpCode: string, tempToken: string) => {
    try {
      await loginVerifyService({ totpCode, tempToken });
      initUserFromCookie();
      router.push('/');
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  }

  const logout = async () => {
    try {
      await logoutService();

      document.cookie = "jwt_hp=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";
      document.cookie = "jwt_s=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";

      user.value = null;
      router.push('/');
    } catch (error) {
      console.error('Logout failed:', error);
      throw error;
    }
  }

  return {
    user,
    register,
    login,
    loginVerify,
    logout
  }

})
