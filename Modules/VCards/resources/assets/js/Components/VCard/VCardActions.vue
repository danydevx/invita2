<template>
  <div class="vcard__actions">
    <a
      :href="`/v/${vcard.slug}/download`"
      class="vcard__action vcard__action--primary"
      :class="{ 'rounded': shape === 'rounded' }"
      download
      @click.stop
    >
      <i class="bi bi-download me-2"></i>
      Guardar contacto
    </a>
    <button
      type="button"
      class="vcard__action"
      :class="{ 'rounded': shape === 'rounded' }"
      @click="copyLink"
    >
      <i class="bi bi-link me-2"></i>
      Copiar enlace
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  shape: {
    type: String,
    default: 'rounded',
  },
})

function copyLink() {
  const url = `${window.location.origin}/v/${props.vcard.slug}`
  navigator.clipboard.writeText(url)
}
</script>

<style scoped>
.vcard__actions {
  display: flex;
  flex-direction: row;
  gap: 0.75rem;
  margin-top: 1.25rem;
  flex-wrap: wrap;
}

.vcard__action {
  display: flex;
  align-items: center;
  justify-content: center;
  flex: 1 1 180px;
  padding: 0.95rem 1.25rem;
  border-radius: 0;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  cursor: pointer;
  border: none;
  font-size: 1rem;
  background: color-mix(in srgb, var(--vcard-primary) 10%, #f3f4f6);
  color: var(--vcard-text);
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
}

.vcard__action.rounded {
  border-radius: 14px;
}

.vcard__action:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.1);
}

.vcard__action--primary {
  background: var(--vcard-primary);
  color: var(--vcard-surface);
}

.vcard__action--primary:hover {
  filter: brightness(1.05);
}

@media (max-width: 420px) {
  .vcard__actions {
    flex-direction: column;
  }

  .vcard__action {
    flex-basis: auto;
    width: 100%;
  }
}
</style>
