<template>
  <MemberLayout>
    <Head :title="`Redes Sociales - ${business?.name || ''}`" />

    <PageHeader
      title="Redes Sociales"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Agregar red social
        </button>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/social-networks`"
      :columns="columns"
      :initial-data="dataTable"
      :reorderable="true"
      :reorder-endpoint="`/member/businesses/${business?.id}/social-networks/reorder`"
      search-placeholder="Buscar redes sociales..."
      empty-title="No hay redes sociales"
      empty-text="Comienza agregando tu primera red social."
      @updated="onDataTableUpdated"
    >
      <template #cell-platform="{ row }">
        <div class="d-flex align-items-center gap-2">
          <i :class="getPlatformIcon(row.platform)" :style="{ color: getPlatformColor(row.platform) }"></i>
          <strong>{{ getPlatformName(row.platform) }}</strong>
        </div>
      </template>

      <template #cell-username="{ row }">
        {{ row.username || '—' }}
      </template>

      <template #cell-url="{ row }">
        <a :href="row.url" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
          {{ row.url }}
        </a>
      </template>

      <template #cell-show_on_hero="{ row }">
        <span :class="row.show_on_hero ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.show_on_hero ? 'Si' : 'No' }}
        </span>
      </template>

      <template #cell-show_on_footer="{ row }">
        <span :class="row.show_on_footer ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.show_on_footer ? 'Si' : 'No' }}
        </span>
      </template>

      <template #cell-show_on_contact="{ row }">
        <span :class="row.show_on_contact ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.show_on_contact ? 'Si' : 'No' }}
        </span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activo' : 'Inactivo' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button class="btn btn-sm btn-outline-primary" @click="openEditModal(row)">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" @click="deleteNetwork(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingNetwork ? 'Editar red social' : 'Agregar red social' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitNetwork">
            <div class="modal-body">
              <div class="mb-3" v-if="!editingNetwork">
                <FieldSelect
                  id="network-platform"
                  label="Plataforma"
                  v-model="form.platform"
                  required
                >
                  <option v-for="(info, key) in platforms" :key="key" :value="key">
                    {{ info.name }}
                  </option>
                </FieldSelect>
              </div>
              <div class="mb-3" v-else>
                <label class="form-label">Plataforma</label>
                <div class="d-flex align-items-center gap-2">
                  <i :class="getPlatformIcon(form.platform)" :style="{ color: getPlatformColor(form.platform) }"></i>
                  <strong>{{ getPlatformName(form.platform) }}</strong>
                </div>
              </div>
              <div class="mb-3">
                <FieldUrl
                  id="network-url"
                  label="URL"
                  v-model="form.url"
                  placeholder="https://..."
                  required
                />
              </div>
              <div class="mb-3">
                <FieldText
                  id="network-username"
                  label="Usuario"
                  v-model="form.username"
                  placeholder="@usuario"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="network-hero"
                  label="Mostrar en Hero"
                  v-model="form.show_on_hero"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="network-footer"
                  label="Mostrar en Footer"
                  v-model="form.show_on_footer"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="network-contact"
                  label="Mostrar en Contacto"
                  v-model="form.show_on_contact"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="network-active"
                  label="Activo"
                  v-model="form.is_active"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  business: Object,
  socialNetworks: Array,
  platforms: Object,
  dataTable: Object,
})

const page = usePage()
const business = computed(() => page.props.business)
const dataTable = computed(() => page.props.dataTable)
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
        { label: 'Redes Sociales', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Redes Sociales', active: true },
  ]
})

const columns = [
  { key: 'sort_order', label: 'Orden', sortable: true, width: '70px' },
  { key: 'platform', label: 'Plataforma', sortable: true },
  { key: 'username', label: 'Usuario', sortable: false },
  { key: 'url', label: 'URL', sortable: false },
  { key: 'show_on_hero', label: 'Hero', sortable: false },
  { key: 'show_on_footer', label: 'Footer', sortable: false },
  { key: 'show_on_contact', label: 'Contacto', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const modalElement = ref(null)
let networkModal = null
const editingNetwork = ref(null)
const sending = ref(false)

const form = useForm({
  platform: '',
  url: '',
  username: '',
  is_active: true,
  show_on_hero: false,
  show_on_footer: true,
  show_on_contact: true,
  sort_order: 0,
})

const platforms = props.platforms

const getPlatformIcon = (platform) => {
  return platforms[platform]?.icon || 'bi bi-globe'
}

const getPlatformName = (platform) => {
  return platforms[platform]?.name || platform
}

const getPlatformColor = (platform) => {
  return platforms[platform]?.color || '#666666'
}

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}

const openCreateModal = () => {
  editingNetwork.value = null
  form.reset()
  nextTick(() => networkModal.show())
}

const openEditModal = (network) => {
  editingNetwork.value = network
  form.platform = network.platform
  form.url = network.url
  form.username = network.username || ''
  form.is_active = network.is_active
  form.show_on_hero = network.show_on_hero
  form.show_on_footer = network.show_on_footer
  form.show_on_contact = network.show_on_contact
  nextTick(() => networkModal.show())
}

const submitNetwork = () => {
  sending.value = true
  if (editingNetwork.value) {
    router.put(`/member/businesses/${business.value.id}/social-networks/${editingNetwork.value.id}`, form.data(), {
      preserveScroll: true,
      onSuccess: () => {
        sending.value = false
        networkModal.hide()
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onError: () => {
        sending.value = false
      },
    })
  } else {
    router.post(`/member/businesses/${business.value.id}/social-networks`, form.data(), {
      preserveScroll: true,
      onSuccess: () => {
        sending.value = false
        networkModal.hide()
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onError: () => {
        sending.value = false
      },
    })
  }
}

const deleteNetwork = (network) => {
  if (confirm('Estas seguro de eliminar esta red social?')) {
    router.delete(`/member/businesses/${business.value.id}/social-networks/${network.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

onMounted(() => {
  networkModal = new Modal(modalElement.value)
  networkModal._element.addEventListener('hidden.bs.modal', () => {
    editingNetwork.value = null
    form.reset()
  })
})
</script>
