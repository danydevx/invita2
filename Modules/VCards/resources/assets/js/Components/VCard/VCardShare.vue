<template>
  <section class="vcard-section vcard-share">
    <h2 class="vcard-section__title">Compartir</h2>

    <div class="vcard-share__buttons">
      <button
        v-for="network in networks"
        :key="network.id"
        class="vcard-share__btn"
        :class="`vcard-share__btn--${network.id}`"
        :style="{ '--share-color': network.color }"
        @click="share(network)"
      >
        <i :class="network.icon"></i>
        <span>{{ network.label }}</span>
      </button>
    </div>

    <div class="vcard-share__copy">
      <input
        type="text"
        :value="vcardUrl"
        readonly
        class="vcard-share__url"
        ref="urlInput"
      >
      <button class="vcard-share__copy-btn" @click="copyLink">
        <i class="bi bi-clipboard"></i>
        Copiar
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { toast } from 'vue3-toastify'

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  vcardUrl: {
    type: String,
    default: '',
  },
})

const urlInput = ref(null)

const networks = [
  { id: 'facebook', label: 'Facebook', icon: 'bi bi-facebook', color: '#1877F2' },
  { id: 'linkedin', label: 'LinkedIn', icon: 'bi bi-linkedin', color: '#0A66C2' },
  { id: 'email', label: 'Correo', icon: 'bi bi-envelope', color: '#EA4335' },
  { id: 'whatsapp', label: 'WhatsApp', icon: 'bi bi-whatsapp', color: '#25D366' },
]

function getShareUrl(network) {
  const url = encodeURIComponent(props.vcardUrl || window.location.href)
  const title = encodeURIComponent(props.vcard?.name || 'Mi Tarjeta Digital')
  const summary = encodeURIComponent(props.vcard?.headline || 'Mira mi tarjeta de presentación digital')

  switch (network.id) {
    case 'facebook':
      return `https://www.facebook.com/sharer/sharer.php?u=${url}`
    case 'linkedin':
      return `https://www.linkedin.com/sharing/share-offsite/?url=${url}`
    case 'email':
      return `mailto:?subject=${title}&body=${summary}%0A%0A${decodeURIComponent(url)}`
    case 'whatsapp':
      return `https://wa.me/?text=${title}%0A${url}`
    default:
      return '#'
  }
}

function share(network) {
  const shareUrl = getShareUrl(network)

  if (network.id === 'email') {
    window.location.href = shareUrl
  } else {
    window.open(
      shareUrl,
      network.id === 'facebook' ? 'facebook-share' : 'share-dialog',
      'width=600,height=400,scrollbars=yes,resizable=yes'
    )
  }
}

function copyLink() {
  const url = props.vcardUrl || (typeof window !== 'undefined' ? window.location.href : '')

  if (!url) {
    toast.error('No hay URL para copiar')
    return
  }

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(url).then(() => {
      toast.success('Enlace copiado al portapapeles')
    }).catch(() => {
      fallbackCopy(url)
    })
  } else {
    fallbackCopy(url)
  }
}

function fallbackCopy(text) {
  const textArea = document.createElement('textarea')
  textArea.value = text
  textArea.style.position = 'fixed'
  textArea.style.left = '-9999px'
  document.body.appendChild(textArea)
  textArea.select()
  try {
    document.execCommand('copy')
    toast.success('Enlace copiado al portapapeles')
  } catch (err) {
    toast.error('No se pudo copiar')
  }
  document.body.removeChild(textArea)
}
</script>

<style scoped>
.vcard-share__buttons {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
  margin-bottom: 1rem;
}

@media (max-width: 35.9375rem) {
  .vcard-share__buttons {
    grid-template-columns: repeat(4, 1fr);
  }

  .vcard-share__buttons span {
    display: none;
  }
}

.vcard-share__btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.875rem 1rem;
  background: var(--vcard-surface);
  border: 0.0625rem solid var(--vcard-border);
  border-radius: 0.875rem;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--share-color);
  transition: all 0.2s;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.06);
}

.vcard-share__btn:hover {
  transform: translateY(-0.125rem);
  box-shadow: 0 0.375rem 1rem rgba(0, 0, 0, 0.1);
  background: var(--share-color);
  color: #fff;
  border-color: var(--share-color);
}

.vcard-share__btn i {
  font-size: 1.125rem;
}

.vcard-share__copy {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.vcard-share__url {
  flex: 1;
  padding: 0.625rem 0.875rem;
  border: 0.0625rem solid var(--vcard-border);
  border-radius: 0.75rem;
  background: var(--vcard-surface);
  color: var(--vcard-text);
  font-size: 0.8rem;
  outline: none;
  min-width: 0;
}

.vcard-share__url:focus {
  border-color: var(--vcard-primary);
}

.vcard-share__copy-btn {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.625rem 1rem;
  background: var(--vcard-primary);
  color: var(--vcard-surface);
  border: none;
  border-radius: 0.75rem;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.vcard-share__copy-btn:hover {
  filter: brightness(1.05);
}
</style>
