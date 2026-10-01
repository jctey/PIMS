import { createRouter, createWebHistory } from 'vue-router'
import { isAuthenticated } from '../composables/useAuth'
import LoginView from '../views/LoginView.vue'
import ProductsView from '../views/ProductsView.vue'
import ProductDetailView from '../views/ProductDetailView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { guest: true },
    },
    {
      path: '/',
      name: 'products',
      component: ProductsView,
      meta: { requiresAuth: true },
    },
    {
      path: '/products/:id',
      name: 'product-detail',
      component: ProductDetailView,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach((to) => {
  const authed = isAuthenticated()

  if (to.meta.requiresAuth && !authed) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.guest && authed) {
    return { name: 'products' }
  }

  return true
})

export default router
