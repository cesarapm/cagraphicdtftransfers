<script setup>
const nextGenA = '/images/componets/NEXT_GENERATION_A.webp'
const nextGenB = '/images/componets/NEXT_GENERATION_B.webp'
</script>

<template>
  <section class="dtf-education-section">
    <div class="dtf-education-container">
      <!-- Title -->
      <h2 class="dtf-title">Mastering the Art of Heat Pressing DTF Transfers</h2>

      <!-- Description -->
      <p class="dtf-description">
        Heat pressing Direct-to-Film (DTF) transfers is essential for achieving vibrant, durable, and professional-quality designs on garments. By using the proper techniques and settings, you can ensure each transfer delivers outstanding color, excellent adhesion, and long-lasting performance. Follow the guidelines below for the best results.
      </p>

      <!-- Section with Items -->
      <div class="dtf-section">
        <h3 class="dtf-section-title">Materials Needed</h3>

        <!-- List Items -->
        <ul class="dtf-items-list">
          <li class="dtf-item">Heat Press Machine (Air Compressor, Electric, Manual, or Home Iron)</li>
          <li class="dtf-item">Direct-to-Film (DTF) Transfers</li>
          <li class="dtf-item">Garment or Fabric for Application</li>
          <li class="dtf-item">Teflon Sheet or Parchment Paper (Optional)</li>
        </ul>
      </div>

      <!-- Conclusion -->
      <p class="dtf-conclusion">
        Using the right equipment and preparation methods will help ensure a smooth application process and a high-quality finished product.
      </p>
    </div>
  </section>

  <!-- ULTRA COLOR SECTION -->
  <section class="ultra-color-section">
    <div class="ultra-color-container">
      <!-- Header -->
      <h2 class="ultra-color-title">The Next Generation of DTF Transfers</h2>

      <!-- Description -->
      <div class="ultra-color-description">
        <p class="description-main">
          Introducing <strong>ULTRA COLOR™ by CA Graphic DTF Transfers</strong>—our next-generation <strong>9-color DTF printing</strong> technology. Compared to traditional 5-color DTF printing, ULTRA COLOR delivers <strong>richer colors, smoother gradients, and more accurate color reproduction</strong> at no extra charge.
        </p>

        <p class="description-highlight">
          Same great pricing. Same press settings. Same durability. Better color. Better results.
        </p>
      </div>

      <!-- Comparison Images -->
      <div class="comparison-grid">
        <!-- Left: ULTRA COLOR -->
        <div class="comparison-card">
          <div class="image-label ultra-label">
            <span>ULTRA COLOR 9-Color DTF</span>
            <span class="label-colors">CMYK + RBGO + White</span>
          </div>
          <img :src="nextGenA" alt="ULTRA COLOR 9-Color DTF" class="comparison-image" />
        </div>

        <!-- Right: Industry Standard -->
        <div class="comparison-card">
          <div class="image-label standard-label">
            <span>Industry Standard 5-Color DTF</span>
            <span class="label-colors">CMYK + White</span>
          </div>
          <img :src="nextGenB" alt="Industry Standard 5-Color DTF" class="comparison-image" />
        </div>
      </div>
    </div>
  </section>

  <!-- COMPARISON TABLE SECTION -->
  <section class="comparison-table-section">
    <div class="comparison-table-container">
      <!-- Comparison Table -->
      <div class="comparison-table-wrapper">
        <table class="comparison-table">
          <thead>
            <tr>
              <th class="ultra-header">
                <div class="header-title">CA Graphic DTF Transfers</div>
                <div class="header-subtitle">ULTRA COLOR 9-COLOR DTF Printing</div>
              </th>
              <th class="competitor-header">
                <div class="header-title">Competitor's Standard DTF</div>
                <div class="header-subtitle">5-Color Printing</div>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="ultra-feature">
                <span class="checkmark">✓</span>
                <span>9-Color Printing (CMYK + RGBO + White)</span>
              </td>
              <td class="competitor-feature">
                <span class="x-mark">✕</span>
                <span>5-Color Printing (CMYK)</span>
              </td>
            </tr>
            <tr>
              <td class="ultra-feature">
                <span class="checkmark">✓</span>
                <span>16.7 Million Colors</span>
              </td>
              <td class="competitor-feature">
                <span class="x-mark">✕</span>
                <span>-20,000 Colors</span>
              </td>
            </tr>
            <tr>
              <td class="ultra-feature">
                <span class="checkmark">✓</span>
                <span>Noticeably Brighter & More Saturated Prints</span>
              </td>
              <td class="competitor-feature">
                <span class="x-mark">✕</span>
                <span>Muted / Dull Prints</span>
              </td>
            </tr>
            <tr>
              <td class="ultra-feature">
                <span class="checkmark">✓</span>
                <span>Near Perfect Pantone Matching</span>
              </td>
              <td class="competitor-feature">
                <span class="x-mark">✕</span>
                <span>Inconsistent / Washed Out</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- REVIEWS SECTION -->
  <section class="reviews-section">
    <div class="reviews-container">
      <h2 class="reviews-title">5 Star DTF</h2>
      <h3 class="reviews-subtitle">Transfer Reviews</h3>
      <p class="reviews-description">Verified Feedback from Authentic Customers</p>
    </div>
  </section>

  </template>
  

<style scoped>
.dtf-education-section {
  background: #000000;
  color: #ffffff;
  padding: 4rem 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dtf-education-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.dtf-title {
  font-size: 2.5rem;
  font-weight: 300;
  letter-spacing: 1px;
  margin-bottom: 2rem;
  text-align: left;
  line-height: 1.3;
}

.dtf-description {
  font-size: 1rem;
  line-height: 1.8;
  color: #e0e0e0;
  margin-bottom: 3rem;
  max-width: 900px;
  text-align: justify;
}

.dtf-section {
  margin-bottom: 3rem;
}

.dtf-section-title {
  font-size: 1.75rem;
  font-weight: 300;
  letter-spacing: 0.5px;
  margin-bottom: 1.5rem;
}

.dtf-items-list {
  list-style: none;
  padding: 0;
  margin-bottom: 1.5rem;
}

.dtf-item {
  font-size: 1rem;
  line-height: 1.8;
  margin-bottom: 0.75rem;
  padding-left: 1.5rem;
  position: relative;
  color: #e0e0e0;
}

.dtf-item::before {
  content: '•';
  position: absolute;
  left: 0;
  color: #ffffff;
}

.dtf-conclusion {
  font-size: 1rem;
  line-height: 1.8;
  color: #e0e0e0;
  max-width: 900px;
  text-align: justify;
}

/* Responsive */
@media (max-width: 1024px) {
  .dtf-education-section {
    padding: 3rem 1rem;
  }

  .dtf-education-container {
    padding: 0 1rem;
  }

  .dtf-title {
    font-size: 2rem;
  }

  .dtf-section-title {
    font-size: 1.5rem;
  }
}

@media (max-width: 768px) {
  .dtf-education-section {
    padding: 2rem 1rem;
  }

  .dtf-title {
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
  }

  .dtf-description {
    font-size: 0.95rem;
    margin-bottom: 2rem;
  }

  .dtf-section-title {
    font-size: 1.25rem;
  }

  .dtf-item {
    font-size: 0.95rem;
  }
}

/* ULTRA COLOR SECTION STYLES */
.ultra-color-section {
  background: #ffffff;
  padding: 4rem 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.ultra-color-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.ultra-color-title {
  font-size: 2.75rem;
  font-weight: 400;
  text-align: center;
  margin-bottom: 2rem;
  color: #1f2937;
}

.ultra-color-description {
  text-align: center;
  margin-bottom: 3rem;
}

.description-main {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #374151;
  margin-bottom: 1.5rem;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
}

.description-main strong {
  color: #1f2937;
  font-weight: 600;
}

.description-highlight {
  font-size: 1.15rem;
  font-weight: 500;
  color: #1f2937;
  margin-top: 1rem;
}

.comparison-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-top: 3rem;
}

.comparison-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.comparison-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.image-label {
  position: absolute;
  top: 1rem;
  left: 1rem;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  z-index: 10;
  color: white;
  backdrop-filter: blur(10px);
}

.ultra-label {
  background: linear-gradient(135deg, #a855f7 0%, #06b6d4 100%);
}

.standard-label {
  background: rgba(107, 114, 128, 0.8);
}

.label-colors {
  font-size: 0.8rem;
  opacity: 0.9;
  font-weight: 500;
}

.comparison-image {
  width: 100%;
  height: auto;
  display: block;
  object-fit: cover;
}

@media (max-width: 1024px) {
  .ultra-color-section {
    padding: 3rem 1rem;
  }

  .ultra-color-container {
    padding: 0 1rem;
  }

  .ultra-color-title {
    font-size: 2.25rem;
  }

  .comparison-grid {
    gap: 1.5rem;
  }
}

@media (max-width: 768px) {
  .ultra-color-section {
    padding: 2rem 1rem;
  }

  .ultra-color-title {
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
  }

  .description-main {
    font-size: 1rem;
  }

  .description-highlight {
    font-size: 1rem;
  }

  .comparison-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .image-label {
    top: 0.75rem;
    left: 0.75rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
  }

  .label-colors {
    font-size: 0.75rem;
  }
}

/* COMPARISON TABLE SECTION */
.comparison-table-section {
  background: #ffffff;
  padding: 4rem 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.comparison-table-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.comparison-table-wrapper {
  overflow-x: auto;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.comparison-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
  line-height: 1.6;
}

.comparison-table thead tr {
  background: #000000;
  color: #ffffff;
}

.comparison-table th {
  padding: 1.5rem;
  text-align: left;
  font-weight: 600;
  border-bottom: 3px solid #ffffff;
}

.ultra-header {
  border-right: 2px solid #ffffff;
}

.competitor-header {
  border-left: 2px solid #ffffff;
}

.header-title {
  font-size: 1.1rem;
  margin-bottom: 0.25rem;
}

.header-subtitle {
  font-size: 0.95rem;
  font-weight: 400;
  opacity: 0.9;
}

.comparison-table tbody tr {
  border-bottom: 1px solid #e5e7eb;
}

.comparison-table tbody tr:nth-child(even) {
  background: #f3f4f6;
}

.comparison-table tbody tr:hover {
  background: #e5e7eb;
}

.comparison-table td {
  padding: 1.5rem;
  vertical-align: top;
  text-align: left;
  width: 50%;
}

.ultra-feature {
  border-right: 2px solid #d1d5db;
  color: #1f2937;
  background: #f9fafb;
}

.competitor-feature {
  color: #1f2937;
  background: #ffffff;
}

.checkmark, .x-mark {
  font-size: 1.25rem;
  font-weight: bold;
  margin-right: 0.75rem;
  display: inline-block;
  vertical-align: middle;
}

.checkmark {
  color: #0ea5e9;
}

.x-mark {
  color: #ef4444;
}

/* REVIEWS SECTION */
.reviews-section {
  background: #f9fafb;
  padding: 4rem 2rem;
  text-align: center;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.reviews-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.reviews-title {
  font-size: 3rem;
  font-weight: 300;
  color: #1f2937;
  margin-bottom: 0.5rem;
  letter-spacing: 1px;
}

.reviews-subtitle {
  font-size: 2rem;
  font-weight: 400;
  color: #1f2937;
  margin-bottom: 1rem;
}

.reviews-description {
  font-size: 1rem;
  color: #6b7280;
  font-weight: 500;
}

@media (max-width: 1024px) {
  .comparison-table-container {
    padding: 0 1rem;
  }

  .comparison-table {
    font-size: 0.95rem;
  }

  .comparison-table th,
  .comparison-table td {
    padding: 1rem;
  }

  .reviews-title {
    font-size: 2.25rem;
  }

  .reviews-subtitle {
    font-size: 1.5rem;
  }
}

@media (max-width: 768px) {
  .comparison-table-section {
    padding: 2rem 1rem;
  }

  .comparison-table {
    font-size: 0.85rem;
  }

  .comparison-table th,
  .comparison-table td {
    padding: 0.75rem;
  }

  .header-title {
    font-size: 0.95rem;
  }

  .header-subtitle {
    font-size: 0.8rem;
  }

  .ultra-header,
  .ultra-feature {
    border-right: none;
  }

  .comparison-table thead tr {
    flex-direction: column;
  }

  .comparison-table tbody tr {
    display: flex;
    flex-direction: column;
    border-bottom: 2px solid #e5e7eb;
  }

  .comparison-table td {
    border-right: none;
    display: flex;
    gap: 0.5rem;
  }

  .reviews-section {
    padding: 2rem 1rem;
  }

  .reviews-title {
    font-size: 1.75rem;
  }

  .reviews-subtitle {
    font-size: 1.25rem;
  }
}

</style>
