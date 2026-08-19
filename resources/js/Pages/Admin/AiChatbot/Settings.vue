<template>
  <AdminLayout>
    <Head title="AI Chatbot - Configuración" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">AI Chatbot</h1>
          <small class="text-muted">Configuración del módulo</small>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
              <div class="mb-3">
                <i class="bi bi-robot display-4 text-primary"></i>
              </div>
              <h5 class="card-title">Presets</h5>
              <p class="card-text text-muted small">
                Plantillas configurables para el chatbot. Cada preset define personalidad, mensajes y configuración.
              </p>
              <div class="mb-3">
                <span class="badge bg-primary">{{ stats.presets_count }} presets</span>
                <span class="badge bg-success">{{ stats.active_presets }} activos</span>
              </div>
              <Link href="/admin/modules/ai_chatbot/presets" class="btn btn-primary">
                <i class="bi bi-gear me-1"></i>Gestionar Presets
              </Link>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
              <div class="mb-3">
                <i class="bi bi-person-badge display-4 text-info"></i>
              </div>
              <h5 class="card-title">Personalidades</h5>
              <p class="card-text text-muted small">
                Catálogo global de personalidades. Cada una incluye hints para system prompt y configuración sugerida.
              </p>
              <div class="mb-3">
                <span class="badge bg-info">{{ stats.personalities_count }} personalidades</span>
                <span class="badge bg-success">{{ stats.active_personalities }} activas</span>
              </div>
              <Link href="/admin/modules/ai_chatbot/personalities" class="btn btn-info text-white">
                <i class="bi bi-people me-1"></i>Gestionar Personalidades
              </Link>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
              <div class="mb-3">
                <i class="bi bi-sliders display-4 text-warning"></i>
              </div>
              <h5 class="card-title">Configuración General</h5>
              <p class="card-text text-muted small">
                Ajustes generales del módulo AI Chatbot para toda la plataforma.
              </p>
              <div class="mb-3">
                <span class="badge bg-warning">Módulo activo</span>
              </div>
              <button class="btn btn-warning" disabled>
                <i class="bi bi-tools me-1"></i>Próximamente
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
              <h5 class="mb-0">Acerca del Módulo</h5>
            </div>
            <div class="card-body">
              <p class="mb-2">
                <strong>AI Chatbot</strong> es un asistente virtual con IA para atender clientes en minisites.
              </p>
              <ul class="mb-0 text-muted small">
                <li>Configura presets con diferentes personalidades y comportamientos</li>
                <li>Personaliza las sugerencias iniciales que ven los usuarios</li>
                <li>Activa o desactiva presets según las necesidades de cada negocio</li>
                <li>El chatbot aprende del contenido de productos y servicios del negocio</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const stats = page.props.stats || {
  presets_count: 0,
  personalities_count: 0,
  active_presets: 0,
  active_personalities: 0,
}
</script>
