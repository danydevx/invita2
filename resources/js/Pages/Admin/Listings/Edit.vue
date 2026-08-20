<template>
  <AdminLayout>
    <Head title="Editar Listado" />

    <PageHeader title="Editar Listado" :breadcrumbs="breadcrumbs" backHref="/admin/listings">
      <template #actions>
        <Link :href="`/admin/listings/${listing.id}/modules`" class="btn btn-outline-secondary">
          Modulos
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label for="listing-user" class="form-label">Propietario</label>
              <select id="listing-user" class="form-select" v-model="form.user_id" :class="{ 'is-invalid': form.errors.user_id }">
                <option value="">Seleccionar usuario...</option>
                <option v-for="user in users" :key="user.id" :value="user.id">
                  {{ user.name }} ({{ user.email }})
                </option>
              </select>
              <div v-if="form.errors.user_id" class="invalid-feedback">{{ form.errors.user_id }}</div>
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="listing-name"
                label="Nombre"
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="listing-slug"
                label="Slug"
                v-model="form.slug"
                :formError="form.errors.slug"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <label for="listing-industry" class="form-label">Industria</label>
              <select id="listing-industry" class="form-select" v-model="form.industry_id">
                <option :value="null">Sin industria</option>
                <option v-for="industry in industries" :key="industry.id" :value="industry.id">
                  {{ industry.name }}
                </option>
              </select>
            </div>

            <div class="col-12 col-md-3">
              <label for="listing-timezone" class="form-label">Zona Horaria</label>
              <select id="listing-timezone" class="form-select" v-model="form.timezone" :class="{ 'is-invalid': form.errors.timezone }">
                <option value="America/Mexico_City">Ciudad de Mexico (America/Mexico_City)</option>
              </select>
              <div v-if="form.errors.timezone" class="invalid-feedback">{{ form.errors.timezone }}</div>
            </div>

            <div class="col-12 col-md-3">
              <label for="listing-currency" class="form-label">Moneda</label>
              <select id="listing-currency" class="form-select" v-model="form.currency" :class="{ 'is-invalid': form.errors.currency }">
                <option value="MXN">MXN - Peso Mexicano</option>
              </select>
              <div v-if="form.errors.currency" class="invalid-feedback">{{ form.errors.currency }}</div>
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="listing-active"
                label="Activo"
                v-model="form.is_active"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSwitch
                id="listing-published"
                label="Publicado"
                v-model="form.is_published"
              />
            </div>

            <div class="col-12 col-md-6">
              <label for="listing-theme" class="form-label">Theme del Minisite</label>
              <select id="listing-theme" class="form-select" v-model="form.minisite_theme_id">
                <option :value="null">Por defecto</option>
                <option v-for="theme in themes" :key="theme.id" :value="theme.id">
                  {{ theme.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar Listado' }}
            </button>
            <Link href="/admin/listings" class="btn btn-outline-secondary">Cancelar</Link>
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
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  listing: {
    type: Object,
    required: true,
  },
  users: {
    type: Array,
    default: () => [],
  },
  themes: {
    type: Array,
    default: () => [],
  },
  industries: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  user_id: props.listing.user_id,
  name: props.listing.name,
  slug: props.listing.slug,
  industry_id: props.listing.industry_id || null,
  timezone: props.listing.timezone || 'America/Mexico_City',
  currency: props.listing.currency || 'MXN',
  is_active: !!props.listing.is_active,
  is_published: !!props.listing.is_published,
  minisite_theme_id: props.listing.minisite_theme_id || null,
})

const breadcrumbs = [
  { label: 'Listados', href: '/admin/listings' },
  { label: 'Editar', active: true },
]

const submit = () => {
  form.put(`/admin/listings/${props.listing.id}`)
}
</script>
