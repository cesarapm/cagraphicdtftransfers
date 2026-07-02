<template>
  <v-container fluid class="auth-container">
    <v-row justify="center" align="center" class="auth-row">
      <v-col cols="12" sm="10" md="8" lg="6" xl="5">
        <div class="auth-wrapper" data-aos="fade-up">
          <v-card elevation="0" class="auth-card">
            <v-card-title class="text-center auth-header">
              <div class="auth-title">
                <div class="title-accent"></div>
                <h2>Sign In</h2>
                <p class="subtitle-text">
                  Access your account to create your designs
                </p>
              </div>
            </v-card-title>

          <v-card-text>
            <v-alert
              v-if="errorMessage"
              type="error"
              variant="tonal"
              class="mb-4"
              closable
              @click:close="errorMessage = ''"
            >
              {{ errorMessage }}
            </v-alert>

            <v-form ref="formRef" @submit.prevent="handleLogin">
              <v-text-field
                v-model="form.email"
                label="Email"
                prepend-inner-icon="mdi-email"
                variant="outlined"
                density="comfortable"
                type="email"
                :rules="[rules.required, rules.email]"
                class="mb-4"
                required
              ></v-text-field>

              <v-text-field
                v-model="form.password"
                label="Password"
                prepend-inner-icon="mdi-lock"
                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                variant="outlined"
                density="comfortable"
                :rules="[rules.required]"
                @click:append-inner="showPassword = !showPassword"
                class="mb-2"
                required
              ></v-text-field>

              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <v-checkbox
                  v-model="form.remember"
                  label="Remember me"
                  density="compact"
                  hide-details
                ></v-checkbox>

                <a href="#" class="text-primary text-decoration-none text-body-2">
                  Forgot your password?
                </a>
              </div>

              <v-btn
                type="submit"
                color="primary"
                size="large"
                block
                :loading="loading"
                :disabled="loading"
              >
                <v-icon start>mdi-login</v-icon>
                Sign In
              </v-btn>

              <v-divider class="my-6"></v-divider>

              <div class="text-center">
                <span class="text-body-2">Don't have an account? </span>
                <router-link to="/register" class="text-primary text-decoration-none font-weight-medium">
                  Sign up free
                </router-link>
              </div>
            </v-form>
          </v-card-text>
        </v-card>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const formRef = ref(null);

const form = ref({
  email: '',
  password: '',
  remember: false
});

const loading = ref(false);
const errorMessage = ref('');
const showPassword = ref(false);

const rules = {
  required: value => !!value || 'Required field',
  email: value => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || 'Invalid email';
  }
};

const handleLogin = async () => {
  const { valid } = await formRef.value.validate();
  
  if (!valid) {
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    await authStore.login(form.value);
    const redirectTo = router.currentRoute.value.query.redirect || '/';
    router.push(redirectTo);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Login error. Please check your credentials.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.auth-container {
  background: 
    radial-gradient(circle at 20% 30%, rgba(12, 159, 227, 0.15), transparent 45%),
    radial-gradient(circle at 80% 70%, rgba(12, 159, 227, 0.1), transparent 42%),
    linear-gradient(135deg, #f0f8ff 0%, #ffffff 100%);
  min-height: 100vh;
  padding: clamp(2rem, 5vw, 4rem) 1rem;
  display: flex;
  align-items: center;
}

.auth-row {
  min-height: 100%;
}

.auth-wrapper {
  padding: clamp(1rem, 3vw, 2rem);
}

.auth-card {
  background: rgba(255, 255, 255, 0.98) !important;
  border: 1px solid rgba(12, 159, 227, 0.15) !important;
  border-radius: 24px !important;
  overflow: hidden;
  box-shadow: 
    0 20px 50px rgba(12, 159, 227, 0.08),
    0 8px 20px rgba(12, 159, 227, 0.05) !important;
  backdrop-filter: blur(12px);
}

.auth-header {
  padding: clamp(2rem, 4vw, 3rem) clamp(1.5rem, 4vw, 2.5rem) clamp(1.5rem, 3vw, 2rem) !important;
  background: linear-gradient(135deg, rgba(12, 159, 227, 0.08), rgba(12, 159, 227, 0.04));
  border-bottom: 1px solid rgba(12, 159, 227, 0.1);
}

.auth-title {
  position: relative;
}

.title-accent {
  width: 80px;
  height: 2px;
  background: linear-gradient(90deg, transparent, #0C9FE3, #0080c5, #0C9FE3, transparent);
  margin: 0 auto 1.5rem;
  border-radius: 2px;
  position: relative;
}

.title-accent::before,
.title-accent::after {
  content: '';
  position: absolute;
  top: -3px;
  width: 5px;
  height: 5px;
  background: #0C9FE3;
  border-radius: 50%;
}

.title-accent::before { left: -2.5px; }
.title-accent::after { right: -2.5px; }

.auth-title h2 {
  font-family: var(--font-brand), serif;
  font-size: clamp(1.8rem, 4vw, 2.2rem);
  font-weight: 500;
  color: #0C9FE3;
  margin: 0 0 0.75rem;
  letter-spacing: -0.01em;
}

.subtitle-text {
  font-family: var(--font-brand), serif;
  font-size: clamp(1rem, 2vw, 1.1rem);
  color: #0080c5;
  font-weight: 300;
  margin: 0;
}

:deep(.v-card-text) {
  padding: clamp(1.5rem, 4vw, 2.5rem) clamp(1.5rem, 4vw, 2.5rem) clamp(2rem, 4vw, 3rem) !important;
}

:deep(.v-text-field),
:deep(.v-textarea) {
  margin-bottom: 0.5rem;
}

:deep(.v-field) {
  border-radius: 12px !important;
  background: rgba(255, 255, 255, 0.85) !important;
  border: 1px solid rgba(12, 159, 227, 0.2) !important;
  transition: all 0.3s ease;
}

:deep(.v-field:hover) {
  background: rgba(255, 255, 255, 0.95) !important;
  border-color: rgba(12, 159, 227, 0.4) !important;
  box-shadow: 0 4px 12px rgba(12, 159, 227, 0.1);
}

:deep(.v-field--focused) {
  background: #ffffff !important;
  border-color: #0C9FE3 !important;
  box-shadow: 0 4px 16px rgba(12, 159, 227, 0.15);
}

:deep(.v-field__input) {
  font-family: var(--font-brand), serif;
  font-size: 1rem;
  color: #2c2c2c;
  padding: 12px 16px;
}

:deep(.v-label) {
  font-family: var(--font-brand), serif;
  color: #0080c5;
  font-weight: 400;
}

:deep(.v-field--focused .v-label) {
  color: #0C9FE3 !important;
}

:deep(.v-icon) {
  color: #0C9FE3;
}

:deep(.v-btn) {
  font-family: var(--font-brand), serif;
  font-weight: 500;
  letter-spacing: 0.03em;
  text-transform: none;
  border-radius: 14px !important;
  transition: all 0.3s ease;
}

:deep(.v-btn.v-btn--size-large) {
  padding: 18px 32px !important;
  font-size: 1.1rem;
}

:deep(.v-btn--variant-elevated) {
  background: linear-gradient(135deg, #0C9FE3, #0080c5) !important;
  box-shadow: 0 6px 20px rgba(12, 159, 227, 0.3) !important;
}

:deep(.v-btn--variant-elevated:hover) {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(12, 159, 227, 0.4) !important;
  background: linear-gradient(135deg, #0080c5, #0C9FE3) !important;
}

:deep(.v-checkbox .v-selection-control) {
  font-family: var(--font-brand), serif;
}

:deep(.v-checkbox .v-label) {
  font-size: 0.95rem;
  color: #0080c5;
}

:deep(.v-divider) {
  border-color: rgba(12, 159, 227, 0.15) !important;
}

:deep(.v-alert) {
  border-radius: 12px !important;
  font-family: var(--font-brand), serif;
}

.text-primary {
  color: #0C9FE3 !important;
  font-weight: 500;
  transition: color 0.2s ease;
}

.text-primary:hover {
  color: #0080c5 !important;
}

.text-body-2 {
  font-family: var(--font-brand), serif;
  color: #0080c5;
}

.font-weight-medium {
  font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .auth-container {
    padding: 1.5rem 0.75rem;
  }

  .auth-wrapper {
    padding: 0.5rem;
  }

  .auth-card {
    border-radius: 20px !important;
  }

  .auth-header {
    padding: 1.5rem 1rem 1rem !important;
  }

  :deep(.v-card-text) {
    padding: 1.5rem 1rem 2rem !important;
  }
}

@media (max-width: 480px) {
  .auth-card {
    border-radius: 16px !important;
  }

  .title-accent {
    width: 60px;
    margin-bottom: 1rem;
  }

  .auth-title h2 {
    font-size: 1.5rem;
  }

  .subtitle-text {
    font-size: 0.95rem;
  }

  :deep(.v-btn.v-btn--size-large) {
    padding: 14px 24px !important;
    font-size: 1rem;
  }
}
</style>
