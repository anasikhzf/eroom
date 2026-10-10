<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="flex-1 flex items-center justify-center p-4 sm:p-8">
    <div class="w-full max-w-md clay-card p-6 sm:p-8 space-y-6 text-center">
        
        <!-- Icon & Header -->
        <div class="space-y-3">
            <div class="mx-auto w-16 h-16 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 shadow-md">
                <i data-lucide="shield-alert" class="w-8 h-8"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-white">Workspace Terproteksi</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                Workspace <span class="font-extrabold text-slate-800 dark:text-white">"<?= esc($board['title']) ?>"</span> dikunci dan membutuhkan kata sandi untuk mengakses isinya.
            </p>
        </div>

        <!-- Unlock Form -->
        <form action="<?= base_url('/boards/' . $board['id'] . '/unlock') ?>" method="POST" class="space-y-4 text-left" data-turbo="false">
            <div>
                <label for="password" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Kata Sandi Workspace</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required autofocus placeholder="Masukkan kata sandi..."
                           class="w-full pl-10 pr-3.5 py-3 clay-input text-slate-800 dark:text-white font-semibold text-sm">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="key-round" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">Sistem membatasi maksimal 5 percobaan demi keamanan akun.</p>
            </div>

            <button type="submit" 
                    class="w-full py-3 clay-btn-primary font-bold text-sm flex items-center justify-center gap-2">
                <i data-lucide="lock-open" class="w-4 h-4"></i>
                <span>Buka Akses Ruang</span>
            </button>
        </form>

        <!-- Back Link -->
        <div class="pt-3 border-t border-slate-200/60 dark:border-white/10">
            <a href="<?= base_url('/') ?>" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Halaman Beranda</span>
            </a>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
