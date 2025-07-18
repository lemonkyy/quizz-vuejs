<template>
  <div class="flex items-center">
    <div 
      v-for="(player, index) in players" 
      :key="player.id"
      class="relative"
:style="{ 
        marginLeft: index > 0 ? '-20px' : '0',
        zIndex: players.length - index
      }"
    >
      <UserIcon 
        :src="getUserProfilePictureUrl(player)"
        :size="2.75"
        class="border-2 border-white bg-white"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import UserIcon from '@/components/profile/UserIcon.vue';
import axios from '@/api/axios';

interface Player {
  id: string;
  player: {
    id: string;
    username: string;
    profilePicture?: {
      id: string;
      fileName: string;
    };
  };
}

const props = defineProps<{
  players: Player[];
}>();

const profilePictures = ref<Map<string, { fileName: string }>>(new Map());

const fetchProfilePictures = async () => {
  try {
    const response = await axios.get('/profile-pictures');
    const pictures = response.data?.member || response.data || [];
    
    const pictureMap = new Map();
    pictures.forEach((pic: any) => {
      pictureMap.set(pic.id, { fileName: pic.fileName });
    });
    
    profilePictures.value = pictureMap;
  } catch (error) {
    console.error('Error fetching profile pictures:', error);
  }
};

watchEffect(() => {
  if (props.players && props.players.length > 0) {
    fetchProfilePictures();
  }
});


const getUserProfilePictureUrl = (player: Player) => {
  const profilePictureId = player?.player?.profilePicture?.id;
  
  if (profilePictureId && profilePictures.value.has(profilePictureId)) {
    const picture = profilePictures.value.get(profilePictureId);
    if (picture?.fileName) {
      const url = `${import.meta.env.VITE_PUBLIC_PFP_URL}/${picture.fileName}`;
      return url;
    }
  }
  
  return '';
};
</script>