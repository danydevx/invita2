<template>
  <section class="vcard-section vcard-socials">
    <h2 class="vcard-section__title">Redes sociales</h2>

    <div class="vcard-socials__grid">
      <a v-for="item in items" :key="item.label" :href="item.href" class="vcard-socials__item" target="_blank" rel="noopener">
        <i :class="item.icon"></i>
        <span>{{ item.label }}</span>
      </a>
    </div>
  </section>
</template>

<script setup>
const items = [
  { label: 'Facebook', icon: 'bi bi-facebook', href: '#' },
  { label: 'Instagram', icon: 'bi bi-instagram', href: '#' },
  { label: 'LinkedIn', icon: 'bi bi-linkedin', href: '#' },
  { label: 'WhatsApp', icon: 'bi bi-whatsapp', href: '#' },
]
</script>
