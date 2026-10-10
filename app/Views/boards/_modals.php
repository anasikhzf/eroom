<?php
    $listCount = isset($lists) ? count($lists) : 0;
    $cardCount = 0;
    if (isset($lists) && is_array($lists)) {
        foreach ($lists as $l) {
            $cardCount += isset($l['cards']) ? count($l['cards']) : 0;
        }
    }
?>
<!-- Card Detail Modal (Claymorphic) -->
<div id="card-modal" class="hidden fixed inset-0 bg-slate-950/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="clay-card bg-white dark:bg-slate-900 w-full max-w-lg max-h-[92vh] flex flex-col overflow-hidden p-6 space-y-4">
        
        <!-- Header -->
        <div class="flex items-start justify-between pb-3 border-b border-slate-200/60 dark:border-white/10 shrink-0">
            <div class="flex items-start gap-2.5">
                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                    <i data-lucide="trello" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 id="modal-card-title-display" class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-white leading-tight">Detail Kartu</h3>
                    <p class="text-xs text-slate-400">Dalam kolom: <span id="modal-list-title" class="font-bold text-slate-700 dark:text-slate-300"></span></p>
                </div>
            </div>
            <button onclick="closeCardModal()" class="clay-btn p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Form Scrollable -->
        <div class="flex-1 overflow-y-auto space-y-4 pr-1">
            <form id="modal-card-form" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="list_id" id="modal-list-id" value="">
                
                <!-- Label Color -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Warna Penanda Label</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="label_color" value="" id="label-none" class="sr-only peer">
                            <div class="px-3 py-1 clay-btn text-xs font-bold text-slate-600 dark:text-slate-300 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500/20">Tanpa Label</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="label_color" value="#22c55e" id="label-green" class="sr-only peer">
                            <div class="w-6 h-6 rounded-full bg-[#22c55e] peer-checked:ring-2 peer-checked:ring-slate-800 dark:peer-checked:ring-white"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="label_color" value="#eab308" id="label-yellow" class="sr-only peer">
                            <div class="w-6 h-6 rounded-full bg-[#eab308] peer-checked:ring-2 peer-checked:ring-slate-800 dark:peer-checked:ring-white"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="label_color" value="#f97316" id="label-orange" class="sr-only peer">
                            <div class="w-6 h-6 rounded-full bg-[#f97316] peer-checked:ring-2 peer-checked:ring-slate-800 dark:peer-checked:ring-white"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="label_color" value="#ef4444" id="label-red" class="sr-only peer">
                            <div class="w-6 h-6 rounded-full bg-[#ef4444] peer-checked:ring-2 peer-checked:ring-slate-800 dark:peer-checked:ring-white"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="label_color" value="#3b82f6" id="label-blue" class="sr-only peer">
                            <div class="w-6 h-6 rounded-full bg-[#3b82f6] peer-checked:ring-2 peer-checked:ring-slate-800 dark:peer-checked:ring-white"></div>
                        </label>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label for="modal-title" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Judul Kartu</label>
                    <input type="text" id="modal-title" name="title" required
                           class="w-full px-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-semibold text-sm">
                </div>

                <!-- Description -->
                <div>
                    <label for="modal-description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Catatan & Rincian Tugas</label>
                    <textarea id="modal-description" name="description" placeholder="Jelaskan instruksi atau catatan kartu..." rows="3"
                              class="w-full px-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-semibold text-sm"></textarea>
                </div>

                <!-- Due Date -->
                <div>
                    <label for="modal-due-date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tenggat Waktu</label>
                    <input type="date" id="modal-due-date" name="due_date"
                           class="w-full px-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-semibold text-sm">
                </div>

                <!-- Add Attachment -->
                <div>
                    <label for="modal-media-upload" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Unggah Berkas / Gambar</label>
                    <input type="file" id="modal-media-upload" name="media"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 clay-input p-1">
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-between gap-3">
                    <a href="" id="modal-delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus kartu ini?')"
                       class="clay-btn-danger px-3 py-1.5 text-xs font-bold flex items-center gap-1.5">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Hapus</span>
                    </a>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeCardModal()" class="clay-btn px-4 py-2 font-bold text-xs text-slate-600 dark:text-slate-300">
                            Tutup
                        </button>
                        <button type="submit" id="modal-submit-btn" class="clay-btn-primary px-5 py-2 font-bold text-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>

            <!-- Attachments List -->
            <div id="modal-attachments-section" class="hidden pt-3 border-t border-slate-200/60 dark:border-white/10 space-y-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Berkas Lampiran</label>
                <div id="modal-attachments-list" class="space-y-2"></div>
            </div>
        </div>
    </div>
</div>

<!-- Share Room Modal (Claymorphic) -->
<div id="share-modal" class="hidden fixed inset-0 bg-slate-950/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="clay-card bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 dark:border-white/10">
            <div class="flex items-center gap-2">
                <i data-lucide="share-2" class="w-5 h-5 text-indigo-500"></i>
                <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Bagikan Ruang Kolaborasi</h3>
            </div>
            <button onclick="closeShareModal()" class="clay-btn p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="clay-card p-4 text-white relative overflow-hidden" style="background-color: <?= esc($board['color']) ?>;">
            <div class="text-[10px] font-extrabold uppercase tracking-widest text-white/70">Undangan Ruang Kerja</div>
            <h4 class="text-lg font-extrabold truncate mt-0.5"><?= esc($board['title']) ?></h4>
            <div class="text-xs text-white/80 mt-1 flex items-center gap-3">
                <span><?= $listCount ?> Kolom</span>
                <span>&bull;</span>
                <span><?= $cardCount ?> Kartu</span>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tautan Workspace</label>
            <div class="flex gap-2">
                <input type="text" id="share-link-input" readonly value="<?= current_url() ?>"
                       class="flex-1 px-3 py-2 clay-input text-xs font-bold text-slate-800 dark:text-white">
                <button onclick="copyShareLink()" class="clay-btn-primary px-4 py-2 font-bold text-xs flex items-center gap-1.5">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span>Salin</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2">
            <a href="https://api.whatsapp.com/send?text=Ayo%20bergabung%20ke%20workspace%20kita%20di%20Eroom:%20<?= urlencode(current_url()) ?>" 
               target="_blank" rel="noopener noreferrer"
               class="clay-btn p-3 text-center flex flex-col items-center gap-1.5 hover:text-emerald-600">
                <i data-lucide="message-square" class="w-5 h-5 text-emerald-500"></i>
                <span class="text-xs font-bold">WhatsApp</span>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" 
               target="_blank" rel="noopener noreferrer"
               class="clay-btn p-3 text-center flex flex-col items-center gap-1.5 hover:text-blue-600">
                <i data-lucide="share" class="w-5 h-5 text-blue-500"></i>
                <span class="text-xs font-bold">Facebook</span>
            </a>
        </div>
    </div>
</div>

<!-- Workspace Settings Modal (Claymorphic) -->
<div id="settings-modal" class="hidden fixed inset-0 bg-slate-950/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="clay-card bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 dark:border-white/10">
            <div class="flex items-center gap-2">
                <i data-lucide="sliders" class="w-5 h-5 text-indigo-500"></i>
                <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Pengaturan Workspace</h3>
            </div>
            <button onclick="closeSettingsModal()" class="clay-btn p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="<?= base_url('/boards/' . $board['id'] . '/settings') ?>" method="POST" class="space-y-4" data-turbo="false">
            <div>
                <label for="setting-title" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Workspace</label>
                <input type="text" id="setting-title" name="title" value="<?= esc($board['title']) ?>" required
                       class="w-full px-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-semibold text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Kunci Workspace</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="security_type" value="public" <?= empty($board['password']) ? 'checked' : '' ?> onchange="toggleSettingsPassword(false)" class="sr-only peer">
                        <div class="p-3 clay-btn peer-checked:border-emerald-500 peer-checked:ring-2 peer-checked:ring-emerald-500/20 text-center space-y-1">
                            <i data-lucide="globe" class="w-5 h-5 mx-auto text-emerald-500"></i>
                            <div class="font-bold text-xs text-slate-800 dark:text-white">Publik (Terbuka)</div>
                            <div class="text-[10px] text-slate-400">Tanpa kata sandi</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="security_type" value="protected" <?= !empty($board['password']) ? 'checked' : '' ?> onchange="toggleSettingsPassword(true)" class="sr-only peer">
                        <div class="p-3 clay-btn peer-checked:border-amber-500 peer-checked:ring-2 peer-checked:ring-amber-500/20 text-center space-y-1">
                            <i data-lucide="lock" class="w-5 h-5 mx-auto text-amber-500"></i>
                            <div class="font-bold text-xs text-slate-800 dark:text-white">Terkunci (Sandi)</div>
                            <div class="text-[10px] text-slate-400">Hanya pemegang sandi</div>
                        </div>
                    </label>
                </div>
            </div>

            <div id="settings-password-group" class="<?= empty($board['password']) ? 'hidden' : '' ?> space-y-1.5">
                <label for="setting-new-password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                    Kata Sandi Baru (Kosongkan jika tetap sama)
                </label>
                <input type="password" id="setting-new-password" name="new_password" placeholder="Minimal 4 karakter..."
                       class="w-full px-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-semibold text-sm">
            </div>

            <?php if (!empty($board['password'])): ?>
                <div class="space-y-1.5 pt-2 border-t border-slate-200/60 dark:border-white/10">
                    <label for="setting-current-password" class="block text-xs font-bold text-rose-500 uppercase tracking-wider">
                        Verifikasi Kata Sandi Sekarang
                    </label>
                    <input type="password" id="setting-current-password" name="current_password" required placeholder="Masukkan kata sandi saat ini..."
                           class="w-full px-3.5 py-2.5 clay-input border-rose-300 text-slate-800 dark:text-white font-semibold text-sm">
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Aksen Warna Tema</label>
                <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                    <?php 
                        $colors = ['#6366f1', '#14b8a6', '#f43f5e', '#f59e0b', '#8b5cf6', '#10b981', '#3b82f6', '#64748b'];
                        foreach ($colors as $c):
                    ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="<?= $c ?>" <?= ($board['color'] === $c) ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-full h-8 rounded-full ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md" style="background-color: <?= $c ?>;"></div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeSettingsModal()" class="clay-btn px-4 py-2 font-bold text-xs text-slate-600 dark:text-slate-300">
                    Batal
                </button>
                <button type="submit" class="clay-btn-primary px-5 py-2 font-bold text-xs">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Chat Group Drawer (Claymorphism Sidebar / Popup) -->
<div id="chat-drawer" class="hidden fixed inset-y-0 right-0 w-full sm:w-96 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md shadow-2xl z-50 flex flex-col border-l border-slate-200/80 dark:border-white/10 animate-in slide-in-from-right duration-300">
    <!-- Chat Header -->
    <div class="p-4 border-b border-slate-200/60 dark:border-white/10 flex items-center justify-between shrink-0 bg-slate-50/50 dark:bg-slate-950/20">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white shadow-sm">
                <i data-lucide="message-square" class="w-4 h-4"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 dark:text-white">Obrolan Ruang Kerja</h3>
                <p class="text-[10px] text-slate-400 font-semibold" id="chat-user-indicator">
                    Bergabung sebagai: <span class="font-extrabold text-indigo-600 dark:text-indigo-400" id="display-current-username">Tamu</span>
                    <button onclick="promptChangeUsername()" class="underline hover:text-indigo-500 ml-1">(Ganti)</button>
                </p>
            </div>
        </div>
        <button onclick="toggleChatDrawer()" class="clay-btn p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Message List (Scrollable) -->
    <div id="chat-messages-container" class="flex-1 overflow-y-auto p-4 space-y-3">
        <div id="chat-loading" class="text-center py-8 text-xs text-slate-400">
            <i data-lucide="loader" class="w-5 h-5 mx-auto animate-spin mb-2 text-indigo-500"></i>
            <span>Memuat pesan...</span>
        </div>
        <div id="chat-empty" class="hidden text-center py-10 px-4 space-y-2">
            <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center mx-auto text-indigo-500">
                <i data-lucide="messages-square" class="w-6 h-6"></i>
            </div>
            <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum ada percakapan</p>
            <p class="text-[11px] text-slate-400">Jadilah yang pertama mengirim pesan di obrolan grup ini!</p>
        </div>
    </div>

    <!-- Chat Input Form -->
    <div class="p-3 border-t border-slate-200/60 dark:border-white/10 shrink-0 bg-slate-50/50 dark:bg-slate-950/30">
        <form id="chat-form" onsubmit="handleSendChatMessage(event)" class="flex gap-2 items-center">
            <input type="text" id="chat-input" placeholder="Ketik pesan..." required maxlength="1000" autocomplete="off"
                   class="flex-1 px-3.5 py-2 clay-input text-xs font-semibold text-slate-800 dark:text-white">
            <button type="submit" id="chat-submit-btn" class="clay-btn-primary p-2.5 rounded-xl shrink-0" title="Kirim Pesan">
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
    </div>
</div>

<!-- Modal Input Username Saat Mau Gabung Chat -->
<div id="chat-username-modal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="clay-card bg-white dark:bg-slate-900 w-full max-w-sm overflow-hidden p-6 space-y-4 text-center">
        <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center mx-auto text-indigo-600 dark:text-indigo-400 shadow-inner">
            <i data-lucide="user-check" class="w-6 h-6"></i>
        </div>
        <div>
            <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Gabung ke Obrolan Grup</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masukkan nama atau panggilan Anda untuk mulai berkirim pesan dengan tim.</p>
        </div>

        <form id="chat-username-form" onsubmit="saveChatUsername(event)" class="space-y-3 text-left">
            <div>
                <label for="input-chat-username" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Tampilan Anda</label>
                <input type="text" id="input-chat-username" required placeholder="Contoh: Rian, Sarah, Alex..." maxlength="30" autofocus
                       class="w-full px-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-bold text-xs sm:text-sm">
            </div>
            <div class="pt-2 flex items-center gap-2">
                <button type="button" onclick="closeChatUsernameModal()" class="w-1/2 clay-btn py-2 text-xs font-bold text-slate-500">
                    Batal
                </button>
                <button type="submit" class="w-1/2 clay-btn-primary py-2 text-xs font-bold">
                    Gabung Chat
                </button>
            </div>
        </form>
    </div>
</div>
