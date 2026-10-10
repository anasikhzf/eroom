<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php
    $listCount = count($lists);
    $cardCount = 0;
    foreach ($lists as $l) {
        $cardCount += isset($l['cards']) ? count($l['cards']) : 0;
    }
?>
<div class="flex-1 flex flex-col overflow-hidden">

    <!-- Workspace Sub-Header / Toolbar (Clay Header) -->
    <div class="clay-card mx-1 sm:mx-2 my-1 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 shrink-0 z-10">
        
        <!-- Board Title & Tags -->
        <div class="flex items-center gap-2.5 sm:gap-4 flex-wrap">
            <div class="w-3.5 h-3.5 rounded-full shadow-sm" style="background-color: <?= esc($board['color']) ?>;"></div>
            <h1 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-white tracking-tight">
                <?= esc($board['title']) ?>
            </h1>

            <div class="h-4 w-px bg-slate-300 dark:bg-white/10 hidden sm:block"></div>

            <!-- Privacy Badge -->
            <?php if (!empty($board['password'])): ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-[11px] font-bold">
                    <i data-lucide="lock" class="w-3 h-3"></i>
                    <span>Terkunci</span>
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[11px] font-bold">
                    <i data-lucide="globe" class="w-3 h-3"></i>
                    <span>Publik</span>
                </span>
            <?php endif; ?>

            <span class="text-xs text-slate-400 dark:text-slate-500 hidden md:inline">
                <?= $listCount ?> Kolom &bull; <?= $cardCount ?> Kartu
            </span>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <!-- Group Chat Button -->
            <button onclick="toggleChatDrawer()"
                    class="clay-btn px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5 relative"
                    title="Obrolan Grup Workspace">
                <i data-lucide="message-square" class="w-3.5 h-3.5 text-indigo-500"></i>
                <span class="hidden sm:inline">Chat Grup</span>
                <span id="chat-badge" class="hidden absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse"></span>
            </button>

            <!-- Share Button -->
            <button onclick="openShareModal()"
                    class="clay-btn px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5"
                    title="Bagikan Tautan Ruang">
                <i data-lucide="share-2" class="w-3.5 h-3.5 text-indigo-500"></i>
                <span class="hidden sm:inline">Bagikan</span>
            </button>

            <!-- Workspace Settings (Change Password/Lock/Color) -->
            <button onclick="openSettingsModal()"
                    class="clay-btn px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5"
                    title="Pengaturan Workspace">
                <i data-lucide="sliders" class="w-3.5 h-3.5 text-slate-500"></i>
                <span class="hidden sm:inline">Pengaturan</span>
            </button>

            <!-- Exit / Logout Workspace Button (For all workspaces) -->
            <a href="<?= base_url('/boards/' . $board['id'] . '/lock') ?>" data-turbo="false"
               class="clay-btn px-3 py-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 flex items-center gap-1.5"
               title="Keluar dari Workspace dan kembali ke Beranda">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Keluar Ruang</span>
            </a>

            <!-- Delete Workspace Button -->
            <button onclick="confirmDeleteBoard('<?= $board['id'] ?>', '<?= esc($board['title'], 'js') ?>', <?= !empty($board['password']) ? 'true' : 'false' ?>)"
                    class="clay-btn-danger px-3 py-1.5 text-xs font-bold flex items-center gap-1.5"
                    title="Hapus Workspace">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span class="hidden md:inline">Hapus</span>
            </button>
        </div>
    </div>

    <!-- Kanban Canvas Area -->
    <div class="flex-1 overflow-y-auto sm:overflow-y-hidden overflow-x-hidden sm:overflow-x-auto p-2 sm:p-4 grid grid-cols-1 sm:grid-cols-2 lg:flex lg:items-start gap-4 select-none snap-x-container scroll-smooth">
        <!-- Loop Lists -->
        <?php foreach ($lists as $list): ?>
            <div class="w-full lg:w-[320px] clay-card p-3 flex flex-col shrink-0 max-h-[480px] sm:max-h-full snap-column" 
                 data-list-id="<?= $list['id'] ?>">
                
                <!-- List Header -->
                <div class="pb-2 flex items-center justify-between gap-1 border-b border-slate-200/50 dark:border-white/5">
                    <!-- Inline Editable Title -->
                    <input type="text" value="<?= esc($list['title']) ?>" 
                           onchange="updateListTitle('<?= $list['id'] ?>', this.value)"
                           class="bg-transparent font-extrabold text-sm text-slate-800 dark:text-white px-2 py-1 rounded-lg w-full clay-input focus:bg-white dark:focus:bg-slate-800 transition-all cursor-pointer">
                    
                    <!-- Delete List -->
                    <a href="<?= base_url('/lists/' . $list['id'] . '/delete') ?>" 
                       onclick="return confirm('Apakah Anda yakin ingin menghapus daftar ini beserta seluruh kartunya?')"
                       class="clay-btn p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400"
                       title="Hapus Daftar">
                        <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <!-- Cards Container (Drop Target) -->
                <div class="flex-1 overflow-y-auto py-2.5 space-y-2.5 card-drop-zone min-h-[60px]" 
                     ondragover="allowDrop(event)" 
                     ondragleave="clearDragOver(event)"
                     ondrop="dropCard(event, '<?= $list['id'] ?>')">
                    
                    <!-- Cards -->
                    <?php if (isset($list['cards']) && !empty($list['cards'])): ?>
                        <?php foreach ($list['cards'] as $card): ?>
                            <div class="clay-card p-3.5 cursor-grab active:cursor-grabbing hover:scale-[1.01] transition-transform"
                                 id="card-<?= $card['id'] ?>"
                                 draggable="true" 
                                 ondragstart="dragStart(event, '<?= $card['id'] ?>')"
                                 onclick="openCardModal(<?= htmlspecialchars(json_encode($card)) ?>)">
                                
                                <?php 
                                    $coverImage = null;
                                    if (!empty($card['attachments'])) {
                                        foreach ($card['attachments'] as $att) {
                                            if (strpos($att['file_type'], 'image/') === 0) {
                                                $coverImage = $att['file_name'];
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <?php if ($coverImage): ?>
                                    <img src="<?= base_url('uploads/' . $coverImage) ?>" class="w-full h-32 object-cover rounded-xl mb-2.5 shadow-inner" alt="Lampiran Sampul" />
                                <?php endif; ?>

                                <!-- Label indicator -->
                                <?php if (!empty($card['label_color'])): ?>
                                    <div class="w-12 h-2 rounded-full mb-2 shadow-sm" style="background-color: <?= esc($card['label_color']) ?>;"></div>
                                <?php endif; ?>

                                <!-- Card Title -->
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug">
                                    <?= esc($card['title']) ?>
                                </h4>

                                <!-- Card Metadata Footer -->
                                <?php if (!empty($card['description']) || !empty($card['due_date']) || !empty($card['attachments'])): ?>
                                    <div class="flex items-center gap-2 mt-3 pt-2.5 border-t border-slate-200/50 dark:border-white/5 text-xs text-slate-400 dark:text-slate-500">
                                        <?php if (!empty($card['description'])): ?>
                                            <span class="inline-flex items-center gap-1" title="Memiliki deskripsi">
                                                <i data-lucide="align-left" class="w-3.5 h-3.5 text-slate-500"></i>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($card['due_date'])): ?>
                                            <?php 
                                                $isOverdue = strtotime($card['due_date']) < time() && date('Y-m-d') != $card['due_date'];
                                                $dueClass = $isOverdue ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300';
                                            ?>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg text-[10px] <?= $dueClass ?>" title="Tenggat Waktu">
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                                <span><?= date('j M', strtotime($card['due_date'])) ?></span>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($card['attachments'])): ?>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg text-[10px] bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300" title="Jumlah Lampiran">
                                                <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                                <span><?= count($card['attachments']) ?></span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Add Card Button -->
                <div class="pt-1">
                    <button onclick="openCreateCardModal('<?= $list['id'] ?>', '<?= esc($list['title'], 'js') ?>')" 
                            class="w-full clay-btn py-2 px-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center justify-center gap-1.5">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Kartu</span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Create List Container -->
        <div class="w-full lg:w-[320px] shrink-0 snap-column">
            <button onclick="showAddListForm()" 
                    id="add-list-btn"
                    class="w-full clay-card p-4 flex items-center justify-center gap-2 text-slate-600 dark:text-slate-300 font-extrabold text-sm hover:text-indigo-600 dark:hover:text-indigo-400">
                <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-500"></i>
                <span>Tambah Kolom Daftar</span>
            </button>

            <!-- Create List Form -->
            <form action="<?= base_url('/lists/store') ?>" method="POST" 
                  id="add-list-form" 
                  class="hidden clay-card p-4 space-y-3">
                <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Daftar Baru</label>
                <input type="text" name="title" required placeholder="Contoh: Sedang Dikerjakan, Selesai..."
                       class="w-full px-3.5 py-2 clay-input text-slate-800 dark:text-white font-semibold text-sm">
                
                <div class="flex items-center gap-2">
                    <button type="submit" class="clay-btn-primary px-4 py-1.5 font-bold text-xs">
                        Simpan Daftar
                    </button>
                    <button type="button" onclick="hideAddListForm()" 
                            class="clay-btn p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Partials (Card, Share, Settings) -->
<?= $this->include('boards/_modals', ['listCount' => $listCount, 'cardCount' => $cardCount]) ?>

<!-- Kanban Board Client Scripts -->
<script>
    // List Form Interactions
    function showAddListForm() {
        document.getElementById('add-list-btn').classList.add('hidden');
        document.getElementById('add-list-form').classList.remove('hidden');
    }

    function hideAddListForm() {
        document.getElementById('add-list-btn').classList.remove('hidden');
        document.getElementById('add-list-form').classList.add('hidden');
    }

    // Modal Card Interactions
    function openCreateCardModal(listId, listName) {
        document.getElementById('modal-card-title-display').textContent = 'Tambah Kartu Baru';
        document.getElementById('modal-list-title').textContent = listName;
        document.getElementById('modal-card-form').action = '<?= base_url('/cards/store') ?>';
        document.getElementById('modal-list-id').value = listId;

        document.getElementById('modal-title').value = '';
        document.getElementById('modal-description').value = '';
        document.getElementById('modal-due-date').value = '';
        document.getElementById('modal-media-upload').value = '';

        document.getElementById('modal-delete-btn').classList.add('hidden');
        document.getElementById('modal-submit-btn').textContent = 'Buat Kartu';
        document.getElementById('modal-attachments-section').classList.add('hidden');

        document.getElementById('label-none').checked = true;
        document.getElementById('card-modal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function openCardModal(card) {
        if (event && event.defaultPrevented) return;

        document.getElementById('modal-title').value = card.title;
        document.getElementById('modal-card-title-display').textContent = card.title;
        document.getElementById('modal-description').value = card.description || '';
        document.getElementById('modal-due-date').value = card.due_date || '';
        
        const cardEl = document.getElementById('card-' + card.id);
        const listName = cardEl.closest('[data-list-id]').querySelector('input').value;
        document.getElementById('modal-list-title').textContent = listName;

        document.getElementById('modal-card-form').action = '<?= base_url('/cards/') ?>' + card.id + '/update';
        document.getElementById('modal-list-id').value = '';
        document.getElementById('modal-media-upload').value = '';

        document.getElementById('modal-delete-btn').classList.remove('hidden');
        document.getElementById('modal-delete-btn').href = '<?= base_url('/cards/') ?>' + card.id + '/delete';
        document.getElementById('modal-submit-btn').textContent = 'Simpan Perubahan';

        // Attachments
        const attachmentsContainer = document.getElementById('modal-attachments-list');
        attachmentsContainer.innerHTML = '';
        if (card.attachments && card.attachments.length > 0) {
            document.getElementById('modal-attachments-section').classList.remove('hidden');
            card.attachments.forEach(att => {
                const div = document.createElement('div');
                div.className = 'clay-card p-2.5 flex items-center justify-between gap-3';
                const sizeKB = (att.file_size / 1024).toFixed(1);
                
                div.innerHTML = `
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i data-lucide="paperclip" class="w-4 h-4 text-indigo-500 shrink-0"></i>
                        <div class="truncate">
                            <div class="text-xs font-bold text-slate-800 dark:text-white truncate">${att.original_name}</div>
                            <div class="text-[10px] text-slate-400">${sizeKB} KB</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="<?= base_url('uploads/') ?>${att.file_name}" target="_blank" class="clay-btn p-1.5 text-slate-600 dark:text-slate-300" title="Unduh">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        </a>
                        <a href="<?= base_url('/attachments/') ?>${att.id}/delete" onclick="return confirm('Hapus lampiran ini?')" class="clay-btn p-1.5 text-rose-500 hover:text-rose-700" title="Hapus">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                `;
                attachmentsContainer.appendChild(div);
            });
        } else {
            document.getElementById('modal-attachments-section').classList.add('hidden');
        }

        const color = card.label_color;
        const radios = document.getElementsByName('label_color');
        for (let r of radios) {
            r.checked = (r.value === (color || ''));
        }

        document.getElementById('card-modal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function closeCardModal() {
        document.getElementById('card-modal').classList.add('hidden');
    }

    // Share Modal
    function openShareModal() {
        document.getElementById('share-modal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function closeShareModal() {
        document.getElementById('share-modal').classList.add('hidden');
    }

    function copyShareLink() {
        const input = document.getElementById('share-link-input');
        input.select();
        navigator.clipboard.writeText(input.value)
            .then(() => alert('Tautan workspace berhasil disalin!'))
            .catch(() => {});
    }

    // Settings Modal
    function openSettingsModal() {
        document.getElementById('settings-modal').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function closeSettingsModal() {
        document.getElementById('settings-modal').classList.add('hidden');
    }

    function toggleSettingsPassword(show) {
        const group = document.getElementById('settings-password-group');
        if (show) {
            group.classList.remove('hidden');
        } else {
            group.classList.add('hidden');
        }
    }

    // Delete Board
    function confirmDeleteBoard(boardId, boardTitle, hasPassword) {
        if (hasPassword) {
            const password = prompt(`Workspace "${boardTitle}" dilindungi kata sandi.\nMasukkan kata sandi untuk menghapus:`);
            if (password === null) return;
            if (!password.trim()) {
                alert('Kata sandi wajib diisi!');
                return;
            }
            submitDeleteForm(boardId, password);
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus workspace "${boardTitle}" beserta seluruh daftarnya?`)) {
                submitDeleteForm(boardId, '');
            }
        }
    }

    function submitDeleteForm(boardId, password) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('/boards/') ?>` + boardId + '/delete';
        
        if (password) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'password';
            input.value = password;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
    }

    // Update List Title
    function updateListTitle(listId, title) {
        if (!title.trim()) return;
        const formData = new FormData();
        formData.append('title', title);

        fetch('<?= base_url('/lists/') ?>' + listId + '/update', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }

    // Drag and drop logic
    let draggedCardId = null;

    function dragStart(event, cardId) {
        draggedCardId = cardId;
        event.dataTransfer.setData('text/plain', cardId);
        event.dataTransfer.effectAllowed = 'move';
    }

    function allowDrop(event) {
        event.preventDefault();
        const dropZone = event.target.closest('.card-drop-zone');
        if (dropZone) {
            dropZone.classList.add('bg-indigo-50/50', 'dark:bg-indigo-950/20');
        }
    }

    function clearDragOver(event) {
        const dropZone = event.target.closest('.card-drop-zone');
        if (dropZone) {
            dropZone.classList.remove('bg-indigo-50/50', 'dark:bg-indigo-950/20');
        }
    }

    function dropCard(event, targetListId) {
        event.preventDefault();
        clearDragOver(event);

        if (!draggedCardId) return;

        const cardElement = document.getElementById('card-' + draggedCardId);
        const targetDropZone = event.target.closest('.card-drop-zone');
        
        if (cardElement && targetDropZone) {
            targetDropZone.appendChild(cardElement);

            const orderedCards = Array.from(targetDropZone.querySelectorAll('[id^="card-"]')).map(el => el.id.replace('card-', ''));
            
            const formData = new FormData();
            formData.append('list_id', targetListId);
            orderedCards.forEach(id => formData.append('card_ids[]', id));

            fetch('<?= base_url('/cards/') ?>' + draggedCardId + '/move', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        }
        draggedCardId = null;
    }
</script>

<!-- Workspace Group Chat Scripts -->
<script>
    <?= $this->include('boards/_chat_scripts') ?>
</script>
<?= $this->endSection() ?>
