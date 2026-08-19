<template>
  <section class="section-availability">
    <div class="section-availability__inner">
      <h2 v-if="title" class="section-availability__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-availability__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-availability__description-text">{{ description }}</p>

      <AvailabilityCalendar
        v-if="schedule && schedule.length"
        :schedule="schedule"
        :exceptions="exceptions || []"
        :appointment-counts="{}"
      />

      <div v-else class="alert alert-info mb-0">
        <i class="bi bi-info-circle me-2"></i>
        Horarios de atención no disponibles.
      </div>
    </div>
  </section>
</template>

<script setup>
import AvailabilityCalendar from '@/Components/Availability/AvailabilityCalendar.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Horario de Atención',
  },
  subtitle: String,
  availability: {
    type: Object,
    required: true,
  },
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
})

const schedule = props.availability?.schedule || []
const exceptions = props.availability?.exceptions || []
</script>

<style lang="less" scoped>
.section-availability {
  padding: 60px 0;
  background: #f8f9fa;

  &__inner {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
  }

  &__title {
    text-align: center;
    margin-bottom: 8px;
    font-weight: 700;
    color: #212529;
  }

  &__subtitle {
    font-weight: 600;
    color: #495057;
    text-align: center;
    margin: 0 0 16px;
  }

  &__description-text {
    font-size: 1rem;
    color: #6c757d;
    text-align: center;
    margin: 0 0 16px;
  }
}
</style>
