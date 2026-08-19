<template>
  <AdminLayout>
    <Head title="Editar permiso" />

    <PageHeader :title="'Editar permiso'" :breadcrumbs="breadcrumbs" backHref="/admin/permissions" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12">
            <FieldText
              id="permission-name"
              label="Nombre del permiso"
              placeholder="Ej: users.view"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12">
            <FieldText
              id="permission-description"
              label="Descripcion"
              placeholder="Describe el permiso"
              v-model="form.description"
              :formError="form.errors.description"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldNumber
              id="permission-order"
              label="Orden"
              placeholder="Ej: 1"
              v-model="form.order"
              :formError="form.errors.order"
              :min="0"
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
            </button>
            <Link href="/admin/permissions" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'

const props = defineProps({
  permission: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  name: props.permission.name,
  description: props.permission.description || '',
  order: props.permission.order ?? '',
})

const breadcrumbs = [
  { label: 'Permisos', href: '/admin/permissions' },
  { label: 'Editar', active: true },
]

const submit = () => {
  form.put(`/admin/permissions/${props.permission.id}`)
}
</script>
