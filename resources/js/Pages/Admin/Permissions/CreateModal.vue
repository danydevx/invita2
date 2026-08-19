<template>
  <div
    id="createPermissionModal"
    ref="modalRef"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="createPermissionModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form @submit.prevent="submit">
          <div class="modal-header">
            <h5 id="createPermissionModalLabel" class="modal-title">Agregar permiso</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body">
            <FieldText
              id="permission-name-modal"
              label="Nombre del permiso"
              placeholder="Ej: users.view"
              v-model="form.name"
              :formError="form.errors.name"
              required
            />
          </div>

          <div class="modal-footer">
            <Link href="/admin/permissions/create" class="btn btn-outline-secondary">
              Formulario completo
            </Link>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import FieldText from '@/Components/Fields/FieldText.vue'

const modalRef = ref(null)

const form = useForm({
  name: '',
})

const closeModal = () => {
  if (!modalRef.value) return
  const instance = Modal.getOrCreateInstance(modalRef.value)
  instance.hide()
}

const submit = () => {
  form.post('/admin/permissions', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      form.clearErrors()
      closeModal()
    },
  })
}
</script>
