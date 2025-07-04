import axios from '@/plugins/axios';

interface LoginParams {
  email: string;
  password: string;
}

export async function login(params: LoginParams): Promise<{token: string}> {
  try {
    const response = await axios.post('/login', params)
    return response.data
  } catch (error) {
    throw error
  }
}

interface LogoutParams {
  token: string;
}

export async function logout(params: LogoutParams): Promise<{response: string}> {
  try {
    const response = await axios.post('/logout', params)
    return response.data
  } catch (error) {
    throw error
  }
}

interface RegisterParams {
  email: string;
  password: string;
  passwordConfirmation: string;
}

export async function register(params: RegisterParams): Promise <{message: string}> {
  try {
    const response = await axios.post('/register', params);
      return response.data;
  } catch (error) {
      throw error;
  }
}
