<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<header class="relative min-h-screen flex items-center pt-20 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute top-20 right-[-10%] w-[500px] h-[500px] bg-primary-100/50 dark:bg-primary-900/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-20 left-[-10%] w-[400px] h-[400px] bg-accent-100/50 dark:bg-accent-900/20 rounded-full blur-[100px]"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="text-center md:text-left">
                <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wider text-primary-600 uppercase bg-primary-50 dark:bg-primary-900/30 rounded-full">
                    Premium Quality Apparel
                </span>
                <h1 class="text-5xl md:text-7xl font-serif font-bold leading-tight mb-6">
                    Define Your <br>
                    <span class="text-primary-600 italic">Unique</span> Style
                </h1>
                <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-lg mx-auto md:mx-0">
                    NSS APPARELS brings you the finest selection of modern clothing. Crafted with passion, designed for comfort, and styled for the trendsetters.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="#products" class="px-8 py-4 bg-primary-600 text-white font-bold rounded-full shadow-lg shadow-primary-500/40 hover:bg-primary-700 transition-all flex items-center justify-center gap-2">
                        Explore Collection <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                    <a href="<?= base_url('login') ?>" class="px-8 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 font-bold rounded-full hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-center">
                        Staff Login
                    </a>
                </div>
            </div>
            <div class="relative hidden md:block">
                <div class="relative z-10 w-full aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl float-animation">
                    <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=800&auto=format&fit=crop" alt="Fashion Apparel" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Featured Categories -->
<section id="products" class="py-24">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div>
                <h2 class="text-4xl font-serif font-bold mb-4">Our Collections</h2>
                <p class="text-slate-600 dark:text-slate-400 max-w-md">Discover the perfect blend of tradition and modern style in our latest apparel range.</p>
            </div>
            <a href="#" class="text-primary-600 font-bold flex items-center gap-2 hover:gap-3 transition-all">
                View All Products <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($categories as $cat): ?>
                <?= $this->setData([
                    'id' => $cat['id'],
                    'title' => $cat['name'],
                    'img' => $cat['image'] ? base_url($cat['image']) : 'https://images.unsplash.com/photo-1594932224010-74f43a185664?q=80&w=500&auto=format&fit=crop',
                    'products' => $cat['products'] ?? []
                ])->include('partials/frontend/category_card') ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Support Section -->
<section id="support" class="py-24 bg-slate-50 dark:bg-slate-900/50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-serif font-bold mb-4">Customer Support</h2>
            <div class="w-20 h-1 bg-primary-600 mx-auto rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?= $this->setData(['icon' => 'truck', 'title' => 'Fast Delivery', 'desc' => 'Quick and reliable shipping service across Tamil Nadu and beyond.'])->include('partials/frontend/support_card') ?>
            <?= $this->setData(['icon' => 'shield-check', 'title' => 'Quality Assurance', 'desc' => 'Every garment undergoes strict quality checks to ensure perfection.'])->include('partials/frontend/support_card') ?>
            <?= $this->setData(['icon' => 'headset', 'title' => '24/7 Support', 'desc' => 'Our support team is always ready to help you with your queries.'])->include('partials/frontend/support_card') ?>
        </div>
    </div>
</section>

<!-- Info Section -->
<section id="contact" class="py-24">
    <div class="container mx-auto px-6">
        <div class="bg-accent-900 text-white rounded-[3rem] p-12 md:p-20 overflow-hidden relative shadow-2xl">
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary-600/20 rounded-full blur-[100px]"></div>

            <div class="grid md:grid-cols-2 gap-12 relative z-10 items-center">
                <div>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold mb-8">Visit Our Store</h2>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <i data-lucide="map-pin" class="w-6 h-6 text-primary-400 shrink-0 mt-1"></i>
                            <p class="text-lg text-slate-300">
                                392/2, Sarada collage road, autostand, sounth alagapuram /Fair lands, salem Tamil nadu 636016
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <i data-lucide="phone" class="w-6 h-6 text-primary-400 shrink-0"></i>
                            <p class="text-lg text-slate-300">+91 123 456 7890</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <i data-lucide="mail" class="w-6 h-6 text-primary-400 shrink-0"></i>
                            <p class="text-lg text-slate-300">hello@nssapparels.com</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <i data-lucide="file-badge" class="w-6 h-6 text-primary-400 shrink-0"></i>
                            <p class="text-lg text-slate-300 italic">GSTIN: 33CESPJ3443N1Z1</p>
                        </div>
                    </div>
                </div>
                <div class="h-64 md:h-96 rounded-3xl bg-slate-800 overflow-hidden border-4 border-slate-700 shadow-inner">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=800&auto=format&fit=crop" alt="Shop Interior" class="w-full h-full object-cover opacity-60">
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>