<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled ? 'glass shadow-md py-3' : 'bg-transparent py-6'">
    <div class="container mx-auto px-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                <i data-lucide="shopping-bag" class="w-6 h-6"></i>
            </div>
            <span class="text-2xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-accent-800 dark:to-primary-400 font-serif">
                NSS APPARELS
            </span>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-8">
            <a href="<?= base_url() ?>" class="font-medium hover:text-primary-600 transition-colors">Home</a>
            <a href="#support" class="font-medium hover:text-primary-600 transition-colors">Support</a>
            <a href="#contact" class="font-medium hover:text-primary-600 transition-colors">Contact</a>
            <button @click="toggleDarkMode()" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Mobile Toggle -->
        <div class="md:hidden flex items-center gap-4">
            <button @click="toggleDarkMode()" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-5 h-5"></i>
            </button>
            <button @click="mobileMenu = !mobileMenu" class="p-2 text-slate-600 dark:text-slate-300">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenu"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        x-cloak class="md:hidden glass absolute top-full left-0 right-0 border-t border-slate-200 dark:border-slate-800 shadow-xl">
        <div class="flex flex-col p-6 gap-4">
            <a href="<?= base_url() ?>" class="text-lg font-medium">Home</a>
            <a href="#support" class="text-lg font-medium">Support</a>
            <a href="#contact" class="text-lg font-medium">Contact</a>
        </div>
    </div>
</nav>