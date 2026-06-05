<template>
  <v-app>
    <!-- Navbar -->
    <header ref="navbar" class="main-navbar">
      <!-- Primera Fila: Logo, Búsqueda e Iconos -->
      <div class="navbar-row-1">
        <v-container class="navbar-top-container">
          <!-- Logo -->
          <router-link to="/" class="header-logo text-decoration-none">
            <img :src="logo" alt="CT Graphic Logo" class="logo-image" />
          </router-link>

          <!-- Search Bar -->
          <div class="search-container d-none d-md-flex">
            <v-text-field
              v-model="searchQuery"
              placeholder="Search products..."
              variant="solo"
              density="comfortable"
              hide-details
              flat
              class="search-input"
              prepend-inner-icon="mdi-magnify"
            >
            </v-text-field>
            <v-btn
              class="search-btn"
              elevation="0"
              @click="handleSearch"
            >
              Search
            </v-btn>
          </div>

          <!-- Header Actions -->
          <div class="header-actions d-none d-md-flex align-center">
            <!-- Account -->
            <div class="header-action-item">
              <template v-if="authStore.isAuthenticated">
                <v-menu>
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      icon
                      variant="text"
                      class="action-btn"
                    >
                      <v-icon>mdi-account-circle</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item>
                      <v-list-item-title class="font-weight-bold">{{ authStore.customerName }}</v-list-item-title>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item :to="{ name: 'CustomerOrders' }">
                      <template v-slot:prepend>
                        <v-icon>mdi-package-variant-closed</v-icon>
                      </template>
                      <v-list-item-title>My Projects</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="handleLogout">
                      <template v-slot:prepend>
                        <v-icon>mdi-logout</v-icon>
                      </template>
                      <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
              <template v-else>
                <v-btn
                  :to="{ name: 'Login' }"
                  icon
                  variant="text"
                  class="action-btn"
                >
                  <v-icon>mdi-account-circle</v-icon>
                </v-btn>
              </template>
              <span class="action-label">ACCOUNT</span>
            </div>

            <!-- Wishlist -->
            <div class="header-action-item">
              <v-btn
                icon
                variant="text"
                class="action-btn"
              >
                <v-badge
                  content="0"
                  color="#EF4444"
                >
                  <v-icon>mdi-heart-outline</v-icon>
                </v-badge>
              </v-btn>
              <span class="action-label">WISHLIST</span>
            </div>

            <!-- Cart -->
            <div class="header-action-item">
              <v-btn
                icon
                variant="text"
                class="action-btn"
              >
                <v-icon>mdi-shopping</v-icon>
              </v-btn>
              <span class="action-label">CART</span>
            </div>
          </div>

          <!-- Mobile Menu Toggle -->
          <v-app-bar-nav-icon
            class="d-md-none mobile-nav-toggle"
            @click="drawer = !drawer"
          ></v-app-bar-nav-icon>
        </v-container>
      </div>

      <!-- Segunda Fila: Menú de Navegación -->
      <div class="navbar-row-2 d-none d-md-block">
        <v-container class="navbar-menu-container">
          <div class="nav-menu">
            <v-btn
              :to="{ name: 'Home' }"
              variant="text"
              class="nav-menu-link"
            >
              PRODUCTS
            </v-btn>
            <v-btn
              variant="text"
              class="nav-menu-link"
            >
              HEAT PRESS GUIDE
            </v-btn>
            <v-menu offset-y>
              <template v-slot:activator="{ props }">
                <v-btn
                  v-bind="props"
                  variant="text"
                  class="nav-menu-link"
                >
                  DTF Transfers
                  <v-icon end>mdi-chevron-down</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item :to="{ name: 'Home' }">
                  <v-list-item-title>All Products</v-list-item-title>
                </v-list-item>
                <v-list-item>
                  <v-list-item-title>Custom Orders</v-list-item-title>
                </v-list-item>
                <v-list-item>
                  <v-list-item-title>Ready to Press</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
            <v-btn
              variant="text"
              class="nav-menu-link"
            >
              Upload Gang Sheet
            </v-btn>
            <v-btn
              :to="{ name: 'GangSheetBuilder' }"
              variant="text"
              class="nav-menu-link"
            >
              Build Gang Sheet
            </v-btn>
          </div>
        </v-container>
      </div>
    </header>

    <!-- Mobile Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      temporary
      location="right"
      class="drawerapp"
    >
      <!-- Mobile Search -->
      <div class="pa-4">
        <v-text-field
          v-model="searchQuery"
          placeholder="Search products..."
          variant="outlined"
          density="comfortable"
          hide-details
          prepend-inner-icon="mdi-magnify"
          @keyup.enter="handleSearch"
        ></v-text-field>
      </div>

      <v-list>
        <v-list-item
          :to="{ name: 'Home' }"
          prepend-icon="mdi-view-grid"
          title="Products"
        ></v-list-item>
        <v-list-item
          prepend-icon="mdi-iron"
          title="Heat Press Guide"
        ></v-list-item>
        <v-list-group value="dtf">
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-image-multiple"
              title="DTF Transfers"
            ></v-list-item>
          </template>
          <v-list-item
            :to="{ name: 'Home' }"
            title="All Products"
            class="pl-8"
          ></v-list-item>
          <v-list-item
            title="Custom Orders"
            class="pl-8"
          ></v-list-item>
          <v-list-item
            title="Ready to Press"
            class="pl-8"
          ></v-list-item>
        </v-list-group>
        <v-list-item
          prepend-icon="mdi-upload"
          title="Upload Gang Sheet"
        ></v-list-item>
        <v-list-item
          :to="{ name: 'GangSheetBuilder' }"
          prepend-icon="mdi-grid"
          title="Build Gang Sheet"
        ></v-list-item>
        
        <v-divider class="my-2"></v-divider>
        
        <!-- Account Section -->
        <template v-if="authStore.isAuthenticated">
          <v-list-item>
            <v-list-item-title class="text-caption text-grey">
              {{ authStore.customer?.email }}
            </v-list-item-title>
          </v-list-item>
          <v-list-item
            :to="{ name: 'CustomerOrders' }"
            prepend-icon="mdi-package-variant-closed"
            title="Mis Proyectos"
          ></v-list-item>
          <v-list-item
            @click="handleLogout"
            prepend-icon="mdi-logout"
            title="Cerrar Sesión"
          ></v-list-item>
        </template>
        <template v-else>
          <v-list-item
            :to="{ name: 'Login' }"
            prepend-icon="mdi-login"
            title="Iniciar Sesión"
          ></v-list-item>
          <v-list-item
            :to="{ name: 'Register' }"
            prepend-icon="mdi-account-plus"
            title="Registrarse"
          ></v-list-item>
        </template>
      </v-list>
    </v-navigation-drawer>

    <!-- Contenido principal -->
    <v-main>
      <router-view></router-view>
    </v-main>

    <!-- Botón flotante de WhatsApp -->
    <v-btn
      class="whatsapp-float"
      color="#25D366"
      size="x-large"
      icon
      elevation="8"
      href="https://wa.me/13128434099?text=Hello%20CT%20Graphic%2C%20I%20have%20a%20question%20about%20your%20products."
      target="_blank"
    >
      <v-icon size="32">mdi-whatsapp</v-icon>
    </v-btn>

    <!-- Footer -->
    <v-footer class="footer">
      <v-container>
        <v-row>
          <!-- Left Column: Links & Contact Info -->
          <v-col cols="12" md="6" class="footer-left">
            <h3 class="footer-section-title">SHOP TRANSFERS</h3>
            
            <div class="footer-nav-links">
              <router-link to="/" class="footer-nav-link">UPLOAD A SHEET</router-link>
              <span class="footer-separator">-</span>
              <router-link to="/" class="footer-nav-link">Ultra color GANG SHEET</router-link>
              <span class="footer-separator">-</span>
              <router-link to="/" class="footer-nav-link">Ultra color</router-link>
            </div>
            <div class="footer-nav-links">
              <router-link to="/" class="footer-nav-link">CUSTOM ART REQUEST</router-link>
            </div>
            <div class="footer-nav-links">
              <router-link to="/" class="footer-nav-link">SEARCH</router-link>
            </div>

            <div class="footer-contact-info">
              <h4 class="footer-contact-title">CALL US! 312.550.7158</h4>
              <a href="mailto:contact@minaogtransfers.com" class="footer-contact-email">
                contact@minaogtransfers.com
              </a>
            </div>

            <div class="footer-logo-wrapper">
              <img :src="footerLogo" alt="CT Graphic DTF Transfers" class="footer-logo-image" />
            </div>
          </v-col>

          <!-- Right Column: Contact Form -->
          <v-col cols="12" md="6" class="footer-right">
            <h3 class="footer-form-title">QUESTION? COMMENT? GOOD JOKE?<br>WELL, DON'T KEEP IT TO YOURSELF!</h3>
            
            <form @submit.prevent="handleContactSubmit" class="footer-contact-form">
              <div class="footer-form-row">
                <v-text-field
                  v-model="contactForm.name"
                  placeholder="Name"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  class="footer-input footer-input-half"
                ></v-text-field>
                <v-text-field
                  v-model="contactForm.email"
                  placeholder="Email"
                  type="email"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  class="footer-input footer-input-half"
                ></v-text-field>
              </div>
              
              <v-text-field
                v-model="contactForm.phone"
                placeholder="Phone"
                variant="outlined"
                density="comfortable"
                hide-details
                class="footer-input"
              ></v-text-field>
              
              <v-textarea
                v-model="contactForm.comment"
                placeholder="Comment"
                variant="outlined"
                rows="5"
                hide-details
                class="footer-textarea"
              ></v-textarea>
              
              <v-btn
                type="submit"
                class="footer-submit-btn"
                size="large"
                elevation="0"
                block
              >
                SUBMIT
              </v-btn>
            </form>
          </v-col>
        </v-row>

        <!-- Bottom Copyright -->
        <v-row class="footer-bottom">
          <v-col cols="12" md="6" class="text-left">
            <p class="footer-copyright">Copyright   2026 cagraphicdtftransfers.com</p>
          </v-col>
          <v-col cols="12" md="6" class="text-right">
            <p class="footer-powered">Powered by cagraphicclosers@hotmas.com</p>
          </v-col>
        </v-row>
      </v-container>
    </v-footer>
  </v-app>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const drawer = ref(false);
const navbar = ref(null);
const searchQuery = ref('');
let lastScrollTop = 0;
let ticking = false;
const scrollThreshold = 5;
const logo = '/images/logo.webp';
const footerLogo = '/images/home/logohome.webp';

// Contact form
const contactForm = ref({
  name: '',
  email: '',
  phone: '',
  comment: ''
});

const handleContactSubmit = () => {
  // Implement contact form submission
  console.log('Contact form submitted:', contactForm.value);
  // Here you would send the form data to your backend
  // Reset form after submission
  contactForm.value = {
    name: '',
    email: '',
    phone: '',
    comment: ''
  };
};

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Implement search functionality
    console.log('Searching for:', searchQuery.value);
  }
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/');
};

// Scroll behavior with requestAnimationFrame for smooth performance
const handleScroll = () => {
  if (!navbar.value) return;

  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const scrollDifference = Math.abs(scrollTop - lastScrollTop);

  // Only process if we've scrolled enough
  if (scrollDifference < scrollThreshold) {
    ticking = false;
    return;
  }

  // Don't hide navbar if drawer is open
  if (drawer.value) {
    navbar.value.classList.remove('hidden');
    ticking = false;
    return;
  }

  // Hide/show navbar based on scroll direction
  if (scrollTop > lastScrollTop && scrollTop > 150) {
    // Scrolling down & past threshold -> hide navbar
    navbar.value.classList.add('hidden');
  } else if (scrollTop < lastScrollTop || scrollTop <= 100) {
    // Scrolling up OR at top -> show navbar
    navbar.value.classList.remove('hidden');
  }

  lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  ticking = false;
};

const onScroll = () => {
  if (!ticking) {
    requestAnimationFrame(handleScroll);
    ticking = true;
  }
};

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll);
});
</script>

<style>
/* Global Styles */
html,
body,
* {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  margin: 0;
  padding: 0;
  scroll-behavior: smooth;
}

header {
  margin: 0;
  padding: 0;
  font-size: 16px;
  scroll-behavior: smooth;
}

/* WhatsApp Float Button */
.whatsapp-float {
  position: fixed !important;
  bottom: 20px;
  right: 20px;
  z-index: 1000;
  box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4) !important;
  transition: all 0.3s ease !important;
}

.whatsapp-float:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 30px rgba(37, 211, 102, 0.6) !important;
}

@media (max-width: 600px) {
  .whatsapp-float {
    bottom: 80px;
    right: 16px;
  }
}

/* Header Styles */
.main-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease-in-out;
  will-change: transform;
}

.main-navbar.hidden {
  transform: translateY(-100%);
}

/* Primera Fila */
.navbar-row-1 {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
}

/* Segunda Fila */
.navbar-row-2 {
  background: #ffffff;
}

/* Top Row - Logo, Search, Icons */
.navbar-top-container {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 18px 16px !important;
  gap: 32px;
  max-width: 1280px;
  margin: 0 auto;
}

.header-logo {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.logo-image {
  height: 65px;
  width: auto;
  object-fit: contain;
}

/* Search Bar */
.search-container {
  flex: 1;
  max-width: 550px;
  display: flex;
  gap: 12px;
  align-items: center;
}

.search-input {
  flex: 1;
}

.search-input :deep(.v-field) {
  border-radius: 8px !important;
  background-color: #f3f4f6 !important;
  border: 1px solid #e5e7eb;
}

.search-input :deep(.v-field__input) {
  padding: 10px 16px;
  min-height: 42px;
  font-size: 14px;
  color: #6b7280;
}

.search-input :deep(.v-field__input::placeholder) {
  color: #9ca3af;
}

.search-input :deep(.v-field--focused) {
  box-shadow: none;
  border-color: #d1d5db;
}

.search-input :deep(.v-field__outline) {
  display: none;
}

.search-btn {
  border-radius: 25px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  letter-spacing: 0.5px !important;
  padding: 0 32px !important;
  height: 42px !important;
  min-width: 110px !important;
  background: #0ea5e9 !important;
  box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2) !important;
  font-size: 15px !important;
  color: #ffffff !important;
}

.search-btn:hover {
  background: #0284c7 !important;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3) !important;
}

/* Header Actions */
.header-actions {
  display: flex;
  gap: 32px;
  flex-shrink: 0;
}

.header-action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}

.action-btn {
  color: #111827 !important;
  width: 40px !important;
  height: 40px !important;
}

.action-btn :deep(.v-icon) {
  font-size: 28px !important;
}

.action-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.8px;
  color: #374151;
  text-transform: uppercase;
  line-height: 1;
}

/* Bottom Row - Navigation Menu */
.navbar-menu-container {
  padding: 0 16px !important;
  max-width: 1280px;
  margin: 0 auto;
}

.nav-menu {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 4px;
  padding: 0;
}

.nav-menu-link {
  color: #111827 !important;
  font-weight: 700 !important;
  letter-spacing: 0.3px !important;
  text-transform: uppercase !important;
  font-size: 13px !important;
  padding: 16px 20px !important;
  height: auto !important;
  border-radius: 0 !important;
  min-width: auto !important;
}

.nav-menu-link:hover {
  background: #f9fafb !important;
}

.nav-menu-link :deep(.v-icon) {
  font-size: 18px !important;
  margin-left: 2px !important;
}

/* Mobile Drawer */
.drawerapp {
  background: #ffffff !important;
  padding-top: 60px;
}

/* Main Content */
.v-main {
  padding-top: 160px !important; /* Espacio para el header fijo con dos filas */
}

@media (max-width: 959px) {
  .v-main {
    padding-top: 70px !important; /* Menos espacio en móvil donde solo hay una fila visible */
  }
}

/* Badge Styles */
.v-badge__badge {
  font-size: 10px !important;
  font-weight: 700 !important;
  min-width: 18px !important;
  height: 18px !important;
  padding: 0 4px !important;
}

/* Mobile Styles */
.mobile-nav-toggle {
  color: #111827 !important;
}

@media (max-width: 959px) {
  .navbar-top-container {
    padding: 14px 0 !important;
    gap: 16px;
  }

  .logo-image {
    height: 50px;
  }
}

@media (max-width: 600px) {
  .navbar-top-container {
    padding: 12px 0 !important;
  }

  .logo-image {
    height: 45px;
  }
}

/* Footer */
.footer {
  background: #111827 !important;
  color: #ffffff !important;
  padding: 60px 0 20px !important;
}

.footer-left {
  padding-right: 40px;
}

.footer-section-title {
  font-size: 20px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 24px;
  letter-spacing: 0.5px;
}

.footer-nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  flex-wrap: wrap;
}

.footer-nav-link {
  color: #d1d5db;
  text-decoration: none;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  transition: color 0.3s ease;
}

.footer-nav-link:hover {
  color: #ffffff;
}

.footer-separator {
  color: #6b7280;
  font-size: 13px;
}

.footer-contact-info {
  margin-top: 32px;
  margin-bottom: 32px;
}

.footer-contact-title {
  font-size: 16px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 8px;
  letter-spacing: 0.3px;
}

.footer-contact-email {
  color: #d1d5db;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s ease;
}

.footer-contact-email:hover {
  color: #ffffff;
}

.footer-logo-wrapper {
  margin-top: 32px;
}

.footer-logo-image {
  max-width: 200px;
  height: auto;
}

.footer-right {
  padding-left: 40px;
}

.footer-form-title {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 24px;
  line-height: 1.4;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.footer-contact-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-form-row {
  display: flex;
  gap: 12px;
}

.footer-input-half {
  flex: 1;
}

.footer-input :deep(.v-field) {
  background: #ffffff !important;
  border-radius: 0 !important;
}

.footer-input :deep(.v-field__input) {
  padding: 12px 16px;
  font-size: 14px;
}

.footer-textarea :deep(.v-field) {
  background: #ffffff !important;
  border-radius: 0 !important;
}

.footer-textarea :deep(.v-field__input) {
  padding: 12px 16px;
  font-size: 14px;
}

.footer-submit-btn {
  background: #0ea5e9 !important;
  color: #ffffff !important;
  font-weight: 700 !important;
  letter-spacing: 1px !important;
  border-radius: 0 !important;
  margin-top: 8px;
  transition: all 0.3s ease !important;
}

.footer-submit-btn:hover {
  background: #0284c7 !important;
}

.footer-bottom {
  border-top: 1px solid #374151;
  margin-top: 40px;
  padding-top: 20px;
}

.footer-copyright,
.footer-powered {
  font-size: 11px;
  color: #9ca3af;
  margin: 0;
}

/* Responsive Footer */
@media (max-width: 959px) {
  .footer {
    padding: 40px 0 20px !important;
  }

  .footer-left,
  .footer-right {
    padding-left: 0;
    padding-right: 0;
  }

  .footer-left {
    margin-bottom: 40px;
  }

  .footer-section-title {
    font-size: 18px;
  }

  .footer-form-title {
    font-size: 13px;
  }

  .footer-bottom .text-right {
    text-align: left !important;
  }
}

@media (max-width: 600px) {
  .footer {
    padding: 30px 0 20px !important;
  }

  .footer-form-row {
    flex-direction: column;
    gap: 12px;
  }

  .footer-logo-image {
    max-width: 150px;
  }
}
</style>
