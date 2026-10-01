<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '../api/client'
import { setAuth } from '../composables/useAuth'

const router = useRouter()
const route = useRoute()

const email = ref('test@example.com')
const password = ref('password')
const loading = ref(false)
const error = ref('')

const submit = async () => {
  loading.value = true
  error.value = ''

  try {
    const data = await api.login({
      email: email.value,
      password: password.value,
    })
    setAuth(data.token, data.user)
    router.replace(route.query.redirect || { name: 'products' })
  } catch (err) {
    error.value = err.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <section class="panel login-card stack">
      <div class="hero">
        <h1>PIMS</h1>
        <p>Sign in to browse inventory products, categories, and suppliers.</p>
      </div>

      <form class="stack" @submit.prevent="submit">
        <label>
          Email
          <input v-model="email" type="email" autocomplete="username" required />
        </label>
        <label>
          Password
          <input v-model="password" type="password" autocomplete="current-password" required />
        </label>

        <p v-if="error" class="error">{{ error }}</p>

        <button class="btn" type="submit" :disabled="loading">
          {{ loading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>

      <p class="muted">Demo: test@example.com / password</p>
    </section>
  </div>
</template>
