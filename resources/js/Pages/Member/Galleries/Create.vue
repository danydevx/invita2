<template>
  <MemberLayout>
    <Head title="Nueva galería" />

    <PageHeader
      title="Nueva galería"
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
              placeholder="Galería del local"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldSwitch
              id="gallery-primary"
              label="Marcar como galería principal"
              v-model="form.is_primary"
            />
            <div class="form-text">Se elegirá automáticamente si no existe ninguna otra.</div>
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
              {{ form.processing ? 'Guardando...' : 'Crear galería' }}
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
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const biz = businessMenu.value.find((b) => b.id === listing.value?.id)
  if (biz) {
    return [
      { label: biz.name, href: `/member/listings/${biz.id}/edit` },
      { label: 'Galerías', href: `/member/listings/${biz.id}/galleries` },
      { label: 'Nueva galería', active: true },
    ]
  }
  return [
    { label: 'Galerías', href: `/member/listings/${listing.value?.id}/galleries` },
    { label: 'Nueva galería', active: true },
  ]
})

const form = useForm({
  name: '',
  description: '',
  is_primary: false,
  is_active: true,
  sort_order: 0,
})

const submit = () => {
  form.post(`/member/listings/${listing.value.id}/galleries`)
}
</script>