<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { api } from '../api/client'

const loading = ref(false)
const error = ref('')
const products = ref([])
const meta = ref(null)

const filters = reactive({
  category_id: '',
  min_price: '',
  max_price: '',
  stock: '',
  per_page: 10,
  page: 1,
})

const stockLabel = (stock) => {
  if (stock <= 0) return { text: 'Out', className: 'out' }
  if (stock <= 10) return { text: 'Low', className: 'low' }
  return { text: 'In stock', className: '' }
}

const pageLabel = computed(() => {
  if (!meta.value) return ''
  return `Page ${meta.value.current_page} of ${meta.value.last_page} · ${meta.value.total} items`
})

const loadProducts = async () => {
  loading.value = true
  error.value = ''

  try {
    const data = await api.getProducts({ ...filters })
    products.value = data.data || []
    meta.value = data.meta || null
  } catch (err) {
    error.value = err.message || 'Failed to load products'
    products.value = []
    meta.value = null
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  filters.page = 1
  loadProducts()
}

const resetFilters = () => {
  filters.category_id = ''
  filters.min_price = ''
  filters.max_price = ''
  filters.stock = ''
  filters.per_page = 10
  filters.page = 1
  loadProducts()
}

const prevPage = () => {
  if (meta.value?.current_page > 1) {
    filters.page -= 1
    loadProducts()
  }
}

const nextPage = () => {
  if (meta.value && meta.value.current_page < meta.value.last_page) {
    filters.page += 1
    loadProducts()
  }
}

watch(
  () => filters.per_page,
  () => {
    filters.page = 1
    loadProducts()
  },
)

onMounted(loadProducts)
</script>

<template>
  <section class="stack">
    <div class="hero">
      <h1>Products</h1>
      <p>Browse seeded inventory data from the Laravel API.</p>
    </div>

    <div class="panel stack">
      <div class="grid-filters">
        <label>
          Category ID
          <input v-model="filters.category_id" type="number" min="1" placeholder="e.g. 1" />
        </label>
        <label>
          Min price
          <input v-model="filters.min_price" type="number" min="0" step="0.01" />
        </label>
        <label>
          Max price
          <input v-model="filters.max_price" type="number" min="0" step="0.01" />
        </label>
        <label>
          Stock
          <select v-model="filters.stock">
            <option value="">All</option>
            <option value="in_stock">In stock (&gt;10)</option>
            <option value="low">Low (1–10)</option>
            <option value="out">Out</option>
          </select>
        </label>
        <label>
          Per page
          <select v-model.number="filters.per_page">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="15">15</option>
          </select>
        </label>
      </div>

      <div class="actions">
        <button class="btn" type="button" :disabled="loading" @click="applyFilters">
          Apply filters
        </button>
        <button class="btn ghost" type="button" :disabled="loading" @click="resetFilters">
          Reset
        </button>
      </div>

      <p v-if="error" class="error">{{ error }}</p>
      <p v-else-if="loading" class="muted">Loading products...</p>

      <div v-else class="table-wrap">
        <table v-if="products.length">
          <thead>
            <tr>
              <th>SKU</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Suppliers</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>
                <RouterLink :to="{ name: 'product-detail', params: { id: product.id } }">
                  {{ product.sku }}
                </RouterLink>
              </td>
              <td>{{ product.name }}</td>
              <td>{{ product.category?.name || '—' }}</td>
              <td>${{ product.formatted_price }}</td>
              <td>
                <span class="badge" :class="stockLabel(product.stock).className">
                  {{ product.stock }} · {{ stockLabel(product.stock).text }}
                </span>
              </td>
              <td>
                <div class="suppliers" v-if="product.suppliers?.length">
                  <span
                    v-for="supplier in product.suppliers"
                    :key="supplier.id"
                    class="chip"
                  >
                    {{ supplier.name }}
                  </span>
                </div>
                <span v-else class="muted">—</span>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="empty">No products match these filters.</div>
      </div>

      <div class="pager" v-if="meta">
        <span class="muted">{{ pageLabel }}</span>
        <div class="actions">
          <button class="btn ghost" type="button" :disabled="meta.current_page <= 1 || loading" @click="prevPage">
            Previous
          </button>
          <button
            class="btn ghost"
            type="button"
            :disabled="meta.current_page >= meta.last_page || loading"
            @click="nextPage"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
