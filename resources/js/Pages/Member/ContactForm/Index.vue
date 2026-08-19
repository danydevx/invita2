<template>
  <MemberLayout>
    <Head :title="`Formularios de Contacto - ${business?.name || ''}`" />

    <PageHeader
      title="Formularios de Contacto"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <Link
          v-if="canCreateMore"
          :href="`/member/businesses/${business?.id}/contact-forms/create`"
          class="btn btn-primary"
        >
          <i class="bi bi-plus me-1"></i>Nuevo Formulario
        </Link>
        <span v-else class="btn btn-secondary" disabled>
          Limite alcanzado ({{ forms.length }}/{{ maxForms }})
        </span>
      </template>
    </PageHeader>

    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="forms.length === 0" class="text-center py-5">
          <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
          <p class="text-muted mt-3">No hay formularios creados.</p>
          <p class="text-muted small">Crea tu primer formulario de contacto para empezar a recibir mensajes.</p>
          <Link
            v-if="canCreateMore"
            :href="`/member/businesses/${business?.id}/contact-forms/create`"
            class="btn btn-primary"
          >
            <i class="bi bi-plus me-1"></i>Crear Primer Formulario
          </Link>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Shortcode</th>
                <th>Campos</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="form in forms" :key="form.id">
                <td>
                  <strong>{{ form.name }}</strong>
                  <p v-if="form.description" class="text-muted small mb-0">{{ form.description }}</p>
                </td>
                <td>
                  <code class="small">{{ form.shortcode }}</code>
                  <button class="btn btn-sm btn-link p-0 ms-1" @click="copyShortcode(form.shortcode)" title="Copiar">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </td>
                <td>{{ form.fields_count }}</td>
                <td>
                  <span v-if="form.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td>{{ formatDate(form.created_at) }}</td>
                <td class="text-end">
                  <div class="btn-group">
                    <Link
                      :href="`/member/businesses/${business?.id}/contact-forms/${form.id}/edit`"
                      class="btn btn-sm btn-outline-primary"
                    >
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <Link
                      :href="`/member/businesses/${business?.id}/contact-forms/${form.id}/submissions`"
                      class="btn btn-sm btn-outline-secondary"
                    >
                      <i class="bi bi-envelope"></i>
                    </Link>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="deleteForm(form)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="alert alert-info mt-4">
      <strong>Como usar:</strong>
      <p class="mb-1">Copia el shortcode y pegalo en cualquier pagina de tu minisitio usando el bloque de HTML.</p>
      <p class="mb-0 small text-muted">Ejemplo: <code>&lt;div data-contact-form="{{ forms[0]?.shortcode }}"&gt;&lt;/div&gt;</code></p>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  business: Object,
  forms: {
    type: Array,
    default: () => [],
  },
  maxForms: {
    type: Number,
    default: 5,
  },
  canCreateMore: {
    type: Boolean,
    default: true,
  },
})

const page = usePage()
const business = computed(() => page.props.business)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Formularios', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Formularios', active: true },
  ]
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-AR')
}

const copyShortcode = (shortcode) => {
  navigator.clipboard.writeText(shortcode)
}

const deleteForm = (form) => {
  if (confirm(`¿Eliminar el formulario "${form.name}"? Esta accion no se puede deshacer.`)) {
    router.delete(`/member/businesses/${business.value.id}/contact-forms/${form.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
