<template>
  <div class="about-page">
    <section class="about-hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="10" class="text-center">
            <div class="hero-emblem">Sobre Nosotros</div>
            <h1 class="hero-title">Nuestra Historia</h1>
            <p class="hero-text">
              Bienvenido a nuestra tienda. Somos un e-commerce comprometido con ofrecer 
              productos de calidad y un servicio excepcional a nuestros clientes.
            </p>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="story-section">
      <v-container class="py-14">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="section-heading text-center mb-10">
              <span class="section-kicker">Quiénes Somos</span>
              <h2>Nuestra Misión</h2>
            </div>
            <div class="story-grid">
              <article class="story-card story-card--wide">
                <p>
                  Somos una empresa dedicada a ofrecer productos de alta calidad. 
                  Nuestro compromiso es brindar la mejor experiencia de compra a nuestros clientes.
                </p>
                <p>
                  Trabajamos con pasión y dedicación para que cada compra sea una experiencia 
                  satisfactoria. Tu confianza es nuestro mayor logro.
                </p>
              </article>
              <article class="story-card">
                <h3>Nuestra Promesa</h3>
                <p>
                  Calidad, servicio y atención son nuestros pilares fundamentales.
                  Estamos aquí para servirte.
                </p>
              </article>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="purpose-section">
      <v-container class="py-14">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="section-heading text-center mb-10">
              <span class="section-kicker">Misión, Visión y Valores</span>
              <h2>Lo que nos guía</h2>
            </div>

            <div class="purpose-grid mb-8">
              <article class="purpose-card">
                <h3>Misión</h3>
                <p>
                  Proporcionar productos de calidad que satisfagan las necesidades de nuestros 
                  clientes, con un servicio excepcional y comprometido con la excelencia.
                </p>
              </article>

              <article class="purpose-card">
                <h3>Visión</h3>
                <p>
                  Ser la tienda de referencia en nuestro sector, reconocida por la calidad 
                  de nuestros productos y la satisfacción de nuestros clientes.
                </p>
              </article>
            </div>

            <div class="values-card">
              <h3>Valores</h3>
              <div class="values-grid">
                <div class="value-item">
                  <strong>Calidad</strong>
                  <p>Ofrecemos solo productos de la más alta calidad.</p>
                </div>
                <div class="value-item">
                  <strong>Servicio</strong>
                  <p>Atención personalizada y compromiso con nuestros clientes.</p>
                </div>
                <div class="value-item">
                  <strong>Innovación</strong>
                  <p>Buscamos constantemente mejorar y ofrecer lo mejor.</p>
                </div>
                <div class="value-item">
                  <strong>Confianza</strong>
                  <p>Construimos relaciones duraderas basadas en la confianza.</p>
                </div>
                <div class="value-item">
                  <strong>Responsabilidad</strong>
                  <p>Comprometidos con nuestros clientes y el medio ambiente.</p>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>
  </div>
</template>

<style scoped>
.about-page {
  margin-top: 70px;
  background: linear-gradient(180deg, #faf7f4 0%, #f4efe9 100%);
  color: #5f5244;
}

.about-hero {
  background:
    radial-gradient(circle at top left, rgba(217, 200, 181, 0.45), transparent 28%),
    linear-gradient(180deg, #f8f4ef 0%, #f3ece5 100%);
}

.hero-emblem,
.section-kicker {
  display: inline-block;
  padding: 0.45rem 1rem;
  border-radius: 999px;
  background: rgba(184, 151, 120, 0.12);
  color: #8c745f;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.78rem;
}

.hero-title,
.section-heading h2,
.story-card h3,
.founder-card h3,
.purpose-card h3,
.values-card h3 {
  color: #6b5b47;
  letter-spacing: 0.04em;
}

.hero-title {
  font-size: clamp(2.8rem, 7vw, 4.8rem);
  margin: 1.25rem 0 1rem;
}

.hero-text,
.story-card p,
.founder-card p,
.purpose-card p,
.value-item p {
  font-size: 1.02rem;
  line-height: 1.9;
  color: #7f6d5a;
}

.hero-text {
  max-width: 760px;
  margin: 0 auto;
}

.hero-quote {
  margin-top: 1.5rem;
  color: #8c745f;
  font-size: 1.15rem;
}

.story-grid,
.purpose-grid,
.founders-grid,
.values-grid {
  display: grid;
  gap: 1.5rem;
}

.story-grid,
.purpose-grid {
  grid-template-columns: 1.5fr 1fr;
}

.founders-grid {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.values-grid {
  grid-template-columns: repeat(2, minmax(0, 1fr));
  margin-top: 1.5rem;
}

.story-card,
.founder-card,
.purpose-card,
.values-card {
  background: rgba(255, 252, 248, 0.88);
  border: 1px solid rgba(184, 151, 120, 0.16);
  border-radius: 28px;
  padding: 2rem;
  box-shadow: 0 18px 40px rgba(140, 116, 95, 0.08);
}

.founder-card {
  padding: 1.2rem;
}

.founder-media {
  position: relative;
  border-radius: 22px;
  overflow: hidden;
  margin-bottom: 1.4rem;
  min-height: 420px;
  background: linear-gradient(180deg, #eadfD4 0%, #c9b29c 100%);
  box-shadow: 0 20px 40px rgba(107, 91, 71, 0.14);
}

.founder-media::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(35, 24, 17, 0.04) 0%, rgba(35, 24, 17, 0.3) 100%);
  pointer-events: none;
}

.founder-photo {
  display: block;
  width: 100%;
  height: 100%;
  min-height: 420px;
  object-fit: cover;
}

.founder-media--romero .founder-photo {
  object-position: center 20%;
}

.founder-media--izaguirre .founder-photo {
  object-position: center 18%;
}

.founder-photo-badge {
  position: absolute;
  left: 1rem;
  bottom: 1rem;
  z-index: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 3.5rem;
  padding: 0.55rem 0.9rem;
  border-radius: 999px;
  background: rgba(255, 250, 244, 0.78);
  border: 1px solid rgba(255, 250, 244, 0.7);
  color: #6b5b47;
  letter-spacing: 0.12em;
  font-size: 0.78rem;
  backdrop-filter: blur(10px);
}

.founder-tag {
  display: block;
  margin-bottom: 0.8rem;
  color: #9a866f;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  font-size: 0.75rem;
}

.value-item {
  padding: 1rem 1.1rem;
  border-radius: 18px;
  background: rgba(245, 239, 233, 0.7);
}

.value-item strong {
  color: #6b5b47;
  display: block;
  margin-bottom: 0.55rem;
}

@media (max-width: 960px) {
  .story-grid,
  .purpose-grid,
  .founders-grid,
  .values-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .about-page {
    margin-top: 84px;
  }

  .founder-media,
  .founder-photo {
    min-height: 360px;
  }

  .story-card,
  .founder-card,
  .purpose-card,
  .values-card {
    padding: 1.5rem;
  }
}
</style>