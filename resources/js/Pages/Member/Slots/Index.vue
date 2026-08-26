<template>
  <MemberLayout>
    <Head :title="`Slots - ${listing?.name || ''}`" />

    <PageHeader
      title="Turnos de Disponibilidad"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <button type="button" class="btn btn-primary btn-sm" @click="openCreateModal">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Turno
        </button>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/slots`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar turnos..."
      empty-title="No hay turnos"
      empty-text="Crea un turno para permitir reservas online."
      @updated="onDataTableUpdated"
    >
      <template #cell-slot_date="{ row }">
        <span v-if="row.specific_date">{{ formatDate(row.specific_date) }}</span>
        <span v-else>{{ dayName(row.day_of_week) }}</span>
      </template>

      <template #cell-start_time="{ row }">
        {{ row.start_time }} - {{ row.end_time }}
      </template>

      <template #cell-service="{ row }">
        {{ row.service?.name || '-' }}
      </template>

      <template #cell-location="{ row }">
        {{ row.location?.name || 'Todas' }}
      </template>

      <template #cell-slots_available="{ row }">
        {{ row.slots_available }}
      </template>

      <template #cell-is_available="{ row }">
        <span :class="row.is_available ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_available ? 'Activo' : 'Inactivo' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button class="btn btn-sm btn-outline-primary" @click="openEditModal(row)" title="Editar">
            <i class="bi bi-pencil"></i>
          </button>
          <button
            class="btn btn-sm"
            :class="row.is_available ? 'btn-outline-warning' : 'btn-outline-success'"
            @click="toggleSlot(row)"
            title="Cambiar estado"
          >
            <i :class="row.is_available ? 'bi bi-x-lg' : 'bi bi-check-lg'"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteSlot(row)"
            title="Eliminar"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>

    <div ref="createModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Turno</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="createSlot">
            <div class="modal-body">
              <div class="mb-3">
                <FieldSelect
                  id="slot-service"
                  label="Servicio"
                  v-model="form.business_service_id"
                  required
                >
                  <option value="">Seleccionar...</option>
                  <option v-for="svc in services" :key="svc.id" :value="svc.id">{{ svc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="slot-location"
                  label="Ubicacion"
                  v-model="form.business_location_id"
                >
                  <option :value="null">Todas las ubicaciones</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="slot-schedule-type"
                  label="Tipo de horario"
                  v-model="scheduleType"
                >
                  <option value="date">Fecha especifica</option>
                  <option value="recurring">Recurrente (dia de la semana)</option>
                </FieldSelect>
              </div>
              <div v-if="scheduleType === 'date'" class="mb-3">
                <FieldDate
                  id="slot-date"
                  label="Fecha"
                  v-model="form.specific_date"
                  :min="minDate"
                  required
                />
              </div>
              <div v-else class="mb-3">
                <FieldSelect
                  id="slot-day"
                  label="Dia de la semana"
                  v-model="form.day_of_week"
                  required
                >
                  <option :value="0">Domingo</option>
                  <option :value="1">Lunes</option>
                  <option :value="2">Martes</option>
                  <option :value="3">Miercoles</option>
                  <option :value="4">Jueves</option>
                  <option :value="5">Viernes</option>
                  <option :value="6">Sabado</option>
                </FieldSelect>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <FieldTime
                    id="slot-start"
                    label="Hora inicio"
                    v-model="form.start_time"
                    required
                  />
                </div>
                <div class="col-6">
                  <FieldTime
                    id="slot-end"
                    label="Hora fin"
                    v-model="form.end_time"
                    required
                  />
                </div>
              </div>
              <div class="mb-3 mt-3">
                <FieldNumber
                  id="slot-capacity"
                  label="Cupos disponibles"
                  v-model="form.slots_available"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="creating">
                {{ creating ? 'Creando...' : 'Crear Turno' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="editModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Turno</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="updateSlot">
            <div class="modal-body">
              <div class="mb-3">
                <FieldSelect
                  id="edit-slot-service"
                  label="Servicio"
                  v-model="editForm.business_service_id"
                  required
                >
                  <option value="">Seleccionar...</option>
                  <option v-for="svc in services" :key="svc.id" :value="svc.id">{{ svc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="edit-slot-location"
                  label="Ubicacion"
                  v-model="editForm.business_location_id"
                >
                  <option :value="null">Todas las ubicaciones</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="edit-slot-schedule-type"
                  label="Tipo de horario"
                  v-model="editScheduleType"
                >
                  <option value="date">Fecha especifica</option>
                  <option value="recurring">Recurrente (dia de la semana)</option>
                </FieldSelect>
              </div>
              <div v-if="editScheduleType === 'date'" class="mb-3">
                <FieldDate
                  id="edit-slot-date"
                  label="Fecha"
                  v-model="editForm.specific_date"
                  required
                />
              </div>
              <div v-else class="mb-3">
                <FieldSelect
                  id="edit-slot-day"
                  label="Dia de la semana"
                  v-model="editForm.day_of_week"
                  required
                >
                  <option :value="0">Domingo</option>
                  <option :value="1">Lunes</option>
                  <option :value="2">Martes</option>
                  <option :value="3">Miercoles</option>
                  <option :value="4">Jueves</option>
                  <option :value="5">Viernes</option>
                  <option :value="6">Sabado</option>
                </FieldSelect>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <FieldTime
                    id="edit-slot-start"
                    label="Hora inicio"
                    v-model="editForm.start_time"
                    required
                  />
                </div>
                <div class="col-6">
                  <FieldTime
                    id="edit-slot-end"
                    label="Hora fin"
                    v-model="editForm.end_time"
                    required
                  />
                </div>
              </div>
              <div class="row g-3 mt-1">
                <div class="col-6">
                  <FieldNumber
                    id="edit-slot-capacity"
                    label="Cupos disponibles"
                    v-model="editForm.slots_available"
                  />
                </div>
                <div class="col-6 d-flex align-items-end">
                  <FieldSwitch
                    id="edit-slot-active"
                    label="Activo"
                    v-model="editForm.is_available"
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, onMounted, nextTick } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable)
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Citas', href: `/member/listings/${listing.value?.id}/appointments` },
  { label: 'Horarios' },
])

const columns = [
  { key: 'slot_date', label: 'Fecha', sortable: true },
  { key: 'start_time', label: 'Hora', sortable: false },
  { key: 'service', label: 'Servicio', sortable: false },
  { key: 'location', label: 'Ubicacion', sortable: false },
  { key: 'slots_available', label: 'Cupos', sortable: true },
  { key: 'is_available', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const createModalElement = ref(null)
const editModalElement = ref(null)
let createModal = null
let editModal = null

const creating = ref(false)
const saving = ref(false)
const scheduleType = ref('date')
const editScheduleType = ref('date')
const editingSlot = ref(null)

const form = reactive({
  business_service_id: '',
  business_location_id: null,
  day_of_week: null,
  specific_date: '',
  start_time: '09:00',
  end_time: '10:00',
  slots_available: 1,
})

const editForm = reactive({
  id: null,
  business_service_id: '',
  business_location_id: null,
  day_of_week: null,
  specific_date: '',
  start_time: '',
  end_time: '',
  slots_available: 1,
  is_available: true,
})

const minDate = computed(() => new Date().toISOString().split('T')[0])

const dayName = (day) => {
  const days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado']
  return days[day] || '-'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR')
}

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}

const openCreateModal = () => {
  form.business_service_id = ''
  form.business_location_id = null
  form.day_of_week = null
  form.specific_date = ''
  form.start_time = '09:00'
  form.end_time = '10:00'
  form.slots_available = 1
  scheduleType.value = 'date'
  nextTick(() => createModal.show())
}

const closeCreateModal = () => {
  createModal.hide()
}

const openEditModal = (slot) => {
  editingSlot.value = slot
  editForm.id = slot.id
  editForm.business_service_id = slot.business_service_id
  editForm.business_location_id = slot.business_location_id
  editForm.start_time = slot.start_time
  editForm.end_time = slot.end_time
  editForm.slots_available = slot.slots_available
  editForm.is_available = slot.is_available

  if (slot.specific_date) {
    editScheduleType.value = 'date'
    editForm.specific_date = slot.specific_date
    editForm.day_of_week = null
  } else {
    editScheduleType.value = 'recurring'
    editForm.day_of_week = slot.day_of_week
    editForm.specific_date = ''
  }

  nextTick(() => editModal.show())
}

const closeEditModal = () => {
  editModal.hide()
}

const createSlot = () => {
  creating.value = true
  const data = { ...form }
  if (scheduleType.value === 'recurring') {
    delete data.specific_date
  } else {
    delete data.day_of_week
  }
  router.post(`/member/listings/${listing.value.id}/slots`, data, {
    onFinish: () => {
      creating.value = false
      closeCreateModal()
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const updateSlot = () => {
  saving.value = true
  const data = { ...editForm }
  if (editScheduleType.value === 'recurring') {
    delete data.specific_date
  } else {
    delete data.day_of_week
  }
  delete data.id
  router.put(`/member/listings/${listing.value.id}/slots/${editingSlot.value.id}`, data, {
    onFinish: () => {
      saving.value = false
      closeEditModal()
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const toggleSlot = (slot) => {
  router.put(`/member/listings/${listing.value.id}/slots/${slot.id}`, {
    is_available: !slot.is_available,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const deleteSlot = (slot) => {
  if (confirm('Eliminar este turno?')) {
    router.delete(`/member/listings/${listing.value.id}/slots/${slot.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

onMounted(() => {
  createModal = new Modal(createModalElement.value)
  editModal = new Modal(editModalElement.value)
  createModal._element.addEventListener('hidden.bs.modal', () => {
    form.business_service_id = ''
    form.business_location_id = null
    form.day_of_week = null
    form.specific_date = ''
    form.start_time = '09:00'
    form.end_time = '10:00'
    form.slots_available = 1
  })
})
</script>
