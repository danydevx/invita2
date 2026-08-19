<template>
  <AdminLayout>
    <Head :title="`Nueva Pregunta - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/faqs`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
        <h1 class="h4 mb-1 mt-1">Nueva Pregunta Frecuente</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Pregunta *</label>
              <input type="text" v-model="form.question" class="form-control" required />
              <div v-if="errors.question" class="text-danger small">{{ errors.question }}</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Categoria</label>
              <select v-model="form.category_id" class="form-select">
                <option :value="null">Sin categoria</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Respuesta *</label>
              <textarea v-model="form.answer" class="form-control" rows="4" required></textarea>
              <div v-if="errors.answer" class="text-danger small">{{ errors.answer }}</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Orden</label>
              <input type="number" v-model="form.sort_order" class="form-control" min="0" />
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.is_active" class="form-check-input" id="is_active" />
                <label class="form-check-label" for="is_active">Activo</label>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/faqs`" class="btn btn-outline-secondary ms-2">
                Cancelar
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
const categories = computed(() => page.props.categories || [])
const errors = computed(() => page.props.errors || {})
const sending = ref(false)

const form = reactive({
  question: '',
  answer: '',
  category_id: null,
  is_active: true,
  sort_order: 0,
})

const submit = () => {
  router.post(`/admin/businesses/${business.value.id}/faqs`, form, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
