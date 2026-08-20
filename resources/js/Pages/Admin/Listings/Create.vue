<template>
  <AdminLayout>
    <Head title="Nuevo Negocio" />

    <PageHeader title="Nuevo Negocio" :breadcrumbs="breadcrumbs" backHref="/admin/listings" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label for="business-user" class="form-label">Propietario</label>
              <select id="business-user" class="form-select" v-model="form.user_id" :class="{ 'is-invalid': form.errors.user_id }">
                <option value="">Seleccionar usuario...</option>
                <option v-for="user in users" :key="user.id" :value="user.id">
                  {{ user.name }} ({{ user.email }})
                </option>
              </select>
              <div v-if="form.errors.user_id" class="invalid-feedback">{{ form.errors.user_id }}</div>
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-name"
                label="Nombre"
                placeholder="Mi Negocio"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-slug"
                label="Slug"
                placeholder="mi-negocio"
                v-model="form.slug"
                :formError="form.errors.slug"
                required
              />
            </div>

            <div class="col-12 col-md-3">
              <label for="business-timezone" class="form-label">Zona Horaria</label>
              <select id="business-timezone" class="form-select" v-model="form.timezone" :class="{ 'is-invalid': form.errors.timezone }">
                <option value="America/Mexico_City">Ciudad de México (America/Mexico_City)</option>
              </select>
              <div v-if="form.errors.timezone" class="invalid-feedback">{{ form.errors.timezone }}</div>
            </div>

            <div class="col-12 col-md-3">
              <label for="business-currency" class="form-label">Moneda</label>
              <select id="business-currency" class="form-select" v-model="form.currency" :class="{ 'is-invalid': form.errors.currency }">
                <option value="MXN">MXN - Peso Mexicano</option>
              </select>
              <div v-if="form.errors.currency" class="invalid-feedback">{{ form.errors.currency }}</div>
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="business-active"
                label="Activo"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="business-published"
                label="Publicado"
                v-model="form.is_published"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Creando...' : 'Crear Negocio' }}
            </button>
            <Link href="/admin/listings" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  user_id: '',
  name: '',
  slug: '',
  timezone: 'America/Mexico_City',
  currency: 'MXN',
  is_active: true,
  is_published: false,
})

const breadcrumbs = [
  { label: 'Negocios', href: '/admin/listings' },
  { label: 'Nuevo', active: true },
]

const submit = () => {
  form.post('/admin/listings')
}
</script>
