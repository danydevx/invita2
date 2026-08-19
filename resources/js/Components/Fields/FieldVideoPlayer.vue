<template>
  <div class="form-group" :class="classObject">
    <label :for="id" class="form-label">
      {{ label }} <strong v-if="required">*</strong>
    </label>

    <!-- Input para subir nuevo video -->
    <input
      :id="id"
      type="file"
      class="form-control"
      :accept="accept"
      :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
      @change="onFileChange"
      @blur="onBlur"
      :disabled="readonly"
    />

    <!-- Errores -->
    <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
      {{ formError || validationMessage }}
    </div>

    <!-- Vista previa con Video.js -->
    <div v-if="videoUrl" class="mt-3">
      <label class="form-label text-muted">Vista previa del nuevo video:</label>
      <video
        ref="videoElement"
        class="video-js vjs-big-play-centered video_player"
        controls
        playsinline
      ></video>
      <button
        v-if="!readonly"
        type="button"
        class="btn btn-sm btn-outline-danger mt-2"
        @click="removeNewFile"
      >
        <i class="bi bi-x-circle"></i> Quitar video
      </button>
    </div>

    <!-- Vista previa del video actual -->
    <div v-else-if="initialPath && !hideInitial" class="mt-3">
      <label class="form-label text-muted">Video actual:</label>
      <video
        controls
        class="w-100"
        style="max-height: 300px;"
        :src="`/storage/${initialPath}`"
      ></video>
      <button
        v-if="!readonly"
        type="button"
        class="btn btn-sm btn-outline-danger mt-2"
        @click="removeExistingVideo"
      >
        <i class="bi bi-x-circle"></i> Quitar video actual
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount, nextTick } from 'vue'
import videojs from 'video.js'
import 'video.js/dist/video-js.css'

const props = defineProps({
  id: { type: String, required: true },
  label: { type: String, default: 'Subir video' },
  required: { type: Boolean, default: false },
  formError: { type: String, default: '' },
  showValidation: { type: Boolean, default: false },
  validateFunction: { type: Function, default: null },
  classObject: { type: String, default: '' },
  accept: {
    type: String,
    default: 'video/mp4,video/x-m4v,video/quicktime'
  },
  initialPath: { type: String, default: '' },
  readonly: { type: Boolean, default: false }, // <-- añadido aquí
})

const emit = defineEmits(['update:modelValue', 'update:keep', 'blur'])

const videoUrl = ref(null)
const hideInitial = ref(false)
const videoElement = ref(null)
let player = null

const validationMessage = computed(() =>
  props.validateFunction ? props.validateFunction() : ''
)

function onFileChange(event) {
  if (props.readonly) return
  const file = event.target.files[0]
  if (file && file.type.startsWith('video/')) {
    videoUrl.value = URL.createObjectURL(file)
    hideInitial.value = true
    emit('update:modelValue', file)
    emit('update:keep', false)

    nextTick(() => {
      if (videoElement.value) {
        if (player) {
          player.dispose()
        }
        player = videojs(videoElement.value, {
          sources: [{ src: videoUrl.value, type: 'video/mp4' }],
          preload: 'auto'
        })
      }
    })
  }
}

function removeNewFile() {
  if (props.readonly) return
  if (videoUrl.value) URL.revokeObjectURL(videoUrl.value)
  videoUrl.value = null
  emit('update:modelValue', null)
  emit('update:keep', false)

  if (player) {
    player.dispose()
    player = null
  }

  const input = document.getElementById(props.id)
  if (input) input.value = ''
}

function removeExistingVideo() {
  if (props.readonly) return
  hideInitial.value = true
  emit('update:modelValue', null)
  emit('update:keep', false)
}

function onBlur() {
  emit('blur')
}

onBeforeUnmount(() => {
  if (player) player.dispose()
  if (videoUrl.value) URL.revokeObjectURL(videoUrl.value)
})
</script>

<style scoped>
.video_player {
  width: 100%;
  max-height: 300px;
  background-color: #000;
}
</style>
