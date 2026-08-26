<template>
  <section class="vcard-section vcard-appointments">
    <h2 class="vcard-section__title">Agendar cita</h2>

    <div class="vcard-appointments__card card border-0 shadow-sm" :class="{ 'rounded': shape === 'rounded' }">
      <div class="card-body">
        <div class="row g-3 align-items-center mb-3">
          <div class="col-12 col-md-3">
            <label class="form-label mb-0">Fecha</label>
          </div>
          <div class="col-12 col-md-9">
            <input type="text" class="form-control" placeholder="Selecciona una fecha">
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-12 col-md-3">
            <label class="form-label mb-0">Hora</label>
          </div>
          <div class="col-12 col-md-9">
            <div class="vcard-appointments__slots">
              <button v-for="slot in slots" :key="slot" type="button" class="vcard-appointments__slot" :class="{ 'rounded': shape === 'rounded' }">{{ slot }}</button>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-primary w-100" :class="{ 'rounded': shape === 'rounded' }">Agendar ahora</button>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  shape: {
    type: String,
    default: 'rounded',
  },
})

const slots = ['08:10 - 10:00', '10:00 - 12:00', '12:00 - 14:00', '16:00 - 18:00']
</script>

<style scoped>
.vcard-appointments__slot {
  border-radius: 0;
  border: 1px solid color-mix(in srgb, var(--vcard-primary) 20%, #e5e7eb);
  background: var(--vcard-surface);
  padding: 0.5rem 1rem;
  transition: all 0.2s;
}

.vcard-appointments__slot.rounded {
  border-radius: 8px;
}

.vcard-appointments__slot:hover {
  background: var(--vcard-primary);
  color: var(--vcard-surface);
  border-color: var(--vcard-primary);
}
</style>
