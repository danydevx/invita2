<template>
  <div
    class="minisite-layout"
    :style="themeStyles"
    :class="[pageStyleClass, layoutClasses]"
  >
    <slot />

    <footer class="minisite-footer" :style="footerStyles">
      <div class="container">
        <div class="row">
          <div class="col-md-6 text-center text-md-start">
            <h6 class="mb-2" :style="{ color: footerTextColor }">{{ business?.name }}</h6>
            <small :style="{ color: footerTextLightColor }">{{ business?.address || 'Visítanos' }}</small>
          </div>
          <div class="col-md-6 text-center text-md-end">
            <small :style="{ color: footerTextLightColor }">
              &copy; {{ new Date().getFullYear() }} {{ business?.name }}. Todos los derechos reservados.
            </small>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'

const props = defineProps({
  business: Object,
  theme: Object,
  modules: {
    type: Array,
    default: () => [],
  },
  branding: Object,
})

const brandingCss = computed(() => {
  return props.branding?.generated_css || null
})

const injectBrandingCss = (css) => {
  let el = document.getElementById('minisite-branding-css')
  if (!el) {
    el = document.createElement('style')
    el.id = 'minisite-branding-css'
    document.head.appendChild(el)
  }
  if (css) {
    el.textContent = css
  } else {
    el.textContent = ''
  }
}

onMounted(() => {
  injectBrandingCss(brandingCss.value)
})

watch(brandingCss, (css) => {
  injectBrandingCss(css)
})

const themeStyles = computed(() => {
  if (!props.theme?.css_variables) return {}

  const { colors, fonts, buttons_style, buttons_uppercase } = props.theme.css_variables

  const mapColor = (key, fallback) => colors?.[key] || fallback
  const mapFont = (key, fallback) => fonts?.[key] || fallback

  const buttonRadius = buttons_style === 'rounded' ? '50px' :
                       buttons_style === 'square' ? '0px' :
                       buttons_style === 'round' ? '8px' : '8px'

  return {
    '--brand-primary': mapColor('brand_primary', '#1a1a2e'),
    '--brand-secondary': mapColor('brand_secondary', '#6B7280'),
    '--brand-tertiary': mapColor('brand_tertiary', '#EC4899'),
    '--brand-quaternary': mapColor('brand_quaternary', '#10B981'),
    '--brand-accent': mapColor('brand_accent', '#3B82F6'),
    '--brand-hover': mapColor('brand_hover', '#1F2937'),
    '--brand-link': mapColor('brand_link', '#3B82F6'),
    '--brand-background': mapColor('brand_background', '#ffffff'),
    '--brand-text': mapColor('brand_text', '#1a1a2e'),
    '--brand-text-light': mapColor('brand_text_light', '#6B7280'),
    '--brand-bgcolor-header': mapColor('brand_bgcolor_header', '#FFFFFF'),
    '--brand-bgcolor-footer': mapColor('brand_bgcolor_footer', '#F8F9FA'),
    '--heading-font': mapFont('font_heading', 'Poppins, sans-serif'),
    '--body-font': mapFont('font_body', 'Open Sans, sans-serif'),
    '--buttons-font': mapFont('font_buttons', 'Poppins, sans-serif'),
    '--brand-button-radius': buttonRadius,
    '--brand-card-radius': '8px',
    '--buttons-text-transform': buttons_uppercase ? 'uppercase' : 'none',
  }
})

const pageStyleClass = computed(() => {
  const style = props.branding?.page_style || props.theme?.layout_config?.page_style || 'light'
  return `minisite-style-${style}`
})

const layoutClasses = computed(() => {
  if (!props.theme?.layout_config && !props.branding) return ''

  const section_style = props.branding?.section_style || props.theme?.layout_config?.section_style || 'spacious'
  const hero_style = props.branding?.hero_style || props.theme?.layout_config?.hero_style || 'fullwidth'
  const classes = []

  if (section_style === 'spacious') classes.push('minisite-section-spacious')
  if (section_style === 'classic') classes.push('minisite-section-classic')
  if (section_style === 'cozy') classes.push('minisite-section-cozy')
  if (section_style === 'rounded') classes.push('minisite-section-rounded')
  if (section_style === 'dramatic') classes.push('minisite-section-dramatic')
  if (section_style === 'balanced') classes.push('minisite-section-balanced')

  if (hero_style === 'fullwidth') classes.push('minisite-hero-fullwidth')
  if (hero_style === 'centered') classes.push('minisite-hero-centered')
  if (hero_style === 'split') classes.push('minisite-hero-split')
  if (hero_style === 'boxed') classes.push('minisite-hero-boxed')
  if (hero_style === 'fullbleed') classes.push('minisite-hero-fullbleed')

  return classes.join(' ')
})

const footerStyles = computed(() => {
  if (!props.theme?.css_variables?.colors && !props.branding?.generated_css) {
    return { backgroundColor: '#1a1a2e', color: '#ffffff' }
  }
  return {
    backgroundColor: 'var(--brand-primary)',
    color: '#ffffff',
  }
})

const footerTextColor = computed(() => {
  return '#ffffff'
})

const footerTextLightColor = computed(() => {
  return 'rgba(255, 255, 255, 0.8)'
})

const animations = computed(() => {
  return props.theme?.layout_config?.animations || {}
})

const isModuleEnabled = (moduleKey) => {
  return props.modules.includes(moduleKey)
}
</script>

<style scoped>
.minisite-layout {
  font-family: var(--body-font);
  background-color: var(--brand-background);
  color: var(--brand-text);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.minisite-layout :deep(h1),
.minisite-layout :deep(h2),
.minisite-layout :deep(h3),
.minisite-layout :deep(h4),
.minisite-layout :deep(h5),
.minisite-layout :deep(h6) {
  font-family: var(--heading-font);
  color: var(--brand-primary);
}

.minisite-layout :deep(.btn-primary) {
  background-color: var(--brand-primary);
  border-color: var(--brand-primary);
  border-radius: var(--brand-button-radius);
  text-transform: var(--buttons-text-transform, none);
}

.minisite-layout :deep(.btn-outline-primary) {
  color: var(--brand-primary);
  border-color: var(--brand-primary);
  border-radius: var(--brand-button-radius);
  text-transform: var(--buttons-text-transform, none);
}

.minisite-layout :deep(.card) {
  border-radius: var(--brand-card-radius);
  border: none;
}

.minisite-layout :deep(.section-divider) {
  border-color: var(--brand-accent);
}

/* Dark style */
.minisite-style-dark {
  --brand-bg-dark: #1a1a2e;
}

.minisite-style-dark .minisite-footer {
  background-color: var(--brand-primary) !important;
  color: #ffffff;
}

/* Light style */
.minisite-style-light {
  --brand-bg-light: #FDF8F3;
}

/* Section styles */
.minisite-section-spacious {
  --section-padding: 5rem 0;
}

.minisite-section-cozy {
  --section-padding: 3rem 0;
}

.minisite-section-dramatic {
  --section-padding: 6rem 0;
}

.minisite-section-balanced {
  --section-padding: 4rem 0;
}

.minisite-section-rounded {
  --section-padding: 4rem 0;
}

/* Animation classes */
:deep(.animate-fade-in) {
  animation: fade-in 0.5s ease-out forwards;
}

:deep(.animate-fade-in-up) {
  animation: fade-in-up 0.6s ease-out forwards;
}

:deep(.animate-slide-in) {
  animation: slide-in 0.4s ease-out forwards;
}

:deep(.animate-bounce-in) {
  animation: bounce-in 0.5s ease-out forwards;
}

:deep(.animate-zoom-in) {
  animation: zoom-in 0.4s ease-out forwards;
}

:deep(.animate-lift) {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

:deep(.animate-lift:hover) {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

:deep(.animate-glow) {
  transition: box-shadow 0.3s ease;
}

:deep(.animate-glow:hover) {
  box-shadow: 0 0 20px rgba(201, 168, 108, 0.4);
}

:deep(.animate-bounce) {
  transition: transform 0.3s ease;
}

:deep(.animate-bounce:hover) {
  transform: translateY(-4px);
}

:deep(.animate-neon-glow) {
  transition: box-shadow 0.3s ease;
}

:deep(.animate-neon-glow:hover) {
  box-shadow: 0 0 30px rgba(255, 69, 0, 0.5);
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-in {
  from { opacity: 0; transform: translateX(-20px); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes bounce-in {
  0% { opacity: 0; transform: scale(0.9); }
  50% { transform: scale(1.02); }
  100% { opacity: 1; transform: scale(1); }
}

@keyframes zoom-in {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.minisite-footer {
  margin-top: auto;
  padding: 2rem 0;
  border-top: 1px solid rgba(128, 128, 128, 0.2);
}
</style>
