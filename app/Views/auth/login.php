<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | NSS Business</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 dark:bg-slate-900 font-sans h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-8 border border-slate-200 dark:border-slate-700">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-2xl mb-4 shadow-lg shadow-primary-500/30">
                    <i data-lucide="shopping-bag" class="text-white w-8 h-8"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white font-serif italic">NSS APPARELS</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Manage your fashion excellence</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="p-3 mb-6 text-sm text-red-600 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="email" name="email" required
                            class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 transition-all outline-none"
                            placeholder="name@company.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="password" name="password" required
                            class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 transition-all outline-none"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                        <label for="remember" class="ml-2 text-sm text-slate-600 dark:text-slate-400">Remember me</label>
                    </div>
                    <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-primary-500/30 transition-all transform active:scale-[0.98]">
                    Sign In
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                &copy; <?= date('Y') ?> NSS Business. All rights reserved.
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>