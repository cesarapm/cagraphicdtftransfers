<template>
  <div class="account-page">
    <div class="container">
      <!-- Header -->
      <div class="account-header">
        <h1>My Account</h1>
        <p>Manage your account information</p>
      </div>

      <!-- Alert Messages -->
      <div v-if="successMessage" class="alert alert-success">
        <span class="alert-icon">✅</span>
        {{ successMessage }}
        <button @click="successMessage = ''" class="alert-close">×</button>
      </div>

      <div v-if="errorMessage" class="alert alert-error">
        <span class="alert-icon">❌</span>
        {{ errorMessage }}
        <button @click="errorMessage = ''" class="alert-close">×</button>
      </div>

      <!-- Content Grid -->
      <div class="content-grid">
        <!-- Main Form -->
        <div class="form-section">
          <!-- Profile Information -->
          <div class="card">
            <div class="card-header">
              <h2>Profile Information</h2>
              <p class="card-subtitle">Update your personal details</p>
            </div>

            <form @submit.prevent="updateProfile" class="profile-form">
              <!-- Name Row -->
              <div class="form-row">
                <div class="form-group">
                  <label for="firstName" class="form-label">First Name</label>
                  <input
                    v-model="form.first_name"
                    type="text"
                    id="firstName"
                    class="form-input"
                    placeholder="John"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input
                    v-model="form.last_name"
                    type="text"
                    id="lastName"
                    class="form-input"
                    placeholder="Doe"
                    required
                  />
                </div>
              </div>

              <!-- Email & Phone Row -->
              <div class="form-row">
                <div class="form-group">
                  <label for="email" class="form-label">Email Address</label>
                  <input
                    v-model="form.email"
                    type="email"
                    id="email"
                    class="form-input"
                    placeholder="john@example.com"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    id="phone"
                    class="form-input"
                    placeholder="+1 (555) 123-4567"
                  />
                </div>
              </div>

              <!-- Address Row -->
              <div class="form-group full-width">
                <label for="address" class="form-label">Street Address</label>
                <input
                  v-model="form.address"
                  type="text"
                  id="address"
                  class="form-input"
                  placeholder="123 Main Street"
                />
              </div>

              <!-- City, State, Zip Row -->
              <div class="form-row three-cols">
                <div class="form-group">
                  <label for="city" class="form-label">City</label>
                  <input
                    v-model="form.city"
                    type="text"
                    id="city"
                    class="form-input"
                    placeholder="New York"
                  />
                </div>
                <div class="form-group">
                  <label for="state" class="form-label">State</label>
                  <input
                    v-model="form.state"
                    type="text"
                    id="state"
                    class="form-input"
                    placeholder="NY"
                  />
                </div>
                <div class="form-group">
                  <label for="zipCode" class="form-label">Zip Code</label>
                  <input
                    v-model="form.zip_code"
                    type="text"
                    id="zipCode"
                    class="form-input"
                    placeholder="10001"
                  />
                </div>
              </div>

              <!-- Country Row -->
              <div class="form-group full-width">
                <label for="country" class="form-label">Country</label>
                <input
                  v-model="form.country"
                  type="text"
                  id="country"
                  class="form-input"
                  placeholder="United States"
                />
              </div>

              <!-- Form Actions -->
              <div class="form-actions">
                <button
                  type="button"
                  @click="resetForm"
                  class="btn btn-secondary"
                  :disabled="loading"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="loading"
                >
                  <span v-if="!loading">Save Changes</span>
                  <span v-else>Saving...</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Change Password Section -->
          <div class="card">
            <div class="card-header">
              <h2>Security</h2>
              <p class="card-subtitle">Change your password</p>
            </div>

            <form @submit.prevent="changePassword" class="profile-form">
              <div class="form-group full-width">
                <label for="currentPassword" class="form-label">Current Password</label>
                <input
                  v-model="passwordForm.current_password"
                  type="password"
                  id="currentPassword"
                  class="form-input"
                  placeholder="Enter current password"
                  required
                />
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="newPassword" class="form-label">New Password</label>
                  <input
                    v-model="passwordForm.new_password"
                    type="password"
                    id="newPassword"
                    class="form-input"
                    placeholder="Enter new password"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="confirmPassword" class="form-label">Confirm Password</label>
                  <input
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    id="confirmPassword"
                    class="form-input"
                    placeholder="Confirm new password"
                    required
                  />
                </div>
              </div>

              <div class="form-actions">
                <button
                  type="button"
                  @click="resetPasswordForm"
                  class="btn btn-secondary"
                  :disabled="loadingPassword"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="loadingPassword"
                >
                  <span v-if="!loadingPassword">Update Password</span>
                  <span v-else>Updating...</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Sidebar Info -->
        <aside class="sidebar">
          <div class="card">
            <div class="card-header">
              <h3>Account Overview</h3>
            </div>
            <div class="sidebar-content">
              <div class="info-item">
                <span class="info-label">Member Since:</span>
                <span class="info-value">{{ memberSince }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Last Order:</span>
                <span class="info-value">{{ lastOrderDate }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Account Status:</span>
                <span class="info-value status-active">Active</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3>Quick Links</h3>
            </div>
            <div class="sidebar-content">
              <router-link to="/cart" class="quick-link">
                <span class="icon">🛒</span> Shopping Cart
              </router-link>
              <!-- <router-link to="/orders" class="quick-link">
                <span class="icon">📦</span> My Orders
              </router-link> -->
              <a href="#" @click.prevent="logout" class="quick-link danger">
                <span class="icon">🚪</span> Logout
              </a>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// State
const initialLoading = ref(true);
const loading = ref(false);
const loadingPassword = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const memberSince = ref('');
const lastOrderDate = ref('Not yet');

// Form Data
const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zip_code: '',
  country: ''
});

const originalForm = ref({ ...form.value });

// Password Form
const passwordForm = ref({
  current_password: '',
  new_password: '',
  password_confirmation: ''
});

// Get auth token
const getAuthToken = () => {
  return localStorage.getItem('auth_token') ||
         localStorage.getItem('token') ||
         localStorage.getItem('sanctum_token') || '';
};

// Load customer data
const loadCustomerData = async () => {
  try {
    initialLoading.value = true;
    const token = getAuthToken();

    if (!token) {
      errorMessage.value = 'No authentication token found. Please log in.';
      setTimeout(() => router.push({ name: 'Login' }), 2000);
      return;
    }

    const headers = {
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`
    };

    const response = await axios.get('/api/customers/profile', { headers });
    const customer = response.data.data || response.data;

    // Load form data
    form.value = {
      first_name: customer.first_name || '',
      last_name: customer.last_name || '',
      email: customer.email || '',
      phone: customer.phone || '',
      address: customer.address || '',
      city: customer.city || '',
      state: customer.state || '',
      zip_code: customer.zip_code || '',
      country: customer.country || ''
    };

    originalForm.value = { ...form.value };

    // Format dates
    if (customer.created_at) {
      const date = new Date(customer.created_at);
      memberSince.value = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }

    if (customer.last_ordered_at) {
      const date = new Date(customer.last_ordered_at);
      lastOrderDate.value = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }

    // console.log('✅ Customer data loaded:', customer);
  } catch (error) {
    console.error('❌ Error loading customer data:', error);
    if (error.response?.status === 401) {
      errorMessage.value = 'Session expired. Please log in again.';
      localStorage.removeItem('auth_token');
      localStorage.removeItem('token');
      localStorage.removeItem('sanctum_token');
      setTimeout(() => router.push({ name: 'Login' }), 2000);
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to load account information.';
    }
  } finally {
    initialLoading.value = false;
  }
};

// Update Profile
const updateProfile = async () => {
  try {
    loading.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    const token = getAuthToken();

    if (!token) {
      errorMessage.value = 'Authentication required.';
      return;
    }

    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    };

    const response = await axios.put('/api/customers/profile', form.value, { headers });

    if (response.data.success) {
      successMessage.value = 'Profile updated successfully! ✅';
      originalForm.value = { ...form.value };
      // console.log('✅ Profile updated:', response.data.data);

      setTimeout(() => {
        successMessage.value = '';
      }, 4000);
    }
  } catch (error) {
    console.error('❌ Error updating profile:', error);
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      const errorList = Object.values(errors)
        .flat()
        .join('\n');
      errorMessage.value = `Validation errors:\n${errorList}`;
    } else if (error.response?.status === 401) {
      errorMessage.value = 'Session expired. Please log in again.';
      setTimeout(() => router.push({ name: 'Login' }), 2000);
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to update profile.';
    }
  } finally {
    loading.value = false;
  }
};

// Change Password
const changePassword = async () => {
  try {
    if (passwordForm.value.new_password !== passwordForm.value.password_confirmation) {
      errorMessage.value = 'New password and confirmation do not match.';
      return;
    }

    if (passwordForm.value.new_password.length < 8) {
      errorMessage.value = 'Password must be at least 8 characters long.';
      return;
    }

    loadingPassword.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    const token = getAuthToken();

    if (!token) {
      errorMessage.value = 'Authentication required.';
      return;
    }

    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    };

    const response = await axios.post('/api/customers/change-password', passwordForm.value, {
      headers
    });

    if (response.data.success) {
      successMessage.value = 'Password changed successfully! ✅';
      resetPasswordForm();
      // console.log('✅ Password changed');

      setTimeout(() => {
        successMessage.value = '';
      }, 4000);
    }
  } catch (error) {
    console.error('❌ Error changing password:', error);
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      const errorList = Object.values(errors)
        .flat()
        .join('\n');
      errorMessage.value = `Validation errors:\n${errorList}`;
    } else if (error.response?.status === 401) {
      errorMessage.value = 'Session expired. Please log in again.';
      setTimeout(() => router.push({ name: 'Login' }), 2000);
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to change password.';
    }
  } finally {
    loadingPassword.value = false;
  }
};

// Reset Form
const resetForm = () => {
  form.value = { ...originalForm.value };
};

const resetPasswordForm = () => {
  passwordForm.value = {
    current_password: '',
    new_password: '',
    password_confirmation: ''
  };
};

// Logout
const logout = () => {
  if (confirm('Are you sure you want to logout?')) {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('token');
    localStorage.removeItem('sanctum_token');
    router.push({ name: 'Home' });
  }
};

// Load on mount
onMounted(() => {
  loadCustomerData();
});
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.account-page {
  background: #f9fafb;
  min-height: 100vh;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Header */
.account-header {
  margin-bottom: 2rem;
}

.account-header h1 {
  font-size: 2.25rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.account-header p {
  color: #6b7280;
  font-size: 1.125rem;
}

/* Alerts */
.alert {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert-icon {
  font-size: 1.25rem;
  flex-shrink: 0;
}

.alert-close {
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: inherit;
  opacity: 0.7;
  transition: opacity 0.2s;
}

.alert-close:hover {
  opacity: 1;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 3rem 1rem;
  background: white;
  border-radius: 8px;
  color: #6b7280;
}

/* Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

/* Card */
.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.card-header {
  background: #f9fafb;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.card-header h2 {
  font-size: 1.25rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.card-header h3 {
  font-size: 1.125rem;
  font-weight: bold;
  color: #1f2937;
}

.card-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

/* Form */
.profile-form {
  padding: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-row.three-cols {
  grid-template-columns: 1fr 1fr 1fr;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  text-transform: capitalize;
}

.form-input {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  color: #1f2937;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  background: #f0f7ff;
}

.form-input::placeholder {
  color: #9ca3af;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

/* Buttons */
.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
  background: #e5e7eb;
  color: #374151;
}

.btn-secondary:hover:not(:disabled) {
  background: #d1d5db;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Sidebar */
.sidebar {
  display: flex;
  flex-direction: column;
}

.sidebar-content {
  padding: 1.5rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.info-item:last-child {
  border-bottom: none;
}

.info-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 600;
}

.info-value {
  font-size: 1rem;
  color: #1f2937;
  font-weight: 500;
}

.status-active {
  color: #10b981;
  font-weight: 600;
}

/* Quick Links */
.quick-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: #3b82f6;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.2s;
  margin-bottom: 0.5rem;
}

.quick-link:hover {
  background: #eff6ff;
  color: #2563eb;
}

.quick-link.danger {
  color: #ef4444;
}

.quick-link.danger:hover {
  background: #fee2e2;
  color: #dc2626;
}

.quick-link:last-child {
  margin-bottom: 0;
}

.icon {
  font-size: 1.25rem;
}

/* Responsive */
@media (max-width: 768px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .account-header h1 {
    font-size: 1.875rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-row.three-cols {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }

  .card-header {
    padding: 1rem;
  }

  .profile-form {
    padding: 1rem;
  }

  .sidebar-content {
    padding: 1rem;
  }
}
</style>