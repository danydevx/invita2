<template>
  <AdminLayout>
    <Head title="Crear rol" />

    <PageHeader :title="'Crear rol'" :breadcrumbs="breadcrumbs" backHref="/admin/roles" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12">
            <FieldText
              id="role-name"
              label="Nombre del rol"
              placeholder="Ej: Administrador"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12">
            <div class="form-check">
              <input
                id="role-blocked"
                v-model="form.blocked"
                class="form-check-input"
                type="checkbox"
              />
              <label class="form-check-label" for="role-blocked">
                Bloquear asignacion a usuarios
              </label>
            </div>
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar' }}
            </button>
            <Link href="/admin/roles" class="btn btn-outline-secondary">Cancelar</Link>
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

const form = useForm({
  name: '',
  blocked: false,
})

const breadcrumbs = [
  { label: 'Roles', href: '/admin/roles' },
  { label: 'Crear', active: true },
]

const submit = () => {
  form.post('/admin/roles')
}
</script>
