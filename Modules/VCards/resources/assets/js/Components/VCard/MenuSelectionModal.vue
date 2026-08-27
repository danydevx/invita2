<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1055;" @click.self="$emit('close')">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Categorías del Menú</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availableCategories.length === 0" class="text-center py-4 text-muted">
            No hay categorías de menú disponibles. Crea categorías en la sección de menú del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">
              Selecciona hasta <strong>{{ maxCategories }}</strong> categorías con <strong>{{ maxProductsPerCategory }}</strong> productos máximo cada una.
            </p>

            <div class="mb-3">
              <small class="text-muted">{{ selectedCount }} categorías seleccionadas de {{ maxCategories }}允许idas</small>
            </div>

            <div class="accordion" id="menuAccordion">
              <div class="accordion-item" v-for="(cat, catIndex) in availableCategories" :key="cat.id">
                <h2 class="accordion-header">
                  <button class="accordion-button" type="button"
                    :class="{ 'collapsed': !isCategorySelected(cat.id) }"
                    @click="toggleCategory(cat)">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox"
                        :checked="isCategorySelected(cat.id)"
                        @click.stop
                        @change="toggleCategory(cat)">
                      <label class="form-check-label fw-bold">
                        {{ cat.title }}
                        <small class="text-muted fw-normal">({{ cat.products?.length || 0 }} productos)</small>
                      </label>
                    </div>
                  </button>
                </h2>
                <div class="accordion-collapse collapse" :class="{ 'show': isCategorySelected(cat.id) }">
                  <div class="accordion-body">
                    <p v-if="cat.description" class="small text-muted mb-2">{{ cat.description }}</p>

                    <div v-if="getSelectedProducts(cat.id).length > 0" class="mb-2">
                      <small class="text-primary">{{ getSelectedProducts(cat.id).length }} de {{ maxProductsPerCategory }} productos seleccionados</small>
                    </div>

                    <div class="row">
                      <div class="col-md-6 col-lg-4 mb-2" v-for="product in cat.products" :key="product.id">
                        <div class="card"
                          :class="{ 'border-primary': isProductSelected(cat.id, product.id) }"
                          style="cursor: pointer"
                          @click="toggleProduct(cat.id, product)">
                          <div class="card-body py-2 px-3">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox"
                                :checked="isProductSelected(cat.id, product.id)"
                                @click.stop
                                @change="toggleProduct(cat.id, product)">
                              <label class="form-check-label w-100">
                                <span class="d-block">{{ product.title }}</span>
                                <small class="text-muted">{{ formatPrice(product.price) }}</small>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
  selectedMenu: { type: Array, default: () => [] },
  maxCategories: { type: Number, default: 6 },
  maxProductsPerCategory: { type: Number, default: 5 },
})

const emit = defineEmits(['close', 'update'])

const availableCategories = ref([])
const selectedCategories = ref({})
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    initSelection()
    await fetchCategories()
  }
})

function initSelection() {
  const cats = {}
  for (const cat of props.selectedMenu) {
    cats[cat.id] = cat.product_ids || []
  }
  selectedCategories.value = cats
}

async function fetchCategories() {
  loading.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcard-menus`)
    const data = await res.json()
    availableCategories.value = data.categories || []
  } catch (e) {
    availableCategories.value = []
  } finally {
    loading.value = false
  }
}

function isCategorySelected(catId) {
  return catId in selectedCategories.value
}

function isProductSelected(catId, productId) {
  if (!isCategorySelected(catId)) return false
  return selectedCategories.value[catId]?.includes(productId)
}

function getSelectedProducts(catId) {
  if (!isCategorySelected(catId)) return []
  return availableCategories.value.find(c => c.id === catId)?.products?.filter(p =>
    selectedCategories.value[catId]?.includes(p.id)
  ) || []
}

function toggleCategory(cat) {
  if (isCategorySelected(cat.id)) {
    delete selectedCategories.value[cat.id]
  } else {
    if (Object.keys(selectedCategories.value).length >= props.maxCategories) {
      alert(`Máximo ${props.maxCategories} categorías permitidas`)
      return
    }
    selectedCategories.value[cat.id] = []
  }
}

function toggleProduct(catId, product) {
  if (!isCategorySelected(catId)) return

  const products = selectedCategories.value[catId]
  if (products.includes(product.id)) {
    selectedCategories.value[catId] = products.filter(id => id !== product.id)
  } else {
    if (products.length >= props.maxProductsPerCategory) {
      alert(`Máximo ${props.maxProductsPerCategory} productos por categoría`)
      return
    }
    selectedCategories.value[catId] = [...products, product.id]
  }
}

const selectedCount = computed(() => Object.keys(selectedCategories.value).length)

function formatPrice(price) {
  if (!price && price !== 0) return ''
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(price)
}

async function save() {
  saving.value = true
  try {
    const categories = Object.entries(selectedCategories.value).map(([catId, productIds]) => ({
      category_id: parseInt(catId),
      product_ids: productIds,
    }))

    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/menu-categories`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({ categories }),
    })
    if (!res.ok) throw new Error('Error saving menu')

    const result = availableCategories.value
      .filter(cat => isCategorySelected(cat.id))
      .map(cat => ({
        id: cat.id,
        title: cat.title,
        description: cat.description,
        product_ids: selectedCategories.value[cat.id],
        products: cat.products.filter(p => selectedCategories.value[cat.id]?.includes(p.id)).map(p => ({
          id: p.id,
          title: p.title,
          description: p.description,
          price: p.price,
          image: p.image,
        })),
      }))

    emit('update', result)
    emit('close')
  } catch (e) {
    alert('Error al guardar el menú')
  } finally {
    saving.value = false
  }
}
</script>
