<template>
  <div class="ca-home">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-content">
          <div class="hero-logo">
            <img :src="logohome" alt="LA Graphic DTF Transfers" />
          </div>
          <h1 class="hero-title">
            PRESS-READY<br />
            DTF ROLLS<br />
            THE EASY WAY.
          </h1>
          <v-btn
            :to="{ name: 'GangSheetBuilder' }"
            size="x-large"
            class="hero-btn"
            elevation="0"
          >
            BUILD YOUR GANG SHEET NOW
          </v-btn>
        </div>
      </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
      <v-container>
        <div class="info-content" data-aos="fade-up">
          <h2 class="info-title">The DTF Transfers Now Process</h2>
          <p class="info-subtitle">Quick ordering • Fast turnaround • Ready to press.</p>
          <div class="info-details">
            <p>Ultra color (9 Color DTF)</p>
            <p>CMYK - RGBO + WHITE</p>
          </div>
        </div>
      </v-container>
    </section>

    <!-- Separator Line -->
    <div class="section-separator"></div>

    <!-- How to Order Section -->
    <section class="how-to-order-section">
      <v-container>
        <h2 class="section-title" data-aos="fade-up">How to Order in 3 Simple Steps</h2>
        
        <v-row class="steps-container">
          <!-- Step 1 -->
          <v-col cols="12" md="4">
            <div class="step-card" data-aos="fade-up" data-aos-delay="100">
              <div class="step-image">
                <img :src="step1Image" alt="Upload or Build Your Gang Sheet" />
              </div>
              <h3 class="step-number">1 - Upload or Build Your Gang Sheet</h3>
              <p class="step-description">
                Upload your finished artwork or use our online builder to arrange multiple designs on one gang sheet. Make sure files are high-resolution for best results.
              </p>
            </div>
          </v-col>

          <!-- Step 2 -->
          <v-col cols="12" md="4">
            <div class="step-card" data-aos="fade-up" data-aos-delay="200">
              <div class="step-image">
                <img :src="step2Image" alt="We Print & Ship Fast" />
              </div>
              <h3 class="step-number">2 - We Print & Ship Fast</h3>
              <p class="step-description">
                Once your order is placed, we print your transfers within 24 hours using premium film and CMYK-RGBO+WHITE ink.
              </p>
            </div>
          </v-col>

          <!-- Step 3 -->
          <v-col cols="12" md="4">
            <div class="step-card" data-aos="fade-up" data-aos-delay="300">
              <div class="step-image">
                <img :src="step3Image" alt="Press & Wear with Ease" />
              </div>
              <h3 class="step-number">3 - Press & Wear with Ease</h3>
              <p class="step-description">
                Apply your transfer with a heat press for 12-15 seconds at the recommended settings. Your designs stay vibrant, stretchable, and last through 40-50+ washes.
              </p>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
      <v-container>
        <v-row class="stats-container">
          <!-- Stat 1 -->
          <v-col cols="12" md="4">
            <div class="stat-item" data-aos="zoom-in" data-aos-delay="100">
              <h3 class="stat-number">100%</h3>
              <h4 class="stat-title">Customer Satisfaction</h4>
              <p class="stat-description">
                We stand behind every order. If it's not perfect, we'll make it right - guaranteed.
              </p>
            </div>
          </v-col>

          <!-- Stat 2 -->
          <v-col cols="12" md="4">
            <div class="stat-item" data-aos="zoom-in" data-aos-delay="200">
              <h3 class="stat-number">24 Hrs</h3>
              <h4 class="stat-title">Fast Turnaround</h4>
              <p class="stat-description">
                Most DTF orders ship within Next day printing so you can press and sell faster.
              </p>
            </div>
          </v-col>

          <!-- Stat 3 -->
          <v-col cols="12" md="4">
            <div class="stat-item" data-aos="zoom-in" data-aos-delay="300">
              <h3 class="stat-number">300 DPI</h3>
              <h4 class="stat-title">High-Resolution Prints</h4>
              <p class="stat-description">
                Your designs are printed in ultra-sharp 300 DPI for crisp details and vibrant on every sheet.
              </p>
            </div>
          </v-col>
        </v-row>

        <!-- CTA Button -->
        <div class="stats-cta">
          <v-btn
            size="x-large"
            class="order-now-btn"
            elevation="0"
          >
            ORDER NOW
          </v-btn>
        </div>
      </v-container>
    </section>

    <!-- DTF Features Section -->
    <section class="features-section">
      <!-- Feature 1: DTF Explanation -->
      <div class="feature-row">
        <div class="feature-half feature-image-half" data-aos="fade-right">
          <img :src="dtfImage" alt="DTF Printing Process" class="feature-full-image" />
        </div>
        <div class="feature-half feature-content-half" data-aos="fade-left">
          <div class="feature-content-wrapper">
            <h2 class="feature-title">DTF: DIRECT-TO-FILM TRANSFERS</h2>
            <p class="feature-text">
              Print vibrant, full-color designs onto nearly any fabric. No screens, No weeding, No limits.
            </p>
            <p class="feature-text">
              Using premium ink and adhesive, your artwork bonds permanently to fabric with crisp color and lasting durability. Works on cotton, polyester, and blends - bright on light or dark garments alike.
            </p>
            <p class="feature-tagline">
              You bring the art (or just the idea), we'll bring the print.
            </p>
          </div>
        </div>
      </div>

      <!-- Feature 2: In-House Services -->
      <div class="feature-row">
        <div class="feature-half feature-content-half feature-content-blue-bg" data-aos="fade-right">
          <div class="feature-content-wrapper-blue">
            <h2 class="feature-title-white">DESIGN, PRINTING, WEBSITE, CUSTOMER CARE: ALL IN-HOUSE.</h2>
            <p class="feature-text-white">
              From art and design to printing and customer care, everything happens under one roof in Northwest Illinois. Our experienced team brings years of hands - on print and production knowledge - every order handled with precision, care, and pride.
            </p>
            <p class="feature-text-white">
              Whether you're a small business, creator, or large brand, you'll get top-tier quora continuamos con esta sectionality, fast turnaround, and personal service on every order.
            </p>
          </div>
        </div>
        <div class="feature-half feature-image-half feature-image-cover" data-aos="fade-left">
          <img :src="inHouseImage" alt="In-House Production" class="feature-cover-image" />
        </div>
      </div>
    </section>

    <!-- Whatever You've Got Section -->
    <section class="whatever-section">
      <v-container>
        <div class="whatever-header" data-aos="fade-up">
          <h2 class="whatever-title">WHATEVER YOU'VE GOT, WE'LL GET IT READY TO PRESS.</h2>
          <p class="whatever-subtitle">
            Whether you're uploading a finished gang sheet, building one from scratch, or starting with just an idea - we'll help you get it print-ready and pressed with confidence. Our tools and team are here to make the process simple, fast, and done right.
          </p>
        </div>

        <v-row class="whatever-cards">
          <!-- Card 1: Upload Gang Sheet -->
          <v-col cols="12" md="4">
            <div class="whatever-card" data-aos="fade-up" data-aos-delay="100">
              <h3 class="whatever-card-title">GOT A GANG SHEET,<br>JUST NEED PRINTS?</h3>
              <v-btn
                class="whatever-btn"
                size="large"
                elevation="0"
              >
                UPLOAD GANG SHEET
              </v-btn>
              <p class="whatever-card-text">
                Upload your print-ready sheet and we'll take it from there. Press quality, quick turnaround. Press with confidence.
              </p>
            </div>
          </v-col>

          <!-- Card 2: Build a Sheet -->
          <v-col cols="12" md="4">
            <div class="whatever-card" data-aos="fade-up" data-aos-delay="200">
              <h3 class="whatever-card-title">GOT PRINT ARTWORK,<br>NEED A GANG SHEET?</h3>
              <v-btn
                class="whatever-btn"
                size="large"
                elevation="0"
              >
                BUILD A SHEET NOW
              </v-btn>
              <p class="whatever-card-text">
                Use our Gang Sheet Builder to arrange your designs exactly how you want them. Upload, size, and send. We'll print it from there.
              </p>
            </div>
          </v-col>

          <!-- Card 3: Request Quote -->
          <v-col cols="12" md="4">
            <div class="whatever-card" data-aos="fade-up" data-aos-delay="300">
              <h3 class="whatever-card-title">GOT A GREAT IDEA,<br>BUT NEED SOME ART?</h3>
              <v-btn
                class="whatever-btn"
                size="large"
                elevation="0"
              >
                REQUEST QUOTE
              </v-btn>
              <p class="whatever-card-text">
                From sketches to full designs, we'll bring your concept to life and professional artwork that's ready to print and built to impress.
              </p>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Ultra Color Section -->
    <section class="ultra-color-section">
      <div class="ultra-color-background">
        <div class="ultra-color-overlay"></div>
        <v-container>
          <div class="ultra-color-content" data-aos="zoom-in">
            <h2 class="ultra-color-title">ULTRA COLOR (9 COLOR DTF) TRANSFERS</h2>
            <p class="ultra-color-text">
              Our Ultra color CMYK - RGBO + WHITE Transfers deliver professional-quality prints that look great and stay strong. Whether you're decorating cotton tees, polyester uniforms, or performance gear - you'll get brilliant, long-lasting decorations every time.
            </p>
       
          </div>
        </v-container>
      </div>
    </section>

    <!-- Setting the Standard Section -->
    <section class="standards-section">
      <v-container>
        <h2 class="standards-title" data-aos="fade-up">Setting the Standard in Custom DTF Transfers</h2>
        
        <div class="standards-grid">
          <!-- Feature 1: Premium Quality -->
          <div class="standard-item" data-aos="flip-up" data-aos-delay="100">
            <div class="standard-icon-wrapper standard-icon-blue">
              <img :src="iconQuality" alt="Premium Quality" class="standard-icon" />
            </div>
            <h3 class="standard-name">Premium Quality</h3>
            <p class="standard-description">
              Vibrant DTF prints made with premium materials
            </p>
          </div>

          <!-- Feature 2: Affordable Pricing -->
          <div class="standard-item" data-aos="flip-up" data-aos-delay="150">
            <div class="standard-icon-wrapper standard-icon-red">
              <img :src="iconPricing" alt="Affordable Pricing" class="standard-icon" />
            </div>
            <h3 class="standard-name">Affordable Pricing</h3>
            <p class="standard-description">
              Offering some of the lowest prices in the industry
            </p>
          </div>

          <!-- Feature 3: Fast Shipping -->
          <div class="standard-item" data-aos="flip-up" data-aos-delay="200">
            <div class="standard-icon-wrapper standard-icon-red">
              <img :src="iconShipping" alt="Fast Shipping" class="standard-icon" />
            </div>
            <h3 class="standard-name">Fast Shipping</h3>
            <p class="standard-description">
              With 24 hour turnaround we get your prints out fast
            </p>
          </div>

          <!-- Feature 4: Hablamos Español -->
          <div class="standard-item" data-aos="flip-up" data-aos-delay="250">
            <div class="standard-icon-wrapper standard-icon-blue">
              <img :src="iconEspanol" alt="Hablamos Español" class="standard-icon" />
            </div>
            <h3 class="standard-name">Hablamos Español</h3>
            <p class="standard-description">
              Llámenos: tenemos hispanohablantes listos para ayudar.
            </p>
          </div>

          <!-- Feature 5: Small Business -->
          <div class="standard-item" data-aos="flip-up" data-aos-delay="300">
            <div class="standard-icon-wrapper standard-icon-red">
              <img :src="iconSmallBiz" alt="Small Business" class="standard-icon" />
            </div>
            <h3 class="standard-name">Small Business</h3>
            <p class="standard-description">
              We take pride in supporting other small business owners
            </p>
          </div>

          <!-- Feature 6: Customer Support -->
          <div class="standard-item" data-aos="flip-up" data-aos-delay="350">
            <div class="standard-icon-wrapper standard-icon-red">
              <img :src="iconSupport" alt="Customer Support" class="standard-icon" />
            </div>
            <h3 class="standard-name">Customer Support</h3>
            <p class="standard-description">
              Whether its help with uploading images or after order support, we're here every step of the way
            </p>
          </div>
        </div>
      </v-container>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
      <v-container>
        <h2 class="faq-title" data-aos="fade-up">Frequently Asked Questions</h2>
        
        <div class="faq-wrapper" data-aos="fade-up" data-aos-delay="200">
          <v-expansion-panels variant="accordion" class="faq-panels">
            <!-- Question 1 -->
            <v-expansion-panel class="faq-panel">
              <v-expansion-panel-title class="faq-question">
                <span class="faq-icon">+</span>
                What is DTF printing?
              </v-expansion-panel-title>
              <v-expansion-panel-text class="faq-answer">
                <div class="faq-answer-content">
                  <h4 class="faq-subtitle">- What is DTF printing?</h4>
                  <p>
                    DTF, or Direct to Film Printing, is an innovative method that produces vibrant, long-lasting designs for a wide range of clothing and materials. Unlike traditional printing methods, DTF offers superior quality and flexibility, making it the go-to choice for businesses and individuals alike.
                  </p>
                  
                  <h4 class="faq-subtitle-bold">The Advantages of DTF Prints</h4>
                  
                  <p><strong>1. Exceptional Quality:</strong> DTF Transfers boast vivid colors and sharp details, ensuring your design pop on any garment.</p>
                  
                  <p><strong>2. Versatility:</strong> DTF printing works on various materials, including cotton, polyester, blends, and more together.</p>
                  
                  <p><strong>3. Durability:</strong> DTF prints withstand multiple washes without fading or cracking, maintaining their quality over time.</p>
                  
                  <p><strong>4. Cost-Effective:</strong> For both small and large orders, DTF printing offers competitive pricing without compromising on quality.</p>
                  
                  <p><strong>5. Eco-Friendly:</strong> DTF technology uses less water and energy compared to traditional printing methods, making it a more sustainable choice.</p>
                </div>
              </v-expansion-panel-text>
            </v-expansion-panel>

            <!-- Question 2 -->
            <v-expansion-panel class="faq-panel">
              <v-expansion-panel-title class="faq-question">
                <span class="faq-icon">+</span>
                What does DTF printing work on?
              </v-expansion-panel-title>
              <v-expansion-panel-text class="faq-answer">
                <div class="faq-answer-content">
                  <h4 class="faq-subtitle">- What does DTF printing work on?</h4>
                  <p>
                    DTF Printing works on shirts, jackets, sweaters, hats, backpacks, home decor, and so much more!
                  </p>
                </div>
              </v-expansion-panel-text>
            </v-expansion-panel>

            <!-- Question 3 -->
            <v-expansion-panel class="faq-panel">
              <v-expansion-panel-title class="faq-question">
                <span class="faq-icon">+</span>
                Why use DTF printing?
              </v-expansion-panel-title>
              <v-expansion-panel-text class="faq-answer">
                <div class="faq-answer-content">
                  <h4 class="faq-subtitle">- Why use DTF printing?</h4>
                  <p>
                    DTF transfers are a game-changer for t-shirt printing businesses and hobbyists alike. Here are a few of reasons. They work on nearly any fabric, handle fine details with ease, and stay vibrant wash after wash. Whether you're making one shirt or a hundred, DTF delivers consistent, professional results every time.
                  </p>
                </div>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </div>
      </v-container>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Usar rutas desde public directamente
const logohome = '/images/home/logohome.webp'
const step1Image = '/images/home/step1.webp'
const step2Image = '/images/home/step2.webp'
const step3Image = '/images/home/step3.webp'
const dtfImage = '/images/home/dtf-process.webp'
const inHouseImage = '/images/home/inhouse-production.webp'
const sample1Image = '/images/home/sample1.webp'
const sample2Image = '/images/home/sample2.webp'
const sample3Image = '/images/home/sample3.webp'
const iconQuality = '/images/home/icon-quality.webp'
const iconPricing = '/images/home/icon-pricing.webp'
const iconShipping = '/images/home/icon-shipping.webp'
const iconEspanol = '/images/home/icon-espanol.webp'
const iconSmallBiz = '/images/home/icon-smallbiz.webp'
const iconSupport = '/images/home/icon-support.webp'



const initAos = () => {
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out-cubic',
      once: true,
      offset: 100
    })
  }
}
onMounted(() => {
  initAos()
})
</script>

<style scoped>
.ca-home {
  width: 100%;
}

/* Hero Section */
.hero-section {
  position: relative;
  width: 100%;
  height: 700px;
  overflow: hidden;
}

.hero-background {
  position: relative;
  width: 100%;
  height: 100%;
  background-image: url('/images/home/hero-background.webp');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: white;
  padding: 40px 20px;
}

.hero-logo {
  margin-bottom: 10px;
  animation: fadeInDown 1s ease-out;
}

.hero-logo img {
  height: 380px;
  width: auto;
  filter: drop-shadow(0 4px 20px rgba(0, 0, 0, 0.5));
}

.hero-title {
  font-size: 48px;
  font-weight: 900;
  letter-spacing: 2px;
  line-height: 1.2;
  margin-bottom: 40px;
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
  animation: fadeInUp 1s ease-out 0.3s backwards;
}

.hero-btn {
  background: #0ea5e9 !important;
  color: white !important;
  font-weight: 700 !important;
  letter-spacing: 1px !important;
  padding: 14px 48px !important;
  font-size: 16px !important;
  border-radius: 4px !important;
  box-shadow: 0 8px 24px rgba(14, 165, 233, 0.4) !important;
  animation: fadeInUp 1s ease-out 0.6s backwards;
  transition: all 0.3s ease !important;
}

.hero-btn:hover {
  background: #0284c7 !important;
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(14, 165, 233, 0.6) !important;
}

/* Info Section */
.info-section {
  background: #ffffff;
  padding: 60px 0;
  border-bottom: 1px solid #e5e7eb;
}

.info-content {
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
}

.info-title {
  font-size: 36px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 16px;
  letter-spacing: 0.5px;
}

.info-subtitle {
  font-size: 18px;
  color: #6b7280;
  margin-bottom: 32px;
  font-weight: 400;
}

.info-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-details p {
  font-size: 16px;
  color: #374151;
  margin: 0;
  font-weight: 500;
}

/* Animations */
@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive */
@media (max-width: 959px) {
  .hero-section {
    height: 450px;
  }

  .hero-logo img {
    height: 100px;
  }

  .hero-title {
    font-size: 36px;
  }

  .hero-btn {
    font-size: 14px !important;
    padding: 20px 32px !important;
  }

  .info-title {
    font-size: 28px;
  }

  .info-subtitle {
    font-size: 16px;
  }
}

@media (max-width: 600px) {
  .hero-section {
    height: 400px;
  }

  .hero-logo img {
    height: 180px;
  }

  .hero-title {
    font-size: 28px;
    margin-bottom: 30px;
  }

  .hero-btn {
    font-size: 13px !important;
    padding: 16px 24px !important;
  }

  .info-section {
    padding: 40px 0;
  }

  .info-title {
    font-size: 24px;
  }

  .info-subtitle {
    font-size: 15px;
  }

  .info-details p {
    font-size: 14px;
  }
}

/* Separator Line */
.section-separator {
  width: 100%;
  height: 1px;
  background: #e5e7eb;
  margin: 0;
}

/* How to Order Section */
.how-to-order-section {
  background: #ffffff;
  padding: 80px 0;
}

.section-title {
  text-align: center;
  font-size: 42px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 60px;
  letter-spacing: 0.5px;
}

.steps-container {
  display: flex;
  justify-content: center;
  align-items: stretch;
}

.step-card {
  background: #ffffff;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 32px 24px;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: all 0.3s ease;
}

.step-card:hover {
  border-color: #0ea5e9;
  box-shadow: 0 8px 24px rgba(14, 165, 233, 0.15);
  transform: translateY(-4px);
}

.step-image {
  width: 100%;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}

.step-image img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.step-number {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 16px;
  letter-spacing: 0.3px;
}

.step-description {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

/* Stats Section */
.stats-section {
  background: #111827;
  padding: 80px 0;
  color: white;
}

.stats-container {
  display: flex;
  justify-content: center;
  align-items: stretch;
  margin-bottom: 40px;
}

.stat-item {
  text-align: center;
  padding: 20px;
}

.stat-number {
  font-size: 64px;
  font-weight: 900;
  color: #0ea5e9;
  margin-bottom: 8px;
  line-height: 1;
}

.stat-title {
  font-size: 22px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 16px;
  letter-spacing: 0.5px;
}

.stat-description {
  font-size: 14px;
  color: #d1d5db;
  line-height: 1.6;
  margin: 0;
}

.stats-cta {
  text-align: center;
  margin-top: 40px;
}

.order-now-btn {
  background: #0ea5e9 !important;
  color: white !important;
  font-weight: 700 !important;
  letter-spacing: 1px !important;
  padding: 14px 64px !important;
  font-size: 18px !important;
  border-radius: 50px !important;
  box-shadow: 0 8px 24px rgba(14, 165, 233, 0.4) !important;
  transition: all 0.3s ease !important;
}

.order-now-btn:hover {
  background: #0284c7 !important;
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(14, 165, 233, 0.6) !important;
}

/* Responsive for new sections */
@media (max-width: 959px) {
  .section-title {
    font-size: 32px;
    margin-bottom: 40px;
  }

  .how-to-order-section,
  .stats-section {
    padding: 60px 0;
  }

  .step-card {
    margin-bottom: 24px;
  }

  .stat-number {
    font-size: 48px;
  }

  .stat-title {
    font-size: 18px;
  }

  .stat-item {
    margin-bottom: 32px;
  }
}

@media (max-width: 600px) {
  .section-title {
    font-size: 28px;
    margin-bottom: 32px;
  }

  .how-to-order-section,
  .stats-section {
    padding: 40px 0;
  }

  .step-image {
    height: 160px;
  }

  .step-number {
    font-size: 16px;
  }

  .step-description {
    font-size: 13px;
  }

  .stat-number {
    font-size: 40px;
  }

  .stat-title {
    font-size: 16px;
  }

  .stat-description {
    font-size: 13px;
  }

  .order-now-btn {
    font-size: 16px !important;
    padding: 20px 48px !important;
  }
}

/* Features Section */
.features-section {
  background: #ffffff;
  padding: 0;
  width: 100%;
}

.feature-row {
  display: flex;
  flex-direction: row;
  width: 100%;
  min-height: 500px;
}

.feature-half {
  flex: 1;
  width: 50%;
}

.feature-image-half {
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px;
  background: #ffffff;
}

.feature-image-half.feature-image-cover {
  padding: 0;
}

.feature-full-image {
  max-width: 400px;
  max-height: 350px;
  width: 100%;
  height: auto;
  object-fit: contain;
  display: block;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.feature-cover-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: 0;
  box-shadow: none;
}

.feature-content-half {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 80px;
  background: #f9fafb;
}

.feature-content-blue-bg {
  background: #0ea5e9 !important;
  padding: 60px 80px;
}

.feature-content-wrapper,
.feature-content-wrapper-blue {
  max-width: 600px;
}

.feature-title {
  font-size: 36px;
  font-weight: 700;
  color: #0ea5e9;
  margin-bottom: 24px;
  letter-spacing: 0.5px;
  line-height: 1.2;
}

.feature-title-white {
  font-size: 32px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 24px;
  letter-spacing: 0.5px;
  line-height: 1.3;
  text-transform: uppercase;
}

.feature-text {
  font-size: 16px;
  color: #374151;
  line-height: 1.8;
  margin-bottom: 20px;
}

.feature-text-white {
  font-size: 16px;
  color: #ffffff;
  line-height: 1.8;
  margin-bottom: 20px;
}

.feature-tagline {
  font-size: 18px;
  color: #111827;
  font-weight: 600;
  margin-top: 24px;
  font-style: italic;
}

/* Responsive for features */
@media (max-width: 959px) {
  .feature-row {
    flex-direction: column;
    min-height: auto;
  }

  .feature-half {
    width: 100%;
  }

  .feature-image-half {
    min-height: auto;
    padding: 40px;
  }

  .feature-full-image {
    max-width: 320px;
    max-height: 280px;
  }

  .feature-content-half,
  .feature-content-blue-bg {
    padding: 50px 40px;
  }

  .feature-title {
    font-size: 28px;
  }

  .feature-title-white {
    font-size: 26px;
  }

  .feature-text,
  .feature-text-white {
    font-size: 15px;
  }

  .feature-tagline {
    font-size: 16px;
  }
}

@media (max-width: 600px) {
  .feature-image-half {
    padding: 30px 20px;
  }

  .feature-full-image {
    max-width: 280px;
    max-height: 240px;
  }

  .feature-content-half,
  .feature-content-blue-bg {
    padding: 40px 24px;
  }

  .feature-title {
    font-size: 24px;
  }

  .feature-title-white {
    font-size: 22px;
  }

  .feature-text,
  .feature-text-white {
    font-size: 14px;
  }

  .feature-tagline {
    font-size: 15px;
  }
}

/* Whatever Section */
.whatever-section {
  background: #ffffff;
  padding: 80px 0;
}

.whatever-header {
  text-align: center;
  max-width: 900px;
  margin: 0 auto 60px;
}

.whatever-title {
  font-size: 36px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 20px;
  letter-spacing: 0.5px;
  line-height: 1.3;
}

.whatever-subtitle {
  font-size: 16px;
  color: #6b7280;
  line-height: 1.7;
  margin: 0;
}

.whatever-cards {
  display: flex;
  justify-content: center;
  align-items: stretch;
}

.whatever-card {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0;
  padding: 40px 28px;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: all 0.3s ease;
}

.whatever-card:hover {
  border-color: #9ca3af;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.whatever-card-title {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 24px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  line-height: 1.4;
  min-height: 48px;
}

.whatever-btn {
  background: #0ea5e9 !important;
  color: white !important;
  font-weight: 700 !important;
  letter-spacing: 0.5px !important;
  padding: 12px 32px !important;
  font-size: 13px !important;
  border-radius: 0 !important;
  margin-bottom: 24px;
  transition: all 0.3s ease !important;
}

.whatever-btn:hover {
  background: #0284c7 !important;
  transform: translateY(-2px);
}

.whatever-card-text {
  font-size: 13px;
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

/* Ultra Color Section */
.ultra-color-section {
  position: relative;
  width: 100%;
  overflow: hidden;
  padding: 0;
}

.ultra-color-background {
  position: relative;
  width: 100%;
  min-height: 600px;
  background-image: url('/images/home/ultra-color-bg.webp');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 80px 0;
}

.ultra-color-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.65);
  z-index: 1;
}

.ultra-color-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: white;
  max-width: 900px;
  margin: 0 auto;
}

.ultra-color-title {
  font-size: 42px;
  font-weight: 900;
  color: #ffffff;
  margin-bottom: 28px;
  letter-spacing: 1px;
  line-height: 1.2;
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
}

.ultra-color-text {
  font-size: 17px;
  color: #ffffff;
  line-height: 1.8;
  margin-bottom: 50px;
  text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
}

.ultra-color-samples {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 30px;
  flex-wrap: wrap;
}

.sample-image {
  width: 200px;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
  transition: transform 0.3s ease;
}

.sample-image:hover {
  transform: scale(1.05);
}

/* Responsive for new sections */
@media (max-width: 959px) {
  .whatever-section {
    padding: 60px 0;
  }

  .whatever-title {
    font-size: 28px;
  }

  .whatever-subtitle {
    font-size: 15px;
  }

  .whatever-header {
    margin-bottom: 40px;
  }

  .whatever-card {
    margin-bottom: 24px;
  }

  .ultra-color-background {
    min-height: 500px;
    padding: 60px 0;
  }

  .ultra-color-title {
    font-size: 32px;
  }

  .ultra-color-text {
    font-size: 16px;
    margin-bottom: 40px;
  }

  .sample-image {
    width: 160px;
    height: 160px;
  }
}

@media (max-width: 600px) {
  .whatever-section {
    padding: 40px 0;
  }

  .whatever-title {
    font-size: 24px;
  }

  .whatever-subtitle {
    font-size: 14px;
  }

  .whatever-card {
    padding: 32px 20px;
  }

  .whatever-card-title {
    font-size: 14px;
    min-height: auto;
  }

  .whatever-btn {
    font-size: 12px !important;
    padding: 10px 24px !important;
  }

  .whatever-card-text {
    font-size: 12px;
  }

  .ultra-color-background {
    min-height: 450px;
    padding: 40px 0;
  }

  .ultra-color-title {
    font-size: 26px;
  }

  .ultra-color-text {
    font-size: 15px;
    margin-bottom: 30px;
  }

  .ultra-color-samples {
    gap: 20px;
  }

  .sample-image {
    width: 140px;
    height: 140px;
  }
}

/* Setting the Standard Section */
.standards-section {
  background: #ffffff;
  padding: 80px 0;
  border-top: 3px solid #111827;
  border-bottom: 3px solid #111827;
}

.standards-title {
  text-align: center;
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 60px;
  letter-spacing: 0.3px;
}

.standards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 50px 40px;
  max-width: 1100px;
  margin: 0 auto;
}

.standard-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.standard-icon-wrapper {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  transition: transform 0.3s ease;
}

.standard-icon-wrapper:hover {
  transform: scale(1.05);
}

.standard-icon-blue {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
}

.standard-icon-red {
  background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.standard-icon {
  width: 60px;
  height: 60px;
  object-fit: contain;
}

.standard-name {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 12px;
  letter-spacing: 0.3px;
}

.standard-description {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
  max-width: 280px;
}

/* Responsive for standards */
@media (max-width: 959px) {
  .standards-section {
    padding: 60px 0;
  }

  .standards-title {
    font-size: 26px;
    margin-bottom: 40px;
  }

  .standards-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 40px 30px;
  }

  .standard-icon-wrapper {
    width: 90px;
    height: 90px;
  }

  .standard-icon {
    width: 55px;
    height: 55px;
  }

  .standard-name {
    font-size: 16px;
  }

  .standard-description {
    font-size: 13px;
  }
}

@media (max-width: 600px) {
  .standards-section {
    padding: 40px 0;
  }

  .standards-title {
    font-size: 22px;
    margin-bottom: 32px;
  }

  .standards-grid {
    grid-template-columns: 1fr;
    gap: 35px;
  }

  .standard-icon-wrapper {
    width: 80px;
    height: 80px;
  }

  .standard-icon {
    width: 50px;
    height: 50px;
  }

  .standard-name {
    font-size: 16px;
  }

  .standard-description {
    font-size: 13px;
  }
}

/* FAQ Section */
.faq-section {
  background: #ffffff;
  padding: 80px 0;
}

.faq-title {
  text-align: center;
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 50px;
  letter-spacing: 0.3px;
}

.faq-wrapper {
  max-width: 900px;
  margin: 0 auto;
}

.faq-panels {
  border: 2px solid #111827;
}

.faq-panel {
  border-bottom: 1px solid #111827;
  background: #ffffff !important;
}

.faq-panel:last-child {
  border-bottom: none;
}

.faq-question {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  padding: 20px 24px;
  background: #ffffff !important;
  min-height: auto !important;
}

.faq-question:hover {
  background: #f9fafb !important;
}

.faq-icon {
  font-size: 20px;
  font-weight: 700;
  color: #111827;
  margin-right: 12px;
  transition: transform 0.3s ease;
}

.v-expansion-panel--active .faq-icon {
  transform: rotate(45deg);
}

.faq-answer {
  background: #ffffff !important;
}

.faq-answer-content {
  padding: 20px 24px;
  border-top: 1px solid #e5e7eb;
}

.faq-answer-content h4 {
  margin-bottom: 16px;
  color: #111827;
}

.faq-subtitle {
  font-size: 15px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 12px;
}

.faq-subtitle-bold {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  margin-top: 20px;
  margin-bottom: 16px;
}

.faq-answer-content p {
  font-size: 14px;
  color: #4b5563;
  line-height: 1.7;
  margin-bottom: 14px;
}

.faq-answer-content p:last-child {
  margin-bottom: 0;
}

.faq-answer-content p strong {
  color: #111827;
  font-weight: 600;
}

/* Override Vuetify expansion panel styles */
.faq-panels :deep(.v-expansion-panel-title__overlay) {
  display: none !important;
}

.faq-panels :deep(.v-expansion-panel-title) {
  padding: 20px 24px;
}

.faq-panels :deep(.v-expansion-panel-text__wrapper) {
  padding: 0;
}

/* Responsive for FAQ */
@media (max-width: 959px) {
  .faq-section {
    padding: 60px 0;
  }

  .faq-title {
    font-size: 26px;
    margin-bottom: 40px;
  }

  .faq-question {
    font-size: 15px;
    padding: 18px 20px;
  }

  .faq-answer-content {
    padding: 18px 20px;
  }
}

@media (max-width: 600px) {
  .faq-section {
    padding: 40px 0;
  }

  .faq-title {
    font-size: 22px;
    margin-bottom: 32px;
  }

  .faq-question {
    font-size: 14px;
    padding: 16px 18px;
  }

  .faq-icon {
    font-size: 18px;
    margin-right: 10px;
  }

  .faq-answer-content {
    padding: 16px 18px;
  }

  .faq-subtitle,
  .faq-subtitle-bold {
    font-size: 14px;
  }

  .faq-answer-content p {
    font-size: 13px;
  }
}
</style>