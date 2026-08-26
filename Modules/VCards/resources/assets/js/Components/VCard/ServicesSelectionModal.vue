<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Servicios</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availableServices.length === 0" class="text-center py-4 text-muted">
            No hay servicios disponibles. Crea servicios en la seccion de servicios del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">Selecciona hasta {{ maxServices }} servicios. Los servicios seleccionados apareceran en tu vCard.</p>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th style="width: 40px;"></th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Duracion</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="service in availableServices" :key="service.id"
                      :class="{ 'table-primary': isSelected(service.id) }"
                      style="cursor: pointer;"
                      @click="toggleService(service)">
                    <td>
                      <input type="checkbox" :checked="isSelected(service.id)" @click.stop="toggleService(service)">
                    </td>
                    <td>{{ service.name }}</td>
                    <td>{{ formatPrice(service.price) }}</td>
                    <td>{{ service.duration_minutes }} min</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-3">
              <small class="text-muted">{{ selectedIds.length }} de {{ maxServices }} seleccionados</small>
            </div>
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
import { ref, computed, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  listingId: { type: [Number, String], required: true },
  vcardId: { type: [Number, String], required: true },
  selectedServices: { type: Array, default: () => [] },
  maxServices: { type: Number, default: 10 },
})

const emit = defineEmits(['close', 'update'])

const availableServices = ref([])
const selectedIds = ref([])
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    console.log('Modal opened with listingId:', props.listingId, 'vcardId:', props.vcardId)
    console.log('Selected services from props:', props.selectedServices)
    console.log('Props type:', typeof props.selectedServices, Array.isArray(props.selectedServices))
    const services = props.selectedServices
    if (services && services.length > 0) {
      selectedIds.value = services.map(s => s.id)
      console.log('Set selectedIds to:', selectedIds.value)
    } else {
      selectedIds.value = []
      console.log('Set selectedIds to empty array')
    }
    await fetchServices()
  }
})

async function fetchServices() {
  loading.value = true
  try {
    console.log('Fetching from:', `/member/listings/${props.listingId}/vcard-services`)
    const res = await fetch(`/member/listings/${props.listingId}/vcard-services`)
    console.log('Fetch response:', res.status)
    const data = await res.json()
    console.log('Services data:', data)
    availableServices.value = data.services || []
  } catch (e) {
    console.error(e)
    availableServices.value = []
  } finally {
    loading.value = false
  }
}

function isSelected(id) {
  return selectedIds.value.includes(id)
}

function toggleService(service) {
  if (isSelected(service.id)) {
    selectedIds.value = selectedIds.value.filter(i => i !== service.id)
  } else {
    if (selectedIds.value.length < props.maxServices) {
      selectedIds.value.push(service.id)
    }
  }
}

function formatPrice(price) {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(price)
}

async function save() {
  if (!props.vcardId || !props.listingId) {
    alert('Error: falta información del vCard')
    return
  }
  saving.value = true
  try {
    console.log('Saving services:', selectedIds.value)
    console.log('URL:', `/member/listings/${props.listingId}/vcards/${props.vcardId}/selected-services`)
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/selected-services`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      body: JSON.stringify({ service_ids: selectedIds.value }),
    })
    console.log('Response status:', res.status)
    if (!res.ok) throw new Error('Error saving services')
    const data = await res.json()
    console.log('Response data:', data)
    const selectedServicesList = availableServices.value.filter(s => selectedIds.value.includes(s.id))
    console.log('Emitting update with:', selectedServicesList)
    emit('update', selectedServicesList)
    emit('close')
  } catch (e) {
    console.error(e)
    alert('Error al guardar servicios')
  } finally {
    saving.value = false
  }
}
</script>
