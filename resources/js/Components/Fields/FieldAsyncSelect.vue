<!-- resources/js/Components/Admin/Fields/FieldAsyncSelect.vue -->
<template>
  <div>
    <label :for="id" class="form-label fw-bold">
      {{ label }} <span v-if="required" class="text-danger">*</span>
    </label>

    <v-select
      :id="id"
      :placeholder="placeholder"
      :filterable="false"
      :options="options"
      :loading="loading"
      :label="labelKey"
      :reduce="valueKey"
      :onSearch="fetchOptions"
      v-model="model"
      @search="fetchOptions"
      @blur="onBlur"
    />
    
    <div v-if="showValidation && formError" class="invalid-feedback d-block">
      {{ formError }}
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

defineProps({
  id: String,
  label: String,
  modelValue: [String, Number],
  formError: String,
  required: Boolean,
  showValidation: Boolean,
  validateFunction: Function,
  placeholder: {
    type: String,
    default: 'Buscar...'
  },
  labelKey: {
    type: String,
    default: 'label'
  },
  valueKey: {
    type: Function,
    default: option => option.value
  },
  onBlur: {
    type: Function,
    default: () => {}
  },
  fetchUrl: String,
})

const emit = defineEmits(['update:modelValue'])

const model = ref(null)
const loading = ref(false)
const options = ref([])

watch(model, val => emit('update:modelValue', val))

const fetchOptions = async (search = '') => {
  loading.value = true
  try {
    const response = await axios.get(fetchUrl, { params: { search } })
    options.value = response.data
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}
</script>
