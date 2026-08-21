<template>
  <div>
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Estadísticas del Chatbot</h1>
        <p class="text-muted mb-0">Analiza el rendimiento de tu chatbot.</p>
      </div>
      <div class="btn-group">
        <button
          v-for="p in periods"
          :key="p.value"
          class="btn"
          :class="period === p.value ? 'btn-primary' : 'btn-outline-primary'"
          @click="changePeriod(p.value)"
        >
          {{ p.label }}
        </button>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary bg-opacity-10 text-primary">
            <i class="bi bi-chat-dots"></i>
          </div>
          <div class="stat-value">{{ formatNumber(totals.total_conversations) }}</div>
          <div class="stat-label">Conversaciones</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success bg-opacity-10 text-success">
            <i class="bi bi-chat-left-text"></i>
          </div>
          <div class="stat-value">{{ formatNumber(totals.total_messages) }}</div>
          <div class="stat-label">Mensajes</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning bg-opacity-10 text-warning">
            <i class="bi bi-lightning-charge"></i>
          </div>
          <div class="stat-value">{{ formatNumber(totals.total_tokens) }}</div>
          <div class="stat-label">Tokens</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info bg-opacity-10 text-info">
            <i class="bi bi-clock"></i>
          </div>
          <div class="stat-value">{{ formatLatency(totals.total_latency_ms) }}</div>
          <div class="stat-label">Latencia prom.</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
            <i class="bi bi-currency-dollar"></i>
          </div>
          <div class="stat-value">${{ formatCost(totals.total_cost) }}</div>
          <div class="stat-label">Costo est.</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-danger bg-opacity-10 text-danger">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
          <div class="stat-value">{{ formatNumber(totals.total_errors) }}</div>
          <div class="stat-label">Errores</div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white">
            <h6 class="mb-0">Conversaciones por dia</h6>
          </div>
          <div class="card-body">
            <div v-if="dailyConversations.length" class="chart-container">
              <div class="chart-bars">
                <div
                  v-for="(item, index) in dailyConversations"
                  :key="index"
                  class="chart-bar-wrapper"
                >
                  <div
                    class="chart-bar"
                    :style="{ height: getBarHeight(item.count) + '%' }"
                    :title="`${item.date}: ${item.count} conversaciones`"
                  ></div>
                  <div class="chart-label">{{ formatDate(item.date) }}</div>
                </div>
              </div>
            </div>
            <div v-else class="text-center text-muted py-5">
              <i class="bi bi-bar-chart display-4"></i>
              <p class="mt-3">No hay datos para este periodo</p>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white">
            <h6 class="mb-0">Preguntas mas frecuentes</h6>
          </div>
          <div class="card-body p-0">
            <div v-if="topQuestions.length" class="list-group list-group-flush">
              <div
                v-for="(q, index) in topQuestions"
                :key="q.id"
                class="list-group-item d-flex justify-content-between align-items-center"
              >
                <div class="d-flex align-items-start gap-3">
                  <span class="badge bg-primary rounded-circle" style="width: 28px; height: 28px; line-height: 20px;">
                    {{ index + 1 }}
                  </span>
                  <span class="question-text">{{ q.question }}</span>
                </div>
                <span class="badge bg-secondary">{{ q.times_asked }} veces</span>
              </div>
            </div>
            <div v-else class="text-center text-muted py-5">
              <i class="bi bi-question-circle display-4"></i>
              <p class="mt-3">No hay preguntas registradas</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white">
            <h6 class="mb-0">Por pais</h6>
          </div>
          <div class="card-body p-0">
            <div v-if="geoStats.length" class="list-group list-group-flush">
              <div
                v-for="geo in geoStats"
                :key="geo.country"
                class="list-group-item d-flex justify-content-between align-items-center"
              >
                <div class="d-flex align-items-center gap-2">
                  <span class="country-flag">{{ getCountryFlag(geo.country_code) }}</span>
                  <span>{{ geo.country }}</span>
                </div>
                <span class="badge bg-secondary">{{ geo.count }}</span>
              </div>
            </div>
            <div v-else class="text-center text-muted py-4">
              <p>Sin datos</p>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white">
            <h6 class="mb-0">Por dispositivo</h6>
          </div>
          <div class="card-body">
            <div v-if="deviceStats.length" class="device-stats">
              <div
                v-for="device in deviceStats"
                :key="device.device_type"
                class="device-item mb-3"
              >
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-capitalize">{{ device.device_type }}</span>
                  <span class="text-muted">{{ device.count }}</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div
                    class="progress-bar"
                    :style="{ width: getDevicePercent(device.count) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
            <div v-else class="text-center text-muted py-4">
              <p>Sin datos</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  business: Object,
  totals: Object,
  dailyStats: Array,
  topQuestions: Array,
  geoStats: Array,
  deviceStats: Array,
  dailyConversations: Array,
  period: String,
})

const emit = defineEmits(['period-change'])

const periods = [
  { label: '7 dias', value: '7days' },
  { label: '30 dias', value: '30days' },
  { label: '90 dias', value: '90days' },
]

const period = ref(props.period)

const changePeriod = (newPeriod) => {
  period.value = newPeriod
  emit('period-change', newPeriod)
}

const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  }
  if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}

const formatLatency = (ms) => {
  if (!ms) return '0ms'
  if (ms >= 1000) {
    return (ms / 1000).toFixed(1) + 's'
  }
  return ms + 'ms'
}

const formatCost = (cost) => {
  if (!cost) return '0.00'
  return parseFloat(cost).toFixed(4)
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}

const maxConversations = computed(() => {
  if (!props.dailyConversations?.length) return 1
  return Math.max(...props.dailyConversations.map(d => d.count))
})

const getBarHeight = (count) => {
  return Math.max((count / maxConversations.value) * 100, 5)
}

const getCountryFlag = (code) => {
  if (!code || code === 'XX') return '🌍'
  const codePoints = code
    .toUpperCase()
    .split('')
    .map(char => 127397 + char.charCodeAt(0))
  return String.fromCodePoint(...codePoints)
}

const totalDevices = computed(() => {
  if (!props.deviceStats?.length) return 1
  return props.deviceStats.reduce((sum, d) => sum + d.count, 0)
})

const getDevicePercent = (count) => {
  return (count / totalDevices.value) * 100
}
</script>

<style lang="less" scoped>
.analytics-tab {
  .stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 12px;
    }

    .stat-value {
      font-size: 1.75rem;
      font-weight: 700;
      color: #212529;
      line-height: 1;
    }

    .stat-label {
      font-size: 0.875rem;
      color: #6c757d;
      margin-top: 4px;
    }
  }

  .chart-container {
    height: 200px;
    display: flex;
    align-items: flex-end;
  }

  .chart-bars {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    width: 100%;
    height: 100%;
    gap: 4px;
  }

  .chart-bar-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    justify-content: flex-end;
  }

  .chart-bar {
    width: 100%;
    max-width: 30px;
    background: #3B82F6;
    border-radius: 4px 4px 0 0;
    min-height: 4px;
    transition: height 0.3s ease;
  }

  .chart-label {
    font-size: 0.65rem;
    color: #6c757d;
    margin-top: 4px;
    white-space: nowrap;
  }

  .question-text {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .country-flag {
    font-size: 1.25rem;
  }

  .device-stats {
    .device-item {
      .progress {
        background: #e9ecef;
      }

      .progress-bar {
        background: #3B82F6;
      }
    }
  }
}
</style>
