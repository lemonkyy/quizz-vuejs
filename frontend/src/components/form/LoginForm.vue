<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/atoms/Input.vue';
import Button from '@/components/ui/atoms/Button.vue';
import Title from '@/components/ui/atoms/Title.vue';
import Error from '@/components/ui/atoms/Error.vue';
import { useAuthStore } from '@/store/auth';
import TotpLoginModal from '../ui/modals/TotpLoginModal.vue';
import type { AxiosError } from 'axios';

const email = ref<string>('');
const password = ref<string>('');
const showTotpModal = ref(false);
const tempToken = ref<string | null>(null);
const formError = ref<string | null>(null);
const isLoading = ref(false);

const { login, loginVerify } = useAuthStore();

const emailRegex = /^[^@]+@[^@]+\.[^@]+$/;

const defaultErrorMessage = 'Une erreur inattendue est survenue.';
const errorMessages: { [key: string]: string } = {
  'BAD_CREDENTIALS': 'Adresse e-mail ou mot de passe incorrect.',

  'ERR_MISSING_TOTP_CREDENTIALS': 'Le code de vérification est manquant. Veuillez réessayer.',
  'ERR_INVALID_TEMP_TOKEN': 'Votre session a expiré. Veuillez vous reconnecter.',
  'ERR_USER_NOT_FOUND': 'Une erreur est survenue. Utilisateur introuvable.',
  'ERR_INVALID_TOTP_CODE': 'Le code de vérification est incorrect.',
};

const handleLogin = async () => {
  formError.value = null;
  isLoading.value = true;

  if (!email.value || !password.value) {
    formError.value = 'Veuillez remplir tous les champs.';
    isLoading.value = false;
    return;
  }

  if (!emailRegex.test(email.value)) {
    formError.value = "Le format de l'adresse e-mail est invalide.";
    isLoading.value = false;
    return;
  }

  try {
    const result = await login(email.value, password.value);

    if (result.code === 'TOTP_REQUIRED') {
      tempToken.value = result.tempToken ?? null;
      showTotpModal.value = true;
    }
  } catch (error) {
    formError.value = errorMessages.BAD_CREDENTIALS;
  } finally {
    isLoading.value = false;
  }
};

const handleTotpSubmit = async (code: string) => {
  if (!tempToken.value) {
    return;
  }
  formError.value = null;
  isLoading.value = true;

  try {
    await loginVerify(code, tempToken.value);
  } catch (error) {
    const axiosError = error as AxiosError<{ code: string; error: string }>;
    const errorCode = axiosError.response?.data?.code;
    formError.value = errorCode ? errorMessages[errorCode] || defaultErrorMessage : defaultErrorMessage;
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div>
    <Title :level="1" center>Welcome Back</Title>
    <form @submit.prevent="handleLogin" class="flex flex-col gap-7 mt-5 w-full sm:w-xl">
      <Input id="username-login" v-model="email" type="text" placeholder="Email" className="mx-4" theme="secondary" without-border />
      <Input id="password-ligin" v-model="password" type="password" placeholder="Password" className="mx-4" theme="secondary" without-border />

      <Button theme="secondary" type="submit" className="w-full" :loading="isLoading" :disabled="isLoading">
        Log In
      </Button>
      <Button transparent theme="primary"><router-link to="/register">Don't have an account? Sign up</router-link></Button>

      <Error v-if="formError">
        <p>{{ formError }}</p>
      </Error>
    </form>

    <TotpLoginModal v-model="showTotpModal" @valid="handleTotpSubmit" :error="formError" />
  </div>
</template>
