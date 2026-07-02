<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/axios'

const employees = ref([])
const form = ref({ name: '', email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function loadEmployees() {
  const { data } = await api.get('/employees')
  employees.value = data
}

async function createEmployee() {
  error.value = ''
  loading.value = true
  try {
    await api.post('/employees', form.value)
    form.value = { name: '', email: '', password: '' }
    await loadEmployees()
  } catch (e) {
    error.value = e.response?.data?.message || 'Could not create employee.'
  } finally {
    loading.value = false
  }
}

onMounted(loadEmployees)
</script>

<template>
  <div class="container">
    <h3 class="mb-3">Employees</h3>

    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Create Employee</h5>
        <form class="row g-2 align-items-end" @submit.prevent="createEmployee">
          <div class="col-md-3">
            <label class="form-label">Name</label>
            <input v-model="form.name" type="text" class="form-control" required />
          </div>
          <div class="col-md-3">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-control" required />
          </div>
          <div class="col-md-3">
            <label class="form-label">Password</label>
            <input v-model="form.password" type="password" class="form-control" required />
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100" :disabled="loading">Create</button>
          </div>
        </form>
        <div v-if="error" class="alert alert-danger py-2 mt-3">{{ error }}</div>
      </div>
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.id">
          <td class="text-capitalize">{{ employee.name }}</td>
          <td>{{ employee.email }}</td>
        </tr>
        <tr v-if="employees.length === 0">
          <td colspan="2" class="text-center text-muted">No employees yet.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
