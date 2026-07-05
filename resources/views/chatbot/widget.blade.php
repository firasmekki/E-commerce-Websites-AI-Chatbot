@if(auth()->check())
<div id="chatbot-widget" class="fixed bottom-4 right-4 z-50">
    <button
        type="button"
        id="chatbot-launcher"
        class="flex items-center gap-2 rounded-full bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-xl transition hover:bg-slate-700"
        onclick="openChatbot()"
    >
        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
        Assistant IA
    </button>

    <div id="chatbot-panel" class="hidden w-[calc(100vw-2rem)] max-w-sm overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-900 px-4 py-3 text-white">
            <div>
                <h2 class="text-sm font-bold">Assistant IA</h2>
                <p class="text-xs text-slate-300">Produits, commandes et statistiques</p>
            </div>
            <button type="button" class="rounded-md p-1 text-slate-300 transition hover:bg-white/10 hover:text-white" onclick="closeChatbot()" aria-label="Reduire le chat">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="hidden h-80 space-y-3 overflow-y-auto bg-slate-50 p-4" id="chatbot-messages">
            <div class="rounded-lg border border-slate-200 bg-white p-3 text-sm leading-6 text-slate-700 shadow-sm">
                👋 <strong>Bonjour ! Bienvenue sur NextCommerce.</strong><br><br>Je suis votre assistant virtuel. Je peux vous renseigner sur notre catalogue de produits, les catégories, l'état des stocks, votre panier ou vos commandes.<br><br>👉 Tapez <strong>"aide"</strong> à tout moment pour découvrir mes rubriques !
            </div>
        </div>

        <div class="hidden border-t border-slate-200 bg-white p-3" id="chatbot-input">
            <div class="flex gap-2">
                <input type="text" id="chatbot-message" class="form-field" placeholder="Tapez votre message..." onkeypress="handleKeyPress(event)">
                <button class="btn-primary px-3" onclick="sendMessage()">Envoyer</button>
            </div>
        </div>
    </div>
</div>

<script>
let isChatHistoryLoaded = false;

function openChatbot() {
    document.getElementById('chatbot-launcher').classList.add('hidden');
    document.getElementById('chatbot-panel').classList.remove('hidden');
    document.getElementById('chatbot-messages').classList.remove('hidden');
    document.getElementById('chatbot-input').classList.remove('hidden');

    if (!isChatHistoryLoaded) {
        loadChatHistory();
        isChatHistoryLoaded = true;
    }

    document.getElementById('chatbot-message').focus();
}

function closeChatbot() {
    document.getElementById('chatbot-panel').classList.add('hidden');
    document.getElementById('chatbot-launcher').classList.remove('hidden');
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

function sendMessage() {
    const messageInput = document.getElementById('chatbot-message');
    const message = messageInput.value.trim();

    if (!message) return;

    addMessage(message, 'user');
    messageInput.value = '';

    fetch('{{ route('chatbot.chat') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            addMessage(data.reply, 'bot');
        } else {
            addMessage('Desole, une erreur est survenue.', 'bot');
        }
    })
    .catch(() => {
        addMessage('Erreur de connexion.', 'bot');
    });
}

function formatMessageText(text) {
    if (!text) return '';
    // Escape HTML to prevent XSS
    let escaped = text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

    // Convert newlines to <br>
    escaped = escaped.replace(/\n/g, '<br>');

    // Convert bold **text** to <strong>text</strong>
    escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

    // Convert italic _text_ to <em>text</em>
    escaped = escaped.replace(/_(.*?)_/g, '<em>$1</em>');

    // Add extra spaces for bullet list alignment
    escaped = escaped.replace(/• /g, '•&nbsp;');
    escaped = escaped.replace(/🔹 /g, '🔹&nbsp;');

    // Convert markdown links [title](url) to styled anchor tags
    escaped = escaped.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="font-semibold underline text-sky-600 hover:text-sky-800 transition">$1</a>');

    return escaped;
}

function addMessage(text, sender) {
    const messagesDiv = document.getElementById('chatbot-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = sender === 'user' ? 'flex justify-end' : 'flex justify-start';

    const bubbleClass = sender === 'user'
        ? 'bg-slate-900 text-white'
        : 'border border-slate-200 bg-white text-slate-700';

    const bubble = document.createElement('span');
    bubble.className = `max-w-[82%] rounded-lg px-3 py-2 text-sm leading-6 shadow-sm ${bubbleClass}`;
    bubble.style.whiteSpace = 'pre-wrap';
    bubble.innerHTML = sender === 'user' ? formatMessageText(text) : formatMessageText(text);

    messageDiv.appendChild(bubble);
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function loadChatHistory() {
    fetch('{{ route('chatbot.history') }}')
        .then(response => response.json())
        .then(data => {
            data.reverse().forEach(chat => {
                addMessage(chat.user_message, 'user');
                addMessage(chat.bot_response, 'bot');
            });
        })
        .catch(error => console.error('Error loading chat history:', error));
}
</script>
@endif
