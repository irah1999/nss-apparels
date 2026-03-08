<!-- Footer -->
<footer class="py-12 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-950">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary-600 rounded flex items-center justify-center text-white">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
                <span class="text-xl font-bold font-serif italic text-primary-600">NSS APPARELS</span>
            </div>

            <div class="text-sm text-slate-500 dark:text-slate-400 text-center md:text-left">
                &copy; <?= date('Y') ?> NSS APPARELS. All rights reserved.
                <span class="hidden md:inline">| Elevate Your Style</span>
                <p class="mt-1 text-xs opacity-75">GSTIN: 33CESPJ3443N1Z1</p>
            </div>

            <div class="flex items-center gap-6">
                <a href="<?= base_url('login') ?>" class="text-sm font-bold text-accent-800 dark:text-primary-400 hover:text-primary-600 flex items-center gap-2 transition-colors">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Administration Login
                </a>
            </div>
        </div>
    </div>
</footer>