import type { ProfilePicture } from "@/types";
import axios from "@/plugins/axios";

export async function getAllProfilePictures(): Promise<{code: string, profilePictures?: ProfilePicture[], error?: string}> {
  try {
    const response = await axios.get('/profile-pictures');
    return response.data;
  } catch (error) {
    throw error;
  }
}
