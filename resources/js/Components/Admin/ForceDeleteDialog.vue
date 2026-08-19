<template>
  <dialog
    ref="dialogRef"
    class="border-0 rounded-3 p-0 shadow position-fixed top-50 start-50 translate-middle"
    style="min-width: 500px; max-width: 90vw;"
    @click="onBackdropClick"
    @cancel.prevent="cancel"
  >
    <div class="p-4">
      <div class="d-flex align-items-start justify-content-between mb-3">
        <h5 class="mb-0 text-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Eliminar permanentemente
        </h5>
        <button type="button" class="btn-close" aria-label="Cerrar" @click="cancel"></button>
      </div>

      <div class="alert alert-danger">
        <p class="mb-2"><strong>Esta accion es IRREVERSIBLE.</strong></p>
        <p class="mb-0">Se eliminaran todos los datos del usuario incluyendo:</p>
        <ul class="mb-0 mt-2">
          <li>Businesses y todos sus datos relacionados</li>
          <li>Citas y turnos</li>
          <li>Leads y contactos</li>
          <li>Productos y servicios</li>
          <li>Resenas y galerias</li>
          <li>Datos de pago y facturacion</li>
          <li>Cuenta de usuario</li>
        </ul>
      </div>

      <div v-if="user" class="mb-3 p-3 bg-light rounded">
        <strong>Usuario:</strong> {{ user.name }} ({{ user.email }})
      </div>

      <div class="form-check mb-3">
        <input
          id="force-delete-confirm"
          v-model="confirmed"
          class="form-check-input"
          type="checkbox"
        />
        <label for="force-delete-confirm" class="form-check-label">
          Entiendo que esta accion no se puede deshacer
        </label>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-outline-secondary" @click="cancel">
          Cancelar
        </button>
        <button
          type="button"
          class="btn btn-danger"
          :disabled="!confirmed"
          @click="confirm"
        >
          <i class="bi bi-trash me-1"></i>
          Eliminar permanentemente
        </button>
      </div>
    </div>
  </dialog>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  user: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])
const dialogRef = ref(null)
const confirmed = ref(false)

watch(
  () => props.modelValue,
  (value) => {
    if (value) {
      confirmed.value = false
      openDialog()
    } else {
      closeDialog()
    }
  }
)

const openDialog = () => {
  if (!dialogRef.value || dialogRef.value.open) return
  dialogRef.value.showModal()
}

const closeDialog = () => {
  if (!dialogRef.value || !dialogRef.value.open) return
  dialogRef.value.close()
}

const cancel = () => {
  emit('cancel')
  emit('update:modelValue', false)
  closeDialog()
}

const confirm = () => {
  if (!confirmed.value) return
  emit('confirm')
  emit('update:modelValue', false)
  closeDialog()
}

const onBackdropClick = (event) => {
  if (event.target === dialogRef.value) {
    cancel()
  }
}
</script>
