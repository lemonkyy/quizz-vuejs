import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { ProfilePicture } from '@/types';
import { getAllProfilePictures as listProfilePicturesService } from '@/services/profilePictureService';

export const useProfilePictureStore = defineStore("profilePicture", () => {
  const profilePictures = ref<ProfilePicture[]>([]);

  const fetchProfilePictures = async () => {
    try {
      const response = await listProfilePicturesService();
      if (response.code === 'SUCCESS' && response.profilePictures) {
        profilePictures.value = response.profilePictures;
      }
    } catch (error) {
      throw error;
    }
  };

  return {
    profilePictures,
    fetchProfilePictures,
  };
});
