<template>
  <div v-if="isAvailable" class="ai-chat-widget" :class="[widgetTheme, { open: isOpen }]">
    <div v-if="!isOpen" class="chat-bubble" :style="{ backgroundColor: widgetColor }" @click="openChat">
      <div class="bubble-icon">
        <img v-if="localChatbotAvatar" :src="localChatbotAvatar" alt="Avatar" class="bubble-avatar" />
        <svg v-else width="28" height="28" viewBox="0 0 24 24" fill="none">
          <path d="M12 2C6.48 2 2 5.58 2 10c0 1.82.62 3.49 1.64 4.83L2 22l4.17-.64A9.93 9.93 0 0012 22c5.52 0 10-3.58 10-8s-4.48-12-10-12z" fill="white"/>
          <circle cx="8" cy="10" r="1.5" :fill="widgetColor"/>
          <circle cx="12" cy="10" r="1.5" :fill="widgetColor"/>
          <circle cx="16" cy="10" r="1.5" :fill="widgetColor"/>
        </svg>
      </div>
      <div class="bubble-badge" v-if="unreadCount > 0">{{ unreadCount }}</div>
    </div>

    <div v-else class="chat-window">
      <div class="chat-header" :style="{ backgroundColor: widgetColor }">
        <div class="chat-header-info">
          <div class="chat-avatar">
            <img v-if="chatbotAvatar" :src="chatbotAvatar" alt="Avatar" class="chat-avatar-img" />
            <i v-else class="bi bi-robot"></i>
          </div>
          <div>
            <div class="chat-title">{{ localChatbotName }}</div>
            <div class="chat-status">
              <span class="status-dot"></span> En línea
            </div>
          </div>
        </div>
        <div class="chat-header-actions">
          <button v-if="allowReset" class="chat-action-btn" @click="resetChat" title="Reiniciar chat">
            <i class="bi bi-arrow-counterclockwise"></i>
          </button>
          <button class="chat-close-btn" @click="closeChat">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <div class="chat-messages" ref="messagesContainer">
        <div v-if="messages.length === 0" class="chat-empty">
          <i class="bi bi-chat-dots"></i>
          <p v-if="!localSuggestions.length">¡Hola! Soy {{ localChatbotName }}. ¿En qué puedo ayudarte?</p>
          <div v-else class="suggestions-container">
            <p class="suggestions-title">Sugerencias:</p>
            <button
              v-for="(suggestion, idx) in localSuggestions"
              :key="idx"
              class="suggestion-btn"
              :style="{ borderColor: widgetColor, color: widgetColor }"
              @click="useSuggestion(suggestion)"
            >
              {{ suggestion }}
            </button>
          </div>
        </div>

        <div
          v-for="(msg, index) in messages"
          :key="index"
          class="chat-message"
          :class="msg.role"
        >
          <div class="message-content" :class="{ expanded: msg.expanded || !msg.isLong }">
            <template v-if="msg.isLong && localExpandableResponses && !msg.expanded">
              {{ msg.preview }}
              <button class="expand-btn" @click="msg.expanded = true">Ver más</button>
            </template>
            <template v-else>
              {{ msg.content }}
            </template>
          </div>

          <div v-if="localShowCitations && msg.sources && msg.sources.length" class="message-sources">
            <div class="sources-header" @click="msg.sourcesExpanded = !msg.sourcesExpanded">
              <i class="bi bi-link-45deg"></i>
              <span>Fuentes ({{ msg.sources.length }})</span>
              <i :class="msg.sourcesExpanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
            </div>
            <div v-if="msg.sourcesExpanded" class="sources-list">
              <div v-for="(source, idx) in msg.sources" :key="idx" class="source-item">
                <span class="source-type">{{ formatSourceType(source.type) }}</span>
                <span class="source-text">{{ source.text }}</span>
              </div>
            </div>
          </div>

          <div v-if="msg.showCta && msg.showCta.url" class="message-cta">
            <a
              :href="msg.showCta.url"
              target="_blank"
              class="cta-btn cta-primary"
              :style="{ backgroundColor: widgetColor }"
            >
              {{ msg.showCta.text }}
            </a>
          </div>

          <div class="message-time" v-if="msg.timestamp">
            {{ formatTime(msg.timestamp) }}
          </div>
        </div>

        <div v-if="isTyping" class="chat-message assistant">
          <div class="message-content typing">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>

        <div v-if="showLeadCapture && !leadCaptureSubmitted" class="lead-capture">
          <div class="lead-capture-icon">
            <i class="bi bi-envelope-plus"></i>
          </div>
          <div class="lead-capture-content">
            <p class="lead-capture-title">{{ leadCaptureTitle }}</p>
            <p class="lead-capture-desc">{{ leadCaptureDescription }}</p>
            <div class="lead-capture-form">
              <input
                type="text"
                v-model="leadName"
                placeholder="Tu nombre (opcional)"
                class="lead-input"
              />
              <input
                type="email"
                v-model="leadEmail"
                placeholder="tu@email.com"
                class="lead-input"
                @keypress.enter="submitLead"
              />
              <button
                class="lead-submit"
                :style="{ backgroundColor: widgetColor }"
                @click="submitLead"
              >
                <i class="bi bi-send"></i>
              </button>
            </div>
            <button class="lead-skip" @click="showLeadCapture = false">
              Nah, mejor no
            </button>
          </div>
        </div>

        <div v-if="leadCaptureSubmitted" class="lead-capture-success">
          <i class="bi bi-check-circle"></i>
          <span>¡Gracias! Te mantendremos informado.</span>
        </div>
      </div>

      <div class="chat-input">
        <input
          type="text"
          v-model="inputMessage"
          placeholder="Escribe un mensaje..."
          :disabled="sending"
          @keypress.enter="sendMessage"
        />
        <button
          class="send-btn"
          :style="{ backgroundColor: widgetColor }"
          :disabled="!inputMessage.trim() || sending"
          @click="sendMessage"
        >
          <i v-if="sending" class="bi bi-hourglass-split"></i>
          <i v-else class="bi bi-send"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, watch } from 'vue'

const props = defineProps({
  businessSlug: {
    type: String,
    required: true,
  },
  businessName: {
    type: String,
    default: '',
  },
  chatbotName: {
    type: String,
    default: 'Asistente Virtual',
  },
  chatbotAvatar: {
    type: String,
    default: '',
  },
  widgetColor: {
    type: String,
    default: '#3B82F6',
  },
  widgetTheme: {
    type: String,
    default: 'light',
  },
  allowReset: {
    type: Boolean,
    default: false,
  },
  initialSuggestions: {
    type: Array,
    default: () => [],
  },
})

const isOpen = ref(false)
const isAvailable = ref(true)
const messages = ref([])
const inputMessage = ref('')
const sending = ref(false)
const isTyping = ref(false)
const messagesContainer = ref(null)
const unreadCount = ref(0)
const sessionId = ref(localStorage.getItem(`chat_session_${props.businessSlug}`) || generateSessionId())
const localChatbotName = ref(props.chatbotName)
const localChatbotAvatar = ref(props.chatbotAvatar)
const localSuggestions = ref([])
const localExpandableResponses = ref(true)
const localShowCitations = ref(true)
const localIntentCta = ref(null)
const leadCaptureEnabled = ref(false)
const leadCaptureTitle = ref('')
const leadCaptureDescription = ref('')
const showLeadCapture = ref(false)
const leadCaptureSubmitted = ref(false)
const leadEmail = ref('')
const leadName = ref('')

function generateSessionId() {
  const id = 'chat_' + Math.random().toString(36).substring(2) + Date.now().toString(36)
  localStorage.setItem(`chat_session_${props.businessSlug}`, id)
  return id
}

const openChat = () => {
  isOpen.value = true
  unreadCount.value = 0
  loadHistory()
  nextTick(() => scrollToBottom())
}

const closeChat = () => {
  isOpen.value = false
}

const resetChat = () => {
  if (confirm('¿Quieres reiniciar la conversación? Se borrarán todos los mensajes.')) {
    messages.value = []
    sessionStorage.removeItem(`chat_messages_${props.businessSlug}_${sessionId.value}`)
    sessionId.value = generateSessionId()
  }
}

const loadHistory = () => {
  const saved = sessionStorage.getItem(`chat_messages_${props.businessSlug}_${sessionId.value}`)
  if (saved) {
    try {
      messages.value = JSON.parse(saved)
    } catch (e) {
      messages.value = []
    }
  }
}

const saveMessages = () => {
  sessionStorage.setItem(
    `chat_messages_${props.businessSlug}_${sessionId.value}`,
    JSON.stringify(messages.value)
  )
}

const sendMessage = () => {
  if (!inputMessage.value.trim() || sending.value) return

  const userMessage = inputMessage.value.trim()
  messages.value.push({
    role: 'user',
    content: userMessage,
    timestamp: new Date().toISOString(),
  })
  inputMessage.value = ''
  saveMessages()
  scrollToBottom()

  sending.value = true
  isTyping.value = true

  const msgIndex = messages.value.length
  messages.value.push({
    role: 'assistant',
    content: '',
    preview: '',
    isLong: false,
    expanded: false,
    sources: [],
    sourcesExpanded: false,
    showCta: false,
    timestamp: new Date().toISOString(),
  })

  const url = `/m/${props.businessSlug}/ai-chatbot/chat`
  console.log('[Chat] fetching:', url)
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
    body: JSON.stringify({
      message: userMessage,
      session_id: sessionId.value,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      console.log('[Chat] response:', data)
      isTyping.value = false
      sending.value = false

      if (data.success && data.message) {
        messages.value[msgIndex].content = data.message
        messages.value[msgIndex].preview = data.message.substring(0, 300)
        messages.value[msgIndex].isLong = data.message.length > 300
        messages.value[msgIndex].sources = data.sources || []
        messages.value[msgIndex].expanded = !data.expandable_responses
        messages.value[msgIndex].showCta = shouldShowCta(data.message, data.intent_cta)
        if (data.cta_settings?.intent_cta) {
          localIntentCta.value = data.cta_settings.intent_cta
        }
      } else {
        messages.value[msgIndex].content = data.message || 'Disculpa, estoy teniendo problemas para responder.'
      }
      saveMessages()
      scrollToBottom()
    })
    .catch((err) => {
      console.log('[Chat] catch error:', err)
      messages.value[msgIndex].content = 'Disculpa, estoy teniendo problemas para responder. Intenta de nuevo.'
      isTyping.value = false
      sending.value = false
      saveMessages()
    })
}

const submitLead = () => {
  if (!leadEmail.value.trim() || !leadEmail.value.includes('@')) {
    return
  }
  fetch(`/m/${props.businessSlug}/ai-chatbot/capture-lead`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
    body: JSON.stringify({
      email: leadEmail.value,
      name: leadName.value,
      session_id: sessionId.value,
    }),
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        leadCaptureSubmitted.value = true
        showLeadCapture.value = false
      }
    })
    .catch(() => {})
}

const checkLeadCaptureTrigger = () => {
  if (!leadCaptureEnabled.value || leadCaptureSubmitted.value || showLeadCapture.value) return
  const userMessages = messages.value.filter(m => m.role === 'user').length
  if (userMessages >= 3) {
    showLeadCapture.value = true
  }
}

const useSuggestion = (suggestion) => {
  inputMessage.value = suggestion
  sendMessage()
}

const formatSourceType = (type) => {
  const types = {
    product: 'Producto',
    service: 'Servicio',
    promotion: 'Promocion',
    faq: 'FAQ',
    location: 'Ubicacion',
    about: 'Acerca de',
    custom: 'Personalizado',
    restaurant_category: 'Categoria',
    restaurant_product: 'Producto',
    social_network: 'Red Social',
    appointment: 'Cita',
    appointment_exception: 'Excepcion de Cita',
  }
  return types[type] || type
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const formatTime = (timestamp) => {
  const date = new Date(timestamp)
  return date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })
}

const shouldShowCta = (content, serverIntentCta) => {
  const intentCta = serverIntentCta || localIntentCta.value

  if (!intentCta || typeof intentCta !== 'object') {
    return null
  }

  const intentMap = [
    { intent: 'appointment', keywords: ['reserva', 'agendar', 'cita', 'turno', 'horario', 'disponible', 'agenda'] },
    { intent: 'purchase', keywords: ['precio', 'cost', 'comprar', 'venta', 'producto', 'cuanto cuesta', 'cuánto vale'] },
    { intent: 'contact', keywords: ['contacto', 'telefono', 'email', 'correo', 'hablar', 'comunicar'] },
    { intent: 'support', keywords: ['ayuda', 'soporte', 'problema', 'error', 'no funciona', 'ayudame'] },
  ]

  const lowerContent = content.toLowerCase()

  for (const item of intentMap) {
    if (item.keywords.some(kw => lowerContent.includes(kw))) {
      const intentConfig = intentCta[item.intent]
      if (intentConfig && typeof intentConfig === 'object' && intentConfig.enabled && intentConfig.url) {
        return intentConfig
      }
    }
  }

  return null
}

const openUrl = (url) => {
  if (url.startsWith('http://') || url.startsWith('https://')) {
    window.open(url, '_blank')
  } else {
    window.location.href = url
  }
}

const checkAvailability = () => {
  fetch(`/m/${props.businessSlug}/ai-chatbot/settings`)
    .then((res) => res.json())
    .then((data) => {
      isAvailable.value = data.available === true
      if (data.chatbot_name) {
        localChatbotName.value = data.chatbot_name
      }
      if (data.chatbot_avatar) {
        localChatbotAvatar.value = data.chatbot_avatar
      }
      if (data.initial_suggestions && Array.isArray(data.initial_suggestions)) {
        localSuggestions.value = data.initial_suggestions
      } else {
        localSuggestions.value = []
      }
      if (data.expandable_responses !== undefined) {
        localExpandableResponses.value = data.expandable_responses
      }
      if (data.show_citations !== undefined) {
        localShowCitations.value = data.show_citations
      }
      if (data.cta_settings) {
        if (data.cta_settings.intent_cta && typeof data.cta_settings.intent_cta === 'object') {
          localIntentCta.value = data.cta_settings.intent_cta
        }
      }
      if (data.intent_cta && typeof data.intent_cta === 'object') {
        localIntentCta.value = data.intent_cta
      }
      if (data.lead_capture && data.lead_capture.enabled) {
        leadCaptureEnabled.value = true
        leadCaptureTitle.value = data.lead_capture.title || '¿Te gustaría recibir noticias sobre nosotros?'
        leadCaptureDescription.value = data.lead_capture.description || 'Déjanos tu correo y te mantendremos informado.'
      }
    })
    .catch(() => {
      isAvailable.value = false
    })
}

watch(isOpen, (open) => {
  if (open) {
    loadHistory()
  }
})

watch(() => messages.value.length, () => {
  if (isOpen.value) {
    checkLeadCaptureTrigger()
  }
})

onMounted(() => {
  checkAvailability()
})
</script>

<style scoped lang="less">
.ai-chat-widget {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chat-bubble {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  transition: transform 0.2s, box-shadow 0.2s;
  position: relative;

  &:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  }

  .bubble-icon {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .bubble-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
  }

  .bubble-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #dc3545;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
  }
}

.chat-window {
  position: absolute;
  bottom: 70px;
  right: 0;
  width: 380px;
  max-width: calc(100vw - 40px);
  height: 520px;
  max-height: calc(100vh - 100px);
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.chat-header {
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;

  .chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .chat-avatar {
    width: 42px;
    height: 42px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    overflow: hidden;
  }

  .chat-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .chat-title {
    font-weight: 600;
    font-size: 1rem;
  }

  .chat-status {
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 0.9;

    .status-dot {
      width: 8px;
      height: 8px;
      background: #4ade80;
      border-radius: 50%;
    }
  }

  .chat-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.2s;

    &:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  }

  .chat-header-actions {
    display: flex;
    gap: 8px;
  }

  .chat-action-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.2s;

    &:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  }
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: #f8f9fa;

  &::-webkit-scrollbar {
    width: 6px;
  }

  &::-webkit-scrollbar-track {
    background: transparent;
  }

  &::-webkit-scrollbar-thumb {
    background: #ced4da;
    border-radius: 3px;
  }

  .chat-empty {
    text-align: center;
    color: #6c757d;
    padding: 40px 20px;

    i {
      font-size: 3rem;
      margin-bottom: 12px;
      opacity: 0.3;
    }

    p {
      font-size: 0.9rem;
      margin: 0;
      line-height: 1.5;
    }

    .suggestions-container {
      margin-top: 16px;

      .suggestions-title {
        font-size: 0.8rem;
        margin-bottom: 10px;
        color: #6c757d;
      }

      .suggestion-btn {
        background: transparent;
        border: 1px solid;
        border-radius: 20px;
        padding: 8px 16px;
        margin: 4px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;

        &:hover {
          background: rgba(59, 130, 246, 0.1);
        }
      }
    }
  }
}

.chat-message {
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;

  &.user {
    align-items: flex-end;

    .message-content {
      background: #0d6efd;
      color: #fff;
      border-radius: 18px 18px 4px 18px;
    }
  }

  &.assistant {
    align-items: flex-start;

    .message-content {
      background: #fff;
      color: #212529;
      border-radius: 18px 18px 18px 4px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
  }

  .message-content {
    max-width: 80%;
    padding: 10px 16px;
    font-size: 0.9rem;
    line-height: 1.5;
    word-wrap: break-word;
    white-space: pre-wrap;
    word-break: break-word;

    &.typing {
      display: flex;
      gap: 4px;
      padding: 14px 18px;

      span {
        width: 8px;
        height: 8px;
        background: #6c757d;
        border-radius: 50%;
        animation: typing 1.4s infinite;

        &:nth-child(2) {
          animation-delay: 0.2s;
        }

        &:nth-child(3) {
          animation-delay: 0.4s;
        }
      }
    }

    .expand-btn {
      background: none;
      border: none;
      color: #0d6efd;
      font-size: 0.85rem;
      cursor: pointer;
      padding: 4px 0;
      margin-top: 4px;

      &:hover {
        text-decoration: underline;
      }
    }
  }

  .message-time {
    font-size: 0.7rem;
    color: #adb5bd;
    margin-top: 4px;
    padding: 0 4px;
  }

  .message-sources {
    margin-top: 8px;
    max-width: 80%;
    background: rgba(0, 0, 0, 0.03);
    border-radius: 8px;
    overflow: hidden;
    font-size: 0.75rem;

    .sources-header {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      cursor: pointer;
      color: #6c757d;
      background: rgba(0, 0, 0, 0.02);

      &:hover {
        background: rgba(0, 0, 0, 0.05);
      }
    }

    .sources-list {
      padding: 8px 12px;
      border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .source-item {
      display: flex;
      gap: 8px;
      margin-bottom: 6px;

      &:last-child {
        margin-bottom: 0;
      }

      .source-type {
        background: #e9ecef;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 500;
        flex-shrink: 0;
      }

      .source-text {
        color: #6c757d;
        line-height: 1.4;
        word-break: break-word;
      }
    }
  }

  .message-cta {
    margin-top: 10px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;

    .cta-btn {
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
      text-decoration: none;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.2s;
      border: 2px solid transparent;

      &:hover {
        opacity: 0.9;
        transform: translateY(-1px);
      }
    }

    .cta-primary {
      color: #fff;
    }

    .cta-secondary {
      background: transparent;

      &:hover {
        background: rgba(0, 0, 0, 0.03);
      }
    }
  }

  .lead-capture {
    margin: 12px 16px;
    padding: 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    text-align: center;

    .lead-capture-icon {
      font-size: 2rem;
      color: #0d6efd;
      margin-bottom: 8px;
    }

    .lead-capture-title {
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 4px;
      color: #212529;
    }

    .lead-capture-desc {
      font-size: 0.8rem;
      color: #6c757d;
      margin-bottom: 12px;
    }

    .lead-capture-form {
      display: flex;
      gap: 8px;
      margin-bottom: 8px;

      .lead-input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        font-size: 0.85rem;
        outline: none;

        &:focus {
          border-color: #0d6efd;
        }
      }

      .lead-submit {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }
    }

    .lead-skip {
      background: none;
      border: none;
      font-size: 0.75rem;
      color: #6c757d;
      cursor: pointer;
      text-decoration: underline;
    }
  }

  .lead-capture-success {
    margin: 12px 16px;
    padding: 12px;
    background: #d4edda;
    border-radius: 8px;
    text-align: center;
    color: #155724;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    i {
      font-size: 1.2rem;
    }
  }
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-4px);
  }
}

.chat-input {
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e9ecef;
  display: flex;
  gap: 10px;

  input {
    flex: 1;
    border: 1px solid #dee2e6;
    border-radius: 24px;
    padding: 12px 18px;
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s;

    &:focus {
      border-color: #0d6efd;
    }

    &:disabled {
      background: #e9ecef;
      cursor: not-allowed;
    }

    &::placeholder {
      color: #adb5bd;
    }
  }

  .send-btn {
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.2s;

    &:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    &:not(:disabled):hover {
      transform: scale(1.05);
    }
  }
}

@media (max-width: 480px) {
  .ai-chat-widget {
    bottom: 16px;
    right: 16px;
  }

  .chat-window {
    width: calc(100vw - 32px);
    height: calc(100vh - 120px);
    max-height: 600px;
  }
}

// Dark theme
.ai-chat-widget.dark {
  .chat-window {
    background: #1a1a2e;
    border-color: #3a3a5a;
  }

  .chat-header {
    color: #fff;
  }

  .chat-messages {
    background: #16162a;

    .chat-empty {
      color: #9ca3af;

      i {
        opacity: 0.5;
      }
    }
  }

  .chat-message {
    &.assistant {
      .message-content {
        background: #2a2a4a;
        color: #e5e5e5;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
      }
    }

    &.user {
      .message-content {
        background: var(--widget-color, #3B82F6);
        color: #fff;
      }
    }

    .message-time {
      color: #6b7280;
    }
  }

  .chat-input {
    background: #1a1a2e;
    border-top-color: #3a3a5a;

    input {
      background: #2a2a4a;
      border-color: #3a3a5a;
      color: #e5e5e5;

      &:focus {
        border-color: var(--widget-color, #3B82F6);
      }

      &::placeholder {
        color: #9ca3af;
      }
    }
  }
}
</style>
