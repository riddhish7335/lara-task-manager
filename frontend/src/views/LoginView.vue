<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api/axios'
import { setAuth } from '../store/auth'

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const router = useRouter()

async function submit() {
  error.value = ''
  loading.value = true
  try {
    const { data } = await api.post('/login', { email: email.value, password: password.value })
    setAuth(data.user, data.token)
    router.push(data.user.role === 'admin' ? '/admin/tasks' : '/my-tasks')
  } catch (e) {
    error.value = e.response?.data?.message || 'Login failed.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="container" style="max-width: 400px; margin-top: 5rem;">
    <h2 class="mb-4 text-center">Task Manager Login</h2>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input v-model="email" type="email" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input v-model="password" type="password" class="form-control" required />
      </div>
      <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
      <button type="submit" class="btn btn-primary w-100" :disabled="loading">
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>
    </form>
  </div>
</template>
