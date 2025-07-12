<script setup lang="ts">
import Image from '@/components/ui/atoms/Image.vue';
import UserIcon from '@/assets/images/user_icon1.png';
import { useAuthStore } from '@/store/auth';
import Title from '@/components/ui/atoms/Title.vue';
import LogoutButton from '@/components/ui/molecules/buttons/LogoutButton.vue';
import QuizHistoryRow from '@/components/profile/QuizHistoryRow.vue';
import AddFriendRow from '@/components/profile/AddFriendRow.vue';
import EditProfileRow from './EditProfileRow.vue';

const auth = useAuthStore();
const emit = defineEmits(['show-add-friend', 'close-modal', 'show-profile-editor']);
</script>

<template>
  <div class="flex flex-col justify-center items-center p-4 w-full">
    <Image :src="UserIcon" alt="user icon" rounded="sm" :size="16" />
    <Title :level="3" class="mt-2">{{ auth.user?.username }}</Title>
    <div class="flex flex-col gap-2 mt-4">
      <QuizHistoryRow @click="$emit('close-modal')" />
      <AddFriendRow @click="$emit('show-add-friend')" />
      <EditProfileRow @click="$emit('show-profile-editor')" />
    </div>
    <LogoutButton @click="$emit('close-modal')" class="mt-6 w-full" />
  </div>
</template>
