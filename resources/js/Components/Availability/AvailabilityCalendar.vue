<template>
  <div class="availability-calendar-preview">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Vista Previa</h5>
      <div class="d-flex gap-3 align-items-center flex-wrap">
        <span class="legend-item">
          <span class="legend-dot bg-success"></span> Disponible
        </span>
        <span class="legend-item">
          <span class="legend-dot bg-danger"></span> Cerrado
        </span>
        <span class="legend-item">
          <span class="legend-dot bg-warning"></span> Excepción
        </span>
        <span class="legend-item">
          <span class="legend-dot bg-info"></span> Con citas
        </span>
      </div>
    </div>

    <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
      <button type="button" class="btn btn-sm btn-outline-secondary" @click="previousMonth">
        <i class="bi bi-chevron-left"></i>
      </button>
      <h4 class="mb-0">{{ monthYearLabel }}</h4>
      <button type="button" class="btn btn-sm btn-outline-secondary" @click="nextMonth">
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>

    <div class="calendar-grid">
      <div class="calendar-weekday" v-for="day in weekDays" :key="day">
        {{ day }}
      </div>
      <div
        v-for="cell in calendarCells"
        :key="cell.key"
        class="calendar-cell"
        :class="{
          'other-month': !cell.currentMonth,
          'today': cell.isToday,
          'available': cell.status === 'available',
          'closed': cell.status === 'closed',
          'exception-available': cell.status === 'exception-available',
          'exception-closed': cell.status === 'exception-closed',
          'has-appointments': cell.appointmentCount > 0,
        }"
      >
        <div class="cell-date">{{ cell.day }}</div>
        <div v-if="cell.exceptionReason" class="cell-reason" :title="cell.exceptionReason">
          {{ cell.exceptionReason }}
        </div>
        <div v-if="(cell.status === 'available' || cell.status === 'exception-available') && cell.startTime" class="cell-time">
          {{ cell.startTime }}-{{ cell.endTime }}
        </div>
        <div v-if="cell.appointmentCount > 0" class="cell-count" :title="`${cell.appointmentCount} cita(s) agendada(s)`">
          <i class="bi bi-calendar-check"></i> {{ cell.appointmentCount }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  schedule: {
    type: Array,
    required: true,
  },
  exceptions: {
    type: Array,
    default: () => [],
  },
  appointmentCounts: {
    type: Object,
    default: () => ({}),
  },
})

const weekDays = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']
const monthNames = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
]

const currentDate = ref(new Date())

const monthYearLabel = computed(() => {
  return `${monthNames[currentDate.value.getMonth()]} ${currentDate.value.getFullYear()}`
})

const previousMonth = () => {
  const d = new Date(currentDate.value)
  d.setMonth(d.getMonth() - 1)
  currentDate.value = d
}

const nextMonth = () => {
  const d = new Date(currentDate.value)
  d.setMonth(d.getMonth() + 1)
  currentDate.value = d
}

const scheduleMap = computed(() => {
  const map = {}
  props.schedule.forEach(day => {
    map[day.day_of_week] = day
  })
  return map
})

const exceptionsMap = computed(() => {
  const map = {}
  props.exceptions.forEach(exc => {
    map[exc.exception_date] = exc
  })
  return map
})

const calendarCells = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()

  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)

  const firstDayOfWeek = firstDay.getDay()
  const daysInMonth = lastDay.getDate()

  const cells = []

  const todayStr = new Date().toISOString().split('T')[0]

  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const d = new Date(year, month, -i)
    cells.push(buildCell(d, false, todayStr))
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const d = new Date(year, month, day)
    cells.push(buildCell(d, true, todayStr))
  }

  const remaining = 42 - cells.length
  for (let i = 1; i <= remaining; i++) {
    const d = new Date(year, month + 1, i)
    cells.push(buildCell(d, false, todayStr))
  }

  return cells
})

const buildCell = (date, currentMonth, todayStr) => {
  const dateStr = date.toISOString().split('T')[0]
  const dayOfWeek = date.getDay()
  const schedule = scheduleMap.value[dayOfWeek]
  const exception = exceptionsMap.value[dateStr]

  let status = 'closed'
  let startTime = null
  let endTime = null
  let exceptionReason = null

  if (exception) {
    status = exception.is_available ? 'exception-available' : 'exception-closed'
    startTime = exception.start_time
    endTime = exception.end_time
    exceptionReason = exception.reason
  } else if (schedule && schedule.is_available) {
    status = 'available'
    startTime = schedule.start_time
    endTime = schedule.end_time
  }

  return {
    key: dateStr,
    day: date.getDate(),
    currentMonth,
    isToday: dateStr === todayStr,
    status,
    startTime,
    endTime,
    exceptionReason,
    appointmentCount: props.appointmentCounts[dateStr] || 0,
  }
}
</script>

<style scoped>
.availability-calendar-preview {
  background: white;
  padding: 1rem;
  border-radius: 0.5rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
}

.calendar-weekday {
  text-align: center;
  font-weight: 600;
  padding: 0.5rem;
  color: #6c757d;
  text-transform: uppercase;
  font-size: 0.75rem;
}

.calendar-cell {
  min-height: 70px;
  padding: 0.5rem;
  border-radius: 4px;
  background-color: #f8f9fa;
  font-size: 0.85rem;
  position: relative;
}

.calendar-cell.other-month {
  opacity: 0.4;
}

.calendar-cell.today {
  border: 2px solid #0d6efd;
}

.calendar-cell.available {
  background-color: #d1e7dd;
}

.calendar-cell.closed {
  background-color: #f8d7da;
}

.calendar-cell.exception-available {
  background-color: #fff3cd;
}

.calendar-cell.exception-closed {
  background-color: #f8d7da;
  background-image: repeating-linear-gradient(
    45deg,
    transparent,
    transparent 5px,
    rgba(255, 255, 255, 0.3) 5px,
    rgba(255, 255, 255, 0.3) 10px
  );
}

.calendar-cell.has-appointments.available {
  background-image: linear-gradient(135deg, #d1e7dd 0%, #cfe2ff 100%);
}

.calendar-cell.has-appointments.exception-available {
  background-image: linear-gradient(135deg, #fff3cd 0%, #cfe2ff 100%);
}

.cell-count {
  font-size: 0.7rem;
  color: #0a58ca;
  margin-top: 0.25rem;
  font-weight: 600;
}

.cell-date {
  font-weight: 600;
}

.cell-time {
  font-size: 0.7rem;
  color: #495057;
  margin-top: 0.25rem;
}

.cell-reason {
  font-size: 0.7rem;
  color: #664d03;
  margin-top: 0.25rem;
  font-style: italic;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.legend-dot {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 4px;
}

.legend-item {
  font-size: 0.85rem;
}
</style>