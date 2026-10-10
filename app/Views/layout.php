<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($title ?? 'Eroom - Ruang Kerja Kolaboratif') ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?= esc($meta_description ?? 'Eroom adalah aplikasi manajemen proyek & kanban kolaboratif modern dengan desain claymorphism yang responsif, cepat, aman, dan mudah digunakan.') ?>">
    <meta name="keywords" content="kanban board, kolaborasi tim, manajemen tugas, eroom, claymorphism, produktivitas, pwa, workspace gratis">
    <meta name="author" content="anasikhzf">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= current_url() ?>">

    <!-- PWA Settings & Circular Icons -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Eroom">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon.png') ?>">
    <link rel="apple-touch-icon" sizes="192x192" href="<?= base_url('icons/icon-192.png') ?>">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= esc($title ?? 'Eroom | Ruang Kerja Kolaboratif') ?>">
    <meta property="og:description" content="<?= esc($meta_description ?? 'Eroom menghadirkan pengalaman kanban visual interaktif bertema claymorphism untuk mengelola tugas dan proyek Anda secara fleksibel.') ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <?php 
        $ogColor = isset($board['color']) ? ltrim($board['color'], '#') : '6366f1';
        $ogTitle = isset($board['title']) ? $board['title'] : 'Eroom';
        $ogImage = "https://dummyimage.com/1200x630/{$ogColor}/ffffff.png?text=" . urlencode($ogTitle);
    ?>
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($title ?? 'Eroom | Ruang Kerja Kolaboratif') ?>">
    <meta name="twitter:description" content="<?= esc($meta_description ?? 'Eroom - Platform papan kanban kolaboratif modern dan responsif.') ?>">
    <meta name="twitter:image" content="<?= $ogImage ?>">

    <!-- Google Fonts: Plus Jakarta Sans (Modern & Ergonomic Indonesian readability) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        clay: {
                            bg: '#f0f4f9',
                            darkBg: '#0f172a',
                            surface: '#ffffff',
                            darkSurface: '#1e293b'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Hotwire Turbo -->
    <script src="https://unpkg.com/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Claymorphism Design System CSS -->
    <style>
        :root {
            --clay-bg: #eef2f7;
            --clay-card-light: #f7f9fc;
            --clay-shadow-out: 8px 8px 18px #d1d9e6, -8px -8px 18px #ffffff;
            --clay-shadow-sm: 4px 4px 10px #d1d9e6, -4px -4px 10px #ffffff;
            --clay-shadow-inset: inset 3px 3px 6px #cbd5e1, inset -3px -3px 6px #ffffff;
            --clay-border: rgba(255, 255, 255, 0.7);
        }

        .dark {
            --clay-bg: #090d16;
            --clay-card-light: #131b2e;
            --clay-shadow-out: 8px 8px 20px #04060b, -6px -6px 16px rgba(255, 255, 255, 0.03);
            --clay-shadow-sm: 4px 4px 12px #04060b, -3px -3px 8px rgba(255, 255, 255, 0.02);
            --clay-shadow-inset: inset 2px 2px 5px #070a12, inset -2px -2px 5px rgba(255, 255, 255, 0.05);
            --clay-border: rgba(255, 255, 255, 0.07);
        }

        body {
            background-color: var(--clay-bg);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Clay Card / Container */
        .clay-card {
            background: var(--clay-card-light);
            border-radius: 20px;
            box-shadow: var(--clay-shadow-out);
            border: 1px solid var(--clay-border);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .clay-card:hover {
            transform: translateY(-2px);
            box-shadow: 12px 12px 24px #cbd5e1, -10px -10px 24px #ffffff;
        }
        .dark .clay-card:hover {
            box-shadow: 10px 10px 24px #020408, -6px -6px 18px rgba(255, 255, 255, 0.05);
        }

        /* Clay Pill / Button */
        .clay-btn {
            background: var(--clay-card-light);
            border-radius: 14px;
            box-shadow: var(--clay-shadow-sm);
            border: 1px solid var(--clay-border);
            transition: all 0.2s ease;
        }
        .clay-btn:hover {
            transform: translateY(-1px);
        }
        .clay-btn:active {
            transform: translateY(1px);
            box-shadow: var(--clay-shadow-inset);
        }

        /* Clay Colored Buttons */
        .clay-btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 4px 4px 14px rgba(79, 70, 229, 0.35), -3px -3px 10px rgba(255, 255, 255, 0.4), inset 1px 1px 2px rgba(255, 255, 255, 0.4);
            transition: all 0.2s ease;
        }
        .clay-btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            box-shadow: 6px 6px 18px rgba(79, 70, 229, 0.45), -4px -4px 12px rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }
        .clay-btn-primary:active {
            box-shadow: inset 3px 3px 6px rgba(0, 0, 0, 0.3);
            transform: translateY(1px);
        }

        .clay-btn-danger {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            color: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 4px 4px 12px rgba(225, 29, 72, 0.3), -2px -2px 8px rgba(255, 255, 255, 0.3);
            transition: all 0.2s ease;
        }
        .clay-btn-danger:hover {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
            transform: translateY(-1px);
        }

        /* Clay Inset Input */
        .clay-input {
            background: var(--clay-card-light);
            border-radius: 14px;
            border: 1px solid var(--clay-border);
            box-shadow: var(--clay-shadow-inset);
            outline: none;
            transition: all 0.2s ease;
        }
        .clay-input:focus {
            border-color: #6366f1;
            box-shadow: var(--clay-shadow-inset), 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        /* Hotwire Turbo Progress Bar */
        .turbo-progress-bar {
            background: linear-gradient(to right, #6366f1, #8b5cf6, #ec4899) !important;
            height: 3.5px !important;
        }

        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #1e293b;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }

        .snap-x-container {
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        .snap-column {
            scroll-snap-align: start;
        }
    </style>
</head>
<body class="h-full flex flex-col overflow-hidden text-slate-800 dark:text-slate-100 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Bar (Clay Header) -->
    <header class="h-16 clay-card mx-3 my-2 sm:mx-4 sm:my-2.5 px-4 sm:px-6 flex items-center justify-between shrink-0 z-20">
        <div class="flex items-center gap-3 sm:gap-5">
            <!-- Brand / Logo -->
            <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/30 group-hover:scale-105 transition-transform">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-slate-900 to-indigo-700 dark:from-white dark:to-indigo-300 bg-clip-text text-transparent">Eroom</span>
                    <span class="text-[10px] -mt-1 font-semibold text-slate-400 dark:text-slate-500">Ruang Kolaborasi</span>
                </div>
            </a>
            
            <nav class="hidden sm:flex items-center">
                <a href="<?= base_url('/') ?>" class="clay-btn px-3 py-1.5 font-bold text-xs text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-indigo-500"></i>
                    <span>Semua Ruang</span>
                </a>
            </nav>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <!-- PWA Install Button (Appears when browser is eligible for installation) -->
            <button id="pwa-install-btn" onclick="installPWA()"
                    class="hidden clay-btn px-3 py-2 text-indigo-600 dark:text-indigo-400 font-bold text-xs sm:text-sm hover:scale-105 transition-all flex items-center gap-1.5"
                    title="Install Aplikasi Eroom">
                <i data-lucide="download" class="w-4 h-4 text-indigo-500"></i>
                <span class="hidden xs:inline">Install App</span>
            </button>

            <!-- Theme Toggle Button -->
            <button onclick="toggleTheme()" id="theme-toggle-btn"
                    class="clay-btn p-2.5 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400"
                    title="Ganti Mode Gelap / Terang">
                <i data-lucide="sun" class="w-4 h-4 dark:hidden text-amber-500"></i>
                <i data-lucide="moon" class="w-4 h-4 hidden dark:block text-indigo-400"></i>
            </button>

            <!-- Quick Create Button -->
            <button onclick="openNewBoardModal()" 
                    class="clay-btn-primary px-3.5 py-2 font-bold text-xs sm:text-sm flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Workspace Baru</span>
            </button>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex overflow-hidden px-2 sm:px-4 pb-2">
        <main class="flex-1 flex flex-col overflow-hidden relative">
            <!-- Toast Notifications (Centered Top Floating, Non-intrusive) -->
            <div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 flex flex-col items-center pointer-events-none w-full max-w-sm px-4">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="toast-item pointer-events-auto clay-card bg-emerald-50 dark:bg-emerald-950/70 border-emerald-500/40 text-emerald-800 dark:text-emerald-200 px-4 py-2.5 flex items-center gap-2.5 shadow-lg animate-in fade-in slide-in-from-top-4 duration-300 mb-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                        <span class="text-xs sm:text-sm font-bold"><?= esc(session()->getFlashdata('success')) ?></span>
                        <button onclick="dismissToast(this.parentElement)" class="ml-2 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="toast-item pointer-events-auto clay-card bg-rose-50 dark:bg-rose-950/70 border-rose-500/40 text-rose-800 dark:text-rose-200 px-4 py-2.5 flex items-center gap-2.5 shadow-lg animate-in fade-in slide-in-from-top-4 duration-300 mb-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0"></i>
                        <span class="text-xs sm:text-sm font-bold"><?= esc(session()->getFlashdata('error')) ?></span>
                        <button onclick="dismissToast(this.parentElement)" class="ml-2 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Footer -->
    <footer class="h-10 clay-card mx-3 mb-2 sm:mx-4 sm:mb-2.5 px-4 sm:px-6 flex items-center justify-between text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 shrink-0 z-10 select-none">
        <div>
            <span>&copy; <?= date('Y') ?> <strong>Eroom</strong>. Dibuat oleh <a href="https://anasikhzf.web.app" target="_blank" rel="noopener noreferrer" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">anasikhzf</a>.</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 font-semibold text-slate-400">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                <span class="hidden sm:inline">Aman & Terenkripsi</span>
            </span>
        </div>
    </footer>

    <!-- Create Board Modal (Claymorphic) -->
    <div id="new-board-modal" class="hidden fixed inset-0 bg-slate-950/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="clay-card bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden transform transition-all p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 dark:border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <i data-lucide="folder-plus" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-800 dark:text-white">Buat Workspace Baru</h3>
                </div>
                <button type="button" onclick="closeNewBoardModal()" class="clay-btn p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="<?= base_url('/boards/store') ?>" method="POST" class="space-y-4" data-turbo="false">
                <div>
                    <label for="board-title" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Nama Workspace</label>
                    <input type="text" id="board-title" name="title" required placeholder="Contoh: Manajemen Proyek, Kampanye Konten"
                           class="w-full px-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-semibold text-sm">
                </div>

                <!-- Privacy & Lock Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Visibilitas & Kunci Workspace</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="is_protected" value="0" checked onchange="togglePasswordVisibility(false)" class="sr-only peer">
                            <div class="p-3 clay-btn peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500/20 text-center space-y-1">
                                <i data-lucide="globe" class="w-5 h-5 mx-auto text-emerald-500"></i>
                                <div class="font-extrabold text-xs text-slate-800 dark:text-white">Publik</div>
                                <div class="text-[10px] text-slate-400">Bisa diakses tanpa kata sandi</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="is_protected" value="1" onchange="togglePasswordVisibility(true)" class="sr-only peer">
                            <div class="p-3 clay-btn peer-checked:border-amber-500 peer-checked:ring-2 peer-checked:ring-amber-500/20 text-center space-y-1">
                                <i data-lucide="lock" class="w-5 h-5 mx-auto text-amber-500"></i>
                                <div class="font-extrabold text-xs text-slate-800 dark:text-white">Terkunci (Privat)</div>
                                <div class="text-[10px] text-slate-400">Wajibkan kata sandi aman</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="password-input-group" class="hidden space-y-1.5">
                    <label for="board-password" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kata Sandi Workspace</label>
                    <div class="relative">
                        <input type="password" id="board-password" name="password" placeholder="Minimal 4 karakter..."
                               class="w-full pl-9 pr-3.5 py-2.5 clay-input text-slate-800 dark:text-white font-semibold text-sm">
                        <i data-lucide="key" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Aksen Warna Tema</label>
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#6366f1" checked class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#6366f1] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#14b8a6" class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#14b8a6] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#f43f5e" class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#f43f5e] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#f59e0b" class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#f59e0b] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#8b5cf6" class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#8b5cf6] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#10b981" class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#10b981] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#3b82f6" class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#3b82f6] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="#64748b" class="sr-only peer">
                            <div class="w-full h-8 rounded-full bg-[#64748b] ring-2 ring-transparent peer-checked:ring-slate-800 dark:peer-checked:ring-white transition-all shadow-md"></div>
                        </label>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeNewBoardModal()" class="clay-btn px-4 py-2 font-bold text-xs text-slate-600 dark:text-slate-300">
                        Batal
                    </button>
                    <button type="submit" class="clay-btn-primary px-5 py-2 font-bold text-xs sm:text-sm">
                        Simpan & Buka
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }

        function openNewBoardModal() {
            document.getElementById('new-board-modal').classList.remove('hidden');
            if (window.lucide) window.lucide.createIcons();
        }

        function closeNewBoardModal() {
            document.getElementById('new-board-modal').classList.add('hidden');
        }

        function togglePasswordVisibility(show) {
            const group = document.getElementById('password-input-group');
            const input = document.getElementById('board-password');
            if (show) {
                group.classList.remove('hidden');
                input.required = true;
                input.focus();
            } else {
                group.classList.add('hidden');
                input.required = false;
                input.value = '';
            }
        }

        function dismissToast(el) {
            if (!el) return;
            el.classList.add('opacity-0', '-translate-y-2', 'transition-all', 'duration-300');
            setTimeout(() => el.remove(), 300);
        }

        function initToasts() {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toasts = container.querySelectorAll('.toast-item');
            
            // Jika ada lebih dari satu notifikasi, hilangkan notifikasi lama segera
            if (toasts.length > 1) {
                for (let i = 0; i < toasts.length - 1; i++) {
                    toasts[i].remove();
                }
            }

            // Hilangkan otomatis setelah 3.5 detik
            toasts.forEach(toast => {
                setTimeout(() => {
                    dismissToast(toast);
                }, 3500);
            });
        }

        // Initialize Lucide icons on Turbo load
        document.addEventListener('turbo:load', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
            initToasts();
        });

        // Register PWA Service Worker & Handle Install Prompt
        let deferredPrompt = null;
        const installBtn = document.getElementById('pwa-install-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const btn = document.getElementById('pwa-install-btn');
            if (btn) {
                btn.classList.remove('hidden');
                if (window.lucide) window.lucide.createIcons();
            }
        });

        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            const btn = document.getElementById('pwa-install-btn');
            if (btn) btn.classList.add('hidden');
            console.log('Eroom PWA telah berhasil dipasang.');
        });

        function installPWA() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('Pengguna menyetujui instalasi PWA');
                    }
                    deferredPrompt = null;
                    const btn = document.getElementById('pwa-install-btn');
                    if (btn) btn.classList.add('hidden');
                });
            } else {
                alert('Aplikasi dapat dipasang melalui menu browser Anda ("Tambahkan ke Layar Utama" atau ikon instalasi di kolom alamat).');
            }
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => {
                        console.log('PWA Service Worker terdaftar dengan sukses:', reg.scope);
                    })
                    .catch((err) => {
                        console.warn('Registrasi Service Worker gagal:', err);
                    });
            });
        }
    </script>
</body>
</html>
