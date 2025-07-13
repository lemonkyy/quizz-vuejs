<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import { useProfilePictureStore } from '@/store/profilePicture';
import Image from '@/components/ui/atoms/Image.vue';
import PrevButton from '@/components/ui/molecules/buttons/PrevButton.vue';
import NextButton from '@/components/ui/molecules/buttons/NextButton.vue';
import LoadingSpinner from '@/components/ui/atoms/LoadingSpinner.vue';

const modelValue = defineModel<string | undefined>();

const profilePictureStore = useProfilePictureStore();

const currentProfilePictureIndex = ref(0);
const isLoading = ref(true);

const setIndexFromId = (id: string | undefined) => {
  if (id && profilePictureStore.profilePictures.length > 0) {
    const index = profilePictureStore.profilePictures.findIndex(
      (pp) => pp.id === id
    );
    if (index !== -1) {
      currentProfilePictureIndex.value = index;
    }
  }
};

onMounted(async () => {
  isLoading.value = true;
  await profilePictureStore.fetchProfilePictures();
  setIndexFromId(modelValue.value);
  isLoading.value = false;
});

//load image of current pfp shown
const currentProfilePictureUrl = computed(() => {
  if (profilePictureStore.profilePictures.length > 0 && !isLoading.value) {
    const fileName = profilePictureStore.profilePictures[currentProfilePictureIndex.value].fileName;
    return import.meta.env.VITE_PUBLIC_PFP_URL + '/' + fileName;
  }
  return '';
});

//get id of the current pfp
watch(currentProfilePictureIndex, (newIndex) => {
  modelValue.value = profilePictureStore.profilePictures[newIndex].id;
  console.log(modelValue.value);
});

watch(modelValue, (newId) => {
    setIndexFromId(newId);
});

const nextPicture = () => {
  currentProfilePictureIndex.value = (currentProfilePictureIndex.value + 1) % profilePictureStore.profilePictures.length;
};

const prevPicture = () => {
  currentProfilePictureIndex.value = (currentProfilePictureIndex.value - 1 + profilePictureStore.profilePictures.length) % profilePictureStore.profilePictures.length;
};
</script>

<template>
  <div class="flex items-center justify-center gap-4 h-[16rem]">
    <LoadingSpinner v-if="isLoading" />
    <template v-else>
      <PrevButton @click="prevPicture" :disabled="profilePictureStore.profilePictures.length <= 1" />
      <Image :src="currentProfilePictureUrl" alt="Profile Picture" rounded="sm" :width="16" :height="16" />
      <NextButton @click="nextPicture" :disabled="profilePictureStore.profilePictures.length <= 1" />
    </template>
  </div>
</template>
