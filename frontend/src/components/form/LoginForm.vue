<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';
import Title from '@/components/ui/Title.vue';
import { useAuthStore } from '@/store/auth';
import TOTPModal from '../ui/TOTPModal.vue';

const email = ref<string>('');
const password = ref<string>('');
const showTOTPModal = ref(false);
const tempToken = ref<string | null>(null);

const { login, loginVerify } = useAuthStore();


const handleLogin = async () => {
  try {
    const result = await login(email.value, password.value);

    if (result.code === 'TOTP_REQUIRED') {
      tempToken.value = result.tempToken ?? null;
      showTOTPModal.value = true;
    }
  } catch (error) {
    console.error('Login failed:', error);
  }
};

const handleTOTPSubmit = async (code: string) => {
  if (!tempToken.value) {
    return;
  }

  try {
    await loginVerify(code, tempToken.value);
  } catch (error) {
    console.error('OTP check failed:', error);
  }
};

</script>

<template>
  <div>
    <Title :level="1">Se connecter</Title>
    <form @submit.prevent="handleLogin" class="flex flex-col gap-5 mt-5">
      <Input v-model="email" type="text" placeholder="Adresse e-mail" />
      <Input v-model="password" type="password" placeholder="Mot de passe" />
      <Button type="submit" className="w-full"> Connexion </Button>
    </form>

    <TOTPModal v-model="showTOTPModal" @valid="handleTOTPSubmit" />
  </div>
</template>
