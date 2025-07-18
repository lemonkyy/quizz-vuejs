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

axiosInstance.interceptors.request.use(config => {
  const token = localStorage.getItem('token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

export default axiosInstance;
