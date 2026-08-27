<template>
  <MemberLayout>
    <Head :title="`Editar ${vcard?.name || 'Tarjeta'} - ${listing?.name || ''}`" />

    <PageHeader
      :title="vcard?.name || 'Editar Tarjeta'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/vcards`"
    >
      <template #actions>
        <a
          :href="`/member/listings/${listing?.id}/vcards/${vcard?.id}/seo`"
          class="btn btn-outline-dark btn-sm"
        >
          <i class="bi bi-graph-up me-1"></i>
          SEO
        </a>
        <button
          class="btn btn-outline-secondary btn-sm"
          @click="copyLink"
        >
          <i class="bi bi-link me-1"></i>
          Copiar Enlace
        </button>
        <button
          class="btn btn-outline-primary btn-sm"
          @click="downloadVCard"
        >
          <i class="bi bi-download me-1"></i>
          .vcf
        </button>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'card' }"
              @click="activeTab = 'card'"
              type="button"
            >
              <i class="bi bi-card-text me-1"></i>
              Tarjeta
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'design' }"
              @click="activeTab = 'design'"
              type="button"
            >
              <i class="bi bi-palette me-1"></i>
              Diseño
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'information' }"
              @click="activeTab = 'information'"
              type="button"
            >
              <i class="bi bi-person me-1"></i>
              Información
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'contacts' }"
              @click="activeTab = 'contacts'"
              type="button"
            >
              <i class="bi bi-telephone me-1"></i>
              Contactos
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'fields' }"
              @click="activeTab = 'fields'"
              type="button"
            >
              <i class="bi bi-list-ul me-1"></i>
              Campos
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'seguimiento' }"
              @click="activeTab = 'seguimiento'"
              type="button"
            >
              <i class="bi bi-graph-up me-1"></i>
              Seguimiento
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'secciones' }"
              @click="activeTab = 'secciones'"
              type="button"
            >
              <i class="bi bi-layout-sidebar me-1"></i>
              Secciones
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-12 col-lg-8">
            <form @submit.prevent="submit">
          <div v-show="activeTab === 'card'">
            <div class="row g-3">
              <div class="col-12 col-md-8">
                <FieldText
                  id="vcard-name"
                  label="Nombre de la tarjeta"
                  v-model="form.name"
                  :formError="errors.name"
                  placeholder="Daniel López"
                  required
                />
              </div>
              <div class="col-12 col-md-4">
                <FieldText
                  id="vcard-slug"
                  label="Slug"
                  v-model="form.slug"
                  :formError="errors.slug"
                  placeholder="daniel-lopez"
                  hint="URL: /v/slug"
                />
              </div>
              <div class="col-12 col-md-6">
                <FieldSelect
                  id="vcard-type"
                  label="Tipo de tarjeta"
                  v-model="form.type"
                  :formError="errors.type"
                >
                  <option value="single">Individual</option>
                  <option value="team">Equipo</option>
                </FieldSelect>
              </div>
              <div class="col-12 col-md-6" v-if="teams.length > 0">
                <FieldSelect
                  id="vcard-team"
                  label="Equipo"
                  v-model="form.vcard_team_id"
                  :formError="errors.vcard_team_id"
                >
                  <option value="">Sin equipo</option>
                  <option v-for="team in teams" :key="team.id" :value="team.id">
                    {{ team.name }}
                  </option>
                </FieldSelect>
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="vcard-active"
                  label="Tarjeta activa"
                  v-model="form.active"
                />
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="vcard-ai-chat"
                  label="Chat de IA"
                  v-model="form.ai_chat_enabled"
                  hint="Muestra el chatbot de IA en la tarjeta digital"
                />
              </div>
              <div class="col-12 mt-4">
                <button
                  type="button"
                  class="btn btn-outline-danger btn-sm"
                  @click="deleteCard"
                >
                  <i class="bi bi-trash me-1"></i>
                  Eliminar tarjeta
                </button>
              </div>
            </div>

            <div class="mt-4 p-3 bg-light rounded">
              <h6 class="mb-3">Vista previa del QR</h6>
              <QRCode :value="vcardPublicUrl" :size="150" />
              <p class="small text-muted mt-2 mb-0">{{ vcardPublicUrl }}</p>
            </div>
          </div>

          <div v-show="activeTab === 'design'">
            <div class="row g-4">
              <div class="col-12">
                <div class="card border">
                  <div class="card-header bg-light">
                    <h6 class="mb-0">Estilo</h6>
                  </div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-12">
                        <label class="form-label small text-muted mb-2">Diseño</label>
                        <div class="row g-2">
                          <div v-for="design in designs" :key="design.value" class="col-4 col-md-2">
                            <div
                              class="design-option border rounded p-2 text-center cursor-pointer"
                              :class="{ 'border-primary': form.design === design.value, 'bg-light': form.design !== design.value, 'border-2': form.design === design.value }"
                              @click="form.design = design.value"
                            >
                              <div class="design-preview mb-2" :style="{ backgroundColor: form.primary_color + '20' }">
                                <div class="design-icon" :style="{ color: form.primary_color }">
                                  <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                                </div>
                              </div>
                              <small class="fw-medium">{{ design.label }}</small>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label small text-muted mb-2">Forma de bordes</label>
                        <div class="d-flex gap-3">
                          <div
                            class="shape-option cursor-pointer p-3 border rounded text-center flex-fill"
                            :class="{ 'border-primary bg-light': form.shape === 'square', 'bg-white': form.shape !== 'square', 'border-2': form.shape === 'square' }"
                            @click="form.shape = 'square'"
                          >
                            <div class="shape-preview shape-square mx-auto mb-2"></div>
                            <small class="fw-medium">Cuadrado</small>
                          </div>
                          <div
                            class="shape-option cursor-pointer p-3 border rounded text-center flex-fill"
                            :class="{ 'border-primary bg-light': form.shape === 'rounded', 'bg-white': form.shape !== 'rounded', 'border-2': form.shape === 'rounded' }"
                            @click="form.shape = 'rounded'"
                          >
                            <div class="shape-preview shape-rounded mx-auto mb-2"></div>
                            <small class="fw-medium">Redondeado</small>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label small text-muted mb-2">Color principal</label>
                        <div class="d-flex gap-2 flex-wrap">
                          <div
                            v-for="color in colors"
                            :key="color"
                            class="color-swatch rounded cursor-pointer"
                            :class="{ 'ring-2 ring-primary': form.primary_color === color }"
                            :style="{ backgroundColor: color }"
                            @click="form.primary_color = color"
                          />
                        </div>
                      </div>

                      <div class="col-12 col-md-6">
                        <FieldSelect
                          id="vcard-font"
                          label="Fuente"
                          v-model="form.font"
                          :formError="errors.font"
                        >
                          <option v-for="font in fonts" :key="font" :value="font">{{ font }}</option>
                        </FieldSelect>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-lg-6">
                <div class="card border h-100">
                  <div class="card-header bg-light">
                    <h6 class="mb-0">Imágenes</h6>
                  </div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-12">
                        <FieldImage
                          id="vcard-profile-photo"
                          label="Foto de perfil"
                          v-model="profileFile"
                          :initialPreview="profilePhotoUrl"
                          :maxSizeMb="5"
                        />
                      </div>
                      <div class="col-12">
                        <FieldImage
                          id="vcard-logo"
                          label="Logo"
                          v-model="logoFile"
                          :initialPreview="form.logo ? `/storage/${form.logo}` : null"
                          :maxSizeMb="5"
                        />
                      </div>
                      <div class="col-12">
                        <FieldImage
                          id="vcard-badge"
                          label="Badge"
                          v-model="badgeFile"
                          :initialPreview="form.badge ? `/storage/${form.badge}` : null"
                          :maxSizeMb="5"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-lg-6">
                <div class="card border h-100">
                  <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Fondo del Hero</h6>
                  </div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-12">
                        <label class="form-label small text-muted mb-2">Tipo de fondo</label>
                        <div class="d-flex gap-2">
                          <div
                            v-for="bt in [{v:'solid',i:'bi-square',l:'Sólido'},{v:'gradient',i:'bi-gradient',l:'Degradado'},{v:'pattern',i:'bi-grid-3x3',l:'Patrón'}]"
                            :key="bt.v"
                            class="bg-type-option border rounded p-2 text-center cursor-pointer flex-fill"
                            :class="{ 'border-primary bg-light': form.background_type === bt.v, 'bg-white': form.background_type !== bt.v, 'border-2': form.background_type === bt.v }"
                            @click="form.background_type = bt.v"
                          >
                            <i :class="bt.i + ' d-block mb-1'"></i>
                            <small class="fw-medium">{{ bt.l }}</small>
                          </div>
                        </div>
                      </div>

                      <div class="col-12" v-if="form.background_type === 'gradient'">
                        <label class="form-label small text-muted mb-2">Dirección del degradado</label>
                        <div class="d-flex gap-2">
                          <div
                            v-for="dir in gradientDirections"
                            :key="dir.value"
                            class="gradient-dir-option border rounded p-2 text-center cursor-pointer flex-fill"
                            :class="{ 'border-primary bg-light': form.gradient_direction === dir.value, 'bg-white': form.gradient_direction !== dir.value, 'border-2': form.gradient_direction === dir.value }"
                            @click="form.gradient_direction = dir.value"
                          >
                            <div class="gradient-preview mx-auto mb-1" :style="{ background: `linear-gradient(${dir.value}, ${form.primary_color}, #ccc)` }"></div>
                            <small class="fw-medium">{{ dir.label }}</small>
                          </div>
                        </div>
                      </div>

                      <div class="col-12" v-if="form.background_type === 'pattern'">
                        <label class="form-label small text-muted mb-2">Patrón</label>
                        <div class="d-flex gap-2 flex-wrap">
                          <div
                            v-for="p in patterns"
                            :key="p.value"
                            class="pattern-option border rounded p-2 text-center cursor-pointer"
                            :class="{ 'border-primary bg-light': form.pattern_key === p.value, 'bg-white': form.pattern_key !== p.value, 'border-2': form.pattern_key === p.value }"
                            @click="form.pattern_key = p.value"
                          >
                            <div class="pattern-preview mx-auto mb-1" :style="{ backgroundImage: p.preview }"></div>
                            <small class="fw-medium">{{ p.label }}</small>
                          </div>
                        </div>
                      </div>

                      <div class="col-12">
                        <FieldImage
                          id="vcard-hero-background-image"
                          label="Imagen de fondo"
                          v-model="heroBackgroundImageFile"
                          :initialPreview="heroBackgroundImageUrl"
                          :maxSizeMb="5"
                        />
                      </div>

                      <div class="col-12" v-if="form.hero_background_image || heroBackgroundImageFile">
                        <label class="form-label small text-muted mb-2">Opacidad ({{ form.hero_image_alpha }}%)</label>
                        <input
                          type="range"
                          class="form-range"
                          min="1"
                          max="100"
                          v-model.number="form.hero_image_alpha"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-lg-6">
                <div class="card border h-100">
                  <div class="card-header bg-light">
                    <h6 class="mb-0">Fondo de página</h6>
                  </div>
                  <div class="card-body">
                    <div class="row g-3">
                      <div class="col-12">
                        <label class="form-label small text-muted mb-2">Tipo de fondo</label>
                        <div class="d-flex gap-2">
                          <div
                            v-for="bt in [{v:'solid',i:'bi-square',l:'Sólido'},{v:'gradient',i:'bi-gradient',l:'Degradado'},{v:'pattern',i:'bi-grid-3x3',l:'Patrón'}]"
                            :key="bt.v"
                            class="bg-type-option border rounded p-2 text-center cursor-pointer flex-fill"
                            :class="{ 'border-primary bg-light': form.body_background_type === bt.v, 'bg-white': form.body_background_type !== bt.v, 'border-2': form.body_background_type === bt.v }"
                            @click="form.body_background_type = bt.v"
                          >
                            <i :class="bt.i + ' d-block mb-1'"></i>
                            <small class="fw-medium">{{ bt.l }}</small>
                          </div>
                        </div>
                      </div>

                      <div class="col-12">
                        <label class="form-label small text-muted mb-2">Color de fondo</label>
                        <div class="d-flex gap-2 align-items-center">
                          <input
                            type="color"
                            class="form-control form-control-color"
                            v-model="form.body_primary_color"
                            title="Elegir color"
                            style="width: 3rem; height: 2.5rem;"
                          >
                          <input
                            type="text"
                            class="form-control"
                            v-model="form.body_primary_color"
                            placeholder="#ffffff"
                          >
                        </div>
                      </div>

                      <div class="col-12" v-if="form.body_background_type === 'gradient'">
                        <label class="form-label small text-muted mb-2">Dirección del degradado</label>
                        <div class="d-flex gap-2">
                          <div
                            v-for="dir in gradientDirections"
                            :key="dir.value"
                            class="gradient-dir-option border rounded p-2 text-center cursor-pointer flex-fill"
                            :class="{ 'border-primary bg-light': form.body_gradient_direction === dir.value, 'bg-white': form.body_gradient_direction !== dir.value, 'border-2': form.body_gradient_direction === dir.value }"
                            @click="form.body_gradient_direction = dir.value"
                          >
                            <div class="gradient-preview mx-auto mb-1" :style="{ background: `linear-gradient(${dir.value}, ${form.body_primary_color}, #ccc)` }"></div>
                            <small class="fw-medium">{{ dir.label }}</small>
                          </div>
                        </div>
                      </div>

                      <div class="col-12" v-if="form.body_background_type === 'pattern'">
                        <label class="form-label small text-muted mb-2">Patrón</label>
                        <div class="d-flex gap-2 flex-wrap">
                          <div
                            v-for="p in patterns"
                            :key="p.value"
                            class="pattern-option border rounded p-2 text-center cursor-pointer"
                            :class="{ 'border-primary bg-light': form.body_pattern_key === p.value, 'bg-white': form.body_pattern_key !== p.value, 'border-2': form.body_pattern_key === p.value }"
                            @click="form.body_pattern_key = p.value"
                          >
                            <div class="pattern-preview mx-auto mb-1" :style="{ backgroundImage: p.preview }"></div>
                            <small class="fw-medium">{{ p.label }}</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-show="activeTab === 'information'">
            <fieldset class="mb-4">
              <legend class="h6">Información personal</legend>
              <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-prefix"
                    label="Prefijo"
                    v-model="form.prefix"
                    placeholder="Lic., Ing., Dr."
                  />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-first-name"
                    label="Nombre"
                    v-model="form.first_name"
                    placeholder="Su nombre"
                  />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-middle-name"
                    label="Segundo nombre"
                    v-model="form.middle_name"
                    placeholder="Segundo nombre"
                  />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-last-name"
                    label="Apellidos"
                    v-model="form.last_name"
                    placeholder="Sus apellidos"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-preferred-name"
                    label="Nombre preferido"
                    v-model="form.preferred_name"
                    placeholder="Ejemplo: Dani"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="vcard-pronouns"
                    label="Pronombres"
                    v-model="form.pronouns"
                  >
                    <option value="">Seleccionar...</option>
                    <option v-for="p in pronouns" :key="p.value" :value="p.value">
                      {{ p.label }}
                    </option>
                  </FieldSelect>
                </div>
                <div class="col-12">
                  <FieldText
                    id="vcard-accreditations"
                    label="Acreditaciones"
                    v-model="form.accreditations"
                    placeholder="Ejemplo: MBA, PMP, CCNA"
                  />
                </div>
              </div>
            </fieldset>

            <fieldset class="mb-4">
              <legend class="h6">Información profesional</legend>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-title"
                    label="Puesto"
                    v-model="form.title"
                    placeholder="Ejemplo: Diseñador web"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-department"
                    label="Departamento"
                    v-model="form.department"
                    placeholder="Ejemplo: Marketing"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-company"
                    label="Empresa"
                    v-model="form.company"
                    placeholder="Nombre de la empresa"
                  />
                </div>
                <div class="col-12">
                  <FieldTextarea
                    id="vcard-headline"
                    label="Descripción"
                    v-model="form.headline"
                    placeholder="Breve descripción profesional"
                    :rows="3"
                  />
                </div>
              </div>
            </fieldset>
          </div>

          <div v-show="activeTab === 'contacts'">
            <div class="mb-3">
              <button
                type="button"
                class="btn btn-primary btn-sm"
                @click="showContactModal = true"
              >
                <i class="bi bi-plus-lg me-1"></i>
                Agregar contacto
              </button>
            </div>

            <draggable
              v-if="localContacts.length > 0"
              v-model="localContacts"
              item-key="id"
              handle=".contact-drag-handle"
              ghost-class="sortable-ghost"
              @end="onContactsDragEnd"
              class="list-group"
            >
              <template #item="{ element: contact }">
                <div
                  class="list-group-item d-flex justify-content-between align-items-center"
                >
                  <div class="d-flex align-items-center">
                    <div class="contact-drag-handle text-muted me-2" style="cursor: grab;">
                      <i class="bi bi-grip-vertical"></i>
                    </div>
                    <i :class="getContactIcon(contact.type)" class="me-2"></i>
                    <strong>{{ contact.value }}</strong>
                    <span class="text-muted ms-2">{{ contact.contact_type }}</span>
                  </div>
                  <div>
                    <button
                      class="btn btn-sm btn-outline-secondary me-1"
                      @click="editContact(contact)"
                    >
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="deleteContact(contact.id)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </template>
            </draggable>
            <div v-else class="text-muted text-center py-4">
              No hay contactos agregados
            </div>

            <div v-if="showContactModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="showContactModal = false">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ editingContact ? 'Editar Contacto' : 'Agregar Contacto' }}</h5>
                    <button type="button" class="btn-close" @click="closeContactModal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <FieldSelect
                        id="contact-type"
                        label="Tipo"
                        v-model="contactForm.type"
                      >
                        <option v-for="ct in contactTypes" :key="ct.value" :value="ct.value">
                          {{ ct.label }}
                        </option>
                      </FieldSelect>
                      <div v-if="contactErrors.type" class="text-danger small mt-1">{{ contactErrors.type }}</div>
                    </div>
                    <div class="mb-3" v-if="contactForm.type === 'phone' || contactForm.type === 'whatsapp'">
                      <label class="form-label">Código país</label>
                      <select v-model="contactForm.country_code" class="form-select" :class="{ 'is-invalid': contactErrors.country_code }">
                        <option value="+52">🇲🇽 México (+52)</option>
                        <option value="+1">🇺🇸 Estados Unidos (+1)</option>
                        <option value="+1">🇨🇦 Canadá (+1)</option>
                        <option value="+54">🇦🇷 Argentina (+54)</option>
                        <option value="+55">🇧🇷 Brasil (+55)</option>
                        <option value="+56">🇨🇱 Chile (+56)</option>
                        <option value="+57">🇨🇴 Colombia (+57)</option>
                        <option value="+593">🇪🇨 Ecuador (+593)</option>
                        <option value="+33">🇫🇷 Francia (+33)</option>
                        <option value="+49">🇩🇪 Alemania (+49)</option>
                        <option value="+51">🇵🇪 Perú (+51)</option>
                        <option value="+34">🇪🇸 España (+34)</option>
                        <option value="+44">🇬🇧 Reino Unido (+44)</option>
                        <option value="+39">🇮🇹 Italia (+39)</option>
                        <option value="+81">🇯🇵 Japón (+81)</option>
                        <option value="+47">🇳🇴 Noruega (+47)</option>
                        <option value="+48">🇵🇱 Polonia (+48)</option>
                        <option value="+40">🇷🇴 Rumania (+40)</option>
                        <option value="+7">🇷🇺 Rusia (+7)</option>
                        <option value="+46">🇸🇪 Suecia (+46)</option>
                        <option value="+41">🇨🇭 Suiza (+41)</option>
                        <option value="+90">🇹🇷 Turquía (+90)</option>
                        <option value="+380">🇺🇦 Ucrania (+380)</option>
                        <option value="+58">🇻🇪 Venezuela (+58)</option>
                      </select>
                      <div v-if="contactErrors.country_code" class="text-danger small mt-1">{{ contactErrors.country_code }}</div>
                    </div>
                    <div class="mb-3">
                      <FieldText
                        id="contact-value"
                        label="Valor"
                        v-model="contactForm.value"
                        :placeholder="contactForm.type === 'email' ? 'email@ejemplo.com' : 'Número'"
                        :type="contactForm.type === 'email' ? 'email' : 'tel'"
                        :formError="contactErrors.value"
                      />
                    </div>
                    <div class="mb-3" v-if="contactForm.type === 'phone'">
                      <FieldText
                        id="contact-extension"
                        label="Extensión"
                        v-model="contactForm.extension"
                        placeholder="123"
                      />
                    </div>
                    <div class="mb-3">
                      <FieldSelect
                        id="contact-contact-type"
                        label="Categoría"
                        v-model="contactForm.contact_type"
                      >
                        <option v-for="st in contactSubtypes" :key="st.value" :value="st.value">
                          {{ st.label }}
                        </option>
                      </FieldSelect>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showContactModal = false">Cancelar</button>
                    <button type="button" class="btn btn-primary" @click="saveContact">{{ editingContact ? 'Actualizar' : 'Guardar' }}</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-show="activeTab === 'fields'">
            <div class="mb-3">
              <button
                type="button"
                class="btn btn-primary btn-sm"
                @click="showFieldModal = true"
              >
                <i class="bi bi-plus-lg me-1"></i>
                Agregar campo
              </button>
            </div>

            <draggable
              v-if="localFields.length > 0"
              v-model="localFields"
              item-key="id"
              handle=".field-drag-handle"
              ghost-class="sortable-ghost"
              @end="onFieldsDragEnd"
              class="list-group"
            >
              <template #item="{ element: field }">
                <div
                  class="list-group-item d-flex justify-content-between align-items-center"
                >
                  <div class="d-flex align-items-center">
                    <div class="field-drag-handle text-muted me-2" style="cursor: grab;">
                      <i class="bi bi-grip-vertical"></i>
                    </div>
                    <i :class="getFieldIcon(field.field_type_key)" class="me-2"></i>
                    <div>
                      <strong>{{ field.label || getFieldName(field.field_type_key) }}</strong>
                      <p class="mb-0 text-muted small">{{ field.display_value }}</p>
                    </div>
                  </div>
                  <div>
                    <button
                      class="btn btn-sm btn-outline-secondary me-1"
                      @click="editField(field)"
                    >
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="deleteField(field.id)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </template>
            </draggable>
            <div v-else class="text-muted text-center py-4">
              No hay campos agregados
            </div>

            <div v-if="showFieldModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="showFieldModal = false">
              <div class="modal-dialog modal-xl">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ editingField ? 'Editar Campo' : 'Agregar Campo' }}</h5>
                    <button type="button" class="btn-close" @click="closeFieldModal"></button>
                  </div>
                  <div class="modal-body p-0">
                    <div class="row g-0" style="min-height: 400px;">
                      <div class="col-5 border-end" style="max-height: 65vh; overflow-y: auto;">
                        <div class="p-3 border-bottom">
                          <input
                            v-model="fieldSearch"
                            type="text"
                            class="form-control"
                            placeholder="Buscar..."
                          />
                        </div>
                        <div class="p-3">
                          <h6 class="text-muted small mb-2">Populares</h6>
                          <div class="d-flex flex-wrap gap-2 mb-4">
                            <button
                              type="button"
                              v-for="field in mostPopularFields"
                              :key="field.key"
                              class="btn btn-sm"
                              :class="fieldForm.field_type_key === field.key ? 'btn-primary' : 'btn-outline-secondary'"
                              @click="selectFieldType(field.key)"
                            >
                              <i :class="field.icon" class="me-1"></i>
                              {{ field.name }}
                            </button>
                          </div>
                          <h6 class="text-muted small mb-2">Por categoría</h6>
                          <div v-for="(catFields, category) in filteredFieldTypes" :key="category" class="mb-3">
                            <h6 class="text-muted small">{{ getCategoryName(category) }}</h6>
                            <div class="d-flex flex-wrap gap-2">
                              <button
                                type="button"
                                v-for="field in catFields"
                                :key="field.key"
                                class="btn btn-sm"
                                :class="fieldForm.field_type_key === field.key ? 'btn-primary' : 'btn-outline-secondary'"
                                @click="selectFieldType(field.key)"
                              >
                                <i :class="field.icon" class="me-1"></i>
                                {{ field.name }}
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-7">
                        <div class="p-4" v-if="selectedFieldType">
                          <h5 class="mb-3">
                            <i :class="selectedFieldType.icon" class="me-2"></i>
                            {{ selectedFieldType.name }}
                          </h5>
                          <div v-for="schemaField in selectedFieldType.schema" :key="schemaField.name" class="mb-3">
                            <FieldUrl
                              v-if="schemaField.type === 'url'"
                              :id="`field-${schemaField.name}`"
                              :label="schemaField.label"
                              v-model="fieldForm.config[schemaField.name]"
                              :required="schemaField.required"
                              :showValidation="true"
                            />
                            <FieldEmail
                              v-else-if="schemaField.type === 'email'"
                              :id="`field-${schemaField.name}`"
                              :label="schemaField.label"
                              v-model="fieldForm.config[schemaField.name]"
                              :required="schemaField.required"
                              :showValidation="true"
                            />
                              <FieldFile
                                v-else-if="schemaField.type === 'file'"
                                :id="`field-${schemaField.name}`"
                                :label="schemaField.label"
                                v-model="fieldForm.config[schemaField.name]"
                                :required="schemaField.required"
                                :showPreviewToggle="true"
                              />
                              <FieldText
                                v-else-if="schemaField.type === 'text'"
                                :id="`field-${schemaField.name}`"
                                :label="schemaField.label"
                                v-model="fieldForm.config[schemaField.name]"
                                type="text"
                                :required="schemaField.required"
                              />
                              <FieldTextarea
                                v-else-if="schemaField.type === 'textarea'"
                                :id="`field-${schemaField.name}`"
                                :label="schemaField.label"
                                v-model="fieldForm.config[schemaField.name]"
                                :required="schemaField.required"
                              />
                            </div>
                          <div class="mb-3">
                            <FieldText
                              id="field-label"
                              label="Etiqueta personalizada"
                              v-model="fieldForm.label"
                              placeholder="Deja vacío para usar la del tipo"
                            />
                          </div>
                          <div class="mb-3 form-check">
                            <input
                              id="field-show-in-hero"
                              v-model="fieldForm.config.show_in_hero"
                              class="form-check-input"
                              type="checkbox"
                            >
                            <label class="form-check-label" for="field-show-in-hero">
                              Mostrar en Hero
                            </label>
                          </div>
                          <div v-if="fieldErrors.config" class="alert alert-danger small py-2">
                            {{ fieldErrors.config }}
                          </div>
                          <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" @click="saveField">
                              {{ editingField ? 'Actualizar' : 'Agregar campo' }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary" @click="closeFieldModal">
                              Cancelar
                            </button>
                          </div>
                        </div>
                        <div class="p-4 text-center text-muted" v-else>
                          <i class="bi bi-cursor-fill display-4 mb-3"></i>
                          <p class="mb-0">Selecciona un tipo de campo de la lista</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-show="activeTab === 'seguimiento'">
            <div class="row g-3">
              <div class="col-12">
                <h5 class="mb-3">Indexación</h5>
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="vcard-search-engine-indexing"
                  label="Indexación en motores de búsqueda"
                  v-model="form.search_engine_indexing"
                />
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="vcard-renew"
                  label="Renovar automáticamente"
                  v-model="form.renew"
                />
              </div>
              <div class="col-12">
                <FieldSwitch
                  id="vcard-paused"
                  label="Pausar tarjeta"
                  v-model="form.paused"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="vcard-tracking-code"
                  label="Codigos de seguimiento (UTM)"
                  v-model="trackingCodeInput"
                  placeholder="utm_source, utm_medium, utm_campaign"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="vcard-meta-pixel-id"
                  label="Meta Pixel ID"
                  v-model="form.meta_pixel_id"
                  placeholder="1234567890123456"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="vcard-google-analytics-id"
                  label="Google Analytics ID (G-XXXXXXXXXX)"
                  v-model="form.google_analytics_id"
                  placeholder="G-XXXXXXXXXX"
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="vcard-google-webmasters"
                  label="Google Search Console (verificacion)"
                  v-model="form.google_webmasters_verification"
                  placeholder="google-site-verification=..."
                />
              </div>
              <div class="col-12">
                <FieldText
                  id="vcard-bing-webmasters"
                  label="Bing Webmasters (verificacion)"
                  v-model="form.bing_webmasters_verification"
                  placeholder="BingSiteAuth=..."
                />
              </div>
            </div>
          </div>

          <div v-show="activeTab === 'secciones'">
            <p class="text-muted small mb-3">Activa o desactiva las secciones que aparecen en tu tarjeta digital.</p>
            <div class="sections-list">
              <div
                v-for="section in sectionsList"
                :key="section.key"
                class="section-item"
                :class="{ 'section-item--active': form.sections[section.key] }"
              >
                <div class="section-item__content">
                  <div class="section-item__toggle">
                    <FieldSwitch
                      :id="`section-${section.key}`"
                      :label="section.label"
                      v-model="form.sections[section.key]"
                    />
                  </div>
                  <div class="section-item__description small text-muted">
                    {{ getSectionDescription(section.key) }}
                  </div>
                </div>
                <div class="section-item__action" v-if="section.key === 'services' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showServicesModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'packages' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showPackagesModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'gallery' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showGalleryModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'products' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showProductsModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'testimonials' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showTestimonialsModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'business_hours' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showBusinessHoursModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'menu' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showMenuModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'location' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showLocationModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
                <div class="section-item__action" v-if="section.key === 'features' && form.sections[section.key]">
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    @click="showFeaturesModal = true"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                </div>
              </div>
            </div>
            <div class="mt-3">
              <button type="button" class="btn btn-primary" @click="saveSections" :disabled="savingSections">
                {{ savingSections ? 'Guardando...' : 'Guardar Secciones' }}
              </button>
            </div>
          </div>

          <FormActions
            :submitText="'Guardar Cambios'"
            :submittingText="'Guardando...'"
            :cancelHref="`/member/listings/${listing?.id}/vcards`"
            :sending="sending"
            class="mt-4"
          />
        </form>
          </div>
          <div class="col-12 col-lg-4">
            <VCardPreview
              :vcard="debouncedForm"
              :contacts="localContacts"
              :fields="localFields"
              :logoUrl="logoUrl"
              :badgeUrl="badgeUrl"
              :profilePhotoUrl="profilePhotoUrl"
              :heroBackgroundImageUrl="heroBackgroundImageUrl"
              :shape="debouncedForm.shape"
              :packages="localSelectedPackages"
              :sections="debouncedForm.sections"
              :selectedServices="localSelectedServices"
              :gallery="localSelectedGallery"
              :products="localSelectedProducts"
              :testimonials="localSelectedTestimonials"
              :businessHours="localBusinessHours"
              :menu="localMenu"
              :location="localSelectedLocation"
              :features="localSelectedFeatures"
              :about="aboutData"
              @change-image-position="updateImagePosition"
            />
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>

  <ServicesSelectionModal
    :show="showServicesModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedServices="localSelectedServices"
    @close="showServicesModal = false"
    @updated="onServicesUpdated"
  />

  <PackagesSelectionModal
    :show="showPackagesModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedPackages="localSelectedPackages"
    @close="showPackagesModal = false"
    @updated="onPackagesUpdated"
  />

  <GallerySelectionModal
    :show="showGalleryModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedGallery="localSelectedGallery"
    @close="showGalleryModal = false"
    @updated="onGalleryUpdated"
  />

  <ProductSelectionModal
    :show="showProductsModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedProducts="localSelectedProducts"
    @close="showProductsModal = false"
    @updated="onProductsUpdated"
  />

  <TestimonialsSelectionModal
    :show="showTestimonialsModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedTestimonials="localSelectedTestimonials"
    @close="showTestimonialsModal = false"
    @updated="onTestimonialsUpdated"
  />

  <BusinessHoursSelectionModal
    :show="showBusinessHoursModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedHours="localBusinessHours"
    @close="showBusinessHoursModal = false"
    @updated="onBusinessHoursUpdated"
  />

  <MenuSelectionModal
    :show="showMenuModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedMenu="localMenu"
    @close="showMenuModal = false"
    @updated="onMenuUpdated"
  />

  <LocationSelectionModal
    :show="showLocationModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedLocation="localSelectedLocation"
    @close="showLocationModal = false"
    @updated="onLocationUpdated"
  />

  <FeaturesSelectionModal
    :show="showFeaturesModal"
    :listingId="listing?.id"
    :vcardId="vcard?.id"
    :selectedFeatures="localSelectedFeatures"
    @close="showFeaturesModal = false"
    @updated="onFeaturesUpdated"
  />
</template>

<script setup>
import { ref, reactive, computed, watch, shallowRef, toRaw } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import axios from 'axios'
import draggable from 'vuedraggable'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldImage from '@/Components/Fields/FieldImage.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldFile from '@/Components/Fields/FieldFile.vue'
import FormActions from '@/Components/FormActions.vue'
import QRCode from '@/Components/QRCode/QRCode.vue'
import VCardPreview from './VCardPreview.vue'
import ServicesSelectionModal from '../../../Components/VCard/ServicesSelectionModal.vue'
import PackagesSelectionModal from '../../../Components/VCard/PackagesSelectionModal.vue'
import GallerySelectionModal from '../../../Components/VCard/GallerySelectionModal.vue'
import ProductSelectionModal from '../../../Components/VCard/ProductSelectionModal.vue'
import TestimonialsSelectionModal from '../../../Components/VCard/TestimonialsSelectionModal.vue'
import BusinessHoursSelectionModal from '../../../Components/VCard/BusinessHoursSelectionModal.vue'
import MenuSelectionModal from '../../../Components/VCard/MenuSelectionModal.vue'
import LocationSelectionModal from '../../../Components/VCard/LocationSelectionModal.vue'
import FeaturesSelectionModal from '../../../Components/VCard/FeaturesSelectionModal.vue'

let debounceTimer = null
const debouncedForm = reactive({})

function debouncedFormUpdate() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    const raw = toRaw(form)
    const { sections, ...rest } = raw
    Object.assign(debouncedForm, rest, {
      sections: sections ? { ...sections } : {}
    })
  }, 150)
}

const props = defineProps({
  listing: Object,
  vcard: Object,
  teams: Array,
  designs: Array,
  fonts: Array,
  colors: Array,
  pronouns: Array,
  contactTypes: Array,
  contactSubtypes: Array,
  fieldTypes: Object,
  fieldTypeCategories: Object,
  mostPopularFields: Array,
})

const activeTab = ref('card')
const sending = ref(false)
const savingSections = ref(false)
const showServicesModal = ref(false)
const showPackagesModal = ref(false)
const showGalleryModal = ref(false)
const showProductsModal = ref(false)
const showTestimonialsModal = ref(false)
const showBusinessHoursModal = ref(false)
const showMenuModal = ref(false)
const showLocationModal = ref(false)
const showFeaturesModal = ref(false)

const sectionsList = [
  { key: 'appointments', label: 'Agendar Cita' },
  { key: 'services', label: 'Servicios' },
  { key: 'packages', label: 'Paquetes' },
  { key: 'gallery', label: 'Galeria' },
  { key: 'products', label: 'Productos' },
  { key: 'testimonials', label: 'Testimonios' },
  { key: 'business_hours', label: 'Horario' },
  { key: 'menu', label: 'Menu' },
  { key: 'contact_form', label: 'Contacto' },
  { key: 'location', label: 'Ubicacion' },
  { key: 'features', label: 'Caracteristicas' },
  { key: 'about', label: 'Acerca de' },
]
const errors = ref({})
const showContactModal = ref(false)
const showFieldModal = ref(false)
const editingContact = ref(null)
const editingField = ref(null)
const savingContact = ref(false)
const savingField = ref(false)
const fieldSearch = ref('')
const selectedFieldType = ref(null)
const logoFile = ref(null)
const badgeFile = ref(null)
const profileFile = ref(null)
const heroBackgroundImageFile = ref(null)

const gradientDirections = [
  { value: '0deg', label: 'Vertical' },
  { value: '90deg', label: 'Horizontal' },
  { value: '45deg', label: 'Diagonal' },
  { value: '135deg', label: 'Diagonal inv.' },
]

const patterns = [
  { value: 'dots', label: 'Puntos', preview: `url("data:image/svg+xml,%3Csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='2' cy='2' r='1.5' fill='%23333' fill-opacity='0.4'/%3E%3C/svg%3E")` },
  { value: 'lines-diagonal', label: 'Líneas diag.', preview: `url("data:image/svg+xml,%3Csvg width='10' height='10' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='10' x2='10' y2='0' stroke='%23333' stroke-opacity='0.4' stroke-width='1'/%3E%3C/svg%3E")` },
  { value: 'lines-horizontal', label: 'Líneas hor.', preview: `url("data:image/svg+xml,%3Csvg width='10' height='4' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='2' x2='10' y2='2' stroke='%23333' stroke-opacity='0.4' stroke-width='1'/%3E%3C/svg%3E")` },
  { value: 'squares', label: 'Cuadrícula', preview: `url("data:image/svg+xml,%3Csvg width='20' height='20' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='0' y='0' width='10' height='10' fill='none' stroke='%23333' stroke-opacity='0.4' stroke-width='1'/%3E%3Crect x='10' y='10' width='10' height='10' fill='none' stroke='%23333' stroke-opacity='0.4' stroke-width='1'/%3E%3C/svg%3E")` },
  { value: 'chevron', label: 'Chevron', preview: `url("data:image/svg+xml,%3Csvg width='40' height='40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 20 L20 0 L40 20 L20 40 Z' fill='none' stroke='%23333' stroke-opacity='0.4' stroke-width='1'/%3E%3C/svg%3E")` },
  { value: 'crosshatch', label: 'Cruz', preview: `url("data:image/svg+xml,%3Csvg width='12' height='12' xmlns='http://www.w3.org/2000/svg'%3E%3Cline x1='0' y1='6' x2='12' y2='6' stroke='%23333' stroke-opacity='0.4' stroke-width='1'/%3E%3Cline x1='6' y1='0' x2='6' y2='12' stroke='%23333' stroke-opacity='0.4' stroke-width='1'/%3E%3C/svg%3E")` },
]

const localContacts = shallowRef([...(props.vcard?.contacts || [])])

const previewPackages = [
  { id: 1, name: 'Basico', description: 'Plan basico con funciones esenciales', price: 9.99, currency: 'USD', duration_days: 30, active: true },
  { id: 2, name: 'Profesional', description: 'Plan profesional con todas las funciones', price: 19.99, currency: 'USD', duration_days: 30, active: true },
  { id: 3, name: 'Premium', description: 'Plan premium con soporte prioritario', price: 39.99, currency: 'USD', duration_days: 30, active: true },
]
const localFields = shallowRef([...(props.vcard?.fields || [])])
const localSelectedServices = shallowRef([...(props.vcard?.services || [])])
const localSelectedPackages = shallowRef([...(props.vcard?.packages || [])])
const localSelectedGallery = shallowRef(props.vcard?.gallery || null)
const localSelectedProducts = shallowRef([...(props.vcard?.products || [])])
const localSelectedTestimonials = shallowRef([...(props.vcard?.testimonials || [])])
const localBusinessHours = shallowRef([...(props.vcard?.business_hours || [])])
const localMenu = shallowRef([...(props.vcard?.menu || [])])
const localSelectedLocation = shallowRef(props.vcard?.location || null)
const localSelectedFeatures = shallowRef([...(props.vcard?.features || [])])

watch(() => props.vcard?.contacts, (newContacts) => {
  localContacts.value = [...(newContacts || [])]
})

watch(() => props.vcard?.fields, (newFields) => {
  localFields.value = [...(newFields || [])]
})

watch(() => props.vcard?.services, (newServices) => {
  localSelectedServices.value = [...(newServices || [])]
})

watch(() => props.vcard?.packages, (newPackages) => {
  localSelectedPackages.value = [...(newPackages || [])]
})

watch(() => props.vcard?.gallery, (newGallery) => {
  localSelectedGallery.value = newGallery || null
})

watch(() => props.vcard?.products, (newProducts) => {
  localSelectedProducts.value = [...(newProducts || [])]
})

watch(() => props.vcard?.testimonials, (newTestimonials) => {
  localSelectedTestimonials.value = [...(newTestimonials || [])]
})

watch(() => props.vcard?.business_hours, (newHours) => {
  localBusinessHours.value = [...(newHours || [])]
})

watch(() => props.vcard?.menu, (newMenu) => {
  localMenu.value = [...(newMenu || [])]
})

watch(() => props.vcard?.location, (newLocation) => {
  localSelectedLocation.value = newLocation || null
})

watch(() => props.vcard?.features, (newFeatures) => {
  localSelectedFeatures.value = [...(newFeatures || [])]
})

const form = reactive({
  name: props.vcard?.name || '',
  slug: props.vcard?.slug || '',
  type: props.vcard?.type || 'single',
  vcard_team_id: props.vcard?.vcard_team_id || '',
  active: props.vcard?.active ?? true,
  search_engine_indexing: props.vcard?.search_engine_indexing ?? true,
  renew: props.vcard?.renew ?? true,
  tracking_code: props.vcard?.tracking_code || [],
  paused: props.vcard?.paused ?? false,
  ai_chat_enabled: props.vcard?.ai_chat_enabled ?? false,
  meta_pixel_id: props.vcard?.meta_pixel_id || '',
  google_analytics_id: props.vcard?.google_analytics_id || '',
  google_webmasters_verification: props.vcard?.google_webmasters_verification || '',
  bing_webmasters_verification: props.vcard?.bing_webmasters_verification || '',
  design: props.vcard?.design || 'classic',
  primary_color: props.vcard?.primary_color || '#2563EB',
  font: props.vcard?.font || 'Inter',
  prefix: props.vcard?.prefix || '',
  first_name: props.vcard?.first_name || '',
  middle_name: props.vcard?.middle_name || '',
  last_name: props.vcard?.last_name || '',
  accreditations: props.vcard?.accreditations || '',
  preferred_name: props.vcard?.preferred_name || '',
  pronouns: props.vcard?.pronouns || '',
  title: props.vcard?.title || '',
  department: props.vcard?.department || '',
  company: props.vcard?.company || '',
  headline: props.vcard?.headline || '',
  logo: props.vcard?.logo || '',
  badge: props.vcard?.badge || '',
  profile_photo: props.vcard?.profile_photo || '',
  hero_background_image: props.vcard?.hero_background_image || '',
  shape: props.vcard?.shape || 'rounded',
  image_x: props.vcard?.image_x || 0,
  image_y: props.vcard?.image_y || 0,
  background_type: props.vcard?.background_type || 'solid',
  gradient_direction: props.vcard?.gradient_direction || '135deg',
  pattern_key: props.vcard?.pattern_key || 'dots',
  hero_image_alpha: props.vcard?.hero_image_alpha ?? 100,
  body_background_type: props.vcard?.body_background_type || 'solid',
  body_primary_color: props.vcard?.body_primary_color || '#ffffff',
  body_gradient_direction: props.vcard?.body_gradient_direction || '135deg',
  body_pattern_key: props.vcard?.body_pattern_key || 'dots',
  sections: props.vcard?.sections || {
    appointments: false,
    services: false,
    packages: false,
    gallery: false,
    products: false,
    testimonials: false,
    business_hours: false,
    menu: false,
    contact_form: false,
    location: false,
    features: false,
    about: false,
  },
})

const rawForm = toRaw(form)
const { sections: rawSections, ...rawRest } = rawForm
Object.assign(debouncedForm, rawRest, {
  sections: rawSections ? { ...rawSections } : {}
})

watch(form, () => {
  debouncedFormUpdate()
}, { deep: true })

const contactForm = reactive({
  type: 'phone',
  contact_type: 'personal',
  country_code: '+52',
  value: '',
  extension: '',
})

const fieldForm = reactive({
  field_type_key: '',
  label: '',
  config: { show_in_hero: false },
})

const contactErrors = reactive({
  type: '',
  value: '',
  country_code: '',
})

const fieldErrors = reactive({
  field_type_key: '',
  config: '',
})

const vcardPublicUrl = computed(() => {
  const slug = props.vcard?.data?.slug ?? props.vcard?.slug
  return `${window.location.origin}/v/${slug}`
})

const aboutData = computed(() => {
  return props.listing?.about || null
})

function updateImagePosition({ x, y }) {
  form.image_x = x
  form.image_y = y
}

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'vCards', href: `/member/listings/${props.listing?.id}/vcards` },
  { label: props.vcard?.data?.name || props.vcard?.name || 'Editar' },
])

const logoUrl = computed(() => {
  if (logoFile.value) return URL.createObjectURL(logoFile.value)
  return form.logo ? `/storage/${form.logo}` : null
})

const badgeUrl = computed(() => {
  if (badgeFile.value) return URL.createObjectURL(badgeFile.value)
  return form.badge ? `/storage/${form.badge}` : null
})

const profilePhotoUrl = computed(() => {
  if (profileFile.value) return URL.createObjectURL(profileFile.value)
  return form.profile_photo ? `/storage/${form.profile_photo}` : null
})

const trackingCodeInput = computed({
  get() {
    if (!form.tracking_code || !Array.isArray(form.tracking_code)) return ''
    return form.tracking_code.join(', ')
  },
  set(value) {
    if (!value || value.trim() === '') {
      form.tracking_code = []
      return
    }
    form.tracking_code = value.split(',').map(s => s.trim()).filter(s => s.length > 0)
  }
})

const heroBackgroundImageUrl = computed(() => {
  if (heroBackgroundImageFile.value) return URL.createObjectURL(heroBackgroundImageFile.value)
  return form.hero_background_image ? `/storage/${form.hero_background_image}` : null
})

const filteredFieldTypes = computed(() => {
  if (!fieldSearch.value) return props.fieldTypes

  const search = fieldSearch.value.toLowerCase()
  const filtered = {}

  for (const [category, fields] of Object.entries(props.fieldTypes)) {
    const categoryFields = fields.filter(f =>
      f.name.toLowerCase().includes(search) ||
      f.key.toLowerCase().includes(search)
    )
    if (categoryFields.length > 0) {
      filtered[category] = categoryFields
    }
  }

  return filtered
})

function getContactIcon(type) {
  const icons = {
    phone: 'bi-telephone',
    email: 'bi-envelope',
    whatsapp: 'bi-whatsapp',
  }
  return icons[type] || 'bi-chat-left'
}

function getFieldIcon(fieldKey) {
  const field = props.fieldTypes[Object.keys(props.fieldTypes).find(k =>
    props.fieldTypes[k].some(f => f.key === fieldKey)
  )]?.find(f => f.key === fieldKey)
  return field?.icon || 'bi-link'
}

function getFieldName(fieldKey) {
  const field = props.fieldTypes[Object.keys(props.fieldTypes).find(k =>
    props.fieldTypes[k].some(f => f.key === fieldKey)
  )]?.find(f => f.key === fieldKey)
  return field?.name || fieldKey
}

function getCategoryName(category) {
  return props.fieldTypeCategories?.[category] || category
}

async function submit() {
  sending.value = true
  errors.value = {}

  try {
    if (logoFile.value) {
      const logoFormData = new FormData()
      logoFormData.append('image', logoFile.value)
      const logoRes = await axios.post(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/logo`,
        logoFormData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )
      form.logo = logoRes.data.logo
      logoFile.value = null
    }

    if (badgeFile.value) {
      const badgeFormData = new FormData()
      badgeFormData.append('image', badgeFile.value)
      const badgeRes = await axios.post(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/badge`,
        badgeFormData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )
      form.badge = badgeRes.data.badge
      badgeFile.value = null
    }

    if (profileFile.value) {
      const profileFormData = new FormData()
      profileFormData.append('image', profileFile.value)
      const profileRes = await axios.post(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/profile-photo`,
        profileFormData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )
      form.profile_photo = profileRes.data.profile_photo
      profileFile.value = null
    }

    if (heroBackgroundImageFile.value) {
      const heroBackgroundFormData = new FormData()
      heroBackgroundFormData.append('image', heroBackgroundImageFile.value)
      const heroBackgroundRes = await axios.post(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/hero-background-image`,
        heroBackgroundFormData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      )
      form.hero_background_image = heroBackgroundRes.data.hero_background_image
      heroBackgroundImageFile.value = null
    }

    router.put(
      `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}`,
      form,
      {
        onError: (err) => {
          errors.value = err
          sending.value = false
        },
        onSuccess: () => {
          toast.success('Tarjeta actualizada correctamente')
          sending.value = false
        },
      }
    )
  } catch (err) {
    toast.error('Error al subir la imagen')
    sending.value = false
  }
}

function copyLink() {
  navigator.clipboard.writeText(vcardPublicUrl.value)
  toast.success('Enlace copiado al portapapeles')
}

function downloadVCard() {
  window.location.href = `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/download`
}

function getSectionDescription(key) {
  const descriptions = {
    appointments: 'Permite agendar citas con tus clientes',
    services: 'Muestra tus servicios disponibles',
    packages: 'Agrega paquetes o planes',
    gallery: 'Comparte fotos de tu negocio',
    products: 'Muestra tu catalogo de productos',
    testimonials: 'Agrega reseñas de clientes',
    business_hours: 'Muestra tu horario de atencion',
    menu: 'Incluye un menu digital',
    contact_form: 'Permite recibir mensajes',
    location: 'Muestra tu ubicacion en el mapa',
    features: 'Muestra las características de tu negocio',
    about: 'Muestra la sección Acerca de tu negocio',
  }
  return descriptions[key] || ''
}

function saveSections() {
  if (savingSections.value) return
  savingSections.value = true

  const sections = sectionsList.map(s => ({
    key: s.key,
    enabled: form.sections[s.key] ?? true,
  }))

  router.post(
    `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/sections`,
    { sections },
    {
      onSuccess: () => {
        toast.success('Secciones actualizadas')
        savingSections.value = false
      },
      onError: () => {
        savingSections.value = false
      },
    }
  )
}

function onServicesUpdated(services) {
  localSelectedServices.value = [...services]
}

function onPackagesUpdated(packages) {
  localSelectedPackages.value = [...packages]
}

function onGalleryUpdated(gallery) {
  localSelectedGallery.value = gallery
}

function onProductsUpdated(products) {
  localSelectedProducts.value = [...products]
}

function onTestimonialsUpdated(testimonials) {
  localSelectedTestimonials.value = [...testimonials]
}

function onBusinessHoursUpdated(hours) {
  localBusinessHours.value = [...hours]
}

function onMenuUpdated(menu) {
  localMenu.value = [...menu]
}

function onLocationUpdated(location) {
  localSelectedLocation.value = location
}

function onFeaturesUpdated(features) {
  localSelectedFeatures.value = [...features]
}

function selectFieldType(key) {
  const field = props.mostPopularFields.find(f => f.key === key) ||
    Object.values(props.fieldTypes).flat().find(f => f.key === key)

  if (field) {
    selectedFieldType.value = field
    fieldForm.field_type_key = key
    fieldForm.config = { show_in_hero: false }
    fieldForm.label = ''
  }
}

function closeContactModal() {
  showContactModal.value = false
  editingContact.value = null
  savingContact.value = false
  contactForm.type = 'phone'
  contactForm.contact_type = 'personal'
  contactForm.country_code = '+52'
  contactForm.value = ''
  contactForm.extension = ''
}

function editContact(contact) {
  editingContact.value = contact
  contactForm.type = contact.type
  contactForm.contact_type = contact.contact_type
  contactForm.country_code = contact.country_code || '+52'
  contactForm.value = contact.value
  contactForm.extension = contact.extension || ''
  showContactModal.value = true
}

function saveContact() {
  if (savingContact.value) return
  savingContact.value = true

  contactErrors.type = ''
  contactErrors.value = ''
  contactErrors.country_code = ''

  let isValid = true

  if (!contactForm.type) {
    contactErrors.type = 'El tipo es obligatorio.'
    isValid = false
  }

  if (!contactForm.value || contactForm.value.trim() === '') {
    contactErrors.value = 'El valor es obligatorio.'
    isValid = false
  }

  if ((contactForm.type === 'phone' || contactForm.type === 'whatsapp') && contactForm.value) {
    const phoneRegex = /^[+\d\s\-()]+$/
    if (!phoneRegex.test(contactForm.value)) {
      contactErrors.value = 'Solo se permiten números, espacios, guiones y el prefijo +.'
      isValid = false
    }
  }

  if ((contactForm.type === 'phone' || contactForm.type === 'whatsapp') && !contactForm.country_code) {
    contactErrors.country_code = 'El código de país es obligatorio.'
    isValid = false
  }

  if (!isValid) {
    savingContact.value = false
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  if (editingContact.value) {
    router.put(
      `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/contacts/${editingContact.value.id}`,
      contactForm,
      {
        onSuccess: () => {
          toast.success('Contacto actualizado')
          closeContactModal()
          savingContact.value = false
          router.reload({ only: ['vcard'] })
        },
        onError: () => {
          savingContact.value = false
        },
      }
    )
  } else {
    router.post(
      `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/contacts`,
      contactForm,
      {
        onSuccess: () => {
          toast.success('Contacto agregado')
          closeContactModal()
          savingContact.value = false
          router.reload({ only: ['vcard'] })
        },
        onError: () => {
          savingContact.value = false
        },
      }
    )
  }
}

function deleteContact(contactId) {
  if (!confirm('¿Eliminar este contacto?')) return

  router.delete(
    `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/contacts/${contactId}`,
    {
      onSuccess: () => {
        toast.success('Contacto eliminado')
        router.reload({ only: ['vcard'] })
      },
    }
  )
}

function closeFieldModal() {
  showFieldModal.value = false
  editingField.value = null
  savingField.value = false
  fieldForm.field_type_key = ''
  fieldForm.label = ''
  fieldForm.config = { show_in_hero: false }
  selectedFieldType.value = null
}

function editField(field) {
  editingField.value = field
  const fieldType = props.mostPopularFields.find(f => f.key === field.field_type_key) ||
    Object.values(props.fieldTypes).flat().find(f => f.key === field.field_type_key)

  if (fieldType) {
    selectedFieldType.value = fieldType
    fieldForm.field_type_key = field.field_type_key
    fieldForm.label = field.label || ''
    fieldForm.config = {
      ...(field.config || {}),
      show_in_hero: field.config?.show_in_hero ?? false,
    }
  }
  showFieldModal.value = true
}

function saveField() {
  if (savingField.value) return
  savingField.value = true

  fieldErrors.field_type_key = ''
  fieldErrors.config = ''

  if (!fieldForm.field_type_key) {
    fieldErrors.field_type_key = 'Selecciona un tipo de campo.'
    savingField.value = false
    toast.warning('Por favor selecciona un tipo de campo')
    return
  }

  if (selectedFieldType.value?.schema) {
    for (const schemaField of selectedFieldType.value.schema) {
      if (schemaField.required && !fieldForm.config[schemaField.name]) {
        fieldErrors.config = `El campo "${schemaField.label}" es obligatorio.`
        savingField.value = false
        toast.warning('Por favor completa los campos requeridos')
        return
      }
    }
  }

  const hasFileField = selectedFieldType.value?.schema?.some(s => s.type === 'file' && fieldForm.config[s.name] instanceof File)

  if (hasFileField) {
    const formData = new FormData()
    formData.append('field_type_key', fieldForm.field_type_key)
    formData.append('label', fieldForm.label || '')
    formData.append('active', fieldForm.active ? '1' : '0')
    formData.append('config', JSON.stringify(fieldForm.config))

    for (const schemaField of selectedFieldType.value.schema) {
      if (schemaField.type === 'file' && fieldForm.config[schemaField.name] instanceof File) {
        formData.append(`config_${schemaField.name}`, fieldForm.config[schemaField.name])
      }
    }

    if (editingField.value) {
      router.post(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/fields/${editingField.value.id}?_method=PUT`,
        formData,
        {
          onSuccess: () => {
            toast.success('Campo actualizado')
            closeFieldModal()
            savingField.value = false
            router.reload({ only: ['vcard'] })
          },
          onError: () => {
            savingField.value = false
          },
        }
      )
    } else {
      router.post(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/fields`,
        formData,
        {
          onSuccess: () => {
            toast.success('Campo agregado')
            closeFieldModal()
            savingField.value = false
            router.reload({ only: ['vcard'] })
          },
          onError: () => {
            savingField.value = false
          },
        }
      )
    }
  } else {
    if (editingField.value) {
      router.put(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/fields/${editingField.value.id}`,
        fieldForm,
        {
          onSuccess: () => {
            toast.success('Campo actualizado')
            closeFieldModal()
            savingField.value = false
            router.reload({ only: ['vcard'] })
          },
          onError: () => {
            savingField.value = false
          },
        }
      )
    } else {
      router.post(
        `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/fields`,
        fieldForm,
        {
          onSuccess: () => {
            toast.success('Campo agregado')
            closeFieldModal()
            savingField.value = false
            router.reload({ only: ['vcard'] })
          },
          onError: () => {
            savingField.value = false
          },
        }
      )
    }
  }
}

function deleteField(fieldId) {
  if (!confirm('¿Eliminar este campo?')) return

  router.delete(
    `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/fields/${fieldId}`,
    {
      onSuccess: () => {
        toast.success('Campo eliminado')
        router.reload({ only: ['vcard'] })
      },
    }
  )
}

function onContactsDragEnd() {
  const order = localContacts.value.map(c => c.id)
  router.post(
    `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/contacts/reorder`,
    { order },
    {
      preserveScroll: true,
    }
  )
}

function onFieldsDragEnd() {
  const order = localFields.value.map(f => f.id)
  router.post(
    `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}/fields/reorder`,
    { order },
    {
      preserveScroll: true,
    }
  )
}

function deleteCard() {
  if (!confirm('¿Estás seguro de que deseas eliminar esta tarjeta? Esta acción no se puede deshacer.')) return

  router.delete(
    `/member/listings/${props.listing.id}/vcards/${props.vcard.data?.id ?? props.vcard.id}`,
    {
      onSuccess: () => {
        toast.success('Tarjeta eliminada correctamente')
        window.location.href = `/member/listings/${props.listing.id}/vcards`
      },
      onError: () => {
        toast.error('Error al eliminar la tarjeta')
      },
    }
  )
}
</script>

<style scoped>
.design-option {
  cursor: pointer;
  transition: all 0.2s;
}

.design-preview {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.color-swatch {
  width: 36px;
  height: 36px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s;
}

.color-swatch:hover {
  transform: scale(1.1);
}

.color-swatch.ring-2 {
  border-color: var(--bs-primary);
}

.modal-body .btn-outline-secondary {
  color: #212529;
  border-color: #6c757d;
}

.modal-body .btn-outline-secondary:hover {
  background-color: #e9ecef;
  color: #212529;
}

.modal-body h6 {
  color: #333;
}

.modal-body .btn-outline-secondary i {
  color: inherit;
}

.sortable-ghost {
  opacity: 0.4;
  background-color: #e9ecef;
}

.contact-drag-handle:active,
.field-drag-handle:active {
  cursor: grabbing;
}

.shape-option {
  min-width: 100px;
}

.shape-preview {
  width: 50px;
  height: 50px;
  background-color: #e5e7eb;
}

.shape-square {
  border-radius: 0;
}

.shape-rounded {
  border-radius: 8px;
}

.bg-type-option {
  transition: all 0.15s;
}

.bg-type-option i {
  font-size: 1.25rem;
}

.gradient-dir-option {
  transition: all 0.15s;
}

.gradient-preview {
  width: 2.5rem;
  height: 1.5rem;
  border-radius: 4px;
}

.pattern-option {
  transition: all 0.15s;
  min-width: 4.5rem;
}

.pattern-preview {
  width: 2.5rem;
  height: 2.5rem;
  background-size: 1rem 1rem;
  background-color: #f0f0f0;
  border-radius: 4px;
}

.card {
  transition: box-shadow 0.2s;
}

.card:hover {
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
}

.sections-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.section-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background: #fff;
  transition: all 0.2s;
}

.section-item:hover {
  border-color: #d1d5db;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.section-item--active {
  border-color: #3b82f6;
  background: #f8fafc;
}

.section-item__content {
  flex: 1;
}

.section-item__description {
  margin-top: 0.25rem;
  margin-left: 0.25rem;
}

.section-item__action {
  margin-left: 1rem;
}
</style>
