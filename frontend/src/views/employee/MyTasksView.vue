<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/axios'
import { formatDate } from '../../utils/date'

const tasks = ref([])

async function loadTasks() {
  const { data } = await api.get('/tasks')
  tasks.value = data
}

async function markComplete(task) {
  await api.patch(`/tasks/${task.id}`, { status: 'completed' })
  await loadTasks()
}

onMounted(loadTasks)
</script>

<template>
  <div class="container">
    <h3 class="mb-3">My Tasks</h3>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Due Date</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id">
          <td>{{ task.title }}</td>
          <td>{{ task.description }}</td>
          <td>{{ formatDate(task.due_date) }}</td>
          <td>
            <span class="badge text-capitalize" :class="task.status === 'completed' ? 'bg-success' : 'bg-secondary'">
              {{ task.status }}
            </span>
          </td>
          <td>
            <button
              v-if="task.status !== 'completed'"
              class="btn btn-sm btn-outline-success"
              @click="markComplete(task)"
            >
              Mark Complete
            </button>
          </td>
        </tr>
        <tr v-if="tasks.length === 0">
          <td colspan="5" class="text-center text-muted">No tasks assigned to you.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
