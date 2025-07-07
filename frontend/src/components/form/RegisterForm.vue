<script setup lang="ts">
import { ref, computed } from 'vue';
import Input from '@/components/ui/atoms/Input.vue';
import Button from '@/components/ui/atoms/Button.vue';
import Title from '@/components/ui/atoms/Title.vue';
import Error from '@/components/ui/atoms/Error.vue';
import { useAuthStore } from '@/store/auth';
import { AxiosError } from 'axios';

const email = ref<string>('');
const password = ref<string>('');
const username = ref<string>('');
const confirmPassword = ref<string>('');
const formError = ref<string | null>(null);
const isLoading = ref(false);

const { register } = useAuthStore();

const emailRegex = /^[^@]+@[^@]+\.[^@]+$/;

const defaultErrorMessage = 'Une erreur inattendue est survenue.';
const errorMessages: { [key: string]: string } = {
  'ERR_MISSING_CREDENTIALS': 'Veuillez remplir tous les champs.',
  'ERR_INVALID_EMAIL': "Le format de l'adresse e-mail est invalide.",
  'ERR_EMAIL_ALREADY_IN_USE': 'Cette adresse e-mail est déjà utilisée.',
  'ERR_USERNAME_VALIDATION_FAILED': 'Le pseudonyme est invalide.',
  'ERR_USERNAME_CONTAINS_SPACES': 'Le pseudonyme ne doit pas contenir d\'espaces.',
  'ERR_USERNAME_LENGTH': 'Le pseudonyme doit contenir entre 1 et 255 caractères.', // Assuming default min/max from backend
  'ERR_USERNAME_INAPPROPRIATE': 'Le pseudonyme contient des mots inappropriés.',
  'ERR_USERNAME_TAKEN': 'Ce pseudonyme est déjà utilisé.',
  'ERR_PASSWORD_MISMATCH': 'Les mots de passe ne correspondent pas.',
  'ERR_PASSWORD_WEAK': 'Le mot de passe est trop faible. Il doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.',
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

  try {
    await register(email.value, password.value, username.value);
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
        <Title :level="1"> Créer un compte </Title>
        <form @submit.prevent="handleRegister" class="flex flex-col gap-5 mt-5 w-md">
        <Input
            v-model="email"
            type="text"
            placeholder="Adresse e-mail"
        />
        <Input
            v-model="username"
            type="text"
            placeholder="Pseudonyme"
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

        <Error v-if="formError">
            <p>{{ formError }}</p>
        </Error>

        <Button type="submit" className="w-full" :loading="isLoading" :disabled="isFormDisabled">
            Créer un compte
        </Button>
        </form>
    </div>
</template>
