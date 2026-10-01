<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { computed } from 'vue'
import { clearAuth, getToken, getUser } from './composables/useAuth'

const router = useRouter()
const token = computed(() => getToken())
const user = computed(() => getUser())

const logout = async () => {
  try {
    if (token.value) {
      await fetch('/api/logout', {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: 'application/json',
        },
      })
    }
  } finally {
    clearAuth()
    router.push({ name: 'login' })
  }
}
</script>

<template>
  <div class="app-shell">
    <header class="topbar" v-if="token">
      <div class="brand">
        <RouterLink to="/">PIMS</RouterLink>
      </div>
      <div class="topbar-meta">
        <span>{{ user?.name || user?.email }}</span>
        <button class="btn ghost" type="button" @click="logout">Logout</button>
      </div>
    </header>
    <main class="page">
      <RouterView />
    </main>
  </div>
</template>
