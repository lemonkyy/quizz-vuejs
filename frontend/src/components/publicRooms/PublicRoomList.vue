<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoomStore } from '@/store/room';
import PublicRoomListItem from './PublicRoomListItem.vue';
import Title from '../ui/atoms/Title.vue';
import Button from '../ui/atoms/Button.vue';
import LoadingSpinner from '../ui/atoms/LoadingSpinner.vue';

const roomStore = useRoomStore();
const currentPage = ref(1);
const itemsPerPage = ref(10);
const hasMoreRooms = ref(true);

const loadRooms = async () => {
  if (!hasMoreRooms.value) return;
  
  await roomStore.listPublicRooms(currentPage.value, itemsPerPage.value);
  if (roomStore.publicRooms.length < currentPage.value * itemsPerPage.value) {
    hasMoreRooms.value = false;
  }
};

const loadMore = () => {
  currentPage.value++;
  loadRooms();
};

onMounted(() => {
  loadRooms();
});
</script>

<template>
  <div class="w-full">
    <Title :level="1" class="mb-5">Public Rooms</Title>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <PublicRoomListItem
        v-for="room in roomStore.publicRooms"
        :key="room.id"
        :room="room"
      />
    </div>
    <p v-if="roomStore.publicRooms.length === 0 && !roomStore.isLoading" class="text-center text-gray-500 mt-8">
      No public rooms available at the moment.
    </p>
    <p v-if="roomStore.isLoading" class="flex justify-center items-center mt-8">
      <LoadingSpinner />
    </p>
    <div v-if="hasMoreRooms && !roomStore.isLoading" class="flex justify-center mt-8">
      <Button @click="loadMore">Load More</Button>
    </div>
  </div>
</template>
