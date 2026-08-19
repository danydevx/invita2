<template>
  <AdminLayout>
    <Head title="Amenidades por Tipo" />

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <Link href="/admin/modules/properties/types" class="btn btn-outline-secondary btn-sm mb-2">
          <i class="bi bi-arrow-left me-1"></i>
          Volver a Tipos
        </Link>
        <h1 class="h3 mb-0">Amenidades para: {{ propertyType.name }}</h1>
      </div>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <p class="text-muted mb-4">
          Selecciona las amenidades que estarán disponibles para este tipo de propiedad.
          Las amenidades no seleccionadas no aparecerán en el formulario de edición.
        </p>

        <form @submit.prevent="saveAmenities">
          <div class="row g-3">
            <div v-for="amenity in allAmenities" :key="amenity.id" class="col-md-4 col-lg-3">
              <div class="form-check">
                <input
                  :id="'amenity-' + amenity.id"
                  v-model="selectedIds"
                  type="checkbox"
                  :value="amenity.id"
                  class="form-check-input"
                >
                <label :for="'amenity-' + amenity.id" class="form-check-label d-flex align-items-center gap-2">
                  <i :class="amenity.icon || 'bi bi-star'" style="font-size: 1.1rem;"></i>
                  {{ amenity.name }}
                </label>
              </div>
            </div>
          </div>

          <div v-if="allAmenities.length === 0" class="text-center py-4 text-muted">
            <p>No hay amenidades creadas. <Link href="/admin/modules/properties/amenities">Crear amenidades</Link></p>
          </div>

          <hr class="my-4">

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving">Guardando...</span>
              <span v-else>Guardar Amenidades</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="assignedAmenities.length > 0" class="card border-0 shadow-sm mt-4">
      <div class="card-header bg-white">
        <h5 class="mb-0">Amenidades Asignadas ({{ assignedAmenities.length }})</h5>
      </div>
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
          <span
            v-for="amenity in assignedAmenities"
            :key="amenity.id"
            class="badge bg-primary d-flex align-items-center gap-2 p-2"
          >
            <i :class="amenity.icon || 'bi bi-star'"></i>
            {{ amenity.name }}
          </span>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  propertyType: { type: Object, required: true },
  allAmenities: { type: Array, default: () => [] },
  assignedAmenities: { type: Array, default: () => [] },
  assignedIds: { type: Array, default: () => [] },
})

const selectedIds = ref([...props.assignedIds])
const saving = ref(false)

const saveAmenities = () => {
  saving.value = true
  router.post(`/admin/modules/properties/types/${props.propertyType.id}/amenities`, {
    amenity_ids: selectedIds.value,
  }, {
    preserveScroll: true,
    onFinish: () => { saving.value = false },
  })
}
</script>
