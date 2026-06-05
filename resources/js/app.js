import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import VueKonva from 'vue-konva';
import { useAuthStore } from './stores/auth';

// Configure axios
axios.defaults.baseURL = window.location.origin;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Crear instancia de Pinia
const pinia = createPinia();

// Crear la aplicación Vue
const app = createApp(App);

// Usar plugins
app.use(pinia);
app.use(router);
app.use(vuetify);
app.use(VueKonva);

// Make axios available globally
app.config.globalProperties.$axios = axios;

// Initialize authentication
const authStore = useAuthStore();
authStore.initAuth();

// Montar la aplicación en el elemento con id "app"
app.mount('#app');
