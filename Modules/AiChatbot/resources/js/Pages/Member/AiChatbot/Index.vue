<template>
  <MemberLayout>
    <Head :title="`AI Chatbot - ${business?.name || ''}`" />

    <PageHeader
      title="AI Chatbot"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <a
          :href="`/m/${business?.slug}/preview`"
          target="_blank"
          class="btn btn-outline-primary btn-sm"
        >
          <i class="bi bi-box-arrow-up-right me-1"></i>Ver en Minisite
        </a>
      </template>
    </PageHeader>

    <div class="ai-chatbot-page">
      <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'config' }"
            @click="activeTab = 'config'"
          >
            <i class="bi bi-gear me-2"></i>Configuración
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'contexts' }"
            @click="activeTab = 'contexts'"
          >
            <i class="bi bi-file-text me-2"></i>Contextos
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'preview' }"
            @click="activeTab = 'preview'"
          >
            <i class="bi bi-chat-left-dots me-2"></i>Vista Previa
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'widget' }"
            @click="activeTab = 'widget'; loadWidgetData()"
          >
            <i class="bi bi-code-square me-2"></i>Widget
          </button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'config' }">
          <ConfigTab
            :business="business"
            :settings="settings"
            :embedding-counts="embeddingCounts"
            @saved="onSettingsSaved"
            @reindex="onReindex"
          />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'contexts' }">
          <ContextsTab
            :business="business"
            :contexts="contexts"
            @saved="refreshPage"
            @deleted="refreshPage"
          />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'preview' }">
          <PreviewTab
            :business="business"
            :settings="settings"
          />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'widget' }">
          <WidgetTab
            :business="business"
            :widget="widgetData"
            :stats="widgetStats"
            :intent-cta="widgetIntentCta"
            @saved="loadWidgetData"
          />
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import ConfigTab from './ConfigTab.vue'
import ContextsTab from './ContextsTab.vue'
import PreviewTab from './PreviewTab.vue'
import WidgetTab from './WidgetTab.vue'

const page = usePage()
const business = computed(() => page.props.business)
const businessMenu = computed(() => page.props.businessMenu || [])
const settings = computed(() => page.props.settings)
const contexts = computed(() => page.props.contexts || [])
const embeddingCounts = computed(() => page.props.embeddingCounts || {})

const activeTab = ref('config')
const widgetData = ref(null)
const widgetStats = ref({})
const widgetIntentCta = ref(null)

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
        { label: 'AI Chatbot', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'AI Chatbot', active: true },
  ]
})

const onSettingsSaved = () => {
  refreshPage()
}

const onReindex = () => {
  refreshPage()
}

const refreshPage = () => {
  router.reload({ preserveScroll: true })
}

const loadWidgetData = () => {
  if (!widgetData.value) {
    axios.get(`/member/businesses/${business.value.id}/ai-chatbot/widget/settings`)
      .then(response => {
        widgetData.value = response.data.widget
        widgetStats.value = response.data.stats
        widgetIntentCta.value = response.data.intent_cta
      })
      .catch(error => {
        console.error('Error loading widget data:', error)
      })
  }
}
</script>

<style lang="less" scoped>
.ai-chatbot-page {
  padding: 0 0 48px;
}

.nav-tabs {
  border-bottom: 2px solid #dee2e6;

  .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    padding: 12px 20px;
    font-weight: 500;

    &:hover {
      color: #0d6efd;
      border-color: transparent;
    }

    &.active {
      color: #0d6efd;
      border-bottom-color: #0d6efd;
      background: transparent;
    }
  }
}

.tab-content {
  padding-top: 24px;
}
</style>
