<script setup lang="ts">
import Title from '@/components/ui/atoms/Title.vue';
import Input from '@/components/ui/atoms/Input.vue';
import Button from '@/components/ui/atoms/Button.vue';
import FriendList from '@/components/profile/FriendList.vue';
import { ref } from 'vue';

const usernameFilter = ref('');
const isLoading = ref(false);

const emit = defineEmits(['show-add-friend']);
</script>

<template>
  <div class="relative flex flex-col items-center gap-7 p-4 w-full">
    <div class="w-full flex justify-between items-center">
      <Title :level="3" class="mt-2">Friends</Title>
      <Button @click="emit('show-add-friend')" theme="primary" rounded="md">Add Friend</Button>
    </div>

    <div class="w-full">
      <Input
        id="friend-filter-input"
        v-model="usernameFilter"
        placeholder="Filter friends by username..."
        type="text"
      />
    </div>

    <FriendList :username-filter="usernameFilter" @update:is-loading="isLoading = $event" />
  </div>
</template>
