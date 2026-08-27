<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Ubicaci&oacute;n</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availableLocations.length === 0" class="text-center py-4 text-muted">
            No hay ubicaciones disponibles. Crea ubicaciones en la secci&oacute;n de ubicaciones del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">Selecciona una ubicaci&oacute;n para mostrar en tu vCard.</p>

            <div class="list-group">
              <button
                v-for="location in availableLocations"
                :key="location.id"
                type="button"
                class="list-group-item list-group-item-action"
                :class="{ 'active': selectedLocationId === location.id }"
                @click="selectLocation(location)"
              >
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <strong>{{ location.name }}</strong>
                    <small class="d-block text-muted">{{ location.address_line_1 }}</small>
                    <small class="d-block">{{ location.city }}, {{ location.state }}</small>
                  </div>
                  <div v-if="selectedLocationId === location.id">
                    <i class="bi bi-check-circle-fill text-success"></i>
                  </div>
                </div>
              </button>
            </div>

            <div v-if="selectedLocationId" class="mt-3 p-3 bg-light rounded">
              <small class="text-muted">Ubicaci&oacute;n seleccionada</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
          <button type="button" class="btn btn-danger" @click="clearLocation" v-if="selectedLocationId">
            Quitar ubicaci&oacute;n
          </button>
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
  selectedLocation: { type: Object, default: null },
})

const emit = defineEmits(['close', 'update'])

const availableLocations = ref([])
const selectedLocationId = ref(null)
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    selectedLocationId.value = props.selectedLocation?.id || null
    await fetchLocations()
  }
})

async function fetchLocations() {
  loading.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcard-locations`)
    const data = await res.json()
    availableLocations.value = data.locations || []
  } catch (e) {
    availableLocations.value = []
  } finally {
    loading.value = false
  }
}

function selectLocation(location) {
  selectedLocationId.value = location.id
}

function clearLocation() {
  selectedLocationId.value = null
}

async function save() {
  saving.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/location`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({ location_id: selectedLocationId.value }),
    })
    if (!res.ok) throw new Error('Error saving location')

    const selectedLoc = availableLocations.value.find(l => l.id === selectedLocationId.value) || null
    emit('update', selectedLoc)
    emit('close')
  } catch (e) {
    alert('Error al guardar la ubicaci&oacute;n')
  } finally {
    saving.value = false
  }
}
</script>
