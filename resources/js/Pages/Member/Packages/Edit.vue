<template>
  <MemberLayout>
    <Head :title="`Editar Paquete - ${listing?.name || ''}`" />

    <PageHeader
      title="Editar Paquete"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/packages`"
    />

    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="mb-3">
                <FieldText
                  id="package-title"
                  label="Título"
                  placeholder="Ej: Paquete Basic"
                  v-model="form.title"
                  :formError="form.errors.title"
                  required
                />
              </div>

              <div class="mb-3">
                <FieldText
                  id="package-short-description"
                  label="Descripción corta"
                  placeholder="Breve descripción del paquete"
                  v-model="form.short_description"
                  :formError="form.errors.short_description"
                  required
                />
              </div>

              <div class="mb-3">
                <FieldTextarea
                  id="package-long-description"
                  label="Descripción larga (opcional)"
                  placeholder="Descripción detallada del paquete"
                  v-model="form.long_description"
                  :formError="form.errors.long_description"
                  :rows="3"
                />
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <FieldText
                    id="package-price"
                    label="Precio (opcional)"
                    placeholder="0.00"
                    v-model="form.price"
                    :formError="form.errors.price"
                    type="number"
                    step="0.01"
                    min="0"
                    max="999999999.99"
                  />
                </div>
                <div class="col-md-6">
                  <FieldText
                    id="package-promo-price"
                    label="Precio promocional (opcional)"
                    placeholder="0.00"
                    v-model="form.promo_price"
                    :formError="form.errors.promo_price"
                    type="number"
                    step="0.01"
                    min="0"
                    max="999999999.99"
                  />
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Imagen (opcional)</label>
                <input
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png"
                  @change="handleImageChange"
                />
                <div class="form-text">JPG o PNG, máximo 2MB</div>
                <div v-if="form.errors.image" class="text-danger small mt-1">{{ form.errors.image }}</div>
                <div v-if="imagePreview || pkg?.image" class="mt-2">
                  <img 
                    :src="imagePreview || pkg?.image" 
                    class="rounded" 
                    style="width: 120px; height: 120px; object-fit: cover;" 
                  />
                </div>
              </div>

              <hr class="my-4">

              <h5 class="mb-3">WhatsApp</h5>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <FieldText
                    id="package-whatsapp"
                    label="WhatsApp (opcional)"
                    :placeholder="defaultWhatsapp || '+52 555 000 0000'"
                    v-model="form.whatsapp"
                    :formError="form.errors.whatsapp"
                  />
                </div>
                <div class="col-md-6 d-flex align-items-end">
                  <button type="button" class="btn btn-outline-secondary" @click="useDefaultWhatsapp">
                    Usar WhatsApp del negocio
                  </button>
                </div>
              </div>

              <div class="mb-3">
                <FieldTextarea
                  id="package-whatsapp-message"
                  label="Mensaje de WhatsApp (opcional)"
                  placeholder="Usa {package_title} para incluir el nombre del paquete"
                  v-model="form.whatsapp_message"
                  :formError="form.errors.whatsapp_message"
                  :rows="2"
                />
              </div>

              <hr class="my-4">

              <h5 class="mb-3">Características</h5>
              <p class="text-muted small mb-3">Agrega las características incluidas en el paquete (máximo 30)</p>

              <div class="mb-3">
                <draggable
                  v-model="form.features"
                  item-key="index"
                  handle=".drag-handle"
                  ghost-class="bg-light"
                >
                  <template #item="{ element, index }">
                    <div class="d-flex align-items-center gap-2 mb-2">
                      <button type="button" class="btn btn-outline-secondary btn-sm drag-handle">
                        <i class="bi bi-arrows-move"></i>
                      </button>
                      <input
                        type="text"
                        class="form-control form-control-sm"
                        v-model="form.features[index]"
                        :placeholder="`Característica ${index + 1}`"
                      />
                      <button 
                        type="button" 
                        class="btn btn-outline-danger btn-sm"
                        @click="removeFeature(index)"
                        :disabled="form.features.length <= 1"
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </template>
                </draggable>

                <button 
                  type="button" 
                  class="btn btn-outline-primary btn-sm mt-2"
                  @click="addFeature"
                  :disabled="form.features.length >= 30"
                >
                  <i class="bi bi-plus me-1"></i>Agregar característica
                </button>
              </div>

              <div class="mb-3">
                <FieldSwitch
                  id="package-active"
                  label="Activo"
                  v-model="form.is_active"
                  :formError="form.errors.is_active"
                />
                <div class="form-text">Los paquetes inactivos no aparecerán en el minisite.</div>
              </div>

              <FormActions :submitText="'Guardar'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing?.id}/packages`" :sending="form.processing" />
            </form>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const pkg = computed(() => page.props.package)
const defaultWhatsapp = computed(() => page.props.defaultWhatsapp || '')
const defaultWhatsappMessage = computed(() => page.props.defaultWhatsappMessage || '')
const businessMenu = computed(() => page.props.businessMenu || [])

const imagePreview = ref(null)

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/listings\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Inicio', href: `/member/listings/${biz.id}/modules` },
        { label: 'Paquetes', href: `/member/listings/${biz.id}/packages` },
        { label: pkg.value?.title || 'Editar', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Paquetes', href: `/member/listings/${listing.value?.id}/packages` },
    { label: pkg.value?.title || 'Editar', active: true },
  ]
})

const form = useForm({
  title: pkg.value?.title || '',
  short_description: pkg.value?.short_description || '',
  long_description: pkg.value?.long_description || '',
  image: null,
  price: pkg.value?.price || '',
  promo_price: pkg.value?.promo_price || '',
  whatsapp: pkg.value?.whatsapp || '',
  whatsapp_message: pkg.value?.whatsapp_message || '',
  features: pkg.value?.features || [''],
  is_active: pkg.value?.is_active ?? true,
})

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.image = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const useDefaultWhatsapp = () => {
  form.whatsapp = defaultWhatsapp.value
}

const addFeature = () => {
  if (form.features.length < 30) {
    form.features.push('')
  }
}

const removeFeature = (index) => {
  if (form.features.length > 1) {
    form.features.splice(index, 1)
  }
}

const submit = () => {
  form.post(`/member/listings/${listing.value.id}/packages/${pkg.value.id}`, {
    preserveScroll: true,
  })
}
</script>
