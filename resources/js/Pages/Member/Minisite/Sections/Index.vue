<template>
  <MemberLayout>
    <Head :title="`Secciones del Minisite - ${listing?.name || ''}`" />

    <PageHeader
      title="Secciones del Minisite"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/minisite`"
    >
      <template #actions>
        <!-- <a v-if="listing?.slug" :href="`/b/${listing.slug}`" target="_blank" class="btn btn-outline-secondary btn-sm me-2">
          <i class="bi bi-display me-1"></i>Ver Desktop
        </a> -->
        <Link :href="`/member/listings/${listing?.id}/minisite/sections/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nueva Sección
        </Link>
      </template>
    </PageHeader>

    <div v-if="!setting?.is_active" class="alert alert-warning mb-3">
      <i class="bi bi-exclamation-triangle me-2"></i>
      El minisite está inactivo. Actívalo en la configuración para que sea visible.
    </div>

    <div class="minisite-sections-editor">
      <div class="minisite-sections-editor__panel minisite-sections-editor__panel--editor">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div v-if="sections.length === 0" class="text-center text-muted py-5">
              <i class="bi bi-layout-text-sidebar display-1"></i>
              <h5 class="mt-3">No hay secciones</h5>
              <p>Crea tu primera sección para empezar a construir tu minisite.</p>
               <Link :href="`/member/listings/${listing?.id}/minisite/sections/create`" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Crear Sección
              </Link>
            </div>

            <div v-else ref="sectionsList" class="minisite-sections">
              <div
                v-for="section in localSections"
                :key="section.id || section.section_key"
                class="minisite-sections__item"
                :data-id="section.id || section.section_key"
              >
                <div class="minisite-sections__drag">
                  <i class="bi bi-grip-vertical"></i>
                </div>
                <div class="minisite-sections__content">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary">{{ sectionTypes[section.section_type] || section.section_type }}</span>
                    <strong>{{ section.title || section.section_key }}</strong>
                  </div>
                </div>
                <div class="minisite-sections__actions">
                  <Link
                    :href="section.id === 'hero' || section.id === 'footer'
                      ? `/member/listings/${listing?.id}/minisite`
                      : `/member/listings/${listing?.id}/minisite/sections/${section.id}/edit`"
                    class="btn btn-sm btn-outline-primary"
                  >
                    <i class="bi bi-pencil"></i>
                  </Link>
                  <button
                    v-if="section.id !== 'hero' && section.id !== 'footer'"
                    class="btn btn-sm btn-outline-danger"
                    @click="deleteSection(section)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="minisite-sections-editor__panel minisite-sections-editor__panel--preview d-none d-lg-block">
        <div class="minisite-sections-editor__preview-wrapper">
          <h6 class="text-muted mb-3 text-center">
            <i class="bi bi-phone me-1"></i>Vista Previa
          </h6>
          <MinisitePreview
            :key="sectionsKey"
            :business="listing"
            :setting="setting"
            :sections="localSections"
            :socialNetworks="socialNetworks"
            :sectionTypes="sectionTypes"
          />
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, watchEffect, onMounted, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import Sortable from 'sortablejs'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import MinisitePreview from '@/Components/Minisite/MinisitePreview.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const sections = computed(() => page.props.sections || [])
const sectionTypes = computed(() => page.props.sectionTypes || {})
const setting = computed(() => page.props.setting)
const socialNetworks = computed(() => page.props.socialNetworks || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const localSections = ref([...sections.value])
const sectionsList = ref(null)
let sortableInstance = null

watchEffect(() => {
  localSections.value = [...sections.value]
})

const sectionsKey = computed(() => {
  return sections.value.map(s => s.id).join(',')
})

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const match = path.match(/^\/member\/listings\/(\d+)/)
  if (match) {
    const bizId = parseInt(match[1])
    const biz = businessMenu.value.find(b => b.id === bizId)
    if (biz) {
      return [
        { label: 'Inicio', href: `/member/listings/${biz.id}/modules` },
        { label: 'Minisite', href: `/member/listings/${biz.id}/minisite` },
        { label: 'Secciones', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Minisite', href: `/member/listings/${listing.value?.id}/minisite` },
    { label: 'Secciones', active: true },
  ]
})

const deleteSection = (section) => {
  if (confirm(`¿Eliminar la sección "${section.title || section.section_key}"?`)) {
    router.delete(`/member/listings/${listing.value.id}/minisite/sections/${section.id}`, {
      preserveScroll: true,
    })
  }
}

const initSortable = () => {
  if (!sectionsList.value) return

  if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }

  sortableInstance = Sortable.create(sectionsList.value, {
    handle: '.minisite-sections__drag',
    animation: 150,
    ghostClass: 'sortable-ghost',
    dragClass: 'sortable-drag',
    onEnd: () => {
      const elements = Array.from(sectionsList.value.querySelectorAll('[data-id]'))
      const ids = elements.map(el => el.dataset.id).filter(id => !isNaN(id))

      const reordered = ids.map(id => localSections.value.find(s => s.id == id))
      localSections.value = reordered.filter(Boolean)

      if (ids.length > 0) {
        router.post(
          `/member/listings/${listing.value.id}/minisite/sections/reorder`,
          { ids },
          {
            preserveScroll: true,
          }
        )
      }
    },
  })
}

onMounted(() => {
  nextTick(() => initSortable())
})
</script>

<style lang="less">
.minisite-sections-editor {
  display: flex;
  gap: 20px;
  align-items: flex-start;

  &__panel {
    &--editor {
      flex: 0 0 60%;
      max-width: 60%;
    }

    &--preview {
      flex: 0 0 40%;
      max-width: 40%;
    }
  }

  &__preview-wrapper {
    position: sticky;
    top: 20px;
  }
}

.minisite-sections {
  &__item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
    transition: background-color 0.2s;

    &:last-child {
      border-bottom: none;
    }

    &.sortable-ghost {
      opacity: 0.4;
      background-color: #e9ecef;
    }

    &.sortable-drag {
      background-color: #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
  }

  &__drag {
    color: #adb5bd;
    cursor: grab;

    &:hover {
      color: #495057;
    }
  }

  &__content {
    flex-grow: 1;
  }

  &__actions {
    display: flex;
    gap: 4px;
  }
}

@media (max-width: 991.98px) {
  .minisite-sections-editor {
    flex-direction: column;

    &__panel {
      &--editor {
        flex: none;
        max-width: 100%;
        width: 100%;
      }

      &--preview {
        display: none !important;
      }
    }
  }
}
</style>
