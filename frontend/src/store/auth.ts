import { defineStore } from 'pinia';
import type { User, JWTUserPayload } from '@/types';
import { onMounted, ref } from 'vue';
import {login as loginService, 
  register as registerService, 
  logout as logoutService, 
  loginVerify as loginVerifyService, 
  generateTotpSecret as generateTotpSecretService,
  updateUser as updateUserService} from '@/services/userService';
import { jwtDecode } from 'jwt-decode';
import router from '@/router';
import { useToast } from "vue-toastification";

export const useAuthStore = defineStore("auth",  () => {

  const user = ref<User |null>(null);
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
          username: userData.username,
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
        toast.success('Compte enregistré !');
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
        toast.success('Connexion réussie !');
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
      toast.success('Connexion réussie !');
      router.push('/');
    } catch (error) {
      throw error;
    }
  }

  const updateUsername = async (newUsername: string) => {
    try {
      await updateUserService({ newUsername });
      initUserFromCookie();
      toast.success('pseudonyme modifié.');
    } catch (error) {
      toast.error('Erreur lors de la  modification du pseudonyme.')
      throw error;
    }
  }

  const clearTotpSecret = async () => {
    try {
      await updateUserService({  clearTotpSecret: true });
      initUserFromCookie();
      toast.success('Le TOTP a été désactivé sur votre compte.');
    } catch (error) {
      toast.error('Erreur lors de la désactivation du TOTP.')
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
      toast.success('Vous êtes déconnecté. ');
    } catch (error) {
      toast.error('Erreur lors de la déconnexion.')
      throw error;
    }
  }

  const generateTotpSecret = async () => {
    try {
      const response = await generateTotpSecretService();
      initUserFromCookie();
      toast.success('TOTP configuré! Veuillez récupérer votre code secret.')
      return response;
    } catch(error) {
      toast.error('Erreur lors de la génération du secret OTP.')
      throw error;
    }
  }

  return {
    user,
    register,
    login,
    loginVerify,
    updateUsername,
    logout,
    generateTotpSecret,
    clearTotpSecret
  }

})
