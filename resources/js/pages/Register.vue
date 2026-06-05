<template>
  <v-container fluid class="auth-container">
    <v-row justify="center" align="center" class="auth-row">
      <v-col cols="12" sm="11" md="9" lg="7" xl="6">
        <div class="auth-wrapper" data-aos="fade-up">
          <v-card elevation="0" class="auth-card">
            <v-card-title class="text-center auth-header">
              <div class="auth-title">
                <div class="title-accent"></div>
                <h2>Create your free account</h2>
                <p class="subtitle-text">
                  Access the Gang Sheet Builder and create your designs
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

            <v-form ref="formRef" @submit.prevent="handleRegister">
              <v-row>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.first_name"
                    label="First Name"
                    prepend-inner-icon="mdi-account"
                    variant="outlined"
                    density="comfortable"
                    :rules="[rules.required]"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.last_name"
                    label="Last Name"
                    prepend-inner-icon="mdi-account"
                    variant="outlined"
                    density="comfortable"
                    :rules="[rules.required]"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-text-field
                    v-model="form.email"
                    label="Email"
                    prepend-inner-icon="mdi-email"
                    variant="outlined"
                    density="comfortable"
                    type="email"
                    :rules="[rules.required, rules.email]"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-text-field
                    v-model="form.phone"
                    label="Phone"
                    prepend-inner-icon="mdi-phone"
                    variant="outlined"
                    density="comfortable"
                    type="tel"
                    placeholder="+1 555 123 4567"
                    :rules="[rules.required]"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    v-model="form.address"
                    label="Address"
                    prepend-inner-icon="mdi-map-marker"
                    variant="outlined"
                    density="comfortable"
                    rows="2"
                    placeholder="Street, number, neighborhood"
                    :rules="[rules.required]"
                    required
                  ></v-textarea>
                </v-col>

                <v-col cols="12" sm="5">
                  <v-text-field
                    v-model="form.city"
                    label="City"
                    prepend-inner-icon="mdi-city"
                    variant="outlined"
                    density="comfortable"
                    :rules="[rules.required]"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model="form.state"
                    label="State"
                    variant="outlined"
                    density="comfortable"
                    :rules="[rules.required]"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="3">
                  <v-text-field
                    v-model="form.zip_code"
                    label="ZIP Code"
                    variant="outlined"
                    density="comfortable"
                    :rules="[rules.required]"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.password"
                    label="Password"
                    prepend-inner-icon="mdi-lock"
                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showPassword ? 'text' : 'password'"
                    variant="outlined"
                    density="comfortable"
                    hint="Minimum 8 characters"
                    :rules="[rules.required, rules.minLength]"
                    @click:append-inner="showPassword = !showPassword"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.password_confirmation"
                    label="Confirm Password"
                    prepend-inner-icon="mdi-lock-check"
                    :append-inner-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showPassword2 ? 'text' : 'password'"
                    variant="outlined"
                    density="comfortable"
                    :rules="[rules.required, rules.passwordMatch]"
                    @click:append-inner="showPassword2 = !showPassword2"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-checkbox
                    v-model="form.terms"
                    :rules="[rules.checked]"
                    required
                  >
                    <template v-slot:label>
                      <div>
                        I accept the <a href="#" class="text-primary" @click.prevent>terms and conditions</a>
                      </div>
                    </template>
                  </v-checkbox>
                </v-col>
              </v-row>

              <v-btn
                type="submit"
                color="primary"
                size="large"
                block
                :loading="loading"
                :disabled="loading"
                class="mt-4"
              >
                <v-icon start>mdi-account-plus</v-icon>
                Create Account
              </v-btn>

              <div class="text-center mt-4">
                <span class="text-body-2">Already have an account? </span>
                <router-link to="/login" class="text-primary text-decoration-none">
                  Sign in
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
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zip_code: '',
  country: 'USA',
  terms: false
});

const loading = ref(false);
const errorMessage = ref('');
const showPassword = ref(false);
const showPassword2 = ref(false);

const rules = {
  required: value => !!value || 'Required field',
  email: value => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || 'Invalid email';
  },
  minLength: value => value.length >= 8 || 'Minimum 8 characters',
  passwordMatch: value => value === form.value.password || 'Passwords do not match',
  checked: value => value === true || 'You must accept the terms'
};

const handleRegister = async () => {
  const { valid } = await formRef.value.validate();
  
  if (!valid) {
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    await authStore.register(form.value);
    router.push('/gang-sheet-builder');
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      errorMessage.value = Object.values(errors).flat().join(', ');
    } else {
      errorMessage.value = error.response?.data?.message || 'Error creating account. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.auth-container {
  background: 
    radial-gradient(circle at 20% 30%, rgba(217, 200, 181, 0.25), transparent 45%),
    radial-gradient(circle at 80% 70%, rgba(184, 151, 120, 0.18), transparent 42%),
    linear-gradient(135deg, #faf9f7 0%, #f5f3f0 100%);
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
  background: rgba(255, 252, 248, 0.95) !important;
  border: 1px solid rgba(184, 151, 120, 0.15) !important;
  border-radius: 24px !important;
  overflow: hidden;
  box-shadow: 
    0 20px 50px rgba(140, 116, 95, 0.12),
    0 8px 20px rgba(107, 91, 71, 0.08) !important;
  backdrop-filter: blur(12px);
}

.auth-header {
  padding: clamp(2rem, 4vw, 3rem) clamp(1.5rem, 4vw, 2.5rem) clamp(1.5rem, 3vw, 2rem) !important;
  background: linear-gradient(135deg, rgba(255, 250, 244, 0.5), rgba(251, 247, 242, 0.3));
  border-bottom: 1px solid rgba(184, 151, 120, 0.1);
}

.auth-title {
  position: relative;
}

.title-accent {
  width: 80px;
  height: 2px;
  background: linear-gradient(90deg, transparent, #b89778, #8c745f, #b89778, transparent);
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
  background: #8c745f;
  border-radius: 50%;
}

.title-accent::before { left: -2.5px; }
.title-accent::after { right: -2.5px; }

.auth-title h2 {
  font-family: var(--font-brand), serif;
  font-size: clamp(1.8rem, 4vw, 2.2rem);
  font-weight: 500;
  color: #6b5b47;
  margin: 0 0 0.75rem;
  letter-spacing: -0.01em;
}

.subtitle-text {
  font-family: var(--font-brand), serif;
  font-size: clamp(1rem, 2vw, 1.1rem);
  color: #8f7a65;
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
  background: rgba(255, 255, 255, 0.75) !important;
  border: 1px solid rgba(184, 151, 120, 0.2) !important;
  transition: all 0.3s ease;
}

:deep(.v-field:hover) {
  background: rgba(255, 255, 255, 0.9) !important;
  border-color: rgba(184, 151, 120, 0.35) !important;
  box-shadow: 0 4px 12px rgba(140, 116, 95, 0.08);
}

:deep(.v-field--focused) {
  background: #ffffff !important;
  border-color: #8c745f !important;
  box-shadow: 0 4px 16px rgba(140, 116, 95, 0.12);
}

:deep(.v-field__input) {
  font-family: var(--font-brand), serif;
  font-size: 1rem;
  color: #2c2c2c;
  padding: 12px 16px;
}

:deep(.v-label) {
  font-family: var(--font-brand), serif;
  color: #7f6d5a;
  font-weight: 400;
}

:deep(.v-field--focused .v-label) {
  color: #6b5b47 !important;
}

:deep(.v-icon) {
  color: #8c745f;
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
  background: linear-gradient(135deg, #8c745f, #a68b73) !important;
  box-shadow: 0 6px 20px rgba(140, 116, 95, 0.25) !important;
}

:deep(.v-btn--variant-elevated:hover) {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(140, 116, 95, 0.35) !important;
  background: linear-gradient(135deg, #a68b73, #8c745f) !important;
}

:deep(.v-checkbox .v-selection-control) {
  font-family: var(--font-brand), serif;
}

:deep(.v-checkbox .v-label) {
  font-size: 0.95rem;
  color: #5a6c7d;
}

:deep(.v-alert) {
  border-radius: 12px !important;
  font-family: var(--font-brand), serif;
}

.text-primary {
  color: #8c745f !important;
  font-weight: 500;
  transition: color 0.2s ease;
}

.text-primary:hover {
  color: #6b5b47 !important;
}

.text-center.mt-4 {
  font-family: var(--font-brand), serif;
  padding-top: 1rem;
  border-top: 1px solid rgba(184, 151, 120, 0.1);
  margin-top: 2rem !important;
}

.text-body-2 {
  color: #7f6d5a;
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
