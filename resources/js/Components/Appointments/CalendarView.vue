<template>
  <div class="calendar-wrapper is-light-mode" style="min-height: 600px;">
    <div class="calendar-toolbar mb-3">
      <div class="d-flex gap-2">
        <select v-model="currentLocation" class="form-select form-select-sm" style="width: auto;">
          <option :value="null">Todas las ubicaciones</option>
          <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
        </select>
        <select v-model="currentService" class="form-select form-select-sm" style="width: auto;">
          <option :value="null">Todos los servicios</option>
          <option v-for="svc in services" :key="svc.id" :value="svc.id">{{ svc.name }}</option>
        </select>
        <select v-model="currentStatus" class="form-select form-select-sm" style="width: auto;">
          <option :value="null">Todos los estados</option>
          <option value="pending">Pendiente</option>
          <option value="confirmed">Confirmada</option>
          <option value="completed">Completada</option>
          <option value="cancelled">Cancelada</option>
          <option value="no_show">No asistio</option>
        </select>
      </div>
    </div>

    <Qalendar
      :key="filterKey"
      :events="calendarEvents"
      :config="calendarConfig"
      @event-was-dragged="handleEventDragged"
      @event-was-clicked="handleEventClicked"
      @datetime-was-clicked="handleDatetimeClicked"
      @date-was-clicked="handleDateClicked"
      style="min-height: 600px;"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Qalendar } from 'qalendar'

const props = defineProps({
  appointments: {
    type: Array,
    default: () => [],
  },
  services: {
    type: Array,
    default: () => [],
  },
  locations: {
    type: Array,
    default: () => [],
  },
  businessId: {
    type: [Number, String],
    required: true,
  },
})

const emit = defineEmits(['reschedule', 'create', 'edit'])

const currentLocation = ref(null)
const currentService = ref(null)
const currentStatus = ref(null)

const statusColors = {
  pending: 'yellow',
  confirmed: 'green',
  completed: 'blue',
  cancelled: 'red',
  no_show: 'red',
}

const filteredAppointments = computed(() => {
  return props.appointments.filter(apt => {
    if (currentLocation.value && apt.business_location_id !== currentLocation.value) return false
    if (currentService.value && apt.business_service_id !== currentService.value) return false
    if (currentStatus.value && apt.status !== currentStatus.value) return false
    return true
  })
})

const filterKey = computed(() => {
  return `${currentLocation.value || 'all'}-${currentService.value || 'all'}-${currentStatus.value || 'all'}`
})

const calendarEvents = computed(() => {
  return filteredAppointments.value.map(apt => {
    const color = statusColors[apt.status] || 'yellow'
    const date = apt.appointment_date.substring(0, 10)
    const startTime = apt.start_time.substring(0, 5)
    const endTime = apt.end_time ? apt.end_time.substring(0, 5) : '12:00'
    const startDateTime = `${date} ${startTime}`
    const endDateTime = `${date} ${endTime}`

    return {
      id: apt.id.toString(),
      title: apt.customer_name,
      time: {
        start: startDateTime,
        end: endDateTime,
      },
      color: color,
      isEditable: apt.status !== 'cancelled',
    }
  })
})

const handleEventDragged = (data) => {
  console.log('Dragged event data:', data)
  const event = data.event || data
  const aptId = parseInt(event.id)
  const apt = props.appointments.find(a => a.id == aptId)
  if (!apt) {
    console.error('Appointment not found for id:', aptId)
    return
  }

  const [date, time] = (event.time?.start || event.start).split(' ')

  emit('reschedule', {
    appointment: apt,
    newDate: date.substring(0, 10),
    newTime: time,
    originalDate: apt.appointment_date.substring(0, 10),
    originalTime: apt.start_time,
  })
}

const handleEventClicked = (data) => {
  console.log('Clicked event data:', data)
  const event = data.event || data
  const aptId = parseInt(event.id)
  const apt = props.appointments.find(a => a.id == aptId)
  if (apt) {
    emit('edit', apt)
  } else {
    console.log('Appointment not found:', aptId, 'Available:', props.appointments.map(a => a.id))
  }
}

const handleDatetimeClicked = (data) => {
  console.log('Datetime clicked data:', data)
  let dateStr = ''
  let timeStr = '09:00'
  if (typeof data === 'string') {
    const parts = data.split(' ')
    dateStr = parts[0]?.substring(0, 10)
    timeStr = parts[1] || '09:00'
  } else if (data?.datetime) {
    const parts = data.datetime.split(' ')
    dateStr = parts[0]?.substring(0, 10)
    timeStr = parts[1] || '09:00'
  } else if (data?.dateTimeString) {
    const parts = data.dateTimeString.split(' ')
    dateStr = parts[0]?.substring(0, 10)
    timeStr = parts[1] || '09:00'
  }
  if (dateStr) {
    emit('create', { date: dateStr, time: timeStr })
  }
}

const handleDateClicked = (data) => {
  console.log('Date clicked data:', data)
  let dateStr = ''
  if (typeof data === 'string') {
    dateStr = data.substring(0, 10)
  } else if (data?.dateTimeString) {
    dateStr = data.dateTimeString.substring(0, 10)
  } else if (data?.date) {
    if (typeof data.date === 'string') {
      dateStr = data.date.substring(0, 10)
    } else if (data.date instanceof Date) {
      dateStr = data.date.toISOString().split('T')[0]
    }
  } else if (data instanceof Date) {
    dateStr = data.toISOString().split('T')[0]
  }
  if (dateStr) {
    emit('create', { date: dateStr, time: '09:00' })
  }
}

const calendarConfig = {
  locale: 'es-ES',
  defaultMode: 'month',
  week: {
    startsOn: 'monday',
    nDays: 7,
    scrollToHour: 6,
  },
  month: {
    showTrailingAndLeadingDates: true,
  },
  eventDialog: {
    isDisabled: true,
  },
  isSilent: true,
  showCurrentTime: true,
}
</script>

<style scoped>
.calendar-wrapper {
  background: white;
  padding: 1rem;
  border-radius: 0.5rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.calendar-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

:deep(.qalendar) {
  font-family: inherit;
}

:deep(.qalendar__event) {
  cursor: pointer;
  border-radius: 4px;
  padding: 2px 6px;
  font-size: 12px;
}

:deep(.qalendar__event--disabled) {
  opacity: 0.5;
  cursor: not-allowed;
}

:deep(.qalendar__event--yellow) {
  background-color: #ffc107 !important;
  color: #000 !important;
  border-left: 3px solid #d39e00 !important;
}

:deep(.qalendar__event--green) {
  background-color: #198754 !important;
  color: #fff !important;
  border-left: 3px solid #146c43 !important;
}

:deep(.qalendar__event--blue) {
  background-color: #0d6efd !important;
  color: #fff !important;
  border-left: 3px solid #0a58ca !important;
}

:deep(.qalendar__event--red) {
  background-color: #dc3545 !important;
  color: #fff !important;
  border-left: 3px solid #bb2d3b !important;
}
</style>
