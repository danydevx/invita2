<template>
  <div class="mb-3">

    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <!-- INPUT -->
    <input
      type="file"
      accept="image/*"
      class="form-control"
      :id="id"
      :multiple="multiple"
      :disabled="readonly"
      @change="onFileSelect"
    />

    <!-- PREVIEWS -->
    <div v-if="realFiles.length" class="mt-3 d-flex flex-wrap gap-3">

      <div
        v-for="(file, index) in realFiles"
        :key="index"
        class="position-relative border rounded"
        style="width:120px;height:120px;overflow:hidden;"
      >

        <img
          :src="file.preview"
          class="img-fluid"
          style="object-fit:cover;width:100%;height:100%;"
        />

        <!-- BOTÓN ELIMINAR -->
        <button
          v-if="!readonly"
          type="button"
          class="btn btn-danger btn-sm position-absolute"
          style="top:4px;right:4px;"
          @click="removeImage(index)"
        >
          ×
        </button>

      </div>

    </div>

    <!-- ERROR -->
    <div v-if="error" class="text-danger small mt-2">{{ error }}</div>

  </div>
</template>

<script setup>
import { ref } from "vue"

const props = defineProps({
  id: String,
  label: String,
  required: Boolean,
  readonly: Boolean,
 
  multiple: { type: Boolean, default: false },
  maxFiles: { type: Number, default: 1 },
  extensions: { type: [Array, String], default: () => ["jpg","jpeg","png","webp"] },
  maxSizeMB: { type: Number, default: 5 }
})

const emit = defineEmits(["update:modelValue"])

const error = ref(null)
const realFiles = ref([])   
let inputEl = null

/* =============================================================
   VALIDACIONES
============================================================= */
function validateFiles(files) {
  const allowed = Array.isArray(props.extensions)
    ? props.extensions
    : props.extensions.split(",").map(e => e.trim().toLowerCase())

  const maxBytes = props.maxSizeMB * 1024 * 1024

  for (const file of files) {
    const ext = file.name.split(".").pop().toLowerCase()

    if (!allowed.includes(ext)) {
      error.value = `Formato no permitido: .${ext}`
      return false
    }

    if (file.size > maxBytes) {
      error.value = `${file.name} excede ${props.maxSizeMB} MB`
      return false
    }
  }

  return true
}

/* =============================================================
   HANDLER DE SELECCIÓN
============================================================= */
function onFileSelect(e) {
  const selected = Array.from(e.target.files || [])
  inputEl = e.target

  if (!validateFiles(selected)) {
    inputEl.value = ""
    return
  }

  // SINGLE MODE → reemplazar
  if (!props.multiple) {
    realFiles.value = selected.map(f => ({
      file: f,
      preview: URL.createObjectURL(f)
    }))
    emit("update:modelValue", selected[0])
    inputEl.value = ""
    return
  }

  // MULTIPLE MODE → agregar al array
  for (const f of selected) {
    if (realFiles.value.length >= props.maxFiles) {
      error.value = `Máximo ${props.maxFiles} imágenes`
      break
    }

    realFiles.value.push({
      file: f,
      preview: URL.createObjectURL(f)
    })
  }

  emit("update:modelValue", realFiles.value.map(f => f.file))

  // limpiar input para permitir seleccionar la misma imagen otra vez
  inputEl.value = ""
}

/* =============================================================
   ELIMINAR IMAGEN
============================================================= */
function removeImage(index) {
  realFiles.value.splice(index, 1)

  if (!props.multiple) {
    emit("update:modelValue", null)
  } else {
    emit("update:modelValue", realFiles.value.map(f => f.file))
  }

  if (realFiles.value.length === 0 && inputEl) {
    inputEl.value = ""
  }
}
</script>
