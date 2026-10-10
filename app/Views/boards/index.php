<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto space-y-6">
        
        <!-- Header Clay Panel -->
        <div class="clay-card p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/40 text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>Sistem Manajemen Tugas Terpadu</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">
                    Ruang Kerja Kolaboratif
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-xl">
                    Pilih salah satu ruang kerja atau buat baru untuk mulai merencanakan proyek, menyusun tugas tim, dan melacak perkembangan secara real-time.
                </p>
            </div>

            <button onclick="openNewBoardModal()" 
                    class="clay-btn-primary px-5 py-3 font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 self-start md:self-center shrink-0">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Buat Workspace Baru</span>
            </button>
        </div>

        <!-- Boards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <?php if (empty($boards)): ?>
                <!-- Empty State Clay Panel -->
                <div class="col-span-full clay-card py-16 px-6 flex flex-col items-center justify-center text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-inner">
                        <i data-lucide="folder-kanban" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Belum Ada Workspace Aktif</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-md">
                        Mulai ruang kerja pertama Anda sekarang. Anda dapat memilih apakah workspace ini bersifat publik atau diproteksi dengan kata sandi.
                    </p>
                    <button onclick="openNewBoardModal()" 
                            class="clay-btn-primary mt-2 px-5 py-2.5 font-bold text-xs sm:text-sm flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Buat Workspace Pertama</span>
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($boards as $board): ?>
                    <!-- Clay Board Card -->
                    <div class="clay-card relative flex flex-col justify-between p-5 h-44 cursor-pointer group hover:scale-[1.02] transition-transform overflow-hidden"
                         onclick="Turbo.visit('<?= base_url('/boards/' . $board['id']) ?>')">
                        
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: <?= esc($board['color']) ?>;"></div>

                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs shadow-sm" style="background-color: <?= esc($board['color']) ?>;">
                                    <i data-lucide="trello" class="w-3.5 h-3.5"></i>
                                </span>

                                <?php if (!empty($board['password'])): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-[10px] font-extrabold tracking-wide">
                                        <i data-lucide="lock" class="w-3 h-3"></i>
                                        <span>Terkunci</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-extrabold tracking-wide">
                                        <i data-lucide="globe" class="w-3 h-3"></i>
                                        <span>Publik</span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h2 class="mt-3 font-extrabold text-base text-slate-800 dark:text-white line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                <?= esc($board['title']) ?>
                            </h2>
                        </div>

                        <!-- Card Footer -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-200/50 dark:border-white/5 text-xs text-slate-400 dark:text-slate-500">
                            <span class="font-bold flex items-center gap-1">
                                <i data-lucide="arrow-right-circle" class="w-3.5 h-3.5 text-indigo-500"></i>
                                <span>Buka Ruang</span>
                            </span>
                            <span class="clay-btn p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 opacity-80 sm:opacity-0 group-hover:opacity-100 transition-opacity"
                                  onclick="event.stopPropagation(); confirmDeleteBoard('<?= $board['id'] ?>', '<?= esc($board['title'], 'js') ?>', <?= !empty($board['password']) ? 'true' : 'false' ?>)"
                                  title="Hapus Workspace">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Create Board Card Tile -->
                <div onclick="openNewBoardModal()" 
                     class="clay-card border-dashed border-2 border-indigo-300 dark:border-indigo-800/40 p-5 h-44 flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-indigo-500 transition-all text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-xs sm:text-sm">Tambah Workspace Baru</span>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function confirmDeleteBoard(boardId, boardTitle, hasPassword) {
    if (hasPassword) {
        const password = prompt(`Workspace "${boardTitle}" dilindungi kata sandi.\nSilakan masukkan kata sandi untuk menghapus secara permanen:`);
        if (password === null) return;
        if (!password.trim()) {
            alert('Kata sandi wajib diisi!');
            return;
        }
        submitDeleteForm(boardId, password);
    } else {
        if (confirm(`Apakah Anda yakin ingin menghapus workspace "${boardTitle}" beserta seluruh daftar dan kartunya secara permanen?`)) {
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
</script>
<?= $this->endSection() ?>
