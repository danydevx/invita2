(function() {
    'use strict';

    const script = document.currentScript;
    if (!script) return;

    const PUBLIC_KEY = script.dataset.publicKey;
    const POSITION = script.dataset.position || 'right';
    const SCRIPT_URL = script.src;
    const API_BASE = SCRIPT_URL.substring(0, SCRIPT_URL.lastIndexOf('/'));
    const SHOW_INTENT_BUTTONS = script.dataset.intentButtons !== 'false';

    if (!PUBLIC_KEY) {
        console.error('AI Chat Widget: No public key provided');
        return;
    }

    const STORAGE_KEY_SESSION = 'ai_chat_widget_session';
    const STORAGE_KEY_MESSAGES = 'ai_chat_widget_messages';
    const CACHE_KEY_SETTINGS = 'ai_chat_widget_settings';
    const CACHE_TTL = 5 * 60 * 1000;

    let state = {
        isOpen: false,
        isAvailable: true,
        messages: [],
        inputMessage: '',
        sending: false,
        isTyping: false,
        isStreaming: false,
        unreadCount: 0,
        sessionId: localStorage.getItem(STORAGE_KEY_SESSION) || generateSessionId(),
        chatbotName: 'Asistente Virtual',
        chatbotAvatar: '',
        widgetColor: '#3B82F6',
        widgetTheme: 'light',
        initialSuggestions: [],
        localExpandableResponses: true,
        localShowCitations: true,
        localIntentCta: null,
        intentButtons: [],
        activeIntent: null,
        leadCaptureEnabled: false,
        leadCaptureTitle: '',
        leadCaptureDescription: '',
        showLeadCapture: false,
        leadCaptureSubmitted: false,
        leadEmail: '',
        leadName: ''
    };

    let abortController = null;
    let settingsCache = { data: null, timestamp: 0 };

    const INTENT_KEYWORDS = {
        appointment: ['agendar', 'reserva', 'cita', 'turno', 'horario', 'disponible', 'agenda', 'necesito una cita', 'quiero reservar', 'hacer una cita', 'disponibilidad'],
        purchase: ['precio', 'precios', 'cost', 'comprar', 'venta', 'cuanto cuesta', 'cuánto vale', 'cuánto sale', 'productos', 'compras', 'valor', 'tarifa'],
        contact: ['contacto', 'telefono', 'teléfono', 'email', 'correo', 'llamar', 'hablar', 'comunicar', 'contactar', 'comunicarse'],
        support: ['ayuda', 'soporte', 'problema', 'error', 'no funciona', 'ayudame', 'help', 'issue', 'soporte técnico', 'fallo']
    };

    function generateSessionId() {
        const id = 'w_' + Math.random().toString(36).substring(2) + Date.now().toString(36);
        localStorage.setItem(STORAGE_KEY_SESSION, id);
        return id;
    }

    function getApiUrl(path) {
        return API_BASE + path;
    }

    function loadSettings() {
        const cached = settingsCache.data;
        if (cached && (Date.now() - settingsCache.timestamp) < CACHE_TTL) {
            return Promise.resolve(cached);
        }

        return fetch(getApiUrl('/settings'), {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            settingsCache.data = data;
            settingsCache.timestamp = Date.now();
            return data;
        })
        .catch(() => {
            return { error: 'Failed to load settings' };
        });
    }

    function trackEvent(eventType, metadata = {}) {
        fetch(getApiUrl('/event'), {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                event_type: eventType,
                metadata: metadata
            })
        }).catch(() => {});
    }

    function createWidgetContainer() {
        const container = document.createElement('div');
        container.id = 'ai-chat-widget';
        container.innerHTML = getWidgetHTML();
        document.body.appendChild(container);
        attachEventListeners();
        return container;
    }

    function getWidgetHTML() {
        const positionClass = POSITION === 'left' ? 'position-left' : 'position-right';

        return `
        <style>
        #ai-chat-widget * { box-sizing: border-box; margin: 0; padding: 0; }
        #ai-chat-widget { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; position: fixed; bottom: 20px; ${POSITION}: 20px; z-index: 999999; }
        #ai-chat-widget .chat-bubble { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 16px rgba(0,0,0,0.2); transition: transform 0.2s; }
        #ai-chat-widget .chat-bubble:hover { transform: scale(1.05); }
        #ai-chat-widget .bubble-icon { display: flex; align-items: center; justify-content: center; }
        #ai-chat-widget .bubble-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }
        #ai-chat-widget .bubble-badge { position: absolute; top: -4px; right: -4px; background: #dc3545; color: #fff; font-size: 0.75rem; font-weight: 700; min-width: 20px; height: 20px; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 0 6px; }
        #ai-chat-widget .chat-window { position: absolute; bottom: 70px; ${POSITION}: 0; width: 380px; max-width: calc(100vw - 40px); height: 520px; max-height: calc(100vh - 100px); background: #fff; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); display: flex; flex-direction: column; overflow: hidden; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        #ai-chat-widget .chat-header { padding: 16px; display: flex; align-items: center; justify-content: space-between; }
        #ai-chat-widget .chat-header-info { display: flex; align-items: center; gap: 12px; }
        #ai-chat-widget .chat-avatar { width: 42px; height: 42px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; overflow: hidden; }
        #ai-chat-widget .chat-avatar-img { width: 100%; height: 100%; object-fit: cover; }
        #ai-chat-widget .chat-title { font-weight: 600; font-size: 1rem; color: #fff; }
        #ai-chat-widget .chat-status { font-size: 0.8rem; display: flex; align-items: center; gap: 6px; opacity: 0.9; color: #fff; }
        #ai-chat-widget .status-dot { width: 8px; height: 8px; background: #4ade80; border-radius: 50%; }
        #ai-chat-widget .chat-header-actions { display: flex; gap: 8px; }
        #ai-chat-widget .chat-close-btn, #ai-chat-widget .chat-action-btn { background: rgba(255,255,255,0.2); border: none; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1rem; transition: background 0.2s; }
        #ai-chat-widget .chat-close-btn:hover, #ai-chat-widget .chat-action-btn:hover { background: rgba(255,255,255,0.3); }
        #ai-chat-widget .chat-messages { flex: 1; overflow-y: auto; padding: 16px; background: #f8f9fa; }
        #ai-chat-widget .chat-messages::-webkit-scrollbar { width: 6px; }
        #ai-chat-widget .chat-messages::-webkit-scrollbar-track { background: transparent; }
        #ai-chat-widget .chat-messages::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 3px; }
        #ai-chat-widget .chat-empty { text-align: center; color: #6c757d; padding: 40px 20px; }
        #ai-chat-widget .chat-empty i { font-size: 3rem; margin-bottom: 12px; opacity: 0.3; }
        #ai-chat-widget .chat-empty p { font-size: 0.9rem; margin: 0; line-height: 1.5; }
        #ai-chat-widget .suggestions-container { margin-top: 16px; }
        #ai-chat-widget .suggestions-title { font-size: 0.8rem; margin-bottom: 10px; color: #6c757d; }
        #ai-chat-widget .suggestion-btn { background: transparent; border: 1px solid; border-radius: 20px; padding: 8px 16px; margin: 4px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; }
        #ai-chat-widget .suggestion-btn:hover { background: rgba(59,130,246,0.1); }
        #ai-chat-widget .intent-buttons { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 16px; }
        #ai-chat-widget .intent-btn { display: flex; align-items: center; gap: 6px; padding: 10px 16px; border-radius: 20px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; background: transparent; border: 1px solid; }
        #ai-chat-widget .intent-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        #ai-chat-widget .intent-btn i { font-size: 1rem; }
        #ai-chat-widget .intent-btn.appointment { border-color: #0d6efd; color: #0d6efd; }
        #ai-chat-widget .intent-btn.purchase { border-color: #198754; color: #198754; }
        #ai-chat-widget .intent-btn.contact { border-color: #6f42c1; color: #6f42c1; }
        #ai-chat-widget .intent-btn.support { border-color: #fd7e14; color: #fd7e14; }
        #ai-chat-widget .chat-message { margin-bottom: 12px; display: flex; flex-direction: column; }
        #ai-chat-widget .chat-message.user { align-items: flex-end; }
        #ai-chat-widget .chat-message.assistant { align-items: flex-start; }
        #ai-chat-widget .message-content { max-width: 80%; padding: 10px 16px; font-size: 0.9rem; line-height: 1.5; word-wrap: break-word; white-space: pre-wrap; word-break: break-word; }
        #ai-chat-widget .chat-message.user .message-content { background: #0d6efd; color: #fff; border-radius: 18px 18px 4px 18px; }
        #ai-chat-widget .chat-message.assistant .message-content { background: #fff; color: #212529; border-radius: 18px 18px 18px 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        #ai-chat-widget .message-content.typing { display: flex; gap: 4px; padding: 14px 18px; }
        #ai-chat-widget .message-content.typing span { width: 8px; height: 8px; background: #6c757d; border-radius: 50%; animation: typing 1.4s infinite; }
        #ai-chat-widget .message-content.typing span:nth-child(2) { animation-delay: 0.2s; }
        #ai-chat-widget .message-content.typing span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-4px); } }
        #ai-chat-widget .expand-btn { background: none; border: none; color: #0d6efd; font-size: 0.85rem; cursor: pointer; padding: 4px 0; margin-top: 4px; }
        #ai-chat-widget .expand-btn:hover { text-decoration: underline; }
        #ai-chat-widget .message-sources { margin-top: 8px; max-width: 80%; background: rgba(0,0,0,0.03); border-radius: 8px; overflow: hidden; font-size: 0.75rem; }
        #ai-chat-widget .sources-header { display: flex; align-items: center; gap: 6px; padding: 6px 12px; cursor: pointer; color: #6c757d; background: rgba(0,0,0,0.02); }
        #ai-chat-widget .sources-header:hover { background: rgba(0,0,0,0.05); }
        #ai-chat-widget .sources-list { padding: 8px 12px; border-top: 1px solid rgba(0,0,0,0.05); }
        #ai-chat-widget .source-item { display: flex; gap: 8px; margin-bottom: 6px; }
        #ai-chat-widget .source-item:last-child { margin-bottom: 0; }
        #ai-chat-widget .source-type { background: #e9ecef; padding: 2px 6px; border-radius: 4px; font-weight: 500; flex-shrink: 0; }
        #ai-chat-widget .source-text { color: #6c757d; line-height: 1.4; word-break: break-word; }
        #ai-chat-widget .message-cta { margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap; }
        #ai-chat-widget .cta-btn { padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; text-decoration: none; cursor: pointer; transition: opacity 0.2s, transform 0.2s; border: 2px solid transparent; }
        #ai-chat-widget .cta-btn:hover { opacity: 0.9; transform: translateY(-1px); }
        #ai-chat-widget .cta-primary { color: #fff; }
        #ai-chat-widget .message-time { font-size: 0.7rem; color: #adb5bd; margin-top: 4px; padding: 0 4px; }
        #ai-chat-widget .lead-capture { margin: 12px 16px; padding: 16px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; text-align: center; }
        #ai-chat-widget .lead-capture-icon { font-size: 2rem; color: #0d6efd; margin-bottom: 8px; }
        #ai-chat-widget .lead-capture-title { font-weight: 600; font-size: 0.95rem; margin-bottom: 4px; color: #212529; }
        #ai-chat-widget .lead-capture-desc { font-size: 0.8rem; color: #6c757d; margin-bottom: 12px; }
        #ai-chat-widget .lead-capture-form { display: flex; gap: 8px; margin-bottom: 8px; }
        #ai-chat-widget .lead-input { flex: 1; padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 20px; font-size: 0.85rem; outline: none; }
        #ai-chat-widget .lead-input:focus { border-color: #0d6efd; }
        #ai-chat-widget .lead-submit { width: 36px; height: 36px; border: none; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        #ai-chat-widget .lead-skip { background: none; border: none; font-size: 0.75rem; color: #6c757d; cursor: pointer; text-decoration: underline; }
        #ai-chat-widget .lead-capture-success { margin: 12px 16px; padding: 12px; background: #d4edda; border-radius: 8px; text-align: center; color: #155724; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 8px; }
        #ai-chat-widget .chat-input { padding: 12px 16px; background: #fff; border-top: 1px solid #e9ecef; display: flex; gap: 10px; }
        #ai-chat-widget .chat-input input { flex: 1; border: 1px solid #dee2e6; border-radius: 24px; padding: 12px 18px; font-size: 0.9rem; outline: none; transition: border-color 0.2s; }
        #ai-chat-widget .chat-input input:focus { border-color: #0d6efd; }
        #ai-chat-widget .chat-input input:disabled { background: #e9ecef; cursor: not-allowed; }
        #ai-chat-widget .chat-input input::placeholder { color: #adb5bd; }
        #ai-chat-widget .send-btn { width: 44px; height: 44px; border: none; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: opacity 0.2s, transform 0.2s; }
        #ai-chat-widget .send-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        #ai-chat-widget .send-btn:not(:disabled):hover { transform: scale(1.05); }
        @media (max-width: 480px) { #ai-chat-widget { bottom: 16px; ${POSITION === 'left' ? 'left' : 'right'}: 16px; } #ai-chat-widget .chat-window { width: calc(100vw - 32px); height: calc(100vh - 120px); max-height: 600px; } }
        #ai-chat-widget.dark .chat-window { background: #1a1a2e; border-color: #3a3a5a; }
        #ai-chat-widget.dark .chat-header { color: #fff; }
        #ai-chat-widget.dark .chat-messages { background: #16162a; }
        #ai-chat-widget.dark .chat-messages .chat-empty { color: #9ca3af; }
        #ai-chat-widget.dark .chat-message.assistant .message-content { background: #2a2a4a; color: #e5e5e5; box-shadow: 0 1px 2px rgba(0,0,0,0.3); }
        #ai-chat-widget.dark .chat-input { background: #1a1a2e; border-top-color: #3a3a5a; }
        #ai-chat-widget.dark .chat-input input { background: #2a2a4a; border-color: #3a3a5a; color: #e5e5e5; }
        #ai-chat-widget.dark .chat-input input:focus { border-color: var(--widget-color, #3B82F6); }
        #ai-chat-widget.position-left { left: 20px; right: auto; }
        #ai-chat-widget.position-left .chat-window { left: 0; right: auto; }
        @media (max-width: 480px) { #ai-chat-widget.position-left { left: 16px; } }
        </style>
        <div class="chat-bubble" id="chatBubble">
            <div class="bubble-icon" id="bubbleIcon"></div>
            <div class="bubble-badge" id="bubbleBadge" style="display: none;">0</div>
        </div>
        <div class="chat-window" id="chatWindow" style="display: none;">
            <div class="chat-header" id="chatHeader"></div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input" id="chatInput">
                <input type="text" id="messageInput" placeholder="Escribe un mensaje..." />
                <button class="send-btn" id="sendBtn"><i class="bi bi-send"></i></button>
            </div>
        </div>`;
    }

    function attachEventListeners() {
        const bubble = document.getElementById('chatBubble');
        const window = document.getElementById('chatWindow');
        const sendBtn = document.getElementById('sendBtn');
        const messageInput = document.getElementById('messageInput');

        bubble.addEventListener('click', openChat);
        sendBtn.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    }

    function openChat() {
        state.isOpen = true;
        state.unreadCount = 0;
        updateBubbleBadge();
        document.getElementById('chatWindow').style.display = 'flex';
        document.getElementById('chatBubble').style.display = 'none';
        loadHistory();
        trackEvent('open', { session_id: state.sessionId });
        scrollToBottom();
    }

    function closeChat() {
        state.isOpen = false;
        document.getElementById('chatWindow').style.display = 'none';
        document.getElementById('chatBubble').style.display = 'flex';
    }

    function loadHistory() {
        const saved = sessionStorage.getItem(STORAGE_KEY_MESSAGES + '_' + state.sessionId);
        if (saved) {
            try {
                state.messages = JSON.parse(saved);
                renderMessages();
            } catch (e) {
                state.messages = [];
            }
        }
    }

    function saveMessages() {
        sessionStorage.setItem(
            STORAGE_KEY_MESSAGES + '_' + state.sessionId,
            JSON.stringify(state.messages)
        );
    }

    function renderMessages() {
        const container = document.getElementById('chatMessages');
        if (!container) return;

        if (state.messages.length === 0) {
            container.innerHTML = getEmptyStateHTML();
        } else {
            container.innerHTML = state.messages.map((msg, idx) => getMessageHTML(msg, idx)).join('');
            bindMessageEvents(container);
        }
    }

    function getEmptyStateHTML() {
        let html = '<div class="chat-empty"><i class="bi bi-chat-dots"></i>';

        if (SHOW_INTENT_BUTTONS && state.intentButtons.length > 0) {
            html += '<p style="margin-bottom:8px;font-size:0.9rem;">¿En qué puedo ayudarte?</p>';
            html += '<div class="intent-buttons">';
            state.intentButtons.forEach(btn => {
                html += '<button class="intent-btn ' + btn.key + '" data-intent="' + btn.key + '">';
                html += '<i class="bi bi-' + getIconName(btn.icon) + '"></i>';
                html += '<span>' + escHtml(btn.text) + '</span>';
                html += '</button>';
            });
            html += '</div>';
        }

        if (state.initialSuggestions.length > 0) {
            html += '<div class="suggestions-container"><p class="suggestions-title">O prueba con:</p>';
            html += state.initialSuggestions.map(s => '<button class="suggestion-btn" style="border-color:' + state.widgetColor + ';color:' + state.widgetColor + ';">' + s + '</button>').join('');
            html += '</div>';
        } else if (!SHOW_INTENT_BUTTONS || state.intentButtons.length === 0) {
            html += '<p>¡Hola! Soy ' + state.chatbotName + '. ¿En qué puedo ayudarte?</p>';
        }

        html += '</div>';
        return html;
    }

    function getIconName(icon) {
        const iconMap = {
            'calendar': 'calendar-event',
            'bag': 'bag',
            'telephone': 'telephone',
            'question-circle': 'question-circle',
            'chat': 'chat-dots',
            'calendar-event': 'calendar-event',
        };
        return iconMap[icon] || 'chat-dots';
    }

    function getMessageHTML(msg, idx) {
        const isUser = msg.role === 'user';
        const time = msg.timestamp ? formatTime(msg.timestamp) : '';

        let html = '<div class="chat-message ' + msg.role + '">';
        html += '<div class="message-content' + (msg.isLong && !msg.expanded ? '' : '') + '">';

        if (msg.isLong && !msg.expanded) {
            html += escHtml(msg.preview || msg.content.substring(0, 300));
            html += '<button class="expand-btn" data-idx="' + idx + '">Ver más</button>';
        } else {
            html += escHtml(msg.content);
        }
        html += '</div>';

        if (msg.sources && msg.sources.length && state.localShowCitations) {
            html += '<div class="message-sources"><div class="sources-header" data-idx="' + idx + '"><i class="bi bi-link-45deg"></i><span>Fuentes (' + msg.sources.length + ')</span><i class="bi bi-chevron-down"></i></div>';
            html += '<div class="sources-list" style="display:none;">';
            msg.sources.forEach(src => {
                html += '<div class="source-item"><span class="source-type">' + escHtml(src.type) + '</span><span class="source-text">' + escHtml(src.text) + '</span></div>';
            });
            html += '</div></div>';
        }

        if (msg.showCta && msg.showCta.url) {
            html += '<div class="message-cta"><a href="' + escHtml(msg.showCta.url) + '" target="_blank" class="cta-btn cta-primary" style="background-color:' + state.widgetColor + ';">' + escHtml(msg.showCta.text) + '</a></div>';
        }

        if (time) {
            html += '<div class="message-time">' + time + '</div>';
        }

        html += '</div>';
        return html;
    }

    function bindMessageEvents(container) {
        container.querySelectorAll('.expand-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.idx);
                state.messages[idx].expanded = true;
                renderMessages();
            });
        });

        container.querySelectorAll('.sources-header').forEach(header => {
            header.addEventListener('click', () => {
                const list = header.nextElementSibling;
                const icon = header.querySelector('i:last-child');
                if (list.style.display === 'none') {
                    list.style.display = 'block';
                    icon.className = 'bi bi-chevron-up';
                } else {
                    list.style.display = 'none';
                    icon.className = 'bi bi-chevron-down';
                }
            });
        });

        container.querySelectorAll('.suggestion-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                useSuggestion(btn.textContent);
            });
        });

        container.querySelectorAll('.intent-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                selectIntent(btn.dataset.intent);
            });
        });
    }

    function updateBubbleBadge() {
        const badge = document.getElementById('bubbleBadge');
        if (state.unreadCount > 0) {
            badge.textContent = state.unreadCount > 9 ? '9+' : state.unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    function scrollToBottom() {
        const container = document.getElementById('chatMessages');
        if (container) {
            setTimeout(() => container.scrollTop = container.scrollHeight, 10);
        }
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
    }

    function escHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function useSuggestion(suggestion) {
        document.getElementById('messageInput').value = suggestion;
        sendMessage();
    }

    function selectIntent(intentKey) {
        const intentBtn = state.intentButtons.find(b => b.key === intentKey);
        if (!intentBtn) return;

        state.activeIntent = intentKey;

        document.getElementById('messageInput').value = intentBtn.text;
        sendMessage();

        trackEvent('intent_select', { intent: intentKey });
    }

    function sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        if (!message || state.sending) return;

        if (!state.activeIntent) {
            const detectedIntent = detectIntentFromMessage(message);
            if (detectedIntent) {
                state.activeIntent = detectedIntent;
                trackEvent('intent_auto_detected', { intent: detectedIntent });
            }
        }

        input.value = '';
        state.messages.push({
            role: 'user',
            content: message,
            timestamp: new Date().toISOString()
        });
        saveMessages();
        renderMessages();
        scrollToBottom();

        state.sending = true;
        state.isTyping = true;
        state.isStreaming = false;

        const msgIndex = state.messages.length;
        state.messages.push({
            role: 'assistant',
            content: '',
            preview: '',
            isLong: false,
            expanded: false,
            sources: [],
            sourcesExpanded: false,
            showCta: false,
            timestamp: new Date().toISOString()
        });

        renderMessages();

        abortController = new AbortController();

        fetch(getApiUrl('/chat'), {
            method: 'POST',
            signal: abortController.signal,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'text/event-stream',
            },
            body: JSON.stringify({
                message: message,
                session_id: state.sessionId,
                intent: state.activeIntent,
            }),
        })
        .then(async (res) => {
            state.isStreaming = true;
            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop() || '';

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        try {
                            const data = JSON.parse(line.slice(6));
                            if (data.type === 'token') {
                                state.messages[msgIndex].content += data.content;
                                renderMessages();
                                scrollToBottom();
                            } else if (data.type === 'done') {
                                state.messages[msgIndex].content = data.content;
                                state.messages[msgIndex].preview = data.content.substring(0, 300);
                                state.messages[msgIndex].isLong = data.content.length > 300;
                                state.messages[msgIndex].sources = data.sources || [];
                                state.messages[msgIndex].expanded = !state.localExpandableResponses;

                                if (data.show_cta && data.intent_cta) {
                                    state.messages[msgIndex].showCta = data.intent_cta;
                                    trackEvent('cta_shown', { intent: state.activeIntent, trigger: 'intent_flow' });
                                } else {
                                    state.messages[msgIndex].showCta = shouldShowCta(data.content, data.intent_cta);
                                }

                                if (data.intent_cta) {
                                    state.localIntentCta = data.intent_cta;
                                }

                                saveMessages();
                                renderMessages();
                            } else if (data.type === 'error') {
                                state.messages[msgIndex].content = 'Error: ' + data.error;
                            } else if (data.success === false) {
                                state.messages[msgIndex].content = data.message || data.error || 'Disculpa, estoy teniendo problemas para responder.';
                            }
                        } catch (e) {}
                    }
                }
            }
        })
        .catch((err) => {
            if (err.name === 'AbortError') {
                state.messages[msgIndex].content += '\n[Respuesta detenida]';
            } else {
                state.messages[msgIndex].content = 'Disculpa, estoy teniendo problemas para responder. Intenta de nuevo.';
            }
            renderMessages();
        })
        .finally(() => {
            state.isTyping = false;
            state.sending = false;
            state.isStreaming = false;
            abortController = null;
            saveMessages();
            scrollToBottom();
        });
    }

    function detectIntentFromMessage(message) {
        const lower = message.toLowerCase();

        if (state.localIntentCta && typeof state.localIntentCta === 'object') {
            for (const [intent, config] of Object.entries(state.localIntentCta)) {
                if (config && config.enabled && config.keywords) {
                    const keywords = config.keywords.split(',').map(k => k.trim().toLowerCase());
                    if (keywords.some(kw => kw && lower.includes(kw))) {
                        return intent;
                    }
                }
            }
        }

        for (const [intent, keywords] of Object.entries(INTENT_KEYWORDS)) {
            if (keywords.some(kw => lower.includes(kw))) {
                return intent;
            }
        }
        return null;
    }

    function shouldShowCta(content, serverIntentCta) {
        const intentCta = serverIntentCta || state.localIntentCta;
        if (!intentCta || typeof intentCta !== 'object') return null;

        const lowerContent = content.toLowerCase();

        for (const [intent, config] of Object.entries(intentCta)) {
            if (config && config.enabled && config.keywords) {
                const keywords = config.keywords.split(',').map(k => k.trim().toLowerCase());
                if (keywords.some(kw => kw && lowerContent.includes(kw))) {
                    if (config.url) {
                        trackEvent('cta_click', { intent: intent, url: config.url });
                        return config;
                    }
                }
            }
        }

        return null;
    }

    function updateHeader() {
        const header = document.getElementById('chatHeader');
        if (!header) return;

        header.style.backgroundColor = state.widgetColor;
        header.innerHTML = '<div class="chat-header-info"><div class="chat-avatar">' +
            (state.chatbotAvatar ? '<img src="' + state.chatbotAvatar + '" class="chat-avatar-img" />' : '<i class="bi bi-robot"></i>') +
            '</div><div><div class="chat-title">' + state.chatbotName + '</div><div class="chat-status"><span class="status-dot"></span> En línea</div></div></div>' +
            '<div class="chat-header-actions"><button class="chat-close-btn" onclick="window.AIChatWidget.close()"><i class="bi bi-x-lg"></i></button></div>';
    }

    function updateBubbleIcon() {
        const icon = document.getElementById('bubbleIcon');
        if (!icon) return;

        if (state.chatbotAvatar) {
            icon.innerHTML = '<img src="' + state.chatbotAvatar + '" class="bubble-avatar" />';
        } else {
            icon.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 5.58 2 10c0 1.82.62 3.49 1.64 4.83L2 22l4.17-.64A9.93 9.93 0 0012 22c5.52 0 10-3.58 10-8s-4.48-12-10-12z" fill="white"/></svg>';
        }

        document.getElementById('chatBubble').style.backgroundColor = state.widgetColor;
    }

    function init() {
        createWidgetContainer();
        updateHeader();
        updateBubbleIcon();
        trackEvent('load', {});

        loadSettings().then(data => {
            if (data.error) {
                state.isAvailable = false;
                return;
            }

            if (data.chatbot_name) state.chatbotName = data.chatbot_name;
            if (data.chatbot_avatar) state.chatbotAvatar = data.chatbot_avatar;
            if (data.widget_color) state.widgetColor = data.widget_color;
            if (data.widget_theme) state.widgetTheme = data.widget_theme;
            if (data.initial_suggestions && Array.isArray(data.initial_suggestions)) {
                state.initialSuggestions = data.initial_suggestions;
            }
            if (data.expandable_responses !== undefined) state.localExpandableResponses = data.expandable_responses;
            if (data.show_citations !== undefined) state.localShowCitations = data.show_citations;
            if (data.intent_cta) state.localIntentCta = data.intent_cta;
            if (data.intent_buttons && Array.isArray(data.intent_buttons)) {
                state.intentButtons = data.intent_buttons;
            }

            updateHeader();
            updateBubbleIcon();
            renderMessages();
        });
    }

    window.AIChatWidget = {
        open: openChat,
        close: closeChat,
        destroy: function() {
            const widget = document.getElementById('ai-chat-widget');
            if (widget) widget.remove();
        },
        getSession: function() {
            return state.sessionId;
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
