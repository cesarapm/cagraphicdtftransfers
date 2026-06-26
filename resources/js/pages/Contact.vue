<script setup>
import { ref, onMounted } from 'vue';
import logoImage from '../../../public/images/logo.webp';
import logoHomeImage from '../../../public/images/home/logohome.webp';

const formData = ref({
  name: '',
  email: '',
  phone: '',
  comment: ''
});

const isSubmitting = ref(false);
const submitMessage = ref('');

// SEO Meta Tags
onMounted(() => {
  document.title = 'Contact Us | CA Graphic DTF Transfers';
  
  const updateMeta = (name, content, isProperty = false) => {
    const selector = isProperty ? `meta[property="${name}"]` : `meta[name="${name}"]`;
    let meta = document.querySelector(selector);
    if (meta) meta.remove();
    
    const newMeta = document.createElement('meta');
    if (isProperty) {
      newMeta.setAttribute('property', name);
    } else {
      newMeta.setAttribute('name', name);
    }
    newMeta.content = content;
    document.head.appendChild(newMeta);
  };
  
  updateMeta('description', 'Get in touch with CA Graphic DTF. We\'re here to help with your DTF transfer questions and orders. Call us at (312) 843-4099.');
  updateMeta('keywords', 'contact DTF, customer service, DTF transfers support, CA Graphic');
  updateMeta('og:title', 'Contact Us | CA Graphic DTF Transfers', true);
  updateMeta('og:description', 'Contact CA Graphic DTF for any questions or assistance with orders.', true);
});

const handleSubmit = async () => {
  if (!formData.value.name || !formData.value.email || !formData.value.comment) {
    submitMessage.value = 'Please fill in all required fields.';
    return;
  }

  isSubmitting.value = true;
  submitMessage.value = '';

  try {
    const response = await fetch('/api/contact', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify(formData.value)
    });

    if (response.ok) {
      submitMessage.value = 'Thank you! We\'ll get back to you soon.';
      formData.value = { name: '', email: '', phone: '', comment: '' };
    } else {
      submitMessage.value = 'Error sending message. Please try again.';
    }
  } catch (error) {
    console.error('Error:', error);
    submitMessage.value = 'Error sending message. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
 
    </section>

    <!-- Contact Content -->
    <section class="contact-content">
      <div class="contact-container">
        <!-- Left Side - Logo -->
        <div class="contact-left">
          <img :src="logoHomeImage" alt="CA Graphic DTF Logo" class="contact-logo">
        </div>

        <!-- Right Side - Contact Info -->
        <div class="contact-right">
          <h1 class="contact-title">Contact Us</h1>
          
          <div class="contact-subtitle-group">
            <p class="contact-subtitle">Have any queries?</p>
            <p class="contact-highlight">We're here to help.</p>
          </div>

          <div class="contact-info-section">
            <h2 class="contact-section-title">Contact</h2>
            <p class="contact-description">
              Contact us with any questions<br>
              or assistance with orders
            </p>
            <p class="contact-phone">(312) 843 - 4099</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section">
      <div class="form-container">
        <!-- Left Side - Form Info -->
        <div class="form-info">
          <p class="form-subtitle">Don't be a stranger!</p>
          <h2 class="form-title">You tell us. We listen.</h2>
          <p class="form-description">
            Fill out our form and we will promptly<br>
            get back to you
          </p>
        </div>

        <!-- Right Side - Form -->
        <div class="form-wrapper">
          <div class="form-header">
            <p class="form-header-text">QUESTION? COMMENT? GOOD JOKE?<br>WELL, DON'T KEEP IT TO YOURSELF</p>
          </div>

          <form @submit.prevent="handleSubmit" class="contact-form">
            <div class="form-row">
              <div class="form-group">
                <input 
                  v-model="formData.name" 
                  type="text" 
                  placeholder="Name" 
                  required
                >
              </div>
              <div class="form-group">
                <input 
                  v-model="formData.email" 
                  type="email" 
                  placeholder="Email" 
                  required
                >
              </div>
            </div>

            <div class="form-group">
              <input 
                v-model="formData.phone" 
                type="tel" 
                placeholder="Phone"
              >
            </div>

            <div class="form-group">
              <textarea 
                v-model="formData.comment" 
                placeholder="Comment"
                rows="4"
                required
              ></textarea>
            </div>

            <button type="submit" :disabled="isSubmitting" class="submit-button">
              SUBMIT
            </button>

            <p v-if="submitMessage" :class="submitMessage.includes('Thank you') ? 'success-message' : 'error-message'">
              {{ submitMessage }}
            </p>
          </form>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.contact-page {
  background: #ffffff;
}

/* Hero Section */
.contact-hero {
  position: relative;
  height: 622px;

  background-image: url('/images/portadas/CONTACT-US.webp');
  background-size: cover; /* ← cambia esto */
  background-repeat: no-repeat;
  background-position: center;

  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 1024px) {
  .contact-hero {
    height: 400px;
    background-attachment: scroll;
  }
}

@media (max-width: 768px) {
  .contact-hero {
    height: 250px;
    background-position: center right;
  }
}

@media (max-width: 480px) {
  .contact-hero {
    height: 170px;
    background-position: center center;
  }
}

/* Contact Content Section */
.contact-content {
  padding: 80px 40px;
  background: #ffffff;
}

.contact-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}

/* Left Side - Logo */
.contact-left {
  display: flex;
  align-items: center;
  justify-content: center;
}

.contact-logo {
  max-width: 100%;
  height: auto;
  filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.1));
}

/* Right Side - Content */
.contact-right {
  padding: 40px;
  text-align: center;
}

.contact-title {
  font-size: 42px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 20px;
  letter-spacing: -0.5px;
}

.contact-subtitle-group {
  margin-bottom: 30px;
}

.contact-subtitle {
  font-size: 16px;
  color: #666;
  margin-bottom: 8px;
}

.contact-highlight {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.contact-info-section {
  margin-top: 40px;
}

.contact-section-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 12px;
}

.contact-description {
  font-size: 15px;
  color: #1a1a1a;
  line-height: 1.8;
  margin-bottom: 16px;
}

.contact-phone {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

/* Responsive Design */

/* Contact Form Section */
.contact-form-section {
  padding: 80px 40px;
  background: #f9f9f9;
}

.form-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: flex-start;
}

/* Left Side - Form Info */
.form-info {
  padding: 40px 0;
}

.form-subtitle {
  font-size: 16px;
  color: #666;
  margin-bottom: 12px;
}

.form-title {
  font-size: 42px;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 24px;
  line-height: 1.2;
}

.form-description {
  font-size: 15px;
  color: #666;
  line-height: 1.8;
}

/* Right Side - Form */
.form-wrapper {
  background: white;
  border: 2px solid #1a1a1a;
  padding: 30px;
}

.form-header {
  border-bottom: 2px solid #1a1a1a;
  padding-bottom: 20px;
  margin-bottom: 25px;
}

.form-header-text {
  font-size: 12px;
  font-weight: 700;
  color: #1a1a1a;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin: 0;
  line-height: 1.6;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: flex;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #ccc;
  font-size: 14px;
  font-family: inherit;
  transition: all 0.3s ease;
  background-color: #ffffff;
  box-sizing: border-box;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: #999;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #1a1a1a;
  box-shadow: 0 0 0 2px rgba(26, 26, 26, 0.05);
}

.submit-button {
  padding: 14px 32px;
  background: #0099ff;
  color: white;
  border: none;
  border-radius: 0;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-top: 12px;
}

.submit-button:hover:not(:disabled) {
  background: #0078cc;
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.success-message {
  color: #22a55a;
  font-size: 14px;
  margin-top: 12px;
  padding: 12px 16px;
  background-color: #f0fdf4;
  border-radius: 4px;
  text-align: center;
}

.error-message {
  color: #dc2626;
  font-size: 14px;
  margin-top: 12px;
  padding: 12px 16px;
  background-color: #fef2f2;
  border-radius: 4px;
  text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
  .contact-content {
    padding: 60px 20px;
  }

  .contact-container {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .contact-title {
    font-size: 32px;
  }

  .contact-right {
    padding: 30px;
  }

  .form-container {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .form-title {
    font-size: 32px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .contact-form-section {
    padding: 60px 20px;
  }
}

@media (max-width: 480px) {
  .contact-content {
    padding: 40px 16px;
  }

  .contact-title {
    font-size: 26px;
  }

  .contact-section-title {
    font-size: 20px;
  }

  .form-title {
    font-size: 24px;
  }

  .form-wrapper {
    padding: 20px;
  }

  .form-header-text {
    font-size: 11px;
  }
}
</style>