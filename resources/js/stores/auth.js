import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    customer: null,
    token: localStorage.getItem('auth_token') || null,
    loading: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.customer,
    currentCustomer: (state) => state.customer,
    customerName: (state) => state.customer ? `${state.customer.first_name} ${state.customer.last_name}` : '',
  },

  actions: {
    async login(credentials) {
      this.loading = true;
      try {
        // Get CSRF cookie first
        await axios.get('/sanctum/csrf-cookie');
        
        // Login
        const response = await axios.post('/login', credentials);
        
        if (response.data.token) {
          this.token = response.data.token;
          this.customer = response.data.customer;
          localStorage.setItem('auth_token', this.token);
          
          // Set default authorization header
          axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        }
        
        return response.data;
      } catch (error) {
        this.token = null;
        this.customer = null;
        localStorage.removeItem('auth_token');
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async register(customerData) {
      this.loading = true;
      try {
        // Get CSRF cookie first
        await axios.get('/sanctum/csrf-cookie');
        
        // Register
        const response = await axios.post('/register', customerData);
        
        if (response.data.token) {
          this.token = response.data.token;
          this.customer = response.data.customer;
          localStorage.setItem('auth_token', this.token);
          
          // Set default authorization header
          axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        }
        
        return response.data;
      } catch (error) {
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      try {
        await axios.post('/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.token = null;
        this.customer = null;
        localStorage.removeItem('auth_token');
        delete axios.defaults.headers.common['Authorization'];
        this.loading = false;
      }
    },

    async fetchCustomer() {
      if (!this.token) return;
      
      this.loading = true;
      try {
        const response = await axios.get('/api/customer');
        this.customer = response.data;
      } catch (error) {
        // Token might be invalid
        this.logout();
      } finally {
        this.loading = false;
      }
    },

    initAuth() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        this.fetchCustomer();
      }
    }
  },
});
