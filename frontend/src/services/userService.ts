import axios from '@/plugins/axios';

export async function login(params: {email: string, password: string}): Promise<{code: string, message?: string, tempToken?: string, error?: string}> {
  try {
    const response = await axios.post('/login', params, {withCredentials: true});
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function logout(): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post('/user/logout');
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function register(params: {email: string, password: string, username?: string}): Promise<{code: string, message?: string, error?: string}> {
  try {
    const response = await axios.post('/register', params);
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function loginVerify(params: {totpCode: string, tempToken: string}): Promise<{code: string, tempToken?: string, error?: string}> {
  try {
    const response = await axios.post('/login-verify', params, {withCredentials: true});
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function generateTotpSecret(): Promise<{code: string, TOTPSecret?: string, error?: string}> {
  try {
    const response = await axios.get('/user/TOTP/secret', {withCredentials: true});
    return response.data;
  } catch (error) {
    throw error;
  }
}
