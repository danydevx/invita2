<template>
  <MemberLayout>
    <Head :title="`Invitados - ${listing?.name || ''}`" />

    <PageHeader
      title="Invitados"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/modules`"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/checkin`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-qr-code-scan me-1"></i>Check-in
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white">
        <h5 class="mb-0">Agregar invitado</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="submitGuest" class="row g-3">
          <div class="col-md-4">
            <input
              v-model="guestForm.name"
              type="text"
              class="form-control"
              placeholder="Nombre completo"
              required
            />
          </div>
          <div class="col-md-3">
            <input
              v-model="guestForm.email"
              type="email"
              class="form-control"
              placeholder="Correo electrónico"
            />
          </div>
          <div class="col-md-3">
            <input
              v-model="guestForm.phone"
              type="text"
              class="form-control"
              placeholder="Teléfono"
            />
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100" :disabled="sending">
              <i class="bi bi-plus-lg me-1"></i>Agregar
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="guests.data.length === 0" class="text-center text-muted py-5">
          <i class="bi bi-people display-1"></i>
          <h5 class="mt-3">No hay invitados registrados</h5>
          <p>Agrega invitados para que puedan registrar su llegada.</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Plus Ones</th>
                <th>Notas</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="guest in guests.data" :key="guest.id">
                <td><strong>{{ guest.name }}</strong></td>
                <td>{{ guest.email || '-' }}</td>
                <td>{{ guest.phone || '-' }}</td>
                <td>{{ guest.plus_ones ?? 0 }}</td>
                <td>{{ guest.notes || '-' }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteGuest(guest)">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="guests.data.length > 0" class="d-flex justify-content-center mt-4">
          <Pagination :links="guests.links" />
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  listing: Object,
  guests: Object,
})

const page = usePage()
const listing = computed(() => page.props.listing)
const sending = ref(false)

const guestForm = reactive({
  name: '',
  email: '',
  phone: '',
  plus_ones: 0,
  notes: '',
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Invitados', active: true },
])

const submitGuest = () => {
  if (!guestForm.name) return
  sending.value = true
  router.post(`/member/listings/${listing.value.id}/guests`, guestForm, {
    preserveScroll: true,
    onFinish: () => {
      sending.value = false
      guestForm.name = ''
      guestForm.email = ''
      guestForm.phone = ''
      guestForm.plus_ones = 0
      guestForm.notes = ''
    },
  })
}

const deleteGuest = (guest) => {
  if (confirm(`¿Eliminar a ${guest.name}?`)) {
    router.delete(`/member/listings/${listing.value.id}/guests/${guest.id}`, {
      preserveScroll: true,
    })
  }
}
</script>
