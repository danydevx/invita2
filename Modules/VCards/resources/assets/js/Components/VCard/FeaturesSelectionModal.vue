<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Características</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availableFeatures.length === 0" class="text-center py-4 text-muted">
            No hay características disponibles. Agrega características en la sección del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">Selecciona las características que aparecerán en tu vCard.</p>
            <div class="row g-2">
              <div v-for="feature in availableFeatures" :key="feature.id" class="col-md-6">
                <div
                  class="card"
                  :class="{ 'border-primary': isSelected(feature.id), 'bg-light': isSelected(feature.id) }"
                  style="cursor: pointer;"
                  @click="toggleFeature(feature)"
                >
                  <div class="card-body py-2 px-3">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" :checked="isSelected(feature.id)" @click.stop="toggleFeature(feature)">
                      <label class="form-check-label ms-2">
                        <i v-if="feature.icon" :class="feature.icon" class="me-2"></i>
                        <strong>{{ feature.title }}</strong>
                        <small v-if="feature.description" class="text-muted d-block">{{ feature.description }}</small>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-3">
              <small class="text-muted">{{ selectedIds.length }} características seleccionadas</small>
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
import { ref, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  listingId: { type: [Number, String], required: true },
  vcardId: { type: [Number, String], required: true },
  selectedFeatures: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'update'])

const availableFeatures = ref([])
const selectedIds = ref([])
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    if (props.selectedFeatures && props.selectedFeatures.length > 0) {
      selectedIds.value = props.selectedFeatures.map(f => f.id)
    } else {
      selectedIds.value = []
    }
    await fetchFeatures()
  }
})

async function fetchFeatures() {
  loading.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcard-features`)
    const data = await res.json()
    availableFeatures.value = data.features || []
  } catch (e) {
    availableFeatures.value = []
  } finally {
    loading.value = false
  }
}

function isSelected(id) {
  return selectedIds.value.includes(id)
}

function toggleFeature(feature) {
  if (isSelected(feature.id)) {
    selectedIds.value = selectedIds.value.filter(i => i !== feature.id)
  } else {
    selectedIds.value.push(feature.id)
  }
}

async function save() {
  if (!props.vcardId || !props.listingId) {
    alert('Error: falta información del vCard')
    return
  }
  saving.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/selected-features`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      body: JSON.stringify({ feature_ids: selectedIds.value }),
    })
    if (!res.ok) throw new Error('Error saving features')
    const selectedFeaturesList = availableFeatures.value.filter(f => selectedIds.value.includes(f.id))
    emit('update', selectedFeaturesList)
    emit('close')
  } catch (e) {
    alert('Error al guardar características')
  } finally {
    saving.value = false
  }
}
</script>
