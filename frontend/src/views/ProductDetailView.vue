<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { api } from '../api/client'

const route = useRoute()
const loading = ref(true)
const error = ref('')
const product = ref(null)

const stockLabel = computed(() => {
  const stock = product.value?.stock ?? 0
  if (stock <= 0) return { text: 'Out of stock', className: 'out' }
  if (stock <= 10) return { text: 'Low stock', className: 'low' }
  return { text: 'In stock', className: '' }
})

onMounted(async () => {
  try {
    const data = await api.getProduct(route.params.id)
    product.value = data.data
  } catch (err) {
    error.value = err.message || 'Failed to load product'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <section class="stack">
    <div class="actions">
      <RouterLink class="btn ghost" :to="{ name: 'products' }">← Back to products</RouterLink>
    </div>

    <p v-if="loading" class="muted">Loading product...</p>
    <p v-else-if="error" class="error">{{ error }}</p>

    <article v-else-if="product" class="panel stack">
      <div class="hero">
        <h1>{{ product.name }}</h1>
        <p>{{ product.sku }} · ${{ product.formatted_price }}</p>
      </div>

      <p>{{ product.description || 'No description provided.' }}</p>

      <div class="grid-filters">
        <div>
          <p class="muted">Category</p>
          <strong>{{ product.category?.name || '—' }}</strong>
        </div>
        <div>
          <p class="muted">Stock</p>
          <span class="badge" :class="stockLabel.className">
            {{ product.stock }} · {{ stockLabel.text }}
          </span>
        </div>
        <div>
          <p class="muted">Price</p>
          <strong>${{ product.formatted_price }}</strong>
        </div>
      </div>

      <div>
        <p class="muted">Suppliers</p>
        <div class="suppliers" v-if="product.suppliers?.length">
          <span v-for="supplier in product.suppliers" :key="supplier.id" class="chip">
            {{ supplier.name }} ({{ supplier.email }})
          </span>
        </div>
        <p v-else class="muted">No suppliers linked.</p>
      </div>
    </article>
  </section>
</template>
