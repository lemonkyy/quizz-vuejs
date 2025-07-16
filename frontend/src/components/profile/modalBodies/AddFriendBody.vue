<script setup lang="ts">
import Title from '@/components/ui/atoms/Title.vue';
import SendFriendRequestButton from '@/components/ui/molecules/buttons/SendFriendRequestButton.vue';
import { ref } from 'vue';
import UsernameAutoComplete from '../../ui/molecules/inputs/UsernameAutoComplete.vue';
import { useFriendStore } from '@/store/friendship';
import Error from '@/components/ui/atoms/Error.vue';
import type { AxiosError } from 'axios';

const friendStore = useFriendStore();

const usernameSearch = ref('');
const isLoading = ref(false);
const formError = ref<string | null>(null);

const defaultErrorMessage = 'An unexpected error occurred.';
const errorMessages: { [key: string]: string } = {
  'ERR_CANNOT_SEND_TO_SELF': 'You cannot send a friend request to yourself.',
  'ERR_USER_NOT_FOUND': 'User not found.',
  'ERR_FRIEND_REQUEST_ALREADY_SENT': 'Friend request already sent.',
  'ERR_ALREADY_FRIENDS': 'You are already friends with this user.',
  'ERR_FRIEND_REQUEST_ALREADY_RECEIVED': 'You have a pending friend request from this user.',
  'ERR_MAX_SENT_FRIEND_REQUESTS_REACHED': 'You have reached the maximum number of sent friend requests.',
  'ERR_MAX_RECEIVED_FRIEND_REQUESTS_REACHED': 'The receiver has reached the maximum number of received friend requests.',
  'ERR_MISSING_USERNAME': 'Username is required.',
};

const handleSendRequest = async () => {
  formError.value = null;
  if (!usernameSearch.value) {
    formError.value = 'Please enter a username.';
    return;
  }
  isLoading.value = true;
  try {
    await friendStore.sendFriendRequestByUsername(usernameSearch.value);
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
    <div class="relative flex flex-col items-center gap-7 p-4 w-full">
        <Title :level="3" class="mt-2">Add A Friend</Title>
        <UsernameAutoComplete v-model="usernameSearch" />
        <SendFriendRequestButton :loading="isLoading" @click="handleSendRequest" class="w-full max-w-64" />

        <Error v-if="formError">
            <p>{{ formError }}</p>
        </Error>
    </div>
</template>
