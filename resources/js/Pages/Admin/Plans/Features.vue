<template>
  <AdminLayout>
    <Head title="Features del plan" />

    <PageHeader :title="`Features de ${plan.name}`" :breadcrumbs="breadcrumbs" backHref="/admin/plans" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th scope="col">Key</th>
                  <th scope="col">Nombre</th>
                  <th scope="col">Tipo</th>
                  <th scope="col">Default</th>
                  <th scope="col">Override</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="flag in form.flags" :key="flag.id">
                  <td class="fw-semibold">{{ flag.key }}</td>
                  <td>{{ flag.name }}</td>
                  <td class="text-muted">{{ flag.type }}</td>
                  <td class="text-muted">{{ flag.default_value || '-' }}</td>
                  <td>
                    <input
                      v-model="flag.value"
                      type="text"
                      class="form-control"
                      :placeholder="placeholder(flag.type)"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <button class="btn btn-primary" type="submit" :disabled="form.processing">
            {{ form.processing ? 'Guardando...' : 'Guardar features' }}
          </button>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  plan: {
    type: Object,
    required: true,
  },
  flags: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  flags: props.flags.map((flag) => ({
    id: flag.id,
    key: flag.key,
    name: flag.name,
    type: flag.type,
    default_value: flag.default_value,
    value: flag.value ?? '',
  })),
})

const breadcrumbs = [
  { label: 'Planes', href: '/admin/plans' },
  { label: props.plan.name, active: true },
]

const submit = () => {
  form.put(`/admin/plans/${props.plan.id}/features`)
}

const placeholder = (type) => {
  if (type === 'boolean') return 'true / false'
  if (type === 'integer') return '0'
  return 'valor'
}
</script>
