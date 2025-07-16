import { defineStore } from 'pinia';
import type { User, JWTUserPayload } from '@/types';
import { onMounted, ref, computed } from 'vue';
import {login as loginService, 
  register as registerService, 
  logout as logoutService, 
  loginVerify as loginVerifyService, 
  generateTotpSecret as generateTotpSecretService,
  updateUser as updateUserService } from '@/services/userService';
import { jwtDecode } from 'jwt-decode';
import router from '@/router';
import { useToast } from "vue-toastification";

export const useAuthStore = defineStore("auth",  () => {

  const user = ref<User |null>(null);

  const userProfilePictureUrl = computed(() => {
    return user.value?.profilePicture ? import.meta.env.VITE_PUBLIC_PFP_URL + '/' + user.value.profilePicture.fileName : "";
  });
  const toast = useToast();

  //get the cookie containing user info
  const getJwtHpFromCookie = (): string | null => {
    const match = document.cookie.match(/(?:^|; )jwt_hp=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : null;
  };

  //init user info from the hp cookie
  const initUserFromCookie = () => {

    const jwtHp = getJwtHpFromCookie();

    if (jwtHp) {
      try {
        const userData = jwtDecode<JWTUserPayload>(jwtHp);
        user.value = {
          id: userData.id,
          username: userData.username,
          profilePicture: {
            id: userData.profilePictureId,
            fileName: userData.profilePictureFileName
          },
          email: userData.email,
          roles: userData.roles,
          hasTotp: userData.hasTotp
        };
      } catch (error) {
        console.warn('Invalid JWT in cookie:', error);
      }
    }
  };
  onMounted(initUserFromCookie);

  const register = async (email: string, password: string, tosAgreedTo: boolean, username?: string) => {
    try {
        await registerService({ email, password, tosAgreedTo, username });
        toast.success('Account registered!');
        router.push('/');
        return { code: 'SUCCESS' };
      } catch (error) {
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
        toast.success('Login successful!');
        router.push('/');
        return { code: 'SUCCESS' };
      }
      
    } catch (error) {
      throw error;
    }
  };

  const loginVerify = async (totpCode: string, tempToken: string) => {
    try {
      await loginVerifyService({ totpCode, tempToken });
      initUserFromCookie();
      toast.success('Login successful!');
      router.push('/');
    } catch (error) {
      throw error;
    }
  }

  const updateUser = async (newUsername?: string, newProfilePictureId?: string) => {
    try {
      await updateUserService({ newUsername, newProfilePictureId });
      initUserFromCookie();
      toast.success('Your account has been updated.');
    } catch (error) {
      toast.error('Error while updating account.')
      throw error;
    }
  }

  const clearTotpSecret = async () => {
    try {
      await updateUserService({  clearTotpSecret: true });
      initUserFromCookie();
      toast.success('2FA has been disabled on your account.');
    } catch (error) {
      toast.error('Error while disabling 2FA.')
      throw error;
    }
  }

  const generateTotpSecret = async () => {
    try {
      const response = await generateTotpSecretService();
      initUserFromCookie();
      toast.success('TOTP configured! Please retrieve your secret code.')
      return response;
    } catch(error) {
      toast.error('Error while generating OTP secret.')
      throw error;
    }
  }
  
  const logout = async () => {
    try {
      await logoutService();
      
      document.cookie = 'jwt_hp=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
      document.cookie = 'jwt_s=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';

      user.value = null;
      router.push('/');
      toast.success('You are logged out. See you later!');
    } catch (error) {
      toast.error('Error during logout.')
      throw error;
    }
  }

  return {
    user,
    userProfilePictureUrl,
    register,
    login,
    loginVerify,
    updateUser,
    logout,
    generateTotpSecret,
    clearTotpSecret
  }

})
