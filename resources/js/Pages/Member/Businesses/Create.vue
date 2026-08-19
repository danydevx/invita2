<template>
  <MemberLayout>
    <Head title="Crear negocio" />

    <PageHeader title="Crear negocio" :breadcrumbs="breadcrumbs" backHref="/member/businesses" />

    <div class="alert alert-light border">
      Plan {{ planName }}: {{ limitText }}.
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="business-name"
              label="Nombre"
              placeholder="Nombre del negocio"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <label for="business-industry" class="form-label">Industria *</label>
            <select
              id="business-industry"
              v-model="form.industry_id"
              class="form-select"
              :class="{ 'is-invalid': form.errors.industry_id }"
              required
            >
              <option value="">Seleccionar industria...</option>
              <option v-for="industry in industries" :key="industry.id" :value="industry.id">
                {{ industry.name }}
              </option>
            </select>
            <div v-if="form.errors.industry_id" class="invalid-feedback">
              {{ form.errors.industry_id }}
            </div>
            <div class="form-text">La industria define los modulos y la apariencia inicial del negocio.</div>
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="business-phone"
              label="Telefono"
              placeholder="Telefono"
              v-model="form.phone"
              :formError="form.errors.phone"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldEmail
              id="business-email"
              label="Email"
              placeholder="contacto@negocio.com"
              v-model="form.email"
              :formError="form.errors.email"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="business-website"
              label="Sitio web"
              placeholder="https://negocio.com"
              v-model="form.website"
              :formError="form.errors.website"
            />
          </div>

          <div class="col-12 col-md-6">
            <label for="business-timezone" class="form-label">Zona horaria</label>
            <select
              id="business-timezone"
              v-model="form.timezone"
              class="form-select"
              :class="{ 'is-invalid': form.errors.timezone }"
            >
              <option value="America/Mexico_City">Ciudad de México (America/Mexico_City)</option>
            </select>
            <div v-if="form.errors.timezone" class="invalid-feedback">{{ form.errors.timezone }}</div>
          </div>

          <div class="col-12 col-md-6">
            <label for="business-currency" class="form-label">Moneda</label>
            <select
              id="business-currency"
              v-model="form.currency"
              class="form-select"
              :class="{ 'is-invalid': form.errors.currency }"
            >
              <option value="MXN">MXN - Peso Mexicano</option>
            </select>
            <div v-if="form.errors.currency" class="invalid-feedback">{{ form.errors.currency }}</div>
          </div>

          <div class="col-12">
            <FieldTextarea
              id="business-description"
              label="Descripcion"
              v-model="form.description"
              :formError="form.errors.description"
              :rows="3"
            />
          </div>

          <div class="col-12">
            <div class="form-text mb-3">
              El slug publico se generara automaticamente y el negocio iniciara sin publicar.
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                {{ form.processing ? 'Creando...' : 'Crear negocio' }}
              </button>
              <Link href="/member/businesses" class="btn btn-outline-secondary">Cancelar</Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  industries: { type: Array, default: () => [] },
  maxBusinesses: { type: Number, default: null },
  businessCount: { type: Number, default: 0 },
  planName: { type: String, default: 'Sin plan' },
})

const form = useForm({
  name: '',
  industry_id: '',
  description: '',
  phone: '',
  email: '',
  website: '',
  timezone: 'America/Mexico_City',
  currency: 'MXN',
})

const breadcrumbs = [
  { label: 'Mis Negocios', href: '/member/businesses' },
  { label: 'Crear', active: true },
]

const limitText = computed(() => {
  if (props.maxBusinesses === null) return `${props.businessCount} creados, sin limite`
  return `${props.businessCount} de ${props.maxBusinesses} negocios creados`
})

const submit = () => {
  form.post('/member/businesses')
}
</script>
