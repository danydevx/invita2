<template>
  <MemberLayout>
    <Head title="Notificaciones" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Notificaciones</h1>
        <p class="text-muted mb-0">Consulta tus eventos recientes y alertas internas.</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div class="text-muted small">{{ unreadCount }} sin leer</div>
        <button
          type="button"
          class="btn btn-outline-secondary btn-sm"
          :disabled="!hasUnread"
          @click="markAllAsRead"
        >
          Marcar todas
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div v-if="notifications.data.length === 0" class="card-body text-center text-muted py-5">
        Aun no tienes notificaciones.
      </div>

      <div v-else class="list-group list-group-flush">
        <div
          v-for="notification in notifications.data"
          :key="notification.id"
          class="list-group-item"
          :class="{ 'bg-light': !notification.is_read }"
        >
          <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div class="flex-grow-1">
              <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <span class="fw-semibold">{{ notification.title }}</span>
                <span class="badge text-bg-light border">{{ formatType(notification.type) }}</span>
                <span
                  v-if="!notification.is_read"
                  class="badge text-bg-warning"
                >
                  No leida
                </span>
              </div>
              <div v-if="notification.message" class="text-muted mb-2">
                {{ notification.message }}
              </div>
              <div class="text-muted small">{{ formatDate(notification.created_at) }}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <Link
                v-if="notification.url"
                :href="notification.url"
                class="btn btn-sm btn-outline-primary"
              >
                Ver
              </Link>
              <button
                v-if="!notification.is_read"
                type="button"
                class="btn btn-sm btn-outline-secondary"
                @click="markAsRead(notification)"
              >
                Marcar leida
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ notifications.data.length }} de {{ notifications.total }} notificaciones
        </div>
        <Pagination :links="notifications.links" />
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  notifications: {
    type: Object,
    required: true,
  },
  unreadCount: {
    type: Number,
    default: 0,
  },
})

const hasUnread = computed(() => props.unreadCount > 0)

const markAsRead = (notification) => {
  router.put(`/member/notifications/${notification.id}/read`, {}, {
    preserveScroll: true,
  })
}

const markAllAsRead = () => {
  router.put('/member/notifications/read-all', {}, {
    preserveScroll: true,
  })
}

const formatType = (value) => {
  if (!value) return 'system'
  return value.replace('_', ' ')
}

const formatDate = (value) => {
  if (!value) return '-'
  const parsed = new Date(value)
  if (Number.isNaN(parsed.getTime())) {
    return value
  }
  return parsed.toLocaleString()
}
</script>
