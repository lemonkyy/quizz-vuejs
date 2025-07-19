<script setup lang="ts">
import { ref, computed } from 'vue';
import Input from '@/components/ui/atoms/Input.vue';
import Button from '@/components/ui/atoms/Button.vue';
import Title from '@/components/ui/atoms/Title.vue';
import Error from '@/components/ui/atoms/Error.vue';
import { useAuthStore } from '@/store/auth';
import { AxiosError } from 'axios';
import Checkbox from '../ui/atoms/Checkbox.vue';

const email = ref<string>('');
const password = ref<string>('');
const username = ref<string>('');
const confirmPassword = ref<string>('');
const formError = ref<string | null>(null);
const isLoading = ref(false);
const tosAgreedTo = ref(false);

const { register } = useAuthStore();

const emailRegex = /^[^@]+@[^@]+\.[^@]+$/;

const defaultErrorMessage = 'An unexpected error occurred.';
const errorMessages: { [key: string]: string } = {
  'ERR_MISSING_CREDENTIALS': 'Please fill in all fields.',
  'ERR_INVALID_EMAIL': 'Invalid email format.',
  'ERR_EMAIL_ALREADY_IN_USE': 'This email is already in use.',
  'ERR_USERNAME_VALIDATION_FAILED': 'Invalid username.',
  'ERR_USERNAME_CONTAINS_SPACES': 'Username must not contain spaces.',
  'ERR_USERNAME_LENGTH': 'Username must be between 1 and 255 characters.',
  'ERR_USERNAME_INAPPROPRIATE': 'Username contains inappropriate words.',
  'ERR_USERNAME_TAKEN': 'This username is already taken.',
  'ERR_PASSWORD_MISMATCH': 'Passwords do not match.',
  'ERR_PASSWORD_WEAK': 'Password is too weak. It must contain at least 12 characters, one uppercase, one lowercase, one number, and one special character.',
  'ERR_TOS_REFUSED' : 'Please accept the terms of service.'
};

const validatePasswordComplexity = (pwd: string): boolean => {
  if (pwd.length < 12) return false;

  if (!/[A-Z]/.test(pwd)) return false;

  if (!/[a-z]/.test(pwd)) return false;

  if (!/[0-9]/.test(pwd)) return false;

  if (!/[^a-zA-Z0-9\s]/.test(pwd)) return false;
  
  return true;
};

const isFormDisabled = computed(() => {
  return isLoading.value;
});

const handleRegister = async () => {
  formError.value = null;
  isLoading.value = true;

  if (!email.value || !password.value || !confirmPassword.value || !username.value) {
    formError.value = errorMessages.ERR_MISSING_CREDENTIALS;
    isLoading.value = false;
    return;
  }

  if (!emailRegex.test(email.value)) {
    formError.value = errorMessages.ERR_INVALID_EMAIL;
    isLoading.value = false;
    return;
  }

  if (password.value !== confirmPassword.value) {
    formError.value = errorMessages.ERR_PASSWORD_MISMATCH;
    isLoading.value = false;
    return;
  }

  if (!validatePasswordComplexity(password.value)) {
    formError.value = errorMessages.ERR_PASSWORD_WEAK;
    isLoading.value = false;
    return;
  }

  if (!tosAgreedTo.value) {
    formError.value = errorMessages.ERR_TOS_REFUSED;
    isLoading.value = false;
    return;
  }

  try {
    await register(email.value, password.value, tosAgreedTo.value, username.value);
  } catch (error) {
    if (error instanceof AxiosError && error.response?.data?.code) {
      formError.value = errorMessages[error.response.data.code] || defaultErrorMessage;
    } else {
      formError.value = defaultErrorMessage;
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
    <div>
        <Title :level="1" center> Create your account </Title>
        <form @submit.prevent="handleRegister" class="flex flex-col gap-7 mt-5 w-full sm:w-xl">
          <Input id="email-register" theme="secondary" v-model="email" type="text" placeholder="Email" className="mx-4" without-border autocomplete="email"/>
          <Input id="username-register" theme="secondary" v-model="username" type="text" placeholder="Username" className="mx-4" without-border autocomplete="username"/>
          <Input id="password-register" theme="secondary" v-model="password" type="password" placeholder="Password" className="mx-4" without-border autocomplete="new-password"/>
          <Input id="password-verify-register" theme="secondary" v-model="confirmPassword" type="password"placeholder="Password Confirmation" className="mx-4" without-border autocomplete="new-password"/>
          <Checkbox id="tos-register" theme="secondary" label="I agree to the Terms of Service" v-model="tosAgreedTo" />

          <Error v-if="formError" class="mx-4">
              <p>{{ formError }}</p>
          </Error>
          
          <Button theme="primary" type="submit" class="w-full" :loading="isLoading" :disabled="isFormDisabled">
            Sign up
          </Button>
          <Button transparent theme="primary"><router-link to="/login">Already have an account? Sign in</router-link></Button>
        </form>
    </div>
</template>
