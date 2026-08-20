<template>
  <AdminLayout>
    <Head title="Nueva galería" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <label class="form-label">Nombre</label>
            <input v-model="form.name" class="form-control" required />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Marcar como principal</label>
            <div class="form-check form-switch mt-2">
              <input v-model="form.is_primary" type="checkbox" class="form-check-input" id="gallery-primary" />
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
              <input v-model="form.is_active" type="checkbox" class="form-check-input" id="gallery-active" />
              <label class="form-check-label" for="gallery-active">Activa</label>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Orden</label>
            <input v-model.number="form.sort_order" type="number" class="form-control" min="0" />
          </div>
          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Crear galería' }}
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

const form = useForm({
  name: '',
  description: '',
  is_primary: false,
  is_active: true,
  sort_order: 0,
})

const submit = () => {
  form.post(`/admin/listings/${listing.value.id}/galleries`)
}
</script>