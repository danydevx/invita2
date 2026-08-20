<template>
  <AdminLayout>
    <Head title="Editar galería" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <label class="form-label">Nombre</label>
            <input
              v-model="form.name"
              class="form-control"
              :readonly="isPrimary"
              required
            />
            <div v-if="isPrimary" class="form-text">
              La galería principal siempre se llama "Galería principal".
            </div>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Principal</label>
            <div class="form-check form-switch mt-2">
              <input
                v-model="form.is_primary"
                type="checkbox"
                class="form-check-input"
                id="gallery-primary"
                :disabled="isPrimary"
              />
              <label class="form-check-label" for="gallery-primary">Principal</label>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea v-model="form.description" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Activa</label>
            <div class="form-check form-switch mt-2">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="form-check-input"
                id="gallery-active"
                :disabled="isPrimary"
              />
              <label class="form-check-label" for="gallery-active">Activa</label>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Orden</label>
            <input v-model.number="form.sort_order" type="number" class="form-control" min="0" />
          </div>
          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
            <Link :href="`/admin/listings/${listing.id}/galleries`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const gallery = computed(() => page.props.gallery)
const isPrimary = computed(() => !!gallery.value?.is_primary)

const form = useForm({
  name: gallery.value?.name,
  description: gallery.value?.description || '',
  is_primary: !!gallery.value?.is_primary,
  is_active: !!gallery.value?.is_active,
  sort_order: gallery.value?.sort_order || 0,
})

const submit = () => {
  form.put(`/admin/listings/${listing.value.id}/galleries/${gallery.value.id}`)
}
</script>