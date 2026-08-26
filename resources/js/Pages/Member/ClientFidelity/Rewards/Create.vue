<template>
  <MemberLayout>
    <Head title="Nueva Recompensa" />

    <PageHeader
      title="Nueva Recompensa"
      :breadcrumbs="breadcrumbs"
      backHref="/member/listings/fidelity-rewards"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Título <span class="text-danger">*</span></label>
              <input
                id="reward-title"
                v-model="form.title"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.title }"
                placeholder="ej. Café gratis"
              />
              <div v-if="errors.title" class="invalid-feedback">{{ errors.title }}</div>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea
                id="reward-description"
                v-model="form.description"
                class="form-control"
                rows="3"
                placeholder="Descripción del premio que obtendrá el cliente..."
              ></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Número de visitas <span class="text-danger">*</span></label>
              <input
                id="reward-max-visits"
                v-model.number="form.max_visits"
                type="number"
                class="form-control"
                :class="{ 'is-invalid': errors.max_visits }"
                min="2"
                max="100"
                placeholder="ej. 5"
              />
              <div v-if="errors.max_visits" class="invalid-feedback">{{ errors.max_visits }}</div>
              <small class="text-muted">Cantidad de visitas para completar la tarjeta</small>
            </div>

            <div class="col-md-6">
              <label class="form-label">Estado</label>
              <div class="form-check form-switch mt-2">
                <input
                  id="reward-is-active"
                  v-model="form.is_active"
                  class="form-check-input"
                  type="checkbox"
                  role="switch"
                />
                <label class="form-check-label" for="reward-is-active">
                  {{ form.is_active ? 'Activa' : 'Inactiva' }}
                </label>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Imagen</label>
              <input
                id="reward-image"
                type="file"
                class="form-control"
                :class="{ 'is-invalid': errors.image }"
                accept="image/jpeg,image/png,image/webp"
                @change="handleImageChange"
              />
              <div v-if="errors.image" class="invalid-feedback">{{ errors.image }}</div>
              <small class="text-muted">Imagen representativa del premio (opcional)</small>

              <div v-if="imagePreview" class="mt-3">
                <img :src="imagePreview" alt="Preview" class="img-thumbnail" style="max-height: 200px;" />
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              <span v-if="sending">Guardando...</span>
              <span v-else>Guardar</span>
            </button>
            <Link :href="`/member/listings/${listing?.id}/fidelity-rewards`" class="btn btn-outline-secondary ms-2">
              Cancelar
            </Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const sending = ref(false)
const imagePreview = ref(null)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Fidelización', href: `/member/listings/${listing.value?.id}/fidelity-cards` },
  { label: 'Recompensas', href: `/member/listings/${listing.value?.id}/fidelity-rewards` },
  { label: 'Nueva' },
])

const form = ref({
  title: '',
  description: '',
  max_visits: 5,
  is_active: true,
  image: null,
})

const errors = ref({})

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  sending.value = true
  errors.value = {}

  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('description', form.value.description || '')
  formData.append('max_visits', form.value.max_visits)
  formData.append('is_active', form.value.is_active ? '1' : '0')
  if (form.value.image) {
    formData.append('image', form.value.image)
  }

  router.post(`/member/listings/${listing.value?.id}/fidelity-rewards`, formData, {
    onSuccess: () => {
      sending.value = false
    },
    onError: (errs) => {
      sending.value = false
      errors.value = errs
    },
  })
}
</script>
