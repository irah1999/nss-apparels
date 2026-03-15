<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Business ERP' ?> | NSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0ea5e9 !important;
            color: white !important;
            border-radius: 0.5rem;
            border: none;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }

        .dark table.dataTable.no-footer {
            border-bottom: 1px solid #374151;
        }

        .dark .dataTables_wrapper .dataTables_length,
        .dark .dataTables_wrapper .dataTables_filter,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_processing,
        .dark .dataTables_wrapper .dataTables_paginate {
            color: #d1d5db;
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-slate-900 font-sans transition-colors duration-300" x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: window.innerWidth > 1024,
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        },
        init() {
            if (this.darkMode) document.documentElement.classList.add('dark');
            lucide.createIcons();
        }
    }">

    <!-- Backdrop for mobile -->
    <div x-show="sidebarOpen"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"
        x-cloak></div>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 shadow-xl lg:shadow-none"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        @click.away="if(window.innerWidth < 1024) sidebarOpen = false"
        aria-label="Sidebar">
        <div class="h-full px-3 py-4 overflow-y-auto relative">
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="absolute top-4 right-4 p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg lg:hidden z-50">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="flex items-center ps-2.5 mb-8 flex-col">
                <div class="w-full h-8 bg-primary-600 rounded-lg flex items-center justify-center mr-3 mb-4">
                    <img src="<?= base_url('img/logo.png') ?>" alt="logo" class="w-full">
                </div>
                <div class="w-full rounded-lg flex items-center justify-center mr-3">
                    <h1 class="font-extrabold text-xl bg-clip-text text-transparent bg-gradient-to-r from-orange-500 to-black">
                        NSS APPARELS
                    </h1>
                </div>
            </div>
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="<?= base_url('dashboard') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= current_url() == base_url('dashboard') ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('customers') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= current_url() == base_url('customers') ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="ms-3">Customers</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('customers/chat') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= strpos(current_url(), 'customers/chat') !== false ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                        <span class="ms-3">Customers Chat</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('templates') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= strpos(current_url(), 'templates') !== false ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                        <i data-lucide="layout-template" class="w-5 h-5"></i>
                        <span class="ms-3">WhatsApp Templates</span>
                    </a>
                </li>

                <?php if (session()->get('role') == 'admin'): ?>
                    <div class="pt-4 mt-4 border-t border-slate-200 dark:border-slate-700">
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase">E-Commerce</span>
                    </div>
                    <li>
                        <a href="<?= base_url('catalog/categories') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= strpos(current_url(), 'catalog/categories') !== false ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                            <i data-lucide="folders" class="w-5 h-5"></i>
                            <span class="ms-3">Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('catalog/products') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= strpos(current_url(), 'catalog/products') !== false ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                            <i data-lucide="package" class="w-5 h-5"></i>
                            <span class="ms-3">Products</span>
                        </a>
                    </li>
                    <div class="pt-4 mt-4 border-t border-slate-200 dark:border-slate-700">
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase">Administration</span>
                    </div>
                    <li>
                        <a href="<?= base_url('users') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= strpos(current_url(), 'users') !== false ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                            <i data-lucide="user-cog" class="w-5 h-5"></i>
                            <span class="ms-3">User Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('settings') ?>" class="flex items-center p-2 text-slate-900 rounded-lg dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 group <?= strpos(current_url(), 'settings') !== false ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' : '' ?>">
                            <i data-lucide="settings" class="w-5 h-5"></i>
                            <span class="ms-3">Configuration</span>
                        </a>
                    </li>
                <?php endif; ?>

                <div class="pt-4 mt-4 border-t border-slate-200 dark:border-slate-700"></div>
                <li>
                    <a href="<?= base_url('logout') ?>" class="flex items-center p-2 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/10 group">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span class="ms-3">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 transition-all duration-300" :class="sidebarOpen ? 'sm:ml-64' : 'ml-0'">
        <!-- Navbar -->
        <nav class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <button @click.stop="sidebarOpen = !sidebarOpen" class="p-2 mr-2 text-slate-600 rounded-lg cursor-pointer hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 focus:outline-none z-50">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h1 class="text-xl font-semibold dark:text-white hidden sm:block"><?= $title ?? 'Dashboard' ?></h1>
            </div>
            <div class="flex items-center gap-2">
                <button @click="toggleDarkMode()" class="p-2 text-slate-500 rounded-lg hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 transition-colors">
                    <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-5 h-5"></i>
                </button>
                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>
                <div class="flex items-center gap-3 ml-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium dark:text-white text-slate-900"><?= session()->get('name') ?></p>
                        <p class="text-xs text-slate-500 capitalize"><?= session()->get('role') ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">
                        <?= strtoupper(substr(session()->get('name'), 0, 1)) ?>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-slate-800 dark:text-red-400" role="alert">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-slate-800 dark:text-green-400" role="alert">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            // Re-initialize icons on dynamic content changes if needed
        });
    </script>
</body>

</html>