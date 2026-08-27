<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Testimonios</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availableTestimonials.length === 0" class="text-center py-4 text-muted">
            No hay testimonios disponibles. Crea testimonios en la seccion de reseñas del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">Selecciona hasta {{ maxTestimonials }} testimonios. Los testimonios seleccionados apareceran en tu vCard.</p>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th style="width: 40px;"></th>
                    <th>Cliente</th>
                    <th>Comentario</th>
                    <th>Rating</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="testimonial in availableTestimonials" :key="testimonial.id"
                      :class="{ 'table-primary': isSelected(testimonial.id) }"
                      style="cursor: pointer;"
                      @click="toggleTestimonial(testimonial)">
                    <td>
                      <input type="checkbox" :checked="isSelected(testimonial.id)" @click.stop="toggleTestimonial(testimonial)">
                    </td>
                    <td>{{ testimonial.client_name }}<br><small class="text-muted">{{ testimonial.company || '-' }}</small></td>
                    <td>{{ truncate(testimonial.comment, 80) }}</td>
                    <td>
                      <div class="text-warning">
                        <i v-for="star in 5" :key="star" class="bi" :class="star <= testimonial.rating ? 'bi-star-fill' : 'bi-star'"></i>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-3">
              <small class="text-muted">{{ selectedIds.length }} de {{ maxTestimonials }} seleccionados</small>
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
  selectedTestimonials: { type: Array, default: () => [] },
  maxTestimonials: { type: Number, default: 10 },
})

const emit = defineEmits(['close', 'update'])

const availableTestimonials = ref([])
const selectedIds = ref([])
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    const testimonials = props.selectedTestimonials
    if (testimonials && testimonials.length > 0) {
      selectedIds.value = testimonials.map(t => t.id)
    } else {
      selectedIds.value = []
    }
    await fetchTestimonials()
  }
})

async function fetchTestimonials() {
  loading.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcard-testimonials`)
    const data = await res.json()
    availableTestimonials.value = data.testimonials || []
  } catch (e) {
    availableTestimonials.value = []
  } finally {
    loading.value = false
  }
}

function isSelected(id) {
  return selectedIds.value.includes(id)
}

function toggleTestimonial(testimonial) {
  if (isSelected(testimonial.id)) {
    selectedIds.value = selectedIds.value.filter(i => i !== testimonial.id)
  } else {
    if (selectedIds.value.length < props.maxTestimonials) {
      selectedIds.value.push(testimonial.id)
    }
  }
}

function truncate(text, length) {
  if (!text) return ''
  if (text.length <= length) return text
  return text.substring(0, length) + '...'
}

async function save() {
  if (!props.vcardId || !props.listingId) {
    alert('Error: falta informacion del vCard')
    return
  }
  saving.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/selected-testimonials`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      body: JSON.stringify({ review_ids: selectedIds.value }),
    })
    if (!res.ok) throw new Error('Error saving testimonials')
    const selectedTestimonialsList = availableTestimonials.value.filter(t => selectedIds.value.includes(t.id))
    emit('update', selectedTestimonialsList)
    emit('close')
  } catch (e) {
    alert('Error al guardar testimonios')
  } finally {
    saving.value = false
  }
}
</script>
