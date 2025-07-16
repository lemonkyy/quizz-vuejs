<script setup lang="ts">
import Image from '@/components/ui/atoms/Image.vue';
import { useAuthStore } from '@/store/auth';
import Title from '@/components/ui/atoms/Title.vue';
import LogoutButton from '@/components/ui/molecules/buttons/LogoutButton.vue';
import QuizHistoryRow from '@/components/profile/menuRows/QuizHistoryRow.vue';
import FriendRow from '@/components/profile/menuRows/FriendRow.vue';
import EditProfileRow from '@/components/profile/menuRows/EditProfileRow.vue';
import ToggleTotpRow from '@/components/profile/menuRows/ToggleTotpRow.vue';

const auth = useAuthStore();
const emit = defineEmits(['show-friend', 'close-modal', 'show-profile-editor', 'toggle-2fa']);
</script>

<template>
  <div class="flex flex-col justify-center items-center p-4 w-full">
    <Image :src="auth.userProfilePictureUrl" alt="user icon" rounded="sm" :size="16" />
    <Title :level="3" class="mt-2">{{ auth.user?.username }}</Title>
    <div class="flex flex-col gap-2 mt-4">
      <QuizHistoryRow @click="$emit('close-modal')" />
      <FriendRow @click="$emit('show-friend')" />
      <EditProfileRow @click="$emit('show-profile-editor')" />
      <ToggleTotpRow @click="$emit('toggle-2fa')"/>
    </div>
    <LogoutButton @click="$emit('close-modal')" class="mt-6 w-full" />
  </div>
</template>
