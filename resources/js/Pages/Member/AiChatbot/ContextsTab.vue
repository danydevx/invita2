<template>
  <div class="contexts-tab">
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = null"></button>
    </div>

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Contextos Personalizados</h5>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm" @click="openImportUrlModal">
            <i class="bi bi-link-45deg me-1"></i>Importar desde URL
          </button>
          <button class="btn btn-primary btn-sm" @click="openCreateModal">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Contexto
          </button>
        </div>
      </div>
      <div class="card-body">
        <div v-if="contexts.length === 0" class="text-center py-5">
          <div class="text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3 mb-0">No hay contextos personalizados.</p>
            <small>Crea contextos para que el chatbot tenga información adicional sobre tu negocio.</small>
          </div>
        </div>

        <div v-else class="contexts-list">
          <div
            v-for="context in contexts"
            :key="context.id"
            class="context-item"
            :class="{ inactive: !context.is_active }"
          >
            <div class="context-content">
              <div class="context-header">
                <h6 class="mb-1">{{ context.title }}</h6>
                <span
                  class="badge"
                  :class="context.is_active ? 'bg-success' : 'bg-secondary'"
                >
                  {{ context.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <p class="text-muted small mb-0">
                {{ context.content?.substring(0, 150) }}{{ context.content?.length > 150 ? '...' : '' }}
              </p>
            </div>
            <div class="context-actions">
              <button class="btn btn-sm btn-outline-primary" @click="openEditModal(context)">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" @click="deleteContext(context)">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingContext ? 'Editar Contexto' : 'Nuevo Contexto' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveContext">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Título</label>
                <input
                  type="text"
                  v-model="form.title"
                  class="form-control"
                  placeholder="Ej: Información sobre envíos"
                  required
                />
              </div>
              <div class="mb-3">
                <label class="form-label">Contenido para edición</label>
                <input type="hidden" name="content_form" id="trix-content-form">
                <trix-editor
                  @trix-change="onFormTrixChange"
                  @trix-initialize="onFormTrixInit"
                  input="trix-content-form"
                  class="trix-editor-fix"
                ></trix-editor>
                <small class="text-muted d-block mt-1">
                  Editor visual. Al guardar, el texto se convertirá a formato plano para el chatbot.
                </small>
              </div>
              <div class="form-check form-switch">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="form.is_active"
                  id="context-active"
                />
                <label class="form-check-label" for="context-active">
                  Contexto activo
                </label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="importUrlModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-link-45deg me-2"></i>Importar desde URL</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="importFromUrl">
            <div class="modal-body">
              <div v-if="urlImportError" class="alert alert-danger">
                {{ urlImportError }}
              </div>
              <div class="mb-3">
                <label class="form-label">URL pública</label>
                <div class="input-group">
                  <input
                    type="url"
                    v-model="urlForm.url"
                    class="form-control"
                    placeholder="https://ejemplo.com/acerca"
                    required
                  />
                  <button
                    type="button"
                    class="btn btn-outline-primary"
                    @click="extractUrl"
                    :disabled="extracting || !urlForm.url"
                  >
                    <span v-if="extracting">
                      <i class="bi bi-hourglass-split me-1"></i>Extrayendo...
                    </span>
                    <span v-else>
                      <i class="bi bi-download me-1"></i>Importar
                    </span>
                  </button>
                </div>
                <small class="text-muted">
                  Ingresa una URL pública de tu negocio para importar su contenido.
                </small>
              </div>

              <div v-if="urlForm.extracted" class="border rounded p-3 bg-light">
                <div class="mb-3">
                  <label class="form-label">Título extraído</label>
                  <input
                    type="text"
                    v-model="urlForm.title"
                    class="form-control"
                    required
                  />
                </div>
                <div class="mb-3">
                  <label class="form-label">Contenido (editor visual)</label>
                  <input type="hidden" name="content_url" id="trix-content-url">
                  <trix-editor
                    @trix-change="onUrlTrixChange"
                    @trix-initialize="onUrlTrixInit"
                    input="trix-content-url"
                    class="trix-editor-fix"
                  ></trix-editor>
                  <small class="text-muted d-block mt-1">
                    {{ urlForm.contentLength }} caracteres para el chatbot (se guardará como texto plano)
                  </small>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="saving || !urlForm.extracted"
              >
                {{ saving ? 'Guardando...' : 'Guardar Contexto' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import 'trix'
import 'trix/dist/trix.css'

const props = defineProps({
  business: Object,
  contexts: Array,
})

const emit = defineEmits(['saved', 'deleted'])

const modalElement = ref(null)
const importUrlModalElement = ref(null)
let contextModal = null
let importUrlModal = null

const formTrixEditorEl = ref(null)
const urlTrixEditorEl = ref(null)

const editingContext = ref(null)
const saving = ref(false)
const successMessage = ref(null)
const extracting = ref(false)
const urlImportError = ref(null)

const form = reactive({
  title: '',
  content: '',
  contentForEditing: '',
  is_active: true,
})

const urlForm = reactive({
  url: '',
  title: '',
  content: '',
  contentForEditing: '',
  contentLength: 0,
  extracted: false,
})

const stripHtml = (html) => {
  if (!html) return ''
  const tmp = document.createElement('div')
  tmp.innerHTML = html
  let text = tmp.textContent || tmp.innerText || ''
  text = text.replace(/[\r\n]+/g, '\n').replace(/\n{3,}/g, '\n\n').trim()
  return text
}

const filterForEditing = (html) => {
  if (!html) return ''

  let text = html
  text = text.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
  text = text.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
  text = text.replace(/<img[^>]*>/gi, '')
  text = text.replace(/<a[^>]* href="[^"]*">/gi, '')
  text = text.replace(/<\/a>/gi, '')
  text = text.replace(/class="[^"]*"/gi, '')
  text = text.replace(/style="[^"]*"/gi, '')
  text = text.replace(/id="[^"]*"/gi, '')
  text = text.replace(/onclick="[^"]*"/gi, '')
  text = text.replace(/onload="[^"]*"/gi, '')
  text = text.replace(/data-[a-z-]+="[^"]*"/gi, '')
  text = text.replace(/<footer[^>]*>[\s\S]*?<\/footer>/gi, '')
  text = text.replace(/<header[^>]*>[\s\S]*?<\/header>/gi, '')
  text = text.replace(/<nav[^>]*>[\s\S]*?<\/nav>/gi, '')
  text = text.replace(/<aside[^>]*>[\s\S]*?<\/aside>/gi, '')

  return text
}

const onFormTrixChange = (event) => {
  const html = event.target.value
  form.contentForEditing = html
  form.content = stripHtml(html)
}

const onUrlTrixChange = (event) => {
  const html = event.target.value
  urlForm.contentForEditing = html
  urlForm.content = stripHtml(html)
  urlForm.contentLength = urlForm.content.length
}

const onFormTrixInit = (event) => {
  formTrixEditorEl.value = event.target
  if (form.contentForEditing) {
    setTimeout(() => {
      const trixEl = document.querySelector('trix-editor[input="trix-content-form"]')
      if (trixEl && trixEl.editor) {
        trixEl.editor.loadHTML(form.contentForEditing)
      }
    }, 100)
  }
}

const onUrlTrixInit = (event) => {
  urlTrixEditorEl.value = event.target
  if (urlForm.extracted && urlForm.contentForEditing) {
    setTimeout(() => {
      const trixEl = document.querySelector('trix-editor[input="trix-content-url"]')
      if (trixEl && trixEl.editor) {
        trixEl.editor.loadHTML(urlForm.contentForEditing)
      }
    }, 100)
  }
}

const setFormEditorContent = (html) => {
  const trixEl = document.querySelector('trix-editor[input="trix-content-form"]')
  if (trixEl && trixEl.editor) {
    trixEl.editor.loadHTML(html)
  }
}

const setUrlEditorContent = (html) => {
  const trixEl = document.querySelector('trix-editor[input="trix-content-url"]')
  if (trixEl && trixEl.editor) {
    trixEl.editor.loadHTML(html)
  }
}

const openCreateModal = () => {
  editingContext.value = null
  form.title = ''
  form.content = ''
  form.contentForEditing = ''
  form.is_active = true
  nextTick(() => {
    setFormEditorContent('')
    contextModal?.show()
  })
}

const openEditModal = (context) => {
  editingContext.value = context
  form.title = context.title
  form.content = stripHtml(context.content_for_editing || context.content)
  form.contentForEditing = context.content_for_editing || context.content || ''
  form.is_active = context.is_active

  contextModal?.show()

  setTimeout(() => {
    setFormEditorContent(form.contentForEditing)
  }, 200)
}

const openImportUrlModal = () => {
  urlForm.url = ''
  urlForm.title = ''
  urlForm.content = ''
  urlForm.contentForEditing = ''
  urlForm.contentLength = 0
  urlForm.extracted = false
  urlImportError.value = null

  importUrlModal?.show()

  setTimeout(() => {
    setUrlEditorContent('')
  }, 200)
}

const closeModal = () => {
  contextModal?.hide()
}

const closeImportUrlModal = () => {
  importUrlModal?.hide()
}

const extractUrl = () => {
  if (!urlForm.url) return

  extracting.value = true
  urlImportError.value = null

  router.post(`/member/listings/${props.business.id}/ai-chatbot/extract-url`, { url: urlForm.url }, {
    preserveScroll: true,
    onSuccess: (page) => {
      if (page.props.flash?.extractResult) {
        const result = page.props.flash.extractResult
        if (result.success) {
          urlForm.title = result.title || 'Contenido importado'

          const filteredHtml = filterForEditing(result.content)
          urlForm.contentForEditing = filteredHtml
          urlForm.content = stripHtml(filteredHtml)
          urlForm.contentLength = urlForm.content.length
          urlForm.extracted = true

          setTimeout(() => {
            setUrlEditorContent(filteredHtml)
          }, 200)
        } else {
          urlImportError.value = result.error || 'Error al extraer contenido de la URL'
        }
      }
      extracting.value = false
    },
    onError: (errors) => {
      urlImportError.value = Object.values(errors).join('\n') || 'Error al extraer contenido'
      extracting.value = false
    },
  })
}

const importFromUrl = () => {
  saving.value = true
  successMessage.value = null

  router.post(
    `/member/listings/${props.business.id}/ai-chatbot/contexts`,
    {
      title: urlForm.title,
      content: urlForm.content,
      content_for_editing: urlForm.contentForEditing,
      is_active: true,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        closeImportUrlModal()
        successMessage.value = 'Contexto importado correctamente.'
        emit('saved')
      },
      onFinish: () => {
        saving.value = false
      },
    }
  )
}

const saveContext = () => {
  saving.value = true
  successMessage.value = null

  const data = {
    title: form.title,
    content: form.content,
    content_for_editing: form.contentForEditing,
    is_active: form.is_active,
  }

  if (editingContext.value) {
    router.put(
      `/member/listings/${props.business.id}/ai-chatbot/contexts/${editingContext.value.id}`,
      data,
      {
        preserveScroll: true,
        onSuccess: () => {
          closeModal()
          successMessage.value = 'Contexto actualizado correctamente.'
          emit('saved')
        },
        onFinish: () => {
          saving.value = false
        },
      }
    )
  } else {
    router.post(`/member/listings/${props.business.id}/ai-chatbot/contexts`, data, {
      preserveScroll: true,
      onSuccess: () => {
        closeModal()
        successMessage.value = 'Contexto creado correctamente.'
        emit('saved')
      },
      onFinish: () => {
        saving.value = false
      },
    })
  }
}

const deleteContext = (context) => {
  if (confirm(`¿Eliminar el contexto "${context.title}"?`)) {
    router.delete(
      `/member/listings/${props.business.id}/ai-chatbot/contexts/${context.id}`,
      {
        preserveScroll: true,
        onSuccess: () => {
          successMessage.value = 'Contexto eliminado correctamente.'
          emit('deleted')
        },
      }
    )
  }
}

onMounted(() => {
  contextModal = new Modal(modalElement.value)
  importUrlModal = new Modal(importUrlModalElement.value)

  modalElement.value?.addEventListener('shown.bs.modal', () => {
    if (editingContext.value && form.contentForEditing) {
      setFormEditorContent(form.contentForEditing)
    }
  })

  importUrlModalElement.value?.addEventListener('shown.bs.modal', () => {
    if (urlForm.extracted && urlForm.contentForEditing) {
      setUrlEditorContent(urlForm.contentForEditing)
    }
  })
})
</script>

<style lang="less" scoped>
.contexts-tab {
  .card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 16px 20px;
  }

  .contexts-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .context-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;

    &.inactive {
      opacity: 0.6;
      background: #fff;
    }

    .context-content {
      flex: 1;
      min-width: 0;

      .context-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;

        h6 {
          margin: 0;
          color: #212529;
        }
      }
    }

    .context-actions {
      display: flex;
      gap: 8px;
      margin-left: 16px;
    }
  }

  .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
  }

  .trix-editor-fix {
    min-height: 200px;
    background: #fff;

    :deep(trix-toolbar) {
      .trix-button-group {
        margin-bottom: 8px;
      }
    }

    :deep(.trix-content) {
      min-height: 180px;
      padding: 12px;
    }
  }
}
</style>
