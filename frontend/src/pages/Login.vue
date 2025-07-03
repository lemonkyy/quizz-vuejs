<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';
import Title from '@/components/ui/Title.vue';
import { login } from '@/services/Auth';

const email = ref('');
const password = ref('');

const handleSubmit = async () => {
  try {
    const response = await login({ email: email.value, password: password.value });
    console.log('Login successful, token:', response.token);
  } catch (error) {
    console.error('Login failed:', error);
  }
};
</script>

<template>
  <div class="bg-gray-100 w-md p-3">
    <Title :level="1"> Login </Title>
    <form @submit.prevent="handleSubmit" class="flex flex-col gap-5 mt-5">
      <Input
        v-model="email"
        type="text"
        placeholder="Email"
        className="border"
      />
      <Input
        v-model="password"
        type="password"
        placeholder="Password"
        className="border"
      />
      <Button type="submit" className="w-full">
        Login
      </Button>
    </form>
  </div>
</template>
