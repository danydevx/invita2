<template>
  <AdminLayout>
    <Head title="Nuevo flag" />

    <PageHeader :title="'Nuevo flag'" :breadcrumbs="breadcrumbs" backHref="/admin/feature-flags" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <label class="form-label">Key</label>
            <input v-model="form.key" type="text" class="form-control" :class="{ 'is-invalid': form.errors.key }" />
            <div v-if="form.errors.key" class="invalid-feedback">{{ form.errors.key }}</div>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Nombre</label>
            <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }" />
            <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
          </div>
          <div class="col-12">
            <label class="form-label">Descripcion</label>
            <textarea v-model="form.description" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Tipo</label>
            <select v-model="form.type" class="form-select">
              <option value="boolean">boolean</option>
              <option value="string">string</option>
              <option value="integer">integer</option>
            </select>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Default</label>
            <input v-model="form.default_value" type="text" class="form-control" />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Activo</label>
            <select v-model="form.is_active" class="form-select">
              <option :value="true">Si</option>
              <option :value="false">No</option>
            </select>
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="submit" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Crear flag' }}
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
  name: '',
  description: '',
  type: 'boolean',
  default_value: '',
  is_active: true,
})

const breadcrumbs = [
  { label: 'Feature Flags', href: '/admin/feature-flags' },
  { label: 'Nuevo', active: true },
]

const submit = () => {
  form.post('/admin/feature-flags')
}
</script>
