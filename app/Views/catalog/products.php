<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden" x-data="{
    products: <?= htmlspecialchars(json_encode($products), ENT_QUOTES, 'UTF-8') ?>,
    categories: <?= htmlspecialchars(json_encode($categories), ENT_QUOTES, 'UTF-8') ?>,
    imagePreview: null,
    selectedColors: [],
    selectedSizes: [],
    customColor: '#000000',
    presetColors: ['#000000','#ffffff','#ef4444','#f97316','#eab308','#22c55e','#3b82f6','#8b5cf6','#ec4899','#06b6d4','#84cc16','#a16207','#6b7280','#1e293b','#fde68a','#bfdbfe'],
    availableSizes: ['XS','S','M','L','XL','XXL','3XL'],
    colorDropdown: false,
    addCustomColor() {
        if (this.customColor && !this.selectedColors.includes(this.customColor)) {
            this.selectedColors.push(this.customColor);
        }
    },
    toggleColor(c) {
        const idx = this.selectedColors.indexOf(c);
        if (idx > -1) this.selectedColors.splice(idx, 1);
        else this.selectedColors.push(c);
    },
    toggleSize(s) {
        const idx = this.selectedSizes.indexOf(s);
        if (idx > -1) this.selectedSizes.splice(idx, 1);
        else this.selectedSizes.push(s);
    },
    previewImage(e) {
        const file = e.target.files[0];
        if (file) {
            const r = new FileReader();
            r.onload = (evt) => {
                this.imagePreview = evt.target.result;
            };
            r.readAsDataURL(file);
        }
    },
    openModal(prod = null) {
        if (prod) {
            $('#prodId').val(prod.id);
            $('#prodName').val(prod.name);
            $('#prodCat').val(prod.category_id);
            $('#prodDesc').val(prod.description);
            $('#prodWa').val(prod.whatsapp_number);
            $('#prodStatus').val(prod.status);
            this.imagePreview = prod.main_image ? '<?= base_url() ?>' + prod.main_image : null;
            // Hydrate colors/sizes from JSON string
            try { this.selectedColors = prod.colors ? JSON.parse(prod.colors) : []; } catch(e) { this.selectedColors = []; }
            try { this.selectedSizes = prod.sizes ? JSON.parse(prod.sizes) : []; } catch(e) { this.selectedSizes = []; }
            $('#modalTitle').text('Edit Product');
        } else {
            $('#prodForm')[0].reset();
            $('#prodId').val('');
            this.imagePreview = null;
            this.selectedColors = [];
            this.selectedSizes = [];
            $('#modalTitle').text('Add Product');
        }
        $('#prodModal').removeClass('hidden');
    }
}">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Products Setup</h2>
            <p class="text-xs text-slate-500" x-text="categories?.find(c => c.id == '<?= $category ? $category['id'] : 0 ?>')?.name || 'All Catalog Listings'"></p>
        </div>
        <button @click="openModal()" class="flex items-center gap-2 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-500/30 transition-all text-sm font-semibold">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Product
        </button>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <template x-for="prod in products" :key="prod.id">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all relative group">
                    <div class="aspect-square bg-slate-100 dark:bg-slate-900/50 flex items-center justify-center overflow-hidden">
                        <img x-show="prod.main_image" :src="'<?= base_url() ?>' + prod.main_image" class="w-full h-full object-cover">
                        <i x-show="!prod.main_image" data-lucide="image" class="w-8 h-8 text-slate-400"></i>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white truncate" x-text="prod.name"></h4>
                        <p class="text-[10px] text-slate-400 truncate mt-1" x-text="prod.description || 'No description'"></p>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold" :class="prod.status === 'active' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'" x-text="prod.status"></span>
                            <button @click="openModal(prod)" class="p-1.5 bg-slate-100 dark:bg-slate-700 rounded-md text-blue-500 hover:bg-blue-200"><i data-lucide="edit-3" class="w-3.5 h-3.5"></i></button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div id="prodModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-bold text-slate-800 dark:text-white" id="modalTitle">Add Product</h3>
                <button onclick="$('#prodModal').addClass('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="prodForm" class="p-6 space-y-4 overflow-y-auto">
                <input type="hidden" name="id" id="prodId">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Category</label>
                        <select name="category_id" id="prodCat" required class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border rounded-lg text-sm">
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Name</label>
                        <input type="text" name="name" id="prodName" required class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border rounded-lg text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Description</label>
                    <textarea name="description" id="prodDesc" rows="3" class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border rounded-lg text-sm"></textarea>
                </div>

                <!-- Colors Multi-Select -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Colors</label>
                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-3 bg-slate-50 dark:bg-slate-900 space-y-2">
                        <!-- Selected colors display -->
                        <div class="flex flex-wrap gap-2 min-h-[28px]">
                            <template x-for="(c, i) in selectedColors" :key="c">
                                <div class="flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-full px-2 py-0.5 shadow-sm">
                                    <span class="inline-block w-4 h-4 rounded-full border border-slate-300" :style="'background:'+c"></span>
                                    <span class="text-[10px] font-mono text-slate-600 dark:text-slate-300" x-text="c"></span>
                                    <button type="button" @click="selectedColors.splice(i,1)" class="text-slate-400 hover:text-red-500 ml-0.5 text-xs leading-none">&times;</button>
                                </div>
                            </template>
                            <span x-show="selectedColors.length === 0" class="text-xs text-slate-400">No colors selected</span>
                        </div>
                        <!-- Preset color swatches -->
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="c in presetColors" :key="c">
                                <button type="button" @click="toggleColor(c)"
                                    :title="c"
                                    class="w-6 h-6 rounded-full border-2 transition-all hover:scale-110"
                                    :style="'background:'+c"
                                    :class="selectedColors.includes(c) ? 'border-primary-500 ring-2 ring-primary-300 scale-110' : 'border-slate-300 dark:border-slate-600'">
                                </button>
                            </template>
                        </div>
                        <!-- Custom color picker -->
                        <div class="flex items-center gap-2 pt-1 border-t border-slate-200 dark:border-slate-700">
                            <input type="color" x-model="customColor" class="w-7 h-7 rounded cursor-pointer border border-slate-300" title="Pick custom color">
                            <input type="text" x-model="customColor" class="flex-1 text-xs font-mono p-1 border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800" placeholder="#hex">
                            <button type="button" @click="addCustomColor()" class="text-xs px-2 py-1 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-semibold">Add</button>
                        </div>
                    </div>
                </div>

                <!-- Sizes Multi-Select -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Sizes</label>
                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-3 bg-slate-50 dark:bg-slate-900">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="s in availableSizes" :key="s">
                                <button type="button" @click="toggleSize(s)"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold border-2 transition-all hover:scale-105"
                                    :class="selectedSizes.includes(s)
                                        ? 'bg-primary-600 border-primary-600 text-white shadow-md shadow-primary-200'
                                        : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-primary-400'">
                                    <span x-text="s"></span>
                                </button>
                            </template>
                        </div>
                        <p class="mt-2 text-[10px] text-slate-400" x-show="selectedSizes.length > 0">
                            Selected: <span class="font-semibold text-primary-600" x-text="selectedSizes.join(', ')"></span>
                        </p>
                        <p class="mt-2 text-[10px] text-slate-400" x-show="selectedSizes.length === 0">Click sizes to select</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Display Image</label>
                        <input type="file" name="main_image" accept="image/*" @change="previewImage($event)" class="w-full mt-1 p-1 bg-slate-50 rounded-lg text-xs border">

                        <!-- Preview Container -->
                        <div class="mt-2 w-full h-24 bg-slate-100 dark:bg-slate-900/50 rounded-xl flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700">
                            <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                            <div x-show="!imagePreview" class="text-center text-slate-400">
                                <i data-lucide="image" class="w-6 h-6 mx-auto mb-1"></i>
                                <span class="text-[9px]">No preview</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" id="prodWa" placeholder="8098760720" class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border rounded-lg text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Gallery Images (Multiple)</label>
                    <input type="file" name="gallery_images[]" accept="image/*" multiple class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border rounded-lg text-xs">
                    <p class="text-[10px] text-slate-400 mt-1">Hold Ctrl/Cmd to select multiple images (5-10 images max recommended).</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <select name="status" id="prodStatus" class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border rounded-lg text-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="$('#prodModal').addClass('hidden')" class="text-sm text-slate-500 font-bold">Cancel</button>
                    <button type="submit" class="p-2 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-bold">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#prodForm').on('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);

        // Inject selected colors & sizes as JSON from Alpine component
        const alpine = Alpine.$data(document.querySelector('[x-data]'));
        fd.set('colors', JSON.stringify(alpine.selectedColors));
        fd.set('sizes', JSON.stringify(alpine.selectedSizes));

        $.ajax({
            url: '<?= base_url('catalog/save_product') ?>',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        location.reload();
                    });
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>