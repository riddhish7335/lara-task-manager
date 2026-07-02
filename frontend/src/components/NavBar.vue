<script setup>
import { useRouter } from 'vue-router'
import { authState, clearAuth } from '../store/auth'
import api from '../api/axios'

const router = useRouter()

async function logout() {
  try {
    await api.post('/logout')
  } catch {
    // ignore errors, log out locally regardless
  }
  clearAuth()
  router.push('/login')
}
</script>

<template>
  <nav v-if="authState.user" class="navbar navbar-expand navbar-dark bg-dark mb-4">
    <div class="container">
      <span class="navbar-brand">Task Manager</span>
      <div class="navbar-nav me-auto">
        <template v-if="authState.user.role === 'admin'">
          <router-link class="nav-link" to="/admin/tasks">Tasks</router-link>
          <router-link class="nav-link" to="/admin/employees">Employees</router-link>
        </template>
        <template v-else>
          <router-link class="nav-link" to="/my-tasks">My Tasks</router-link>
        </template>
      </div>
      <span class="navbar-text text-light me-3">{{ authState.user.name }} ({{ authState.user.role }})</span>
      <button class="btn btn-outline-light btn-sm" @click="logout">Logout</button>
    </div>
  </nav>
</template>
