<template>
  <AdminLayout>
    <Head title="Nuevo Negocio" />

    <PageHeader title="Nuevo Negocio" :breadcrumbs="breadcrumbs" backHref="/admin/businesses" />

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
                placeholder="Describe tu negocio..."
                v-model="form.description"
                :formError="form.errors.description"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-phone"
                label="Telefono"
                placeholder="+54 11 1234 5678"
                v-model="form.phone"
                :formError="form.errors.phone"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-email"
                label="Email"
                placeholder="contacto@minegocio.com"
                v-model="form.email"
                :formError="form.errors.email"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-website"
                label="Website"
                placeholder="https://minegocio.com"
                v-model="form.website"
                :formError="form.errors.website"
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
            <Link href="/admin/businesses" class="btn btn-outline-secondary">Cancelar</Link>
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
  user_id: '',
  name: '',
  slug: '',
  business_type: '',
  industry_id: null,
  description: '',
  phone: '',
  email: '',
  website: '',
  timezone: 'America/Mexico_City',
  currency: 'MXN',
  is_active: true,
  is_published: false,
})

const breadcrumbs = [
  { label: 'Negocios', href: '/admin/businesses' },
  { label: 'Nuevo', active: true },
]

const submit = () => {
  form.post('/admin/businesses')
}
</script>
