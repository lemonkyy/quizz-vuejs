<script setup lang="ts">
import type { Room } from '@/types';
import Button from '@/components/ui/atoms/Button.vue';
import { useRoomStore } from '@/store/room';
import { useRouter } from 'vue-router';
import UserIcon from '@/components/profile/UserIcon.vue';
import Title from '../ui/atoms/Title.vue';

const props = defineProps<{ room: Room }>();
const roomStore = useRoomStore();
const router = useRouter();
const maxPlayers = 12;

const joinPublicRoom = async () => {
  try {
    await roomStore.joinRoom(props.room.id);
    router.push({ name: 'Room', params: { id: props.room.id } });
  } catch (error) {
    console.error('Failed to join room:', error);
  }
};
</script>

<template>
  <div class="bg-white rounded-lg shadow-md p-6 flex flex-col gap-3 justify-between items-center">
    <UserIcon
    :src="room.owner.profilePicture?.fileName ? `/images/profile_pictures/${room.owner.profilePicture.fileName}` : undefined"
    :size="5"
    />
    <Title :level="3">{{ room.owner.username }}'s room</Title>
    <p>Code: <span class="font-bold">{{ room.code }}</span></p>
    <p>{{ room.roomPlayers.length }}/{{ maxPlayers }} players</p>
    <Button @click="joinPublicRoom" :disabled="roomStore.isLoading">Join Room</Button>
  </div>
</template>
