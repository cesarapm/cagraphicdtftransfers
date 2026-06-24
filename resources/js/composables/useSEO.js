import { onMounted } from 'vue';

export function useSEO(options = {}) {
  const {
    title = 'CA Graphic DTF',
    description = 'Premium DTF transfers for custom apparel',
    keywords = 'DTF transfers, direct-to-film, custom apparel',
    ogTitle = null,
    ogDescription = null,
    ogType = 'website',
    ogUrl = 'https://cagraphicdtftransfers.com',
    ogImage = 'https://cagraphicdtftransfers.com/images/og-image.jpg',
  } = options;

  onMounted(() => {
    // Actualizar título
    document.title = `${title} | CA Graphic DTF`;
    
    // Función helper para actualizar o crear meta tags
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
    
    // Agregar meta tags
    updateMeta('description', description);
    updateMeta('keywords', keywords);
    updateMeta('og:title', ogTitle || title, true);
    updateMeta('og:description', ogDescription || description, true);
    updateMeta('og:type', ogType, true);
    updateMeta('og:url', ogUrl, true);
    updateMeta('og:image', ogImage, true);
    updateMeta('twitter:card', 'summary_large_image');
    updateMeta('twitter:title', ogTitle || title);
    updateMeta('twitter:description', ogDescription || description);
    updateMeta('viewport', 'width=device-width, initial-scale=1');
    updateMeta('author', 'CA Graphic DTF');
    updateMeta('robots', 'index, follow');
    
    // Agregar canonical link
    let canonical = document.querySelector('link[rel="canonical"]');
    if (canonical) canonical.remove();
    
    const newCanonical = document.createElement('link');
    newCanonical.rel = 'canonical';
    newCanonical.href = ogUrl;
    document.head.appendChild(newCanonical);
  });
}

