<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/atoms/Input.vue';
import Button from '@/components/ui/atoms/Button.vue';
import Title from '@/components/ui/atoms/Title.vue';
import Error from '@/components/ui/atoms/Error.vue';
import { useAuthStore } from '@/store/auth';
import TotpLoginModal from '../ui/molecules/modals/TotpLoginModal.vue';
import type { AxiosError } from 'axios';

const email = ref<string>('');
const password = ref<string>('');
const showTotpModal = ref(false);
const tempToken = ref<string | null>(null);
const formError = ref<string | null>(null);
const isLoading = ref(false);

const auth = useAuthStore();

const emailRegex = /^[^@]+@[^@]+\.[^@]+$/;

const defaultErrorMessage = 'An unexpected error occurred.';
const errorMessages: { [key: string]: string } = {
  'BAD_CREDENTIALS': 'Incorrect email or password.',

  'ERR_MISSING_TOTP_CREDENTIALS': 'Verification code is missing. Please try again.',
  'ERR_INVALID_TEMP_TOKEN': 'Your session has expired. Please log in again.',
  'ERR_USER_NOT_FOUND': 'An error occurred. User not found.',
  'ERR_INVALID_TOTP_CODE': 'Incorrect verification code.',
};

const handleLogin = async () => {
  formError.value = null;
  isLoading.value = true;

  if (!email.value || !password.value) {
    formError.value = 'Please fill in all fields.';
    isLoading.value = false;
    return;
  }

  if (!emailRegex.test(email.value)) {
    formError.value = 'Invalid email format.';
    isLoading.value = false;
    return;
  }

  try {
    const result = await auth.login(email.value, password.value);

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
    await auth.loginVerify(code, tempToken.value);
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
    <p v-if="auth.user" class="mt-8 text-center"> You are already logged in as {{ auth.user.username }}. <span @click="auth.logout" class="border-b cursor-pointer"> Logout </span></p>
    <Title :level="1" center>Welcome Back</Title>
    <form @submit.prevent="handleLogin" class="flex flex-col gap-7 mt-5 w-full sm:w-xl">
      <Input id="email-login" v-model="email" type="text" placeholder="Email" className="mx-4" theme="secondary" without-border autocomplete="username" />
      <Input id="password-login" v-model="password" type="password" placeholder="Password" className="mx-4" theme="secondary" without-border autocomplete="current-password" />

      <Button theme="primary" type="submit" class="w-full" :loading="isLoading" :disabled="isLoading">
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
