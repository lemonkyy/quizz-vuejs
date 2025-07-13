<script setup lang="ts">
import Button from '@/components/ui/atoms/Button.vue';
import UserIcon from '@/components/profile/UserIcon.vue';
import type { PublicUser } from '@/types';
import { ref, type PropType } from 'vue';

const props = defineProps({
  friend: { type: Object as PropType<PublicUser>, required: true },
});

const profilePictureUrl = import.meta.env.VITE_PUBLIC_PFP_URL + '/' + props.friend.profilePicture.fileName;

const emit = defineEmits(['remove-friend']);

const removeConfirmed = ref(false);

const handleRemoveFriend = () => {
  if (removeConfirmed.value) {
    emit('remove-friend', props.friend.id);
  } else {
    removeConfirmed.value = true;
  }
};
</script>

<template>
  <div
    class="flex items-center justify-between p-3 bg-background-alt rounded-lg shadow"
  >
    <div class="flex items-center gap-3">
      <UserIcon :src="profilePictureUrl" />
      <span class="font-semibold">{{ friend.username }}</span>
    </div>
    <Button
      @click="handleRemoveFriend()"
      theme="monochrome"
      rounded="md"
    >
      {{ removeConfirmed ? 'Are you sure?' : 'Remove' }}
    </Button>
  </div>
</template>
