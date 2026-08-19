<template>
  <div class="mb-3">
    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <input
      type="file"
      class="form-control"
      :id="id"
      :disabled="readonly"
      :multiple="multiple"
      @change="onSelect"
    />

    <!-- Lista de archivos seleccionados -->
    <ul v-if="filesList.length" class="mt-2 small">
      <li
        v-for="(f, i) in filesList"
        :key="i"
        class="text-muted"
      >
        <i class="bi bi-file-earmark"></i>
        {{ f.name }} ({{ formatSize(f.size) }})
      </li>
    </ul>

    <!-- Errores -->
    <div v-if="error" class="text-danger small mt-2">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"

const props = defineProps({
  id: String,
  label: String,
  required: Boolean,
  readonly: Boolean,

  multiple: {
    type: Boolean,
    default: false
  },

  maxFiles: {
    type: Number,
    default: 1
  },

  extensions: {
    type: [Array, String],
    default: () => ["pdf", "doc", "docx"]
  },

  maxSizeMB: {
    type: Number,
    default: 5
  }
})

const emit = defineEmits(["update:modelValue"])

const filesList = ref([])
const error = ref(null)

/* =========================================================
   VALIDACIONES
========================================================= */
function validate(files) {
  error.value = null

  const allowedExt = Array.isArray(props.extensions)
    ? props.extensions.map(e => e.toLowerCase().trim())
    : props.extensions.split(",").map(e => e.toLowerCase().trim())

  const maxBytes = props.maxSizeMB * 1024 * 1024

  // Limitar cantidad
  if (props.multiple && files.length > props.maxFiles) {
    error.value = `Máximo permitido: ${props.maxFiles} archivos`
    return false
  }

  for (const file of files) {
    const ext = file.name.split(".").pop().toLowerCase()

    // Validar extensión
    if (!allowedExt.includes(ext)) {
      error.value = `Extensión no permitida: .${ext}. Permitidas: ${allowedExt.join(", ")}`
      return false
    }

    // Validar tamaño
    if (file.size > maxBytes) {
      error.value = `El archivo ${file.name} excede el límite de ${props.maxSizeMB} MB`
      return false
    }
  }

  return true
}

/* =========================================================
   HANDLER
========================================================= */
function onSelect(event) {
  const files = Array.from(event.target.files || [])

  if (!validate(files)) {
    filesList.value = []
    emit("update:modelValue", props.multiple ? [] : null)
    return
  }

  filesList.value = files

  if (!props.multiple) {
    emit("update:modelValue", files[0] ?? null)
  } else {
    emit("update:modelValue", files)
  }
}

/* =========================================================
   HELPER
========================================================= */
function formatSize(bytes) {
  if (!bytes) return "0 KB"
  const kb = bytes / 1024
  if (kb < 1024) return kb.toFixed(1) + " KB"
  return (kb / 1024).toFixed(1) + " MB"
}
</script>
