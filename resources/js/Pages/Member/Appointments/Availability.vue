<template>
  <MemberLayout>
    <Head :title="`Disponibilidad - ${business?.name || ''}`" />

    <PageHeader
      title="Disponibilidad de Citas"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/appointments`"
    >
      <template #actions>
        <Link :href="`/member/businesses/${business?.id}/appointments`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>
          Volver a Citas
        </Link>
      </template>
    </PageHeader>

    <div v-if="flashSuccess" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ flashSuccess }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div v-if="flashError" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ flashError }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              type="button"
              class="nav-link"
              :class="{ active: activeTab === 'weekly' }"
              @click="activeTab = 'weekly'"
            >
              <i class="bi bi-calendar-week me-1"></i>
              Horarios Semanales
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              type="button"
              class="nav-link"
              :class="{ active: activeTab === 'exceptions' }"
              @click="activeTab = 'exceptions'"
            >
              <i class="bi bi-calendar-x me-1"></i>
              Excepciones
              <span v-if="exceptions.length > 0" class="badge bg-primary ms-1">
                {{ exceptions.length }}
              </span>
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              type="button"
              class="nav-link"
              :class="{ active: activeTab === 'preview' }"
              @click="activeTab = 'preview'"
            >
              <i class="bi bi-calendar3 me-1"></i>
              Vista Previa
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <div v-show="activeTab === 'weekly'" class="tab-pane">
          <p class="text-muted mb-3">
            Define los días de la semana en que tu negocio acepta citas y el horario disponible para cada día.
          </p>
          <WeeklyScheduleEditor
            :schedule="schedule"
            :businessId="business?.id"
            :errors="errors"
            @saved="onSaved"
          />
        </div>

        <div v-show="activeTab === 'exceptions'" class="tab-pane">
          <p class="text-muted mb-3">
            Agrega fechas específicas como excepciones (feriados, vacaciones, horarios especiales).
          </p>
          <ExceptionDatesList
            :exceptions="exceptions"
            :businessId="business?.id"
            @create="openCreateException"
            @edit="openEditException"
            @saved="onSaved"
            @deleted="onSaved"
          />
        </div>

        <div v-show="activeTab === 'preview'" class="tab-pane">
          <p class="text-muted mb-3">
            Vista previa del calendario con la disponibilidad configurada.
          </p>
          <AvailabilityCalendar
            :schedule="schedule"
            :exceptions="exceptions"
            :appointmentCounts="appointmentCounts"
          />
        </div>
      </div>
    </div>

    <ExceptionModal
      ref="exceptionModalRef"
      :businessId="business?.id"
    />
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import WeeklyScheduleEditor from '@/Components/Availability/WeeklyScheduleEditor.vue'
import ExceptionDatesList from '@/Components/Availability/ExceptionDatesList.vue'
import ExceptionModal from '@/Components/Availability/ExceptionModal.vue'
import AvailabilityCalendar from '@/Components/Availability/AvailabilityCalendar.vue'

const page = usePage()

const business = computed(() => page.props.business)
const schedule = computed(() => page.props.weeklySchedule || [])
const exceptions = computed(() => page.props.exceptions || [])
const appointmentCounts = computed(() => page.props.appointmentCounts || {})
const errors = computed(() => page.props.errors || {})

const flashSuccess = computed(() => page.props.flash?.success || null)
const flashError = computed(() => page.props.flash?.error || null)

const activeTab = ref('weekly')
const exceptionModalRef = ref(null)

const breadcrumbs = computed(() => {
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: business.value?.name || '', href: `/member/businesses/${business.value?.id}/edit` },
    { label: 'Citas', href: `/member/businesses/${business.value?.id}/appointments` },
    { label: 'Disponibilidad', active: true },
  ]
})

const openCreateException = () => {
  if (exceptionModalRef.value) {
    exceptionModalRef.value.open(null)
  }
}

const openEditException = (exception) => {
  if (exceptionModalRef.value) {
    exceptionModalRef.value.open(exception)
  }
}

const onSaved = () => {
  router.reload({
    only: ['exceptions', 'weeklySchedule'],
    preserveScroll: true,
    preserveState: false,
  })
}
</script>