<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">
    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <!-- Safari: fallback a textarea -->
    <textarea
      v-if="isSafari"
      class="form-control"
      :id="id"
      :readonly="readonly"
      :required="required"
      :placeholder="config.htmlPlaceholder"
      :style="{ minHeight: (config.htmlHeight || 240) + 'px' }"
      v-model="localValue"
    />

    <!-- Otros navegadores: Quill -->
    <div
      v-else
      :id="id"
      ref="editor"
      class="quill-editor"
    ></div>

    <small v-if="config.descripcion" class="text-muted d-block mt-1">
      {{ config.descripcion }}
    </small>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, onBeforeUnmount } from 'vue'
import 'quill/dist/quill.snow.css'

/* ================================================
   PROPS
================================================ */
const props = defineProps({
  id: String,
  label: String,
  modelValue: String,
  required: Boolean,
  readonly: Boolean,
  config: {
    type: Object,
    default: () => ({
      fieldColumns: 12,
      descripcion: '',

      // Config HTML
      htmlHeight: 240,
      htmlToolbar: 'standard', // basic | standard | full
      htmlUploadUrl: '/admin/uploads/quill',
      htmlPlaceholder: 'Escribe contenido...'
    })
  }
})

const emit = defineEmits(['update:modelValue'])

/* ================================================
   Safari detect
================================================ */
const isSafari = ref(false)

function detectSafari() {
  if (typeof window === 'undefined') return false
  const ua = navigator.userAgent
  // Safari real (no Chrome/Chromium/Edge en iOS/macOS)
  return /Safari/i.test(ua) && !/Chrome|CriOS|Chromium|Edg/i.test(ua)
}

/* ================================================
   v-model para textarea (fallback)
================================================ */
const localValue = computed({
  get: () => props.modelValue || '',
  set: (v) => emit('update:modelValue', v)
})

/* ================================================
   Quill
================================================ */
const editor = ref(null)
let quill = null

const TOOLBARS = {
  basic: [
    ['bold', 'italic', 'underline'],
    [{ list: 'ordered' }, { list: 'bullet' }],
  ],
  standard: [
    [{ header: [1, 2, 3, 4, 5, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ script: 'sub' }, { script: 'super' }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ align: [] }],
    ['blockquote'],
    ['link']
  ],
  full: [
    [{ header: [1, 2, 3, 4, 5, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ script: 'sub' }, { script: 'super' }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ align: [] }],
    ['blockquote', 'code-block'],
    ['link', 'image']
  ]
}

function imageHandler() {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'

  input.onchange = async () => {
    const file = input.files[0]
    if (!file) return

    const formData = new FormData()
    formData.append('image', file)

    const uploadUrl = props.config.htmlUploadUrl

    try {
      const response = await fetch(uploadUrl, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
      })

      const data = await response.json()

      const range = quill.getSelection(true)
      quill.insertEmbed(range.index, 'image', data.url)
      quill.setSelection(range.index + 1)
    } catch (error) {
      console.error('Upload error:', error)
    }
  }

  input.click()
}

onMounted(async () => {
  isSafari.value = detectSafari()

  // Safari: NO inicializar Quill
  if (isSafari.value) return

  // Import dinámico para evitar problemas en Safari
  const { default: Quill } = await import('quill')

  quill = new Quill(editor.value, {
    theme: 'snow',
    readOnly: props.readonly,
    placeholder: props.config.htmlPlaceholder,
    modules: {
      toolbar: {
        container: TOOLBARS[props.config.htmlToolbar] || TOOLBARS.standard,
        handlers: { image: imageHandler }
      }
    }
  })

  // Alto dinámico
  const qlEditor = editor.value.querySelector('.ql-editor')
  if (qlEditor) qlEditor.style.minHeight = (props.config.htmlHeight || 240) + 'px'

  // Valor inicial
  quill.clipboard.dangerouslyPasteHTML(props.modelValue || '')

  // Emit cambios
  quill.on('text-change', () => {
    emit('update:modelValue', quill.root.innerHTML)
  })
})

onBeforeUnmount(() => {
  if (quill) {
    // Quill no tiene destroy oficial, pero esto ayuda a soltar refs
    quill = null
  }
})

watch(
  () => props.modelValue,
  (newVal) => {
    if (!quill) return
    const incoming = newVal || ''
    if (quill.root.innerHTML !== incoming) {
      quill.clipboard.dangerouslyPasteHTML(incoming)
    }
  }
)
</script>

<style scoped>
.quill-editor {
  border: 1px solid #ced4da;
  border-radius: 5px;
}
</style>
