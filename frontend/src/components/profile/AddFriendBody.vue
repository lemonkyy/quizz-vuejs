<script setup lang="ts">
import Title from '@/components/ui/atoms/Title.vue';
import Input from '@/components/ui/atoms/Input.vue';
import SendFriendRequestButton from '@/components/ui/buttons/SendFriendRequestButton.vue';
import { ref } from 'vue';

const usernameSearch = ref('');
const isLoading = ref(false);
defineEmits(['back']);

const handleSendRequest = async () => {
  if (!usernameSearch.value) return;
  isLoading.value = true;
  try {
    console.log(`Sending friend request to ${usernameSearch.value}`);
    await new Promise(resolve => setTimeout(resolve, 1000));
  } catch (error) {
    console.error('Friend request failed:', error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
    <div class="relative flex flex-col items-center gap-7 p-4 w-full">
        <Title :level="3" class="mt-2">Add A Friend</Title>
        <Input id="search-friend-modal" v-model="usernameSearch" theme="primary" placeholder="Enter username" class="w-full" />
        <SendFriendRequestButton :loading="isLoading" @click="handleSendRequest" class="max-w-64" />
    </div>
</template>
