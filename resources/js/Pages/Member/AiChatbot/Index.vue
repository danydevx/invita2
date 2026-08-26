<template>
  <MemberLayout>
    <Head :title="`AI Chatbot - ${listing?.name || ''}`" />

    <PageHeader
      title="AI Chatbot"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <a
          :href="`/m/${listing?.slug}`"
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
            :class="{ active: activeTab === 'reindex' }"
            @click="activeTab = 'reindex'"
          >
            <i class="bi bi-arrow-repeat me-2"></i>Reindexar
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
            :class="{ active: activeTab === 'history' }"
            @click="activeTab = 'history'"
          >
            <i class="bi bi-clock-history me-2"></i>Historial
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'analytics' }"
            @click="goToAnalytics"
          >
            <i class="bi bi-graph-up me-2"></i>Analytics
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <Link
            :href="`/member/listings/${listing?.id}/ai-chatbot/presets`"
            class="nav-link"
          >
            <i class="bi bi-robot me-2"></i>Presets
          </Link>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'config' }">
          <ConfigTab
            :business="listing"
            :settings="settings"
            :presets="presets"
            @saved="onSettingsSaved"
          />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'contexts' }">
          <ContextsTab
            :business="listing"
            :contexts="contexts"
            @saved="refreshPage"
            @deleted="refreshPage"
          />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'reindex' }">
          <ReindexTab
            :business="listing"
            :settings="settings"
            :embedding-counts="embeddingCounts"
            @reindex="onReindex"
          />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'preview' }">
          <PreviewTab
            :business="listing"
            :settings="settings"
          />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'history' }">
          <HistoryTab :business="listing" />
        </div>

        <div class="tab-pane fade" :class="{ 'show active': activeTab === 'analytics' }">
          <AnalyticsTab
            :business="listing"
            :totals="analyticsData.totals"
            :daily-stats="analyticsData.dailyStats"
            :top-questions="analyticsData.topQuestions"
            :geo-stats="analyticsData.geoStats"
            :device-stats="analyticsData.deviceStats"
            :daily-conversations="analyticsData.dailyConversations"
            :period="analyticsData.period"
            @period-change="onPeriodChange"
          />
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import ConfigTab from './ConfigTab.vue'
import ContextsTab from './ContextsTab.vue'
import ReindexTab from './ReindexTab.vue'
import PreviewTab from './PreviewTab.vue'
import HistoryTab from './HistoryTab.vue'
import AnalyticsTab from './AnalyticsTab.vue'

const page = usePage()
const listing = computed(() => page.props.business)
const businessMenu = computed(() => page.props.businessMenu || [])
const settings = computed(() => page.props.settings)
const presets = computed(() => page.props.presets || [])
const contexts = computed(() => page.props.contexts || [])
const embeddingCounts = computed(() => page.props.embeddingCounts || {})

const activeTab = ref('config')

const analyticsData = ref({
  totals: { total_conversations: 0, total_messages: 0, total_tokens: 0, total_errors: 0 },
  dailyStats: [],
  topQuestions: [],
  geoStats: [],
  deviceStats: [],
  dailyConversations: [],
  period: '30days',
})

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/listings\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Inicio', href: `/member/listings/${biz.id}/modules` },
        { label: 'Chatbot', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Chatbot', active: true },
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

const loadAnalyticsData = (period = '30days') => {
  fetch(`/member/listings/${listing.value.id}/ai-chatbot/analytics-json?period=${period}`, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
  })
    .then(res => res.json())
    .then(data => {
      analyticsData.value = data
    })
    .catch(err => {
      console.error('Error loading analytics:', err)
    })
}

const goToAnalytics = () => {
  activeTab.value = 'analytics'
  loadAnalyticsData()
}

const onPeriodChange = (newPeriod) => {
  loadAnalyticsData(newPeriod)
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
