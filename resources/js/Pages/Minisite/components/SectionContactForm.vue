<template>
  <section class="section-contact">
    <div class="section-contact__inner">
      <h2 v-if="title" class="section-contact__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-contact__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-contact__description-text">{{ description }}</p>

      <div v-if="!form" class="text-muted text-center py-4">
        Formulario no disponible.
      </div>

      <form v-else @submit.prevent="submitForm" class="section-contact__form">
        <div v-for="field in form.fields" :key="field.name" class="section-contact__field">
          <label :for="field.name" class="section-contact__label">
            {{ field.label }}
            <span v-if="field.required" class="text-danger">*</span>
          </label>

          <input
            v-if="field.type === 'text' || field.type === 'email' || field.type === 'tel'"
            :id="field.name"
            :type="field.type"
            :name="field.name"
            :placeholder="field.placeholder"
            :required="field.required"
            class="form-control"
            v-model="formData[field.name]"
          />

          <textarea
            v-else-if="field.type === 'textarea'"
            :id="field.name"
            :name="field.name"
            :placeholder="field.placeholder"
            :required="field.required"
            class="form-control"
            rows="3"
            v-model="formData[field.name]"
          ></textarea>

          <select
            v-else-if="field.type === 'select'"
            :id="field.name"
            :name="field.name"
            :required="field.required"
            class="form-control"
            v-model="formData[field.name]"
          >
            <option value="">{{ field.placeholder || 'Selecciona...' }}</option>
            <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="sending">
          {{ sending ? 'Enviando...' : 'Enviar' }}
        </button>

        <div v-if="successMessage" class="alert alert-success mt-3">
          {{ successMessage }}
        </div>
      </form>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'

const props = defineProps({
  title: String,
  subtitle: String,
  form: Object,
  config: Object,
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
})

const sending = ref(false)
const successMessage = ref('')
const formData = reactive({})

const submitForm = async () => {
  if (!props.form) return

  sending.value = true
  successMessage.value = ''

  try {
    // TODO: implement form submission
    await new Promise(resolve => setTimeout(resolve, 1000))
    successMessage.value = 'Mensaje enviado correctamente.'
    Object.keys(formData).forEach(key => formData[key] = '')
  } catch (error) {
    console.error('Form submission error:', error)
  } finally {
    sending.value = false
  }
}
</script>

<style lang="less">
.section-contact {
  padding: 48px 16px;
  background: #fff;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }

  &__title {
    font-weight: 700;
    margin: 0 0 8px;
    text-align: center;
    color: #212529;
  }

  &__subtitle {
    font-weight: 600;
    color: #495057;
    text-align: center;
    margin: 0 0 16px;
  }

  &__description-text {
    font-size: 1rem;
    color: #6c757d;
    text-align: center;
    margin: 0 0 16px;
  }

  &__form {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  &__field {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  &__label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #212529;
  }
}
</style>
