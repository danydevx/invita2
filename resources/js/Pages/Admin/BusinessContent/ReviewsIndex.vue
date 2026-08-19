<template>
  <AdminLayout>
    <Head :title="`Reviews - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Businesses
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Reviews</h1>
      </div>
      <Link :href="`/admin/businesses/${business.id}/reviews/create`" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>
        New Review
      </Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Client</th>
                <th scope="col">Company</th>
                <th scope="col">Rating</th>
                <th scope="col">Active</th>
                <th scope="col" class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="reviews.data.length === 0">
                <td colspan="5" class="text-center text-muted py-4">
                  No reviews registered.
                </td>
              </tr>
              <tr v-for="review in reviews.data" :key="review.id">
                <td class="fw-semibold">{{ review.client_name }}</td>
                <td>{{ review.company || '-' }}</td>
                <td>
                  <span class="text-warning">
                    <i v-for="n in review.rating" :key="n" class="bi bi-star-fill"></i>
                  </span>
                </td>
                <td>
                  <span v-if="review.is_active" class="badge bg-success">Active</span>
                  <span v-else class="badge bg-secondary">Inactive</span>
                </td>
                <td class="text-end">
                  <Link :href="`/admin/businesses/${business.id}/reviews/${review.id}/edit`" class="btn btn-sm btn-outline-primary">
                    Edit
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="reviews.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="reviews.links" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const reviews = computed(() => page.props.reviews || { data: [], links: [] })
</script>
