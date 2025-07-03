<script setup lang="ts">
import { ref } from 'vue';
import Input from '../components/ui/Input.vue';
import Button from '../components/ui/Button.vue';
import Title from '../components/ui/Title.vue';

const email = ref('');
const password = ref('');

const handleSubmit = () => {
  console.log('Email:', email.value);
  console.log('Password:', password.value);
};
</script>

<template>
  <div class="bg-gray-100 w-md p-3">
    <Title :level="1">Login</Title>
    <form class="flex flex-col gap-5 mt-5" @submit.prevent="handleSubmit">
      <Input label="Adresse e-mail:" v-model="email" placeholder="your@email.com" />
      <Input label="Mot de passe:" type="password" v-model="password" placeholder="Your password" />
      <Button label="Connexion" type="submit" />
    </form>
  </div>
</template>
