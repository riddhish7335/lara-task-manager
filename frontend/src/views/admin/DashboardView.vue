<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/axios'

const employees = ref([])
const form = ref({ name: '', email: '', password: '' })
const error = ref('')
const loading = ref(false)

const editingId = ref(null)
const editForm = ref({ name: '', email: '', password: '' })
const editError = ref('')

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

function startEdit(employee) {
  editingId.value = employee.id
  editError.value = ''
  editForm.value = { name: employee.name, email: employee.email, password: '' }
}

function cancelEdit() {
  editingId.value = null
}

async function saveEdit(employee) {
  editError.value = ''
  try {
    const payload = { name: editForm.value.name, email: editForm.value.email }
    if (editForm.value.password) {
      payload.password = editForm.value.password
    }
    await api.put(`/employees/${employee.id}`, payload)
    editingId.value = null
    await loadEmployees()
  } catch (e) {
    editError.value = e.response?.data?.message || 'Could not update employee.'
  }
}

async function deleteEmployee(employee) {
  if (!confirm(`Delete ${employee.name}? Their assigned tasks will be deleted too.`)) {
    return
  }
  await api.delete(`/employees/${employee.id}`)
  await loadEmployees()
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

    <div v-if="editError" class="alert alert-danger py-2">{{ editError }}</div>

    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>New Password</th>
          <th style="width: 160px"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.id">
          <template v-if="editingId === employee.id">
            <td><input v-model="editForm.name" type="text" class="form-control form-control-sm" /></td>
            <td><input v-model="editForm.email" type="email" class="form-control form-control-sm" /></td>
            <td>
              <input
                v-model="editForm.password"
                type="password"
                class="form-control form-control-sm"
                placeholder="Leave blank to keep"
              />
            </td>
            <td>
              <button class="btn btn-sm btn-success me-1" @click="saveEdit(employee)">Save</button>
              <button class="btn btn-sm btn-secondary" @click="cancelEdit">Cancel</button>
            </td>
          </template>
          <template v-else>
            <td class="text-capitalize">{{ employee.name }}</td>
            <td>{{ employee.email }}</td>
            <td class="text-muted">-</td>
            <td>
              <button class="btn btn-sm btn-outline-primary me-1" @click="startEdit(employee)">Edit</button>
              <button class="btn btn-sm btn-outline-danger" @click="deleteEmployee(employee)">Delete</button>
            </td>
          </template>
        </tr>
        <tr v-if="employees.length === 0">
          <td colspan="4" class="text-center text-muted">No employees yet.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
