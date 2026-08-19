<template>
  <AdminLayout>
    <Head title="Nuevo documento legal" />

    <PageHeader :title="'Nuevo documento legal'" :breadcrumbs="breadcrumbs" backHref="/admin/legal-documents" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <label class="form-label">Key</label>
            <input v-model="form.key" type="text" class="form-control" :class="{ 'is-invalid': form.errors.key }" />
            <div v-if="form.errors.key" class="invalid-feedback">{{ form.errors.key }}</div>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Titulo</label>
            <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': form.errors.title }" />
            <div v-if="form.errors.title" class="invalid-feedback">{{ form.errors.title }}</div>
          </div>
          <div class="col-12">
            <label class="form-label">Contenido</label>
            <textarea v-model="form.content" class="form-control" rows="10" :class="{ 'is-invalid': form.errors.content }"></textarea>
            <div v-if="form.errors.content" class="invalid-feedback">{{ form.errors.content }}</div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-check">
              <input id="legal-required" v-model="form.is_required" type="checkbox" class="form-check-input" />
              <label class="form-check-label" for="legal-required">Requiere aceptacion</label>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-check">
              <input id="legal-reaccept" v-model="form.requires_reaccept" type="checkbox" class="form-check-input" />
              <label class="form-check-label" for="legal-reaccept">Requiere reaceptacion</label>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-check">
              <input id="legal-active" v-model="form.is_active" type="checkbox" class="form-check-input" />
              <label class="form-check-label" for="legal-active">Activo</label>
            </div>
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="submit" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Crear documento' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const form = useForm({
  key: '',
  title: '',
  content: '',
  is_required: true,
  requires_reaccept: true,
  is_active: true,
})

const breadcrumbs = [
  { label: 'Documentos legales', href: '/admin/legal-documents' },
  { label: 'Nuevo', active: true },
]

const submit = () => {
  form.post('/admin/legal-documents')
}
</script>
