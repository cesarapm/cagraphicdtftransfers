import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Importar las páginas/vistas
import Home from '../pages/Home.vue';
import About from '../pages/About.vue';
import Contact from '../pages/Contact.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import GangSheetBuilder from '../pages/GangSheetBuilder.vue';
import PaymentSuccess from '../pages/PaymentSuccess.vue';
import PaymentPending from '../pages/PaymentPending.vue';
import PaymentFailure from '../pages/PaymentFailure.vue';
import CustomerOrders from '../pages/CustomerOrders.vue';
import OrderTracking from '../pages/OrderTracking.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/gang-sheet-builder',
    name: 'GangSheetBuilder',
    component: GangSheetBuilder,
    meta: { requiresAuth: true }
  },
  {
    path: '/nosotros',
    name: 'About',
    component: About
  },
  {
    path: '/contacto',
    name: 'Contact',
    component: Contact
  },
  {
    path: '/checkout/exito',
    name: 'PaymentSuccess',
    component: PaymentSuccess,
    meta: { requiresAuth: true }
  },
  {
    path: '/checkout/pendiente',
    name: 'PaymentPending',
    component: PaymentPending,
    meta: { requiresAuth: true }
  },
  {
    path: '/checkout/error',
    name: 'PaymentFailure',
    component: PaymentFailure,
    meta: { requiresAuth: true }
  },
  {
    path: '/mis-pedidos',
    name: 'CustomerOrders',
    component: CustomerOrders,
    meta: { requiresAuth: true }
  },
  {
    path: '/seguimiento-pedido/:orderNumber',
    name: 'OrderTracking',
    component: OrderTracking,
    props: true,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Si hay una posición guardada (navegación hacia atrás/adelante)
    if (savedPosition) {
      return savedPosition;
    }
    // Siempre volver al inicio en navegación nueva
    return { top: 0, behavior: 'smooth' };
  }
});

// Navigation Guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Initialize auth if not done yet
  if (!authStore.customer && authStore.token) {
    await authStore.fetchCustomer();
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const isGuest = to.matched.some(record => record.meta.guest);
  
  if (requiresAuth && !authStore.isAuthenticated) {
    // Redirect to login with intended destination
    next({
      name: 'Login',
      query: { redirect: to.fullPath }
    });
  } else if (isGuest && authStore.isAuthenticated) {
    // Already logged in, redirect to gang sheet builder
    next({ name: 'GangSheetBuilder' });
  } else {
    next();
  }
});

export default router;
