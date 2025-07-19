import type { ProfilePicture } from "@/types";
import axios from "@/api/axios";

export async function getAllProfilePictures(): Promise<{code: string, profilePictures?: ProfilePicture[]}> {
  try {
    const response = await axios.get('/profile-pictures');
    return {
      code: 'SUCCESS',
      profilePictures: response.data.member
    };
  } catch (error) {
    throw error;
  }
}
