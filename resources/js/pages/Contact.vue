<template>
  <div class="contact-page">
    <section class="contact-hero">
      <v-container class="py-16">
        <v-row justify="center">
          <v-col cols="12" lg="9" class="text-center">
            <span class="contact-kicker">Contacto</span>
            <h1>Hablemos de la pieza que te esta buscando</h1>
            <p>
              Si quieres una pieza especial, una asesoria o conocer mas sobre IzaguirreQu,
              estamos listas para escucharte.
            </p>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section class="contact-content">
      <v-container class="py-14">
        <v-row justify="center">
          <v-col cols="12" lg="10">
            <div class="contact-grid">
              <article class="contact-card contact-card--primary">
                <span class="mini-kicker">Escribenos</span>
                <h2>Conecta con nuestro universo</h2>
                <p>
                  Cada simbolo guarda un secreto, y a veces una conversacion es el inicio de todo.
                  Podemos ayudarte a elegir una joya, resolver dudas o recibir pedidos especiales.
                </p>

                <div class="contact-list">
                  <a class="contact-link" href="https://wa.me/5580091558?text=Hola,%20quiero%20informacion%20sobre%20IzaguirreQu" target="_blank" rel="noreferrer">
                    <span class="contact-icon">WA</span>
                    <div>
                      <strong>WhatsApp</strong>
                      <span>+52 558 009 1558</span>
                    </div>
                  </a>

                  <a class="contact-link" href="mailto:atencionaclientes@izaguirrequ.com">
                    <span class="contact-icon">@</span>
                    <div>
                      <strong>Email</strong>
                      <span>Atencionaclientes@izaguirrequ.com</span>
                    </div>
                  </a>

                  <div class="contact-link contact-link--static">
                    <span class="contact-icon">MX</span>
                    <div>
                      <strong>Origen</strong>
                      <span>Jardines de Bellavista, Tlanepantla Estado de México </span>
                    </div>
                  </div>
                </div>
              </article>

              <article class="contact-card">
                <span class="mini-kicker">Nuestra energia</span>
                <h2>Lo que puedes esperar</h2>
                <div class="expectation-list">
                  <div class="expectation-item">
                    <strong>Asesoria cercana</strong>
                    <p>Te ayudamos a elegir una pieza alineada con tu gusto, momento y simbolismo.</p>
                  </div>
                  <div class="expectation-item">
                    <strong>Detalle artesanal</strong>
                    <p>Cada joya nace del respeto por el oficio y la belleza de lo hecho a mano.</p>
                  </div>
                  <div class="expectation-item">
                    <strong>Conexiones reales</strong>
                    <p>No buscamos vender rapido; buscamos que encuentres una pieza que te hable.</p>
                  </div>
                </div>

                <v-btn class="contact-cta" :to="{ name: 'ProductList' }" elevation="0">
                  Ver coleccion
                </v-btn>
              </article>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>
  </div>
</template>

<style scoped>
.contact-page {
  margin-top: 70px;
  background: linear-gradient(180deg, #fbf8f4 0%, #f1ebe4 100%);
  color: #5f5244;
}

.contact-hero {
  background:
    radial-gradient(circle at top right, rgba(217, 200, 181, 0.42), transparent 30%),
    linear-gradient(180deg, #f8f4ef 0%, #f3ece5 100%);
}

.contact-kicker,
.mini-kicker {
  display: inline-block;
  padding: 0.45rem 1rem;
  border-radius: 999px;
  background: rgba(184, 151, 120, 0.12);
  color: #8c745f;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.78rem;
}

.contact-hero h1,
.contact-card h2 {
  color: #6b5b47;
  margin: 1rem 0;
}

.contact-hero h1 {
  font-size: clamp(2.5rem, 6vw, 4.4rem);
}

.contact-hero .text-center {
  max-width: 760px;
  margin-inline: auto;
}

.contact-hero p,
.contact-card p,
.expectation-item p,
.contact-link span {
  color: #7f6d5a;
  line-height: 1.9;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 1.5rem;
}

.contact-card {
  background: rgba(255, 252, 248, 0.88);
  border: 1px solid rgba(184, 151, 120, 0.16);
  border-radius: 28px;
  padding: 2rem;
  box-shadow: 0 18px 40px rgba(140, 116, 95, 0.08);
}

.contact-card--primary {
  background: linear-gradient(180deg, rgba(255, 252, 248, 0.96), rgba(245, 239, 233, 0.94));
}

.contact-list,
.expectation-list {
  display: grid;
  gap: 1rem;
  margin-top: 1.5rem;
}

.contact-link,
.contact-link--static,
.expectation-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding: 1rem 1.1rem;
  border-radius: 20px;
  background: rgba(245, 239, 233, 0.74);
  text-decoration: none;
}

.contact-link > div,
.contact-link--static > div,
.expectation-item > div,
.contact-link span {
  min-width: 0;
}

.contact-link strong,
.expectation-item strong {
  display: block;
  color: #6b5b47;
  margin-bottom: 0.35rem;
}

.contact-link span,
.contact-card p,
.expectation-item p {
  overflow-wrap: anywhere;
  word-break: break-word;
}

.contact-icon {
  width: 42px;
  height: 42px;
  flex: 0 0 42px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #d9c8b5 0%, #b89778 100%);
  color: #fffaf4;
  font-size: 0.8rem;
  letter-spacing: 0.08em;
}

.contact-cta {
  margin-top: 1.5rem;
  background: linear-gradient(135deg, #8c745f, #a68b73) !important;
  color: #ffffff !important;
  border-radius: 999px !important;
  letter-spacing: 0.08em;
}

@media (max-width: 960px) {
  .contact-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .contact-page {
    margin-top: 84px;
  }

  .contact-hero {
    padding-top: 0.5rem;
  }

  .contact-hero h1 {
    font-size: clamp(2rem, 11vw, 3rem);
    line-height: 1.02;
  }

  .contact-hero p {
    font-size: 0.98rem;
  }

  .contact-card {
    padding: 1.5rem;
    border-radius: 24px;
  }

  .contact-link,
  .contact-link--static,
  .expectation-item {
    padding: 0.95rem;
  }

  .contact-link,
  .contact-link--static {
    align-items: center;
  }

  .contact-cta {
    width: 100%;
  }
}

@media (max-width: 420px) {
  .contact-content .py-14,
  .contact-hero .py-16 {
    padding-top: 2.25rem !important;
    padding-bottom: 2.25rem !important;
  }

  .contact-card {
    padding: 1.1rem;
  }

  .contact-link,
  .contact-link--static,
  .expectation-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .contact-icon {
    width: 38px;
    height: 38px;
    flex-basis: 38px;
  }

  .contact-kicker,
  .mini-kicker {
    font-size: 0.7rem;
    letter-spacing: 0.12em;
  }
}
</style>