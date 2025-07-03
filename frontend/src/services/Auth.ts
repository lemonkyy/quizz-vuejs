import axios from '@/plugins/axios'
import Button from '@/components/ui/Button.vue';

interface LoginParams {
  email: string;
  password: string;
}

interface LoginResponse {
  token: string;
}

export async function login(params: LoginParams): Promise<LoginResponse> {
  try {
    const response = await axios.post('/login', params)
    return response.data
  } catch (error) {
    throw error
  }
}
