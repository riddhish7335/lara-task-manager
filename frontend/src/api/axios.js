import axios from 'axios'
import { authState, clearAuth } from '../store/auth'
import router from '../router'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
})

api.interceptors.request.use((config) => {
  if (authState.token) {
    config.headers.Authorization = `Bearer ${authState.token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      clearAuth()
      router.push('/login')
    }
    return Promise.reject(error)
  }
)

export default api
