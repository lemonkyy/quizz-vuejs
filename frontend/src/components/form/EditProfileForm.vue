<script setup lang="ts">
import Image from '@/components/ui/atoms/Image.vue';
import UserIcon from '@/assets/images/user_icon1.png';
import { useAuthStore } from '@/store/auth';
import { computed, ref } from 'vue';
import { AxiosError } from 'axios';
import Button from '../ui/atoms/Button.vue';
import Error from '../ui/atoms/Error.vue';
import Input from '../ui/atoms/Input.vue';

const emit = defineEmits(['back']);

const auth = useAuthStore();

const newUsername = ref<string>(auth.user?.username || '');
const formError = ref<string | null>(null);
const isLoading = ref(false);

const defaultErrorMessage = 'An unexpected error occurred.';
const errorMessages: { [key: string]: string } = {
  'INVALID_USERNAME': 'Invalid username.',
  'ERR_USERNAME_INVALID_TYPE': 'Invalid username type.',
  'ERR_USERNAME_CONTAINS_SPACES': 'Username must not contain spaces.',
  'ERR_USERNAME_LENGTH': 'Username must be between 1 and 255 characters.',
  'ERR_USERNAME_INAPPROPRIATE': 'Username contains inappropriate words.',
  'ERR_USERNAME_TAKEN': 'This username is already taken.',
};

const isFormDisabled = computed(() => {
  return isLoading.value || newUsername.value === auth.user?.username;
});

const handleUpdateAccount = async () => {
  formError.value = null;
  isLoading.value = true;

  if (!newUsername.value) {
    formError.value = 'Please enter a username.';
    isLoading.value = false;
    return;
  }

  try {
    await auth.updateUsername(newUsername.value);
    emit('back');
  } catch (error) {
    if (error instanceof AxiosError && error.response?.data?.code) {
      formError.value = errorMessages[error.response.data.code] || defaultErrorMessage;
    } else {
      formError.value = defaultErrorMessage;
    }
  } finally {
    isLoading.value = false;
  }
};

</script>

<template>
  <form @submit.prevent="handleUpdateAccount" class="flex flex-col items-center gap-4 p-4 w-full">
    <Image :src="UserIcon" alt="user icon" rounded="sm" :size="16" />
    
    <Input
        id="username-edit"
        theme="secondary"
        v-model="newUsername"
        type="text"
        placeholder="New Username"
        className="text-center w-full"
        without-border
    />

    <Error v-if="formError" class="mx-4">
      <p>{{ formError }}</p>
    </Error>
        
    <div class="w-full mt-20 absolute bottom-8 sm:bottom-9 px-8 sm:px-9">
      <Button
      theme="primary"
      type="submit"
      class="w-full"
      rounded="md"
      :loading="isLoading"
      :disabled="isFormDisabled"
      >
          Update Profile
      </Button>
    </div>
  </form>
</template>
