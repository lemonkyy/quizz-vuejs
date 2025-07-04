<script setup lang="ts">
import { ref } from 'vue';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';
import Title from '@/components/ui/Title.vue';
import { useAuthStore } from '@/store/auth';

const email = ref<string>('');
const password = ref<string>('');
const confirmPassword = ref<string>('');
const { register } = useAuthStore();

</script>

<template>
    <div>
        <Title :level="1"> Créer un compte </Title>
        <form @submit.prevent="register(email, password, confirmPassword)" class="flex flex-col gap-5 mt-5">
        <Input
            v-model="email"
            type="text"
            placeholder="Adresse e-mail"
        />
        <Input
            v-model="password"
            type="password"
            placeholder="Mot de passe"
        />
        <Input
            v-model="confirmPassword"
            type="password"
            placeholder="Confirmation du mot de passe"
        />
        <Button type="submit" className="w-full">
            Envoyer
        </Button>
        </form>
  </div>
</template>