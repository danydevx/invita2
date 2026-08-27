<template>
  <section class="vcard-section vcard-hours">
    <h2 class="vcard-section__title">Horario</h2>

    <div class="vcard-hours__grid">
      <div
        v-for="item in displayHours"
        :key="item.day"
        class="vcard-hours__item card border-0 shadow-sm"
        :class="{ 'is-open': item.is_open, 'is-closed': !item.is_open }"
      >
        <div class="card-body d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <i :class="item.is_open ? 'bi-check-lg text-success' : 'bi-x-lg text-danger'"></i>
            <span class="fw-semibold">{{ item.day }}</span>
          </div>
          <span class="text-muted">{{ item.range }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  hours: {
    type: Array,
    default: () => [],
  },
  shape: {
    type: String,
    default: 'rounded',
  },
})

const dayMap = {
  sunday: 0,
  monday: 1,
  tuesday: 2,
  wednesday: 3,
  thursday: 4,
  friday: 5,
  saturday: 6,
}

function isOpenDay(dayName) {
  const lowerDay = dayName.toLowerCase()
  const today = new Date().getDay()
  return dayMap[lowerDay] === today
}

const displayHours = computed(() => {
  if (props.hours && props.hours.length > 0) {
    return props.hours.map(h => ({
      day: h.day_name || h.day || '',
      range: h.is_open ? `${h.opening_time || '08:00'} - ${h.closing_time || '18:00'}` : 'Cerrado',
      is_open: h.is_open ?? false,
    }))
  }
  return []
})
</script>

<style scoped>
.vcard-hours__grid {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.vcard-hours__item {
  border-left: 2px solid var(--vcard-primary) !important;
  transition: transform 0.2s, box-shadow 0.2s;
}

.vcard-hours__item:hover {
  transform: translateX(0.25rem);
}

.vcard-hours__item i {
  font-size: 1.125rem;
}

.vcard-hours__item.is-closed {
  border-left-color: #dc3545 !important;
  opacity: 0.8;
}
</style>
