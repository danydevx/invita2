<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Configurar Horario</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-3">Configura el horario de atenci&oacute;n para cada d&iacute;a de la semana.</p>

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th style="width: 120px;">D&iacute;a</th>
                  <th style="width: 100px;">Abierto</th>
                  <th>Apertura</th>
                  <th>Cierre</th>
                  <th>Ini. Almuerzo</th>
                  <th>Fin Almuerzo</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(day, index) in localHours" :key="day.day_of_week">
                  <td class="align-middle">
                    <strong>{{ day.day_name }}</strong>
                  </td>
                  <td class="align-middle text-center">
                    <input
                      type="checkbox"
                      v-model="day.is_open"
                      class="form-check-input"
                    >
                  </td>
                  <td>
                    <input
                      type="time"
                      v-model="day.opening_time"
                      class="form-control form-control-sm"
                      :disabled="!day.is_open"
                    >
                  </td>
                  <td>
                    <input
                      type="time"
                      v-model="day.closing_time"
                      class="form-control form-control-sm"
                      :disabled="!day.is_open"
                    >
                  </td>
                  <td>
                    <input
                      type="time"
                      v-model="day.lunch_start_time"
                      class="form-control form-control-sm"
                      :disabled="!day.is_open"
                      placeholder="--:--"
                    >
                  </td>
                  <td>
                    <input
                      type="time"
                      v-model="day.lunch_end_time"
                      class="form-control form-control-sm"
                      :disabled="!day.is_open"
                      placeholder="--:--"
                    >
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
          <button type="button" class="btn btn-primary" @click="save" :disabled="saving">
            {{ saving ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  listingId: { type: [Number, String], required: true },
  vcardId: { type: [Number, String], required: true },
  selectedHours: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'update'])

const localHours = ref([])
const saving = ref(false)

const dayNames = {
  0: 'Domingo',
  1: 'Lunes',
  2: 'Martes',
  3: 'Miércoles',
  4: 'Jueves',
  5: 'Viernes',
  6: 'Sábado',
}

function initHours() {
  const hours = []
  for (let i = 0; i <= 6; i++) {
    const existing = props.selectedHours.find(h => h.day_of_week === i)
    hours.push({
      day_of_week: i,
      day_name: dayNames[i],
      is_open: existing ? existing.is_open : (i >= 1 && i <= 5),
      opening_time: existing ? existing.opening_time : '08:00',
      closing_time: existing ? existing.closing_time : '18:00',
      lunch_start_time: existing ? existing.lunch_start_time : null,
      lunch_end_time: existing ? existing.lunch_end_time : null,
    })
  }
  localHours.value = hours
}

watch(() => props.show, (val) => {
  if (val) {
    initHours()
  }
})

async function save() {
  saving.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/business-hours`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({ hours: localHours.value }),
    })
    if (!res.ok) throw new Error('Error saving business hours')
    emit('update', [...localHours.value])
    emit('close')
  } catch (e) {
    alert('Error al guardar el horario')
  } finally {
    saving.value = false
  }
}
</script>
