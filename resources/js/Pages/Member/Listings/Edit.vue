<template>
  <MemberLayout>
    <Head :title="`Editar ${listing.name}`" />

    <PageHeader
      :title="'Editar Negocio'"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12">
              <div class="text-center mb-4">
                <div class="position-relative d-inline-block">
                  <div
                    class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                    style="width: 120px; height: 120px; overflow: hidden;"
                  >
                    <img
                      v-if="logoPreview || listing.logo_path"
                      :src="logoPreview || listing.logo_path"
                      alt="Logo"
                      class="w-100 h-100"
                      style="object-fit: cover;"
                    />
                    <i v-else class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                  </div>
                  <label
                    for="logo-input"
                    class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                  >
                    <i class="bi bi-camera"></i>
                    <input
                      id="logo-input"
                      type="file"
                      accept="image/jpeg,image/png,image/gif,image/webp"
                      class="d-none"
                      @change="handleLogoChange"
                    />
                  </label>
                </div>
                <div v-if="errors.logo" class="text-danger small mt-1">{{ errors.logo }}</div>
                <div class="text-muted small mt-2">JPG, PNG o WebP. Max 2MB.</div>
                <button
                  v-if="listing.logo_path && !removeLogoFlag"
                  type="button"
                  class="btn btn-sm btn-outline-danger mt-2"
                  @click="removeLogo"
                >
                  <i class="bi bi-trash me-1"></i>Eliminar logo
                </button>
              </div>
              <input type="hidden" name="remove_logo" :value="removeLogoFlag ? '1' : '0'" />
            </div>

            <div class="col-12 col-md-6">
              <FieldText
                id="business-name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
            <Link href="/member/listings" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
      <div class="card-body">
        <h5 class="card-title mb-3">
          <i class="bi bi-qr-code me-2"></i>Código QR del Negocio
        </h5>
        <p class="text-muted small mb-3">Escanea el código para acceder directamente al minisite de tu negocio.</p>

        <div class="row align-items-center">
          <div class="col-auto">
            <div class="bg-white p-3 rounded border" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
              <img v-if="qrCodeUrl" :src="qrCodeUrl" alt="QR Code" style="max-width: 100%; max-height: 100%;" />
              <div v-else class="text-muted">
                <i class="bi bi-hourglass-split"></i>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label class="form-label">Tipo de versión</label>
              <div class="d-flex gap-2">
                <button
                  type="button"
                  class="btn"
                  :class="qrVersion === 'mobile' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="qrVersion = 'mobile'"
                >
                  <i class="bi bi-phone me-1"></i>Móvil (/m/)
                </button>
                <button
                  type="button"
                  class="btn"
                  :class="qrVersion === 'desktop' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="qrVersion = 'desktop'"
                >
                  <i class="bi bi-display me-1"></i>Escritorio (/b/)
                </button>
              </div>
            </div>
            <div class="mb-2">
              <label class="form-label fw-semibold">URL del minisite:</label>
              <code class="d-block mb-2 p-2 bg-light rounded">{{ qrLink }}</code>
            </div>
            <a :href="qrLink" target="_blank" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-box-arrow-up-right me-1"></i>Abrir minisite
            </a>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref, inject } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  listing: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const sending = ref(false)

const listing = computed(() => props.listing)
const removeLogoFlag = ref(false)
const logoPreview = ref(null)

const dynamicBreadcrumbs = inject('dynamicBreadcrumbs', null)

const breadcrumbs = computed(() => {
  if (dynamicBreadcrumbs?.value) {
    return dynamicBreadcrumbs.value
  }
  return [
    { label: 'Mis Negocios', href: '/member/listings' },
    { label: listing.value.name, active: true },
  ]
})

const form = ref({
  name: props.listing.name,
})

const qrVersion = ref('mobile')

const qrLink = computed(() => {
  const baseUrl = window.location.origin
  const prefix = qrVersion.value === 'mobile' ? '/m' : '/b'
  return `${baseUrl}${prefix}/${props.listing.slug}`
})

const qrCodeUrl = computed(() => {
  return `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrLink.value)}`
})

const handleLogoChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    logoPreview.value = URL.createObjectURL(file)
    removeLogoFlag.value = false
  }
}

const removeLogo = () => {
  removeLogoFlag.value = true
  logoPreview.value = null
}

const submit = () => {
  sending.value = true
  const data = new FormData()
  data.append('name', form.value.name)

  if (logoPreview.value) {
    const fileInput = document.getElementById('logo-input')
    if (fileInput && fileInput.files[0]) {
      data.append('logo', fileInput.files[0])
    }
  }

  if (removeLogoFlag.value) {
    data.append('remove_logo', '1')
  }

  data.append('_method', 'PUT')

  router.post(`/member/listings/${props.listing.id}`, data, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
