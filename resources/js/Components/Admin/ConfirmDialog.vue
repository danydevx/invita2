<template>
  <dialog
    ref="dialogRef"
    class="border-0 rounded-3 p-0 shadow position-fixed top-50 start-50 translate-middle"
    @click="onBackdropClick"
    @cancel.prevent="cancel"
  >
    <div class="p-4">
      <div class="d-flex align-items-start justify-content-between mb-3">
        <h5 class="mb-0">{{ title }}</h5>
        <button type="button" class="btn-close" aria-label="Cerrar" @click="cancel"></button>
      </div>

      <p class="text-muted mb-4">{{ message }}</p>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-outline-secondary" @click="cancel">
          {{ cancelLabel }}
        </button>
        <button type="button" class="btn" :class="confirmClass" @click="confirm">
          {{ confirmLabel }}
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
  title: {
    type: String,
    default: 'Confirmar accion',
  },
  message: {
    type: String,
    default: 'Estas seguro de continuar?'
  },
  confirmLabel: {
    type: String,
    default: 'Confirmar',
  },
  cancelLabel: {
    type: String,
    default: 'Cancelar',
  },
  confirmVariant: {
    type: String,
    default: 'danger',
  },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])
const dialogRef = ref(null)

const confirmClass = computed(() => {
  return `btn-${props.confirmVariant}`
})

const openDialog = () => {
  if (!dialogRef.value || dialogRef.value.open) return
  dialogRef.value.showModal()
}

const closeDialog = () => {
  if (!dialogRef.value || !dialogRef.value.open) return
  dialogRef.value.close()
}

watch(
  () => props.modelValue,
  (value) => {
    if (value) {
      openDialog()
    } else {
      closeDialog()
    }
  }
)

const cancel = () => {
  emit('cancel')
  emit('update:modelValue', false)
  closeDialog()
}

const confirm = () => {
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
