<template>
  <div class="preview-tab">
    <div class="preview-container">
      <div class="preview-header">
        <i class="bi bi-info-circle me-2"></i>
        Vista previa del widget de chat
      </div>

      <div v-if="!settings?.is_enabled" class="alert alert-warning mb-0">
        <i class="bi bi-exclamation-triangle me-2"></i>
        El chatbot está desactivado. Actívalo en la pestaña de Configuración para ver la vista previa.
      </div>

      <div v-else class="chat-widget-preview" :class="themeClass">
        <div class="chat-bubble" :style="{ backgroundColor: settings?.widget_color || '#3B82F6' }">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C6.48 2 2 5.58 2 10c0 1.82.62 3.49 1.64 4.83L2 22l4.17-.64A9.93 9.93 0 0012 22c5.52 0 10-3.58 10-8s-4.48-12-10-12z" fill="white"/>
            <circle cx="8" cy="10" r="1.5" :fill="settings?.widget_color || '#3B82F6'"/>
            <circle cx="12" cy="10" r="1.5" :fill="settings?.widget_color || '#3B82F6'"/>
            <circle cx="16" cy="10" r="1.5" :fill="settings?.widget_color || '#3B82F6'"/>
          </svg>
        </div>

        <div class="chat-window" :style="{ borderColor: settings?.widget_color || '#3B82F6' }">
          <div class="chat-header" :style="{ backgroundColor: settings?.widget_color || '#3B82F6' }">
            <div class="chat-header-info">
              <div class="chat-avatar">
                <i class="bi bi-robot"></i>
              </div>
              <div>
                <div class="chat-title">Asistente {{ business?.name || '' }}</div>
                <div class="chat-status">
                  <span class="status-dot"></span> En línea
                </div>
              </div>
            </div>
            <button class="chat-close-btn">
              <i class="bi bi-x"></i>
            </button>
          </div>

          <div class="chat-messages" ref="messagesContainer">
            <div v-if="messages.length === 0" class="chat-empty">
              <i class="bi bi-chat-dots"></i>
              <p>¡Hola! Soy el asistente virtual de {{ business?.name }}. ¿En qué puedo ayudarte?</p>
            </div>

            <div
              v-for="(msg, index) in messages"
              :key="index"
              class="chat-message"
              :class="msg.role"
            >
              <div class="message-content">
                {{ msg.content }}
              </div>
            </div>

            <div v-if="isTyping" class="chat-message assistant">
              <div class="message-content typing">
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>
          </div>

          <div class="chat-input">
            <input
              type="text"
              v-model="inputMessage"
              :placeholder="settings?.is_enabled ? 'Escribe un mensaje...' : 'Chatbot desactivado'"
              :disabled="!settings?.is_enabled || sending"
              @keypress.enter="sendMessage"
            />
            <button
              class="send-btn"
              :style="{ backgroundColor: settings?.widget_color || '#3B82F6' }"
              :disabled="!inputMessage.trim() || sending"
              @click="sendMessage"
            >
              <i v-if="sending" class="bi bi-hourglass-split"></i>
              <i v-else class="bi bi-send"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="preview-footer">
        <small class="text-muted">
          <i class="bi bi-lightbulb me-1"></i>
          Esta es una vista previa. El widget aparecerá en la esquina inferior derecha del minisite.
        </small>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  business: Object,
  settings: Object,
})

const messages = ref([])
const inputMessage = ref('')
const sending = ref(false)
const isTyping = ref(false)
const messagesContainer = ref(null)

const sessionId = ref('preview-' + Math.random().toString(36).substring(7))

const themeClass = computed(() => {
  return props.settings?.widget_theme === 'dark' ? 'theme-dark' : 'theme-light'
})

const sendMessage = () => {
  if (!inputMessage.value.trim() || sending.value || !props.settings?.is_enabled) return

  const userMessage = inputMessage.value.trim()
  messages.value.push({
    role: 'user',
    content: userMessage,
  })
  inputMessage.value = ''
  scrollToBottom()

  sending.value = true
  isTyping.value = true

  fetch(`/m/${props.business?.slug}/ai-chatbot/chat`, {
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
      isTyping.value = false
      sending.value = false

      if (data.success && data.message) {
        messages.value.push({
          role: 'assistant',
          content: data.message,
        })
      } else {
        messages.value.push({
          role: 'assistant',
          content: data.message || 'Disculpa, estoy teniendo problemas para responder.',
        })
      }
      scrollToBottom()
    })
    .catch((err) => {
      isTyping.value = false
      sending.value = false
      messages.value.push({
        role: 'assistant',
        content: 'Disculpa, estoy teniendo problemas para responder. Intenta de nuevo.',
      })
      scrollToBottom()
    })
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}
</script>

<style lang="less" scoped>
.preview-tab {
  .preview-container {
    max-width: 500px;
    margin: 0 auto;
  }

  .preview-header {
    background: #e7f1ff;
    border: 1px solid #b6d7ff;
    border-radius: 8px 8px 0 0;
    padding: 12px 16px;
    font-weight: 500;
    color: #0d6efd;
  }

  .preview-footer {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-top: none;
    border-radius: 0 0 8px 8px;
    padding: 12px 16px;
    text-align: center;
  }

  .chat-widget-preview {
    position: relative;
    border: 1px solid #e9ecef;
    border-top: none;
    background: #fff;
    height: 450px;
    display: flex;
    align-items: flex-end;
    padding: 16px;
    gap: 12px;
  }

  .chat-bubble {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.2s;

    &:hover {
      transform: scale(1.05);
    }
  }

  .chat-window {
    flex: 1;
    max-width: 350px;
    height: 400px;
    border: 2px solid;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    background: #fff;
  }

  .chat-header {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #fff;

    .chat-header-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .chat-avatar {
      width: 36px;
      height: 36px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
    }

    .chat-title {
      font-weight: 600;
      font-size: 0.9rem;
    }

    .chat-status {
      font-size: 0.75rem;
      display: flex;
      align-items: center;
      gap: 4px;
      opacity: 0.9;

      .status-dot {
        width: 8px;
        height: 8px;
        background: #4ade80;
        border-radius: 50%;
      }
    }

    .chat-close-btn {
      background: none;
      border: none;
      color: #fff;
      font-size: 1.25rem;
      cursor: pointer;
      opacity: 0.8;
      padding: 4px;

      &:hover {
        opacity: 1;
      }
    }
  }

  .chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8f9fa;

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
      }
    }
  }

  .chat-message {
    margin-bottom: 12px;
    display: flex;

    &.user {
      justify-content: flex-end;

      .message-content {
        background: #0d6efd;
        color: #fff;
        border-radius: 16px 16px 4px 16px;
      }
    }

    &.assistant {
      justify-content: flex-start;

      .message-content {
        background: #fff;
        color: #212529;
        border-radius: 16px 16px 16px 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);

        &.typing {
          display: flex;
          gap: 4px;
          padding: 12px 16px;

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
      }
    }

    .message-content {
      max-width: 80%;
      padding: 10px 14px;
      font-size: 0.9rem;
      line-height: 1.4;
      white-space: pre-wrap;
      word-break: break-word;
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
    gap: 8px;

    input {
      flex: 1;
      border: 1px solid #dee2e6;
      border-radius: 24px;
      padding: 10px 16px;
      font-size: 0.9rem;
      outline: none;

      &:focus {
        border-color: #0d6efd;
      }

      &:disabled {
        background: #e9ecef;
        cursor: not-allowed;
      }
    }

    .send-btn {
      width: 42px;
      height: 42px;
      border: none;
      border-radius: 50%;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: opacity 0.2s;

      &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }

      &:not(:disabled):hover {
        opacity: 0.9;
      }
    }
  }

  // Dark theme styles
  &.theme-dark {
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
}
</style>
