// Workspace Chat Client Controller
let chatPollInterval = null;
let currentChatUsername = localStorage.getItem('eroom_chat_username') || '<?= session()->get('chat_username') ?? '' ?>';
const currentBoardId = '<?= $board['id'] ?>';

function updateChatHeaderUsername() {
    const displayEl = document.getElementById('display-current-username');
    if (displayEl) {
        displayEl.textContent = currentChatUsername || 'Tamu';
    }
}

function toggleChatDrawer() {
    const drawer = document.getElementById('chat-drawer');
    if (!drawer) return;

    if (drawer.classList.contains('hidden')) {
        // Jika belum set nama, minta user isi nama dulu sebelum gabung obrolan
        if (!currentChatUsername) {
            openChatUsernameModal();
            return;
        }

        drawer.classList.remove('hidden');
        updateChatHeaderUsername();
        document.getElementById('chat-badge').classList.add('hidden');
        if (window.lucide) window.lucide.createIcons();

        // Load pesan pertama kali & mulai polling tiap 3 detik
        fetchChatMessages(true);
        if (!chatPollInterval) {
            chatPollInterval = setInterval(() => fetchChatMessages(false), 3000);
        }
    } else {
        drawer.classList.add('hidden');
        if (chatPollInterval) {
            clearInterval(chatPollInterval);
            chatPollInterval = null;
        }
    }
}

function openChatUsernameModal() {
    const modal = document.getElementById('chat-username-modal');
    const input = document.getElementById('input-chat-username');
    if (modal && input) {
        modal.classList.remove('hidden');
        input.value = currentChatUsername || '';
        input.focus();
        if (window.lucide) window.lucide.createIcons();
    }
}

function closeChatUsernameModal() {
    const modal = document.getElementById('chat-username-modal');
    if (modal) modal.classList.add('hidden');
}

function promptChangeUsername() {
    openChatUsernameModal();
}

function saveChatUsername(event) {
    event.preventDefault();
    const input = document.getElementById('input-chat-username');
    const name = input.value.trim();
    if (!name) return;

    currentChatUsername = name;
    localStorage.setItem('eroom_chat_username', name);
    closeChatUsernameModal();
    updateChatHeaderUsername();

    // Buka drawer setelah nama di-set
    const drawer = document.getElementById('chat-drawer');
    if (drawer && drawer.classList.contains('hidden')) {
        toggleChatDrawer();
    }
}

function fetchChatMessages(initial = false) {
    fetch('<?= base_url('/boards/') ?>' + currentBoardId + '/messages', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        const loadingEl = document.getElementById('chat-loading');
        if (loadingEl) loadingEl.remove();

        if (data.success && Array.isArray(data.messages)) {
            renderChatMessages(data.messages, initial);
        }
    })
    .catch(err => console.error('Chat poll error:', err));
}

function renderChatMessages(messages, forceScroll = false) {
    const container = document.getElementById('chat-messages-container');
    const emptyEl = document.getElementById('chat-empty');

    if (!messages.length) {
        if (emptyEl) emptyEl.classList.remove('hidden');
        return;
    }
    if (emptyEl) emptyEl.classList.add('hidden');

    const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 80;

    let html = '';
    messages.forEach(msg => {
        const isMe = currentChatUsername && (msg.sender_name.toLowerCase() === currentChatUsername.toLowerCase());
        
        if (isMe) {
            html += `
                <div class="flex flex-col items-end space-y-1">
                    <span class="text-[10px] font-bold text-slate-400">Anda &bull; ${msg.formatted_time}</span>
                    <div class="clay-btn-primary px-3.5 py-2 max-w-[85%] text-xs font-semibold break-words leading-relaxed rounded-2xl rounded-tr-none">
                        ${msg.message}
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="flex flex-col items-start space-y-1">
                    <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400">${msg.sender_name} &bull; ${msg.formatted_time}</span>
                    <div class="clay-card px-3.5 py-2 max-w-[85%] text-xs font-semibold text-slate-800 dark:text-slate-100 break-words leading-relaxed rounded-2xl rounded-tl-none">
                        ${msg.message}
                    </div>
                </div>
            `;
        }
    });

    container.innerHTML = html;

    if (forceScroll || isNearBottom) {
        container.scrollTop = container.scrollHeight;
    }
}

function handleSendChatMessage(event) {
    event.preventDefault();
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    if (!message) return;

    if (!currentChatUsername) {
        openChatUsernameModal();
        return;
    }

    const submitBtn = document.getElementById('chat-submit-btn');
    if (submitBtn) submitBtn.disabled = true;

    const formData = new FormData();
    formData.append('sender_name', currentChatUsername);
    formData.append('message', message);

    fetch('<?= base_url('/boards/') ?>' + currentBoardId + '/messages', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            fetchChatMessages(true);
        } else {
            alert(data.message || 'Gagal mengirim pesan');
        }
    })
    .catch(err => console.error('Send error:', err))
    .finally(() => {
        if (submitBtn) submitBtn.disabled = false;
        input.focus();
    });
}
