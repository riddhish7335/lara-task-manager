import { createRouter, createWebHistory } from 'vue-router'
import { authState } from '../store/auth'
import LoginView from '../views/LoginView.vue'
import AdminDashboardView from '../views/admin/DashboardView.vue'
import AdminTasksView from '../views/admin/TasksView.vue'
import MyTasksView from '../views/employee/MyTasksView.vue'

const routes = [
  { path: '/login', name: 'login', component: LoginView },
  {
    path: '/admin/employees',
    name: 'admin.employees',
    component: AdminDashboardView,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/tasks',
    name: 'admin.tasks',
    component: AdminTasksView,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/my-tasks',
    name: 'my-tasks',
    component: MyTasksView,
    meta: { requiresAuth: true, role: 'employee' },
  },
  { path: '/', redirect: () => (authState.user?.role === 'admin' ? '/admin/tasks' : '/my-tasks') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  if (to.meta.requiresAuth && !authState.token) {
    return { name: 'login' }
  }

  if (to.name === 'login' && authState.token) {
    return authState.user?.role === 'admin' ? { name: 'admin.tasks' } : { name: 'my-tasks' }
  }

  if (to.meta.role && authState.user?.role !== to.meta.role) {
    return authState.user?.role === 'admin' ? { name: 'admin.tasks' } : { name: 'my-tasks' }
  }

  return true
})

export default router
