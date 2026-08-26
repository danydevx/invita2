<template>
  <section class="vcard-section vcard-hours">
    <h2 class="vcard-section__title">Horario</h2>

    <div class="vcard-hours__grid">
      <div v-for="item in hours" :key="item.day" class="vcard-hours__item card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <span class="fw-semibold">{{ item.day }}</span>
          <span class="text-muted">{{ item.range }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const hours = [
  { day: 'Lunes', range: '08:00 - 20:00' },
  { day: 'Martes', range: '08:00 - 20:00' },
  { day: 'Miércoles', range: '08:00 - 20:00' },
  { day: 'Jueves', range: '08:00 - 20:00' },
  { day: 'Viernes', range: '08:00 - 20:00' },
  { day: 'Sábado', range: '08:00 - 14:00' },
  { day: 'Domingo', range: 'Cerrado' },
]
</script>
