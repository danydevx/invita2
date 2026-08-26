<template>
  <MemberLayout>
    <Head title="Editar galería" />

    <PageHeader
      title="Editar galería"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id || ''}/galleries`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="gallery-name"
              label="Nombre"
              v-model="form.name"
              :formError="form.errors.name"
              :readonly="isPrimary"
              required
            />
            <div v-if="isPrimary" class="form-text">
              La galería principal siempre se llama "Galería principal".
            </div>
          </div>

          <div class="col-12 col-md-6">
            <FieldSwitch
              id="gallery-primary"
              label="Marcar como galería principal"
              v-model="form.is_primary"
              :disabled="isPrimary"
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="gallery-description"
              label="Descripción"
              v-model="form.description"
              :formError="form.errors.description"
              :rows="2"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldSwitch
              id="gallery-active"
              label="Galería activa"
              v-model="form.is_active"
              :disabled="isPrimary"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldNumber
              id="gallery-sort"
              label="Orden"
              v-model="form.sort_order"
              :formError="form.errors.sort_order"
              :min="0"
            />
          </div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
            <Link :href="`/member/listings/${listing?.id || ''}/galleries`" class="btn btn-outline-secondary">
              Cancelar
            </Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const gallery = computed(() => page.props.gallery)
const businessMenu = computed(() => page.props.businessMenu || [])

const isPrimary = computed(() => !!gallery.value?.is_primary)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Galería', href: `/member/listings/${listing.value?.id}/galleries` },
  { label: gallery.value?.name || 'Editar' },
])

const form = useForm({
  name: gallery.value?.name,
  description: gallery.value?.description || '',
  is_primary: !!gallery.value?.is_primary,
  is_active: !!gallery.value?.is_active,
  sort_order: gallery.value?.sort_order || 0,
})

const submit = () => {
  form.put(`/member/listings/${listing.value.id}/galleries/${gallery.value.id}`)
}
</script>