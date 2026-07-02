import { reactive } from 'vue'

const authState = reactive({
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  token: localStorage.getItem('token') || null,
})

function setAuth(user, token) {
  authState.user = user
  authState.token = token
  localStorage.setItem('user', JSON.stringify(user))
  localStorage.setItem('token', token)
}

function clearAuth() {
  authState.user = null
  authState.token = null
  localStorage.removeItem('user')
  localStorage.removeItem('token')
}

export { authState, setAuth, clearAuth }
