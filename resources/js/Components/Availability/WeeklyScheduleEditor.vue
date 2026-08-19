<template>
  <div class="weekly-schedule-editor">
    <div v-for="day in localSchedule" :key="day.day_of_week" class="day-row mb-3 p-3 border rounded">
      <div class="row align-items-center g-2">
        <div class="col-md-3">
          <div class="form-check form-switch">
            <input
              class="form-check-input"
              type="checkbox"
              role="switch"
              :id="`day-toggle-${day.day_of_week}`"
              v-model="day.is_available"
            />
            <label class="form-check-label fw-bold" :for="`day-toggle-${day.day_of_week}`">
              {{ day.day_name }}
            </label>
          </div>
        </div>

        <div class="col-md-4">
          <div v-if="day.is_available" class="d-flex align-items-center gap-2">
            <FieldTime
              :id="`start-${day.day_of_week}`"
              label=""
              v-model="day.start_time"
              :formError="errors[`schedule.${day.day_of_week}.start_time`]"
            />
            <span class="text-muted">a</span>
            <FieldTime
              :id="`end-${day.day_of_week}`"
              label=""
              v-model="day.end_time"
              :formError="errors[`schedule.${day.day_of_week}.end_time`]"
            />
          </div>
          <span v-else class="badge bg-secondary">CERRADO</span>
        </div>

        <div class="col-md-2" v-if="day.is_available">
          <FieldNumber
            :id="`slot-duration-${day.day_of_week}`"
            label=""
            v-model="day.slot_duration_minutes"
            :min="5"
            :max="480"
            :step="5"
            placeholder="30"
          />
          <small class="text-muted d-block">min/slot</small>
        </div>

        <div class="col-md-2" v-if="day.is_available">
          <FieldNumber
            :id="`slots-per-${day.day_of_week}`"
            label=""
            v-model="day.slots_per_slot"
            :min="1"
            :max="100"
            :step="1"
            placeholder="1"
          />
          <small class="text-muted d-block">cupos/slot</small>
        </div>
      </div>

      <div v-if="day.is_available && errors[`schedule.${day.day_of_week}`]" class="text-danger small mt-2">
        {{ errors[`schedule.${day.day_of_week}`] }}
      </div>
    </div>

    <div class="text-end">
      <button
        type="button"
        class="btn btn-primary"
        :disabled="saving"
        @click="save"
      >
        <i class="bi bi-check-lg me-1"></i>
        {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'

const props = defineProps({
  schedule: {
    type: Array,
    required: true,
  },
  businessId: {
    type: [Number, String],
    required: true,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['saved'])

const localSchedule = ref(JSON.parse(JSON.stringify(props.schedule)))

watch(() => props.schedule, (newVal) => {
  localSchedule.value = JSON.parse(JSON.stringify(newVal))
}, { deep: true })

const saving = ref(false)

const save = () => {
  saving.value = true
  router.put(
    `/member/businesses/${props.businessId}/appointments/availability/weekly`,
    { schedule: localSchedule.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        saving.value = false
        emit('saved')
      },
      onError: () => {
        saving.value = false
      },
      onFinish: () => {
        saving.value = false
      },
    }
  )
}
</script>

<style scoped>
.day-row {
  background-color: #fafbfc;
  transition: background-color 0.2s;
}

.day-row:hover {
  background-color: #f0f4f8;
}

:deep(.form-floating) {
  width: 110px;
}

:deep(.form-floating > .form-control) {
  height: 38px;
  min-height: 38px;
}

:deep(.form-floating > label) {
  font-size: 0.7rem;
}
</style>