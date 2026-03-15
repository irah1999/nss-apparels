<?php
// Gather all available images in the category (including additional gallery images)
$all_images = [];
if (!empty($products)) {
    foreach ($products as $p) {
        if (!empty($p['main_image'])) {
            $all_images[] = $p['main_image'];
        }
        if (!empty($p['additional_images'])) {
            $addtl = json_decode($p['additional_images'], true);
            if (is_array($addtl)) {
                foreach ($addtl as $img_path) {
                    $all_images[] = $img_path;
                }
            }
        }
    }
}
$all_images = array_values(array_unique($all_images));
?>
<a href="<?= isset($id) ? base_url('products/' . $id) : '#' ?>" class="group block bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition-all duration-500 relative">

    <!-- Main Image — Full Cover -->
    <div class="relative w-full aspect-[4/3] overflow-hidden bg-slate-100 dark:bg-slate-900">
        <?php if (!empty($all_images)): ?>
            <img src="<?= base_url($all_images[0]) ?>"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                alt="<?= $title ?>">
        <?php elseif (!empty($img)): ?>
            <img src="<?= $img ?>"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                alt="<?= $title ?>">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <i data-lucide="image" class="w-12 h-12 text-slate-300"></i>
            </div>
        <?php endif; ?>

        <!-- Category Title Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent flex items-end p-5">
            <h3 class="text-xl font-bold font-serif text-white drop-shadow-lg group-hover:text-primary-300 transition-colors">
                <?= $title ?>
            </h3>
        </div>

        <!-- Product Count Badge -->
        <?php if (!empty($products)): ?>
            <div class="absolute top-3 right-3">
                <span class="bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-200 text-[10px] font-bold px-2 py-1 rounded-full shadow">
                    <?= count($products) ?> items
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Thumbnail Strip — full width, no padding, no gaps -->
    <?php if (count($all_images) > 1): ?>
        <div class="grid grid-cols-4 gap-px bg-slate-200 dark:bg-slate-700">
            <?php foreach (array_slice($all_images, 1, 4) as $img): ?>
                <div class="aspect-square overflow-hidden bg-slate-100 dark:bg-slate-900">
                    <img src="<?= base_url($img) ?>" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
            <?php endforeach; ?>
            <!-- Fill empty slots -->
            <?php for ($i = count(array_slice($all_images, 1, 4)); $i < 4; $i++): ?>
                <div class="aspect-square bg-slate-100 dark:bg-slate-800"></div>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <!-- VIEW CTA -->
    <div class="bg-primary-50 dark:bg-primary-900/10 text-primary-600 font-bold p-3 text-center text-sm flex items-center justify-center gap-1 group-hover:bg-primary-600 group-hover:text-white transition-colors duration-300">
        VIEW COLLECTION <i data-lucide="arrow-right" class="w-4 h-4"></i>
    </div>

</a>