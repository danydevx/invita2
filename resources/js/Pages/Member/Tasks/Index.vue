<template>
  <MemberLayout>
    <Head :title="`Tareas - ${business?.name || ''}`" />

    <PageHeader
      title="Tareas"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button class="btn btn-primary btn-sm" @click="openCreateModal">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Tarea
        </button>
      </template>
    </PageHeader>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <button class="nav-link" :class="{ active: activeTab === 'board' }" @click="activeTab = 'board'">
          <i class="bi bi-kanban me-1"></i>Tablero
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" :class="{ active: activeTab === 'history' }" @click="activeTab = 'history'">
          <i class="bi bi-clock-history me-1"></i>Historial ({{ completedTasks.length }})
        </button>
      </li>
    </ul>

    <div v-if="activeTab === 'board'">
      <div class="kanban-board">
        <div class="kanban-column">
          <div class="kanban-column-header bg-secondary text-white">
            <i class="bi bi-list-task me-2"></i>Por Hacer
            <span class="badge bg-light text-dark ms-2">{{ columns.todo.length }}</span>
          </div>
          <draggable
            v-model="columns.todo"
            group="tasks"
            item-key="id"
            class="kanban-cards"
            :animation="200"
            @end="onDragEnd"
          >
            <template #item="{ element }">
              <div class="kanban-card" @click="openEditModal(element)">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 class="mb-1 text-truncate">{{ element.title }}</h6>
                  <button class="btn btn-sm text-danger p-0" @click.stop="deleteTask(element)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <p v-if="element.description" class="text-muted small mb-2">{{ element.description.substring(0, 60) }}{{ element.description.length > 60 ? '...' : '' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">#{{ element.id }}</small>
                  <span class="badge" :class="element.status === 'todo' ? 'bg-secondary' : 'bg-info'">
                    {{ element.status === 'todo' ? 'Pendiente' : 'En progreso' }}
                  </span>
                </div>
              </div>
            </template>
          </draggable>
        </div>

        <div class="kanban-column">
          <div class="kanban-column-header bg-primary text-white">
            <i class="bi bi-arrow-repeat me-2"></i>En Progreso
            <span class="badge bg-light text-primary ms-2">{{ columns.in_progress.length }}</span>
          </div>
          <draggable
            v-model="columns.in_progress"
            group="tasks"
            item-key="id"
            class="kanban-cards"
            :animation="200"
            @end="onDragEnd"
          >
            <template #item="{ element }">
              <div class="kanban-card in-progress" @click="openEditModal(element)">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 class="mb-1 text-truncate">{{ element.title }}</h6>
                  <button class="btn btn-sm text-danger p-0" @click.stop="deleteTask(element)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <p v-if="element.description" class="text-muted small mb-2">{{ element.description.substring(0, 60) }}{{ element.description.length > 60 ? '...' : '' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">#{{ element.id }}</small>
                  <span class="badge bg-primary">En progreso</span>
                </div>
              </div>
            </template>
          </draggable>
        </div>

        <div class="kanban-column">
          <div class="kanban-column-header bg-success text-white">
            <i class="bi bi-check-circle me-2"></i>Hecho
            <span class="badge bg-light text-success ms-2">{{ columns.done.length }}</span>
          </div>
          <draggable
            v-model="columns.done"
            group="tasks"
            item-key="id"
            class="kanban-cards"
            :animation="200"
            @end="onDragEnd"
          >
            <template #item="{ element }">
              <div class="kanban-card done" @click="openEditModal(element)">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 class="mb-1 text-truncate text-muted">{{ element.title }}</h6>
                  <button class="btn btn-sm text-danger p-0" @click.stop="deleteTask(element)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <p v-if="element.description" class="text-muted small mb-2">{{ element.description.substring(0, 60) }}{{ element.description.length > 60 ? '...' : '' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">#{{ element.id }}</small>
                  <span class="badge bg-success">Completado</span>
                </div>
              </div>
            </template>
          </draggable>
        </div>
      </div>
    </div>

    <div v-else class="card">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Tareas Completadas</h5>
      </div>
      <div class="card-body p-0">
        <div v-if="completedTasks.length === 0" class="text-center py-5 text-muted">
          <i class="bi bi-inbox fs-1 d-block mb-2"></i>
          No hay tareas completadas
        </div>
        <table v-else class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Tarea</th>
              <th>Descripcion</th>
              <th>Completada el</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in completedTasks" :key="task.id">
              <td>
                <strong class="text-muted">{{ task.title }}</strong>
              </td>
              <td>
                <span class="text-muted small">{{ task.description || '-' }}</span>
              </td>
              <td>
                <small class="text-muted">{{ formatDate(task.completed_at) }}</small>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-danger" @click="deleteTask(task)">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingTask ? 'Editar Tarea' : 'Nueva Tarea' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitTask">
            <div class="modal-body">
              <FieldText
                id="task-title"
                label="Titulo"
                v-model="form.title"
                required
              />
              <FieldTextarea
                id="task-description"
                label="Descripcion"
                v-model="form.description"
                :rows="3"
              />
              <FieldSelect
                id="task-status"
                label="Estado"
                v-model="form.status"
              >
                <option value="todo">Por hacer</option>
                <option value="in_progress">En progreso</option>
                <option value="done">Hecho</option>
              </FieldSelect>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, onMounted, nextTick } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import draggable from 'vuedraggable'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'

const page = usePage()
const business = computed(() => page.props.business)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Tareas', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Tareas', active: true },
  ]
})

const props = defineProps({
  tasks: Object,
  completedTasks: { type: Array, default: () => [] },
})

const activeTab = ref('board')

const columns = reactive({
  todo: [...(props.tasks?.todo || [])],
  in_progress: [...(props.tasks?.in_progress || [])],
  done: [...(props.tasks?.done || [])],
})

const completedTasks = computed(() => props.completedTasks || [])

const modalElement = ref(null)
let taskModal = null
const editingTask = ref(null)
const sending = ref(false)

const form = reactive({
  title: '',
  description: '',
  status: 'todo',
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const openCreateModal = () => {
  editingTask.value = null
  form.title = ''
  form.description = ''
  form.status = 'todo'
  nextTick(() => taskModal.show())
}

const openEditModal = (task) => {
  editingTask.value = task
  form.title = task.title
  form.description = task.description || ''
  form.status = task.status
  nextTick(() => taskModal.show())
}

const closeModal = () => {
  taskModal.hide()
}

const submitTask = () => {
  sending.value = true

  if (editingTask.value) {
    router.put(`/member/businesses/${business.value.id}/tasks/${editingTask.value.id}`, form, {
      preserveScroll: true,
      onSuccess: () => {
        sending.value = false
        closeModal()
        refreshTasks()
      },
      onError: () => {
        sending.value = false
      },
    })
  } else {
    router.post(`/member/businesses/${business.value.id}/tasks`, form, {
      preserveScroll: true,
      onSuccess: () => {
        sending.value = false
        closeModal()
        refreshTasks()
      },
      onError: () => {
        sending.value = false
      },
    })
  }
}

const deleteTask = (task) => {
  if (confirm(`Eliminar la tarea "${task.title}"?`)) {
    router.delete(`/member/businesses/${business.value.id}/tasks/${task.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        refreshTasks()
      },
    })
  }
}

const refreshTasks = () => {
  router.get(`/member/businesses/${business.value.id}/tasks`, {}, {
    preserveState: true,
    onSuccess: (page) => {
      columns.todo = [...(page.props.tasks?.todo || [])]
      columns.in_progress = [...(page.props.tasks?.in_progress || [])]
      columns.done = [...(page.props.tasks?.done || [])]
    },
  })
}

const onDragEnd = () => {
  const items = []

  columns.todo.forEach((task, index) => {
    items.push({ id: task.id, status: 'todo', sort_order: index })
  })
  columns.in_progress.forEach((task, index) => {
    items.push({ id: task.id, status: 'in_progress', sort_order: index })
  })
  columns.done.forEach((task, index) => {
    items.push({ id: task.id, status: 'done', sort_order: index })
  })

  router.post(`/member/businesses/${business.value.id}/tasks/reorder`, {
    items,
  }, {
    preserveScroll: true,
  })
}

onMounted(() => {
  taskModal = new Modal(modalElement.value)
})
</script>

<style scoped>
.kanban-board {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  padding-bottom: 1rem;
}

.kanban-column {
  flex: 1;
  min-width: 280px;
  max-width: 350px;
  background: #f8f9fa;
  border-radius: 0.5rem;
  min-height: 500px;
}

.kanban-column-header {
  padding: 0.75rem 1rem;
  border-radius: 0.5rem 0.5rem 0 0;
  font-weight: 600;
  display: flex;
  align-items: center;
}

.kanban-cards {
  padding: 0.75rem;
  min-height: 450px;
}

.kanban-card {
  background: white;
  border-radius: 0.375rem;
  padding: 0.75rem;
  margin-bottom: 0.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: all 0.2s;
  border-left: 3px solid transparent;
}

.kanban-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transform: translateY(-2px);
}

.kanban-card.in-progress {
  border-left-color: #0d6efd;
}

.kanban-card.done {
  border-left-color: #198754;
  background: #f8fff8;
}

.kanban-card.done h6 {
  text-decoration: line-through;
  color: #6c757d !important;
}

.nav-tabs .nav-link {
  color: #495057;
  cursor: pointer;
}

.nav-tabs .nav-link.active {
  color: #0d6efd;
  font-weight: 500;
  border-bottom: 2px solid #0d6efd;
}

.table {
  font-size: 0.9rem;
}

.table td {
  vertical-align: middle;
}
</style>
