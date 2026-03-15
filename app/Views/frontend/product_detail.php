<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>
<?php
$gallery = [];
if (!empty($product['main_image'])) {
    $gallery[] = base_url($product['main_image']);
}
$additional = json_decode($product['additional_images'] ?? '[]', true);
if (is_array($additional)) {
    foreach ($additional as $img) {
        $gallery[] = base_url($img);
    }
}
?>
<div class="pt-24 pb-16 bg-slate-50 dark:bg-slate-950 min-h-screen" x-data="{
    product: <?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>,
    gallery: <?= htmlspecialchars(json_encode($gallery), ENT_QUOTES, 'UTF-8') ?>,
    activeImage: '<?= !empty($gallery) ? $gallery[0] : '' ?>',
    selectedColor: null,
    selectedSize: null,
    whatsappNumber: '<?= $product['whatsapp_number'] ?: '8098760720' ?>',

    sendOrder() {
        if (!this.selectedSize && this.product.sizes && this.product.sizes != '[]') {
            alert('Please select a Size first!'); return;
        }
        let text = `Hi, I am interested in ordering:\n\n*Product:* ${this.product.name}\n*Category:* <?= $category ? $category['name'] : 'Apparel' ?>\n*Size:* ${this.selectedSize || 'FreeSize'}`;
        if (this.selectedColor) text += `\n*Color Info:* Refer detail`;
        text += `\n\nIs this available?`;

        let url = `https://wa.me/91${this.whatsappNumber}?text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
    }
}">
    <div class="container mx-auto px-6">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-8">
            <a href="<?= base_url() ?>" class="hover:text-primary-600">Home</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="<?= base_url('products/' . ($category ? $category['id'] : '')) ?>" class="hover:text-primary-600"><?= $category ? $category['name'] : 'Products' ?></a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-slate-900 dark:text-white font-bold"><?= $product['name'] ?></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white dark:bg-slate-800 p-6 md:p-10 rounded-3xl border border-slate-200 dark:border-slate-700/50 shadow-sm">

            <!-- Left: Product Image Slider/Preview -->
            <div class="flex flex-col gap-4">
                <!-- Main Focus Image -->
                <div class="aspect-square bg-slate-100 dark:bg-slate-900 rounded-3xl overflow-hidden relative border border-slate-100 dark:border-slate-800">
                    <template x-if="activeImage">
                        <img :src="activeImage" class="w-full h-full object-cover transition-all duration-500 hover:scale-105">
                    </template>
                    <template x-if="!activeImage">
                        <i data-lucide="image" class="w-16 h-16 text-slate-300 absolute inset-0 m-auto"></i>
                    </template>
                </div>

                <!-- Thumbnails Strip -->
                <div class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar" x-show="gallery.length > 1">
                    <template x-for="img in gallery" :key="img">
                        <button @click="activeImage = img"
                            class="flex-shrink-0 w-20 h-20 rounded-2xl overflow-hidden border-4 transition-all duration-300"
                            :class="activeImage === img ? 'border-primary-500 scale-105 shadow-lg shadow-primary-500/20' : 'border-slate-100 dark:border-slate-800 opacity-60 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Right: Product Information -->
            <div class="flex flex-col justify-center">
                <h1 class="text-3xl md:text-4xl font-serif font-bold text-slate-900 dark:text-white"><?= $product['name'] ?></h1>

                <p class="text-slate-500 dark:text-slate-400 mt-4 text-sm leading-relaxed">
                    <?= $product['description'] ?: 'Crafted with premium quality materials to offer maximum comfort and durability. This exquisite wear matches with any modern trendsetters style flawlessly.' ?>
                </p>

                <!-- Color Selection (If any) -->
                <?php
                $colors = json_decode($product['colors'] ?? '[]', true);
                if (!empty($colors)):
                ?>
                    <div class="mt-6">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Available Colors:</label>
                        <div class="flex items-center gap-2">
                            <?php foreach ($colors as $c): ?>
                                <button @click="selectedColor = '<?= $c ?>'" class="w-7 h-7 rounded-full border-2 shadow-sm transition-transform hover:scale-110 flex items-center justify-center" :class="selectedColor === '<?= $c ?>' ? 'border-primary-500 scale-110' : 'border-white dark:border-slate-800'" style="background-color: <?= $c ?>">
                                    <span x-show="selectedColor === '<?= $c ?>'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Size Selection (If any) -->
                <?php
                $sizes = json_decode($product['sizes'] ?? '[]', true);
                if (!empty($sizes)):
                ?>
                    <div class="mt-6">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Select Size:</label>
                        <div class="flex items-center gap-2 flex-wrap">
                            <?php foreach ($sizes as $sz): ?>
                                <button @click="selectedSize = '<?= $sz ?>'" class="px-4 py-2 rounded-xl border text-sm font-bold transition-all" :class="selectedSize === '<?= $sz ?>' ? 'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-500/30' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-100'">
                                    <?= $sz ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-6">
                        <span class="text-xs text-slate-400 italic">One Size fits all</span>
                        <input type="hidden" x-init="selectedSize = 'FreeSize'">
                    </div>
                <?php endif; ?>

                <!-- Action CTA -->
                <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-700/50 flex flex-col sm:flex-row items-center gap-4">
                    <button @click="sendOrder()" class="w-full sm:w-auto px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-extrabold rounded-2xl shadow-xl shadow-green-500/20 flex items-center justify-center gap-3 transition-all hover:-translate-y-0.5">
                        <i data-lucide="message-square" class="w-5 h-5"></i> Order via WhatsApp
                    </button>
                    <div class="flex flex-col text-slate-400 text-xs">
                        <span>Direct vendor pricing</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300"><?= $product['whatsapp_number'] ?: '8098760720' ?></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Related Products Section -->
        <?php if (!empty($related_products)): ?>
            <div class="mt-16">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Related Products</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <?php foreach ($related_products as $rel): ?>
                        <a href="<?= base_url('product-detail/' . $rel['id']) ?>" class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all">
                            <div class="aspect-square bg-slate-100 dark:bg-slate-900 overflow-hidden relative">
                                <?php if ($rel['main_image']): ?>
                                    <img src="<?= base_url($rel['main_image']) ?>" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                            <div class="p-3">
                                <h4 class="font-bold text-sm text-slate-800 dark:text-white truncate"><?= $rel['name'] ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>