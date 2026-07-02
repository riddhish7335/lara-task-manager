<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/axios'
import { formatDate } from '../../utils/date'

const tasks = ref([])
const employees = ref([])
const form = ref({ title: '', description: '', due_date: '', assigned_to: '' })
const error = ref('')
const loading = ref(false)

async function loadTasks() {
  const { data } = await api.get('/tasks')
  tasks.value = data
}

async function loadEmployees() {
  const { data } = await api.get('/employees')
  employees.value = data
}

async function createTask() {
  error.value = ''
  loading.value = true
  try {
    await api.post('/tasks', form.value)
    form.value = { title: '', description: '', due_date: '', assigned_to: '' }
    await loadTasks()
  } catch (e) {
    error.value = e.response?.data?.message || 'Could not create task.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadTasks()
  loadEmployees()
})
</script>

<template>
  <div class="container">
    <h3 class="mb-3">All Tasks</h3>

    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Create Task</h5>
        <form class="row g-2 align-items-end" @submit.prevent="createTask">
          <div class="col-md-3">
            <label class="form-label">Title</label>
            <input v-model="form.title" type="text" class="form-control" required />
          </div>
          <div class="col-md-3">
            <label class="form-label">Description</label>
            <input v-model="form.description" type="text" class="form-control" />
          </div>
          <div class="col-md-2">
            <label class="form-label">Due Date</label>
            <input v-model="form.due_date" type="date" class="form-control" />
          </div>
          <div class="col-md-2">
            <label class="form-label">Assign To</label>
            <select v-model="form.assigned_to" class="form-select" required>
              <option value="" disabled>Select employee</option>
              <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                {{ employee.name }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100" :disabled="loading">Create</button>
          </div>
        </form>
        <div v-if="error" class="alert alert-danger py-2 mt-3">{{ error }}</div>
      </div>
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Assigned To</th>
          <th>Due Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id">
          <td>{{ task.title }}</td>
          <td>{{ task.description }}</td>
          <td class="text-capitalize">{{ task.assignee?.name }}</td>
          <td>{{ formatDate(task.due_date) }}</td>
          <td>
            <span class="badge text-capitalize" :class="task.status === 'completed' ? 'bg-success' : 'bg-secondary'">
              {{ task.status }}
            </span>
          </td>
        </tr>
        <tr v-if="tasks.length === 0">
          <td colspan="5" class="text-center text-muted">No tasks yet.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
