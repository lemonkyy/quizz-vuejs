<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';
import Title from '@/components/ui/Title.vue';
import { useAuthStore } from '@/store/auth';

const email = ref<string>('');
const password = ref<string>('');
const { login } = useAuthStore();

</script>

<template>
  <div>
    <Title :level="1">Se connecter</Title>
    <form @submit.prevent="login(email, password)" class="flex flex-col gap-5 mt-5">
      <Input v-model="email" type="text" placeholder="Adresse e-mail" />
      <Input v-model="password" type="password" placeholder="Mot de passe" />
      <Button type="submit" className="w-full"> Connexion </Button>
    </form>
  </div>
</template>
