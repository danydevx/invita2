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
                id="listing-slug"
                label="Slug"
                v-model="form.slug"
                :formError="form.errors.slug"
                required
              />
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
})

const form = useForm({
  user_id: props.listing.user_id,
  slug: props.listing.slug,
  is_active: !!props.listing.is_active,
  is_published: !!props.listing.is_published,
})

const breadcrumbs = [
  { label: 'Listados', href: '/admin/listings' },
  { label: 'Editar', active: true },
]

const submit = () => {
  form.put(`/admin/listings/${props.listing.id}`)
}
</script>
