<template>
  <div class="heat-press-guide">
    <!-- Hero Section -->
    <section class="hero-section">
      <!-- <h1>Heat Press Guide</h1>
      <p class="hero-subtitle">Master the Art of Heat Pressing DTF Transfers</p> -->
    </section>

    <!-- Main Content -->
    <div class="guide-content">
      <!-- Introduction -->
      <section class="guide-section">
        <h2>Mastering the Art of Heat Pressing DTF Transfers</h2>
        <p>
          Heat pressing Direct-to-Film (DTF) transfers is key to achieving bold, durable designs 
          on garments. With the right technique and settings, you can ensure that each transfer 
          looks vivid and stands up to wear. Here, we'll walk you through the process for optimal results.
        </p>
      </section>

      <!-- Materials Section -->
      <section class="guide-section">
        <h2>Materials Needed</h2>
        <ul class="materials-list">
          <li>Heat Press Machine (Air Compressor, Electric, Manual, Home Irons)</li>
          <li>Direct-to-Film (DTF) Transfers</li>
          <li>Clothing for Transfer</li>
          <li>Teflon Sheet or Parchment Paper (Optional)</li>
        </ul>
      </section>

      <!-- Steps Section -->
      <section class="guide-section steps-section">
        <div class="step">
          <h3>1. Preparing Your Heat Press</h3>
          <p>
            Set your heat press temperature to between 275°F. This temperature range is ideal 
            for cotton and polyester garments, promoting a secure bond without risking damage 
            to the transfer or the fabric. Note: Different materials may require adjustments to 
            temperature, pressure, or time for best results.
          </p>
        </div>

        <div class="step">
          <h3>2. Initial Press</h3>
          <ul class="step-list">
            <li>Position your garment on the heat press, and align the DTF transfer where you want the design.</li>
            <li>For air compressor presses, set the pressure to 70-80 psi; for electric presses, set the pressure to level 4. With manual presses, apply firm, even pressure.</li>
            <li>Press the transfer onto the garment for 12-15 seconds. This brief but firm press is enough to bond the transfer securely.</li>
          </ul>
        </div>

        <div class="step">
          <h3>3. Peeling the Film</h3>
          <p>
            As soon as the press releases, immediately peel the film off the transfer to reveal 
            your design. This instant peel ensures a crisp finish. Adjustments may be needed for 
            specific transfer requirements, but this method generally yields the best results.
          </p>
        </div>

        <div class="step">
          <h3>4. Second Press (Recommended for Added Durability)</h3>
          <p>
            For a smoother finish and enhanced durability, place a Teflon sheet or parchment paper over the transferred design. Press again using the same settings as the initial press (275°F and 12-15 seconds). This second press further secures the design to the fabric for a professional, long-lasting finish.
          </p>
        </div>

        <div class="step">
          <h3>5. Final Cooling</h3>
          <p>
            Remove the garment from the press and allow it to cool completely. Your DTF transfer is now ready, looking vibrant and professionally applied!
          </p>
        </div>
      </section>

      <!-- Tips for Success Section -->
      <section class="guide-section">
        <h2>Tips for Success</h2>
        <ul class="tips-list">
          <li><strong>Ensure Even Pressure:</strong> Make sure your press applies uniform pressure across the entire transfer to avoid patchy or uneven results.</li>
          <li><strong>Quality Transfers:</strong> Using high-quality DTF transfers (like our 😊) will yield the best appearance and longevity.</li>
          <li><strong>Test Runs:</strong> If you're working with a new fabric type, conduct a test press to adjust heat, pressure, and timing accordingly.</li>
        </ul>
      </section>

      <!-- Conclusion Section -->
      <section class="guide-section">
        <h2>Conclusion</h2>
        <p>
          Perfecting your heat press technique for DTF transfers allows for professional, long-lasting results on any garment. By following these steps—whether using an air compressor, electric, or manual press—you'll achieve vibrant and durable prints that impress. Experiment and practice to find the perfect balance for your materials, and enjoy the results of mastering this essential skill.
        </p>
      </section>

      <!-- Care Instructions Section -->
      <section class="care-instructions-section">
        <h2>CA Graphic DTF Transfers - Care Instructions</h2>
        <div class="care-instructions-grid">
          <div class="care-column">
            <h3>Garment Care Instructions</h3>
            <ul class="care-list">
              <li>Wash garments inside out</li>
              <li>Use cold water</li>
              <li>Low heat tumble dry or hang dry</li>
              <li>Do not bleach</li>
              <li>Do not iron directly over the design</li>
            </ul>
          </div>
          <div class="care-column">
            <h3>Important Notices</h3>
            <ul class="care-list">
              <li>Wait at least 24 hours before washing after pressing</li>
              <li>Avoid fabric softeners for best longevity</li>
              <li>Proper care will extend the life of the transfer</li>
            </ul>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
        <!-- <section class="guide-section cta-section">
          <h2>Ready to Get Started?</h2>
          <p>Use our tools to build and upload your DTF transfer sheets today!</p>
          <div class="cta-buttons">
            <a href="/upload" class="cta-btn primary">Upload a Sheet</a>
            <a href="/build" class="cta-btn secondary">Build a Sheet</a>
          </div>
        </section> -->
    </div>
  </div>
</template>

<script setup>
// Los meta tags se sirven desde Laravel (welcome.blade.php)
// No es necesario modificarlos con JavaScript aquí
</script>

<style scoped>
/* Hero Section */
.hero-section {

  background-image: url('/images/portadas/Heat-Press-Guide.webp');
  background-size: cover;
  background-position: center;
  color: #ffffff;
  padding: 250px 20px;
  text-align: center;


    position: relative;
  height: 622px;

 
  background-size: cover; /* ← cambia esto */
  background-repeat: no-repeat;
  background-position: center;

  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-section h1 {
  font-size: 48px;
  font-weight: 700;
  margin: 0 0 16px 0;
}

.hero-subtitle {
  font-size: 18px;
  color: #0ea5e9;
  margin: 0;
}

/* Content Container */
.guide-content {
  max-width: 900px;
  margin: 0 auto;
  padding: 60px 20px;
}

/* Guide Sections */
.guide-section {
  margin-bottom: 50px;
}

.guide-section h2 {
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 20px;
  border-bottom: 2px solid #0ea5e9;
  padding-bottom: 12px;
}

.guide-section h3 {
  font-size: 20px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 12px;
  margin-top: 0;
}

.guide-section p {
  font-size: 15px;
  line-height: 1.7;
  color: #4b5563;
  margin-bottom: 12px;
}

/* Materials List */
.materials-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.materials-list li {
  font-size: 15px;
  color: #4b5563;
  padding: 10px 0;
  padding-left: 24px;
  position: relative;
  line-height: 1.6;
}

.materials-list li:before {
  content: "•";
  position: absolute;
  left: 0;
  color: #0ea5e9;
  font-weight: bold;
  font-size: 18px;
}

/* Steps */
.steps-section {
  background: #f9fafb;
  padding: 30px;
  border-radius: 8px;
}

.step {
  margin-bottom: 30px;
}

.step:last-child {
  margin-bottom: 0;
}

.step-list {
  list-style: none;
  padding: 0;
  margin: 12px 0;
}

.step-list li {
  font-size: 14px;
  color: #4b5563;
  padding: 8px 0;
  padding-left: 24px;
  position: relative;
  line-height: 1.6;
}

.step-list li:before {
  content: "•";
  position: absolute;
  left: 0;
  color: #0ea5e9;
  font-weight: bold;
}

/* Tips List */
.tips-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.tips-list li {
  font-size: 15px;
  color: #4b5563;
  padding: 12px 0;
  padding-left: 24px;
  position: relative;
  line-height: 1.7;
}

.tips-list li:before {
  content: "•";
  position: absolute;
  left: 0;
  color: #0ea5e9;
  font-weight: bold;
  font-size: 18px;
}

/* Care Instructions Section */
.care-instructions-section {  
  color:black;
  padding: 40px;
  border-radius: 8px;
  margin-bottom: 50px;
}

.care-instructions-section h2 {
  color: #000000;
  font-size: 28px;
  font-weight: 700;

}

.care-instructions-section h3 {
  color: #ffffff;
}

.care-instructions-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  margin-top: 30px;
}

.care-column h3 {
  font-size: 18px;
  font-weight: 600;
  color: #000000;
  margin-bottom: 16px;
  margin-top: 0;
}

.care-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.care-list li {
  font-size: 15px;
  color: #010101;
  padding: 10px 0;
  padding-left: 24px;
  position: relative;
  line-height: 1.6;
}

.care-list li:before {
  content: "•";
  position: absolute;
  left: 0;
  color: #0ea5e9;
  font-weight: bold;
  font-size: 18px;
}

/* CTA Section */
.cta-section {
  background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
  color: white;
  padding: 40px;
  border-radius: 8px;
  text-align: center;
}

.cta-section h2,
.cta-section p {
  color: white;
}

.cta-section h2 {
  border-bottom-color: rgba(255, 255, 255, 0.2);
}

.cta-buttons {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 24px;
}

.cta-btn {
  padding: 12px 32px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  font-size: 15px;
  transition: all 0.3s ease;
  display: inline-block;
}

.cta-btn.primary {
  background: white;
  color: #0284c7;
}

.cta-btn.primary:hover {
  background: #f0f9ff;
  transform: translateY(-2px);
}

.cta-btn.secondary {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.cta-btn.secondary:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .hero-section {
    padding: 20% 16px;
    height: auto;
  }

  .hero-section h1 {
    font-size: 32px;
  }

  .hero-subtitle {
    font-size: 16px;
  }

  .guide-content {
    padding: 40px 16px;
  }

  .guide-section h2 {
    font-size: 24px;
  }

  .guide-section h3 {
    font-size: 18px;
  }

  .steps-section {
    padding: 20px;
  }

  .cta-buttons {
    flex-direction: column;
    gap: 12px;
  }

  .cta-btn {
    width: 100%;
  }

  .care-instructions-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .care-instructions-section {
    padding: 24px;
  }
}
</style>