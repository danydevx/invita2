<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Productos</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availableProducts.length === 0" class="text-center py-4 text-muted">
            No hay productos disponibles. Crea productos en la seccion de productos del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">Selecciona hasta {{ maxProducts }} productos. Los productos seleccionados apareceran en tu vCard.</p>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th style="width: 40px;"></th>
                    <th>Nombre</th>
                    <th>Precio</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="product in availableProducts" :key="product.id"
                      :class="{ 'table-primary': isSelected(product.id) }"
                      style="cursor: pointer;"
                      @click="toggleProduct(product)">
                    <td>
                      <input type="checkbox" :checked="isSelected(product.id)" @click.stop="toggleProduct(product)">
                    </td>
                    <td>{{ product.name }}</td>
                    <td>{{ formatPrice(product.price) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-3">
              <small class="text-muted">{{ selectedIds.length }} de {{ maxProducts }} seleccionados</small>
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
  selectedProducts: { type: Array, default: () => [] },
  maxProducts: { type: Number, default: 10 },
})

const emit = defineEmits(['close', 'update'])

const availableProducts = ref([])
const selectedIds = ref([])
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    const products = props.selectedProducts
    if (products && products.length > 0) {
      selectedIds.value = products.map(p => p.id)
    } else {
      selectedIds.value = []
    }
    await fetchProducts()
  }
})

async function fetchProducts() {
  loading.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcard-products`)
    const data = await res.json()
    availableProducts.value = data.products || []
  } catch (e) {
    availableProducts.value = []
  } finally {
    loading.value = false
  }
}

function isSelected(id) {
  return selectedIds.value.includes(id)
}

function toggleProduct(product) {
  if (isSelected(product.id)) {
    selectedIds.value = selectedIds.value.filter(i => i !== product.id)
  } else {
    if (selectedIds.value.length < props.maxProducts) {
      selectedIds.value.push(product.id)
    }
  }
}

function formatPrice(price) {
  if (!price && price !== 0) return '-'
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(price)
}

async function save() {
  if (!props.vcardId || !props.listingId) {
    alert('Error: falta informacion del vCard')
    return
  }
  saving.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/selected-products`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      body: JSON.stringify({ product_ids: selectedIds.value }),
    })
    if (!res.ok) throw new Error('Error saving products')
    const selectedProductsList = availableProducts.value.filter(p => selectedIds.value.includes(p.id))
    emit('update', selectedProductsList)
    emit('close')
  } catch (e) {
    alert('Error al guardar productos')
  } finally {
    saving.value = false
  }
}
</script>
