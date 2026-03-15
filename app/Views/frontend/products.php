<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>
<div class="pt-24 pb-16 bg-slate-50 dark:bg-slate-900/50 min-h-screen">
    <div class="container mx-auto px-6">

        <!-- Breadcrumb / Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="<?= base_url() ?>" class="hover:text-primary-600">Home</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-slate-900 dark:text-white font-bold" x-text="'<?= $category ? $category['name'] : 'All Products' ?>'"></span>
            </div>

            <h1 class="text-4xl font-serif font-bold text-slate-900 dark:text-white">
                <?= $category ? $category['name'] : 'Our Collection' ?>
            </h1>
            <?php if ($category && !empty($category['description'] ?? '')): ?>
                <p class="text-slate-500 mt-1 max-w-xl text-sm"><?= $category['description'] ?? '' ?></p>
            <?php endif; ?>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Sidebar Filters -->
            <div class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm sticky top-24">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="<?= base_url('products') ?>" class="flex items-center justify-between p-2 rounded-lg text-sm <?= !$category ? 'bg-primary-50 text-primary-600 font-bold dark:bg-primary-900/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' ?>">
                                <span>All Products</span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= base_url('products/' . $cat['id']) ?>" class="flex items-center justify-between p-2 rounded-lg text-sm <?= $category && $category['id'] == $cat['id'] ? 'bg-primary-50 text-primary-600 font-bold dark:bg-primary-900/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' ?>">
                                    <span><?= $cat['name'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="flex-1">
                <?php if (empty($products)): ?>
                    <div class="text-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <i data-lucide="package-search" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                        <h3 class="text-xl font-bold text-slate-700 dark:text-white">No items found</h3>
                        <p class="text-sm text-slate-500 mt-1">We are updating our catalog soon, check back later!</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($products as $prod): ?>
                            <a href="<?= base_url('product-detail/' . $prod['id']) ?>" class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-700/50 shadow-sm hover:-translate-y-1 hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                                <div>
                                    <div class="aspect-[3/4] bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden relative">
                                        <?php if ($prod['main_image']): ?>
                                            <img src="<?= base_url($prod['main_image']) ?>" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0 group-hover:scale-105" style="z-index: 2;">
                                            <?php
                                            // Fallback/Hover image
                                            $addl = json_decode($prod['additional_images'] ?? '[]', true);
                                            $hoverImg = !empty($addl) && isset($addl[0]) ? base_url($addl[0]) : base_url($prod['main_image']);
                                            ?>
                                            <img src="<?= $hoverImg ?>" class="relative w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" style="z-index: 1;">
                                        <?php else: ?>
                                            <i data-lucide="image" class="w-10 h-10 text-slate-300"></i>
                                        <?php endif; ?>
                                        <div class="absolute top-3 left-3">
                                            <span class="bg-gradient-to-r from-primary-500 to-primary-600 text-white text-[9px] font-bold px-2.5 py-1 rounded-full shadow">NEW</span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <h4 class="font-bold text-sm text-slate-800 dark:text-white truncate group-hover:text-primary-600 transition-colors" title="<?= $prod['name'] ?>"><?= $prod['name'] ?></h4>
                                        <p class="text-[10px] text-slate-400 truncate mt-1"><?= $prod['description'] ?: 'Premium wear selection' ?></p>
                                    </div>
                                </div>

                                <div class="px-4 pb-4">
                                    <div class="flex items-center gap-1.5 mt-2">
                                        <!-- Color dots preview if any -->
                                        <?php
                                        $colors = json_decode($prod['colors'] ?? '[]', true);
                                        if ($colors): foreach (array_slice($colors, 0, 4) as $c):
                                        ?>
                                                <span class="w-3 h-3 rounded-full border border-white dark:border-slate-700 shadow-sm" style="background-color: <?= $c ?>"></span>
                                        <?php endforeach;
                                        endif; ?>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                        <span class="text-primary-600 font-bold text-xs">WhatsApp Order</span>
                                        <div class="p-1.5 bg-green-50 dark:bg-green-900/10 text-green-600 dark:text-green-400 rounded-lg group-hover:scale-110 transition-transform"><i data-lucide="message-square" class="w-3.5 h-3.5"></i></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>
<?= $this->endSection() ?>