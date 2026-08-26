<template>
  <div class="vcard-copy">
    <a href="https://orpot.com" target="_blank" rel="noopener" class="vcard-copy__link">
      Desarrollado por <strong>Orpot.com</strong>
    </a>
  </div>
</template>

<style scoped>
.vcard-copy {
  text-align: center;
  padding: 1.5rem 1rem 0.5rem;
}

.vcard-copy__link {
  font-size: 0.75rem;
  color: var(--vcard-muted, #6b7280);
  text-decoration: none;
  transition: color 0.2s;
}

.vcard-copy__link:hover {
  color: var(--vcard-primary, #2563EB);
}

.vcard-copy__link strong {
  font-weight: 600;
}
</style>
