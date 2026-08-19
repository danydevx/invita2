<template>
  <div class="minisite-page">
    <NavigationMenu :business="business" :existingSections="existingSections" />

    <HeroSimple
      :title="pageTitle"
      :backgroundImage="business.cover_image"
      :businessSlug="business.slug"
    />

    <section class="page-header">
      <div class="page-header__inner">
        <BreadcrumbNav
          :baseSlug="business.slug"
          :items="[{ label: pageTitle }]"
        />
      </div>
    </section>

    <section class="page-content">
      <div class="page-content__inner">
        <div v-if="propertyTypes && propertyTypes.length > 0" class="category-filter">
          <button
            class="category-badge"
            :class="{ active: selectedType === null }"
            @click="selectedType = null"
          >
            Todos
          </button>
          <button
            v-for="type in propertyTypes"
            :key="type.id"
            class="category-badge"
            :class="{ active: selectedType === type.key }"
            @click="toggleType(type.key)"
          >
            {{ type.name }}
          </button>
        </div>

        <div v-if="operationFilters && operationFilters.length > 1" class="operation-filter">
          <button
            class="operation-badge"
            :class="{ active: selectedOperation === null }"
            @click="selectedOperation = null"
          >
            Todas las operaciones
          </button>
          <button
            v-for="op in operationFilters"
            :key="op"
            class="operation-badge"
            :class="{ active: selectedOperation === op }"
            @click="toggleOperation(op)"
          >
            {{ getOperationLabel(op) }}
          </button>
        </div>

        <SectionProperties
          v-if="filteredItems && filteredItems.length"
          :title="pageTitle"
          :items="filteredItems"
          :config="sectionConfig"
          :businessSlug="business.slug"
          :showAllButton="false"
        />
        <div v-else class="text-muted text-center py-5">
          No hay propiedades disponibles.
        </div>
      </div>
    </section>

    <Footer
      :business="business"
      :text="setting.footer_text"
      :showSocial="setting.footer_show_social"
      :socialNetworks="socialNetworks"
    />

    <AiChatWidget
      v-if="aiChatbot && aiChatbot.is_enabled"
      :businessSlug="business.slug"
      :businessName="business.name"
      :widgetColor="aiChatbot.widget_color || '#3B82F6'"
      :widgetTheme="aiChatbot.widget_theme || 'light'"
      :allowReset="aiChatbot.allow_reset_chat"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import NavigationMenu from '../../components/NavigationMenu.vue'
import HeroSimple from '../../components/HeroSimple.vue'
import SectionProperties from '../../components/SectionProperties.vue'
import Footer from '../../components/Footer.vue'
import BreadcrumbNav from '@/Components/Minisite/BreadcrumbNav.vue'
import AiChatWidget from '@/Components/Minisite/AiChatWidget.vue'

const props = defineProps({
  business: Object,
  setting: Object,
  pageTitle: String,
  sectionData: Object,
  socialNetworks: Array,
  existingSections: Array,
  aiChatbot: Object,
})

const propertyTypes = computed(() => {
  const types = props.sectionData?.property_types || []
  return types.filter(t => t.name)
})

const selectedType = ref(null)
const selectedOperation = ref(null)

const allItems = computed(() => props.sectionData?.items || [])

const operationFilters = computed(() => {
  const operations = [...new Set(allItems.value.map(item => item.operation_type).filter(Boolean))]
  return operations
})

const sectionConfig = computed(() => ({
  view_mode: 'grid',
  show_image: true,
  show_price: true,
  show_location: true,
  show_description: true,
  show_all: true,
}))

const filteredItems = computed(() => {
  let items = allItems.value

  if (selectedType.value) {
    items = items.filter(item => item.property_type_key === selectedType.value)
  }

  if (selectedOperation.value) {
    items = items.filter(item => item.operation_type === selectedOperation.value)
  }

  return items
})

const toggleType = (typeKey) => {
  if (selectedType.value === typeKey) {
    selectedType.value = null
  } else {
    selectedType.value = typeKey
  }
}

const toggleOperation = (op) => {
  if (selectedOperation.value === op) {
    selectedOperation.value = null
  } else {
    selectedOperation.value = op
  }
}

const getOperationLabel = (op) => {
  const labels = { sale: 'Venta', rent: 'Renta', transfer: 'Traspaso' }
  return labels[op] || op
}
</script>

<style lang="less">
.category-filter,
.operation-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
  justify-content: center;
}

.category-badge {
  padding: 8px 16px;
  border-radius: 20px;
  border: 2px solid #dee2e6;
  background: #fff;
  color: #495057;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    border-color: #0d6efd;
    color: #0d6efd;
  }

  &.active {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
  }
}

.operation-badge {
  padding: 6px 12px;
  border-radius: 16px;
  border: 1px solid #dee2e6;
  background: #fff;
  color: #6c757d;
  font-size: 0.8125rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    border-color: #0d6efd;
    color: #0d6efd;
  }

  &.active {
    background: #198754;
    border-color: #198754;
    color: #fff;
  }
}

.page-content {
  padding: 32px 16px;
  background: #f8f9fa;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }
}

.page-header {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
    padding: 0 16px;
  }
}
</style>
