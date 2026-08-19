<template>
  <AdminLayout>
    <Head title="Editar Negocio" />

    <PageHeader title="Editar Negocio" :breadcrumbs="breadcrumbs" backHref="/admin/businesses">
      <template #actions>
        <Link :href="`/admin/businesses/${business.id}/modules`" class="btn btn-outline-secondary">
          Modulos
        </Link>
      </template>
    </PageHeader>

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
                v-model="form.name"
                :formError="form.errors.name"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-slug"
                label="Slug"
                v-model="form.slug"
                :formError="form.errors.slug"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <label for="business-type" class="form-label">Tipo de Negocio</label>
              <select id="business-type" class="form-select" v-model="form.business_type" :class="{ 'is-invalid': form.errors.business_type }">
                <option value="">Seleccionar...</option>
                <option v-for="(label, value) in businessTypes" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
              <div v-if="form.errors.business_type" class="invalid-feedback">{{ form.errors.business_type }}</div>
            </div>

            <div class="col-12 col-md-6">
              <label for="business-industry" class="form-label">Industria</label>
              <select id="business-industry" class="form-select" v-model="form.industry_id">
                <option :value="null">Sin industria</option>
                <option v-for="industry in industries" :key="industry.id" :value="industry.id">
                  {{ industry.name }}
                </option>
              </select>
            </div>

            <div class="col-12">
              <FieldText
                id="business-description"
                label="Descripcion"
                v-model="form.description"
                :formError="form.errors.description"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-phone"
                label="Telefono"
                v-model="form.phone"
                :formError="form.errors.phone"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-email"
                label="Email"
                v-model="form.email"
                :formError="form.errors.email"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-website"
                label="Website"
                v-model="form.website"
                :formError="form.errors.website"
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldText
                id="business-timezone"
                label="Zona Horaria"
                v-model="form.timezone"
                :formError="form.errors.timezone"
              />
            </div>

            <div class="col-12 col-md-3">
              <FieldText
                id="business-currency"
                label="Moneda"
                v-model="form.currency"
                :formError="form.errors.currency"
              />
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

            <div class="col-12 col-md-6">
              <label for="business-theme" class="form-label">Theme del Minisite</label>
              <select id="business-theme" class="form-select" v-model="form.minisite_theme_id">
                <option :value="null">Por defecto (según tipo de negocio)</option>
                <option v-for="theme in themes" :key="theme.id" :value="theme.id">
                  {{ theme.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Actualizando...' : 'Actualizar Negocio' }}
            </button>
            <Link href="/admin/businesses" class="btn btn-outline-secondary">Cancelar</Link>
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
  business: {
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

const businessTypes = {
  'barber_shop': 'Barberia',
  'beauty_salon': 'Salon de Belleza',
  'dentist': 'Dentista',
  'medical_clinic': 'Clinica Medica',
  'doctor': 'Doctor',
  'spa': 'Spa',
  'veterinarian': 'Veterinario',
  'physiotherapist': 'Fisioterapeuta',
  'psychologist': 'Psicologo',
  'nutritionist': 'Nutricionista',
  'tattoo_studio': 'Estudio de Tattoo',
  'generic': 'General',
}

const form = useForm({
  user_id: props.business.user_id,
  name: props.business.name,
  slug: props.business.slug,
  business_type: props.business.business_type,
  industry_id: props.business.industry_id || null,
  description: props.business.description || '',
  phone: props.business.phone || '',
  email: props.business.email || '',
  website: props.business.website || '',
  timezone: props.business.timezone || 'UTC',
  currency: props.business.currency || 'USD',
  is_active: !!props.business.is_active,
  is_published: !!props.business.is_published,
  minisite_theme_id: props.business.minisite_theme_id || null,
})

const breadcrumbs = [
  { label: 'Negocios', href: '/admin/businesses' },
  { label: 'Editar', active: true },
]

const submit = () => {
  form.put(`/admin/businesses/${props.business.id}`)
}
</script>
