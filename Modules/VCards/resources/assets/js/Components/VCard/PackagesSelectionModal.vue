<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Paquetes</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availablePackages.length === 0" class="text-center py-4 text-muted">
            No hay paquetes disponibles. Crea paquetes en la seccion de paquetes del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">Selecciona hasta {{ maxPackages }} paquetes. Los paquetes seleccionados apareceran en tu vCard.</p>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th style="width: 40px;"></th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Precio Promo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pkg in availablePackages" :key="pkg.id"
                      :class="{ 'table-primary': isSelected(pkg.id) }"
                      style="cursor: pointer;"
                      @click="togglePackage(pkg)">
                    <td>
                      <input type="checkbox" :checked="isSelected(pkg.id)" @click.stop="togglePackage(pkg)">
                    </td>
                    <td>{{ pkg.title }}</td>
                    <td>{{ formatPrice(pkg.price) }}</td>
                    <td>{{ pkg.promo_price ? formatPrice(pkg.promo_price) : '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-3">
              <small class="text-muted">{{ selectedIds.length }} de {{ maxPackages }} seleccionados</small>
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
  selectedPackages: { type: Array, default: () => [] },
  maxPackages: { type: Number, default: 10 },
})

const emit = defineEmits(['close', 'update'])

const availablePackages = ref([])
const selectedIds = ref([])
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    const packages = props.selectedPackages
    if (packages && packages.length > 0) {
      selectedIds.value = packages.map(p => p.id)
    } else {
      selectedIds.value = []
    }
    await fetchPackages()
  }
})

async function fetchPackages() {
  loading.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcard-packages`)
    const data = await res.json()
    availablePackages.value = data.packages || []
  } catch (e) {
    availablePackages.value = []
  } finally {
    loading.value = false
  }
}

function isSelected(id) {
  return selectedIds.value.includes(id)
}

function togglePackage(pkg) {
  if (isSelected(pkg.id)) {
    selectedIds.value = selectedIds.value.filter(i => i !== pkg.id)
  } else {
    if (selectedIds.value.length < props.maxPackages) {
      selectedIds.value.push(pkg.id)
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
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/selected-packages`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      body: JSON.stringify({ package_ids: selectedIds.value }),
    })
    if (!res.ok) throw new Error('Error saving packages')
    const selectedPackagesList = availablePackages.value.filter(p => selectedIds.value.includes(p.id))
    emit('update', selectedPackagesList)
    emit('close')
  } catch (e) {
    alert('Error al guardar paquetes')
  } finally {
    saving.value = false
  }
}
</script>
