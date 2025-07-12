import axios from 'axios';

function createAxiosInstance(baseUrl?: string, token?: string) {
  return axios.create({
    baseURL: baseUrl,
    headers: {
      Authorization: token ? `Bearer ${token}` : '',
    },
    withCredentials: true,
  })
}

const axiosInstance = createAxiosInstance(import.meta.env.VITE_PUBLIC_API_URL);

export default axiosInstance;
