<template>
  <AdminLayout>
    <Head :title="`Edit Review - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/reviews`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Back
        </Link>
        <h1 class="h4 mb-1 mt-1">Edit Review</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Client Name *</label>
              <input type="text" v-model="form.client_name" class="form-control" required />
              <div v-if="errors.client_name" class="text-danger small">{{ errors.client_name }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Company</label>
              <input type="text" v-model="form.company" class="form-control" />
            </div>

            <div class="col-12">
              <label class="form-label">Comment *</label>
              <textarea v-model="form.comment" class="form-control" rows="4" required></textarea>
              <div v-if="errors.comment" class="text-danger small">{{ errors.comment }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Rating *</label>
              <select v-model="form.rating" class="form-select" required>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
              </select>
              <div v-if="errors.rating" class="text-danger small">{{ errors.rating }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Google Link</label>
              <input type="url" v-model="form.google_link" class="form-control" placeholder="https://..." />
            </div>

            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" v-model="form.is_active" class="form-check-input" id="is_active" />
                <label class="form-check-label" for="is_active">Active</label>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Saving...' : 'Save' }}
              </button>
              <Link :href="`/admin/businesses/${business.id}/reviews`" class="btn btn-outline-secondary ms-2">
                Cancel
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
const review = computed(() => page.props.review)
const errors = computed(() => page.props.errors || {})
const sending = computed(() => false)

const form = reactive({
  client_name: review.value.client_name,
  company: review.value.company || '',
  comment: review.value.comment,
  rating: review.value.rating,
  google_link: review.value.google_link || '',
  is_active: review.value.is_active || false,
})

const submit = () => {
  router.put(`/admin/businesses/${business.value.id}/reviews/${review.value.id}`, form)
}
</script>
