<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div id="productsApp" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden" x-data="{
    products: [],
    categories: <?= htmlspecialchars(json_encode($categories), ENT_QUOTES, 'UTF-8') ?>,
    imagePreview: null,
    selectedColors: [],
    selectedSizes: [],
    customColor: '#000000',
    presetColors: ['#000000','#ffffff','#ef4444','#f97316','#eab308','#22c55e','#3b82f6','#8b5cf6','#ec4899','#06b6d4','#84cc16','#a16207','#6b7280','#1e293b','#fde68a','#bfdbfe'],
    availableSizes: ['XS','S','M','L','XL','XXL','3XL'],
    colorDropdown: false,
    isLoadingSave: false,
    existingImages: [],
    newGalleryImages: [],
    
    previewMultiple(e) {
        const files = Array.from(e.target.files);
        if (this.newGalleryImages.length + files.length > 5) {
            Swal.fire('Warning', 'Maximum 5 images allowed per batch. Please upload and then add more if needed.', 'warning');
            return;
        }
        files.forEach(f => {
            const r = new FileReader();
            r.onload = (evt) => {
                this.newGalleryImages.push({ file: f, preview: evt.target.result });
                setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 100);
            };
            r.readAsDataURL(f);
        });
        e.target.value = ''; // clear
    },
    totalRecords: 0,
    search: '',
    limit: 10,
    offset: 0,
    orderBy: 'id',
    orderDir: 'DESC',
    isLoading: false,
    categoryId: '<?= $category_id ?? '' ?>',

    async loadProducts() {
        this.isLoading = true;
        try {
            const fd = new FormData();
            if (this.categoryId) fd.append('category_id', this.categoryId);
            fd.append('search', this.search);
            fd.append('limit', this.limit);
            fd.append('offset', this.offset);
            fd.append('orderBy', this.orderBy);
            fd.append('orderDir', this.orderDir);

            const res = await fetch('<?= base_url('catalog/products_list') ?>', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'success') {
                this.products = data.products;
                this.totalRecords = data.totalRecords;
            }
        } catch (e) { console.error(e); }
        this.isLoading = false;
        setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
    },

    init() {
        this.loadProducts();
        this.$watch('search', () => { this.offset = 0; this.loadProducts(); });
        this.$watch('limit', () => { this.offset = 0; this.loadProducts(); });
        this.$watch('orderBy', () => { this.offset = 0; this.loadProducts(); });
        this.$watch('orderDir', () => { this.offset = 0; this.loadProducts(); });
    },
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
            try { this.existingImages = prod.additional_images ? JSON.parse(prod.additional_images) : []; } catch(e) { this.existingImages = []; }
            this.newGalleryImages = [];
            $('#modalTitle').text('Edit Product');
        } else {
            $('#prodForm')[0].reset();
            $('#prodId').val('');
            this.imagePreview = null;
            this.selectedColors = [];
            this.selectedSizes = [];
            this.existingImages = [];
            this.newGalleryImages = [];
            $('#modalTitle').text('Add Product');
        }
        $('#prodModal').removeClass('hidden');
        setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 150);
    },
    deleteProduct(id) {
        Swal.fire({
            title: 'Delete Product?',
            text: 'Are you sure you want to delete this product?',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('catalog/delete_product') ?>', { id: id }, (res) => {
                    if (res.status === 'success') {
                        this.products = this.products.filter(p => p.id != id);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
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
    
    <!-- Toolbar Filters -->
    <div class="px-6 py-3 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
        <div class="relative w-full md:w-64">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="search" class="w-4 h-4"></i></span>
            <input type="text" x-model.debounce.300ms="search" placeholder="Search products..." class="w-full pl-9 pr-4 py-1.5 bg-white dark:bg-slate-800 border rounded-lg outline-none text-sm border-slate-200 dark:border-slate-700">
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <select x-model="limit" class="text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 outline-none">
                <option value="10">10 per page</option>
                <option value="20">20 per page</option>
                <option value="50">50 per page</option>
            </select>
            <select x-model="orderBy" class="text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 outline-none">
                <option value="id">Order by ID</option>
                <option value="name">Order by Name</option>
            </select>
            <select x-model="orderDir" class="text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 outline-none">
                <option value="DESC">Descending</option>
                <option value="ASC">Ascending</option>
            </select>
        </div>
    </div>

    <div class="p-6 relative min-h-[300px]">
        
        <!-- Loader -->
        <div x-show="isLoading" class="absolute inset-0 bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm z-30 flex items-center justify-center">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-primary-500"></i>
        </div>
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
                            <div class="flex items-center gap-1">
                                <button @click="openModal(prod)" class="p-1.5 bg-slate-100 dark:bg-slate-700 rounded-md text-blue-500 hover:bg-blue-200" title="Edit"><i data-lucide="edit-3" class="w-3.5 h-3.5"></i></button>
                                <button @click="deleteProduct(prod.id)" class="p-1.5 bg-red-50 dark:bg-red-900/10 rounded-md text-red-500 hover:bg-red-100" title="Delete"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination Footer -->
        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4 border-t dark:border-slate-700/40 pt-4" x-show="products.length > 0">
            <div class="text-xs text-slate-500">
                Showing <span class="font-bold text-slate-700 dark:text-slate-300" x-text="offset + 1"></span> to
                <span class="font-bold text-slate-700 dark:text-slate-300" x-text="Math.min(offset + products.length, totalRecords)"></span>
                of <span class="font-bold text-slate-700 dark:text-slate-300" x-text="totalRecords"></span> records
            </div>

            <div class="flex items-center gap-2">
                <button @click="offset = Math.max(0, offset - limit); loadProducts()" :disabled="offset <= 0" class="px-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 border dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 disabled:opacity-50 font-bold">Prev</button>
                <button @click="offset = offset + limit; loadProducts()" :disabled="offset + products.length >= totalRecords" class="px-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 border dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 disabled:opacity-50 font-bold">Next</button>
            </div>
        </div>
    </div>

    <div id="prodModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-2xl rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden max-h-[90vh] flex flex-col">
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

                <!-- Colors Multi-Select removed -->

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
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Gallery Images (Multiple) <span class="text-[10px] text-slate-400 font-normal">(Max 5 per batch)</span></label>
                    <input type="file" accept="image/*" multiple @change="previewMultiple($event)" class="w-full mt-1 p-1 bg-slate-50 dark:bg-slate-900 border rounded-lg text-xs">
                    
                    <!-- New Uploads Preview Display -->
                    <div class="grid grid-cols-4 gap-2 mt-2" x-show="newGalleryImages.length > 0">
                        <template x-for="(img, idx) in newGalleryImages" :key="idx">
                            <div class="relative aspect-square border border-dashed border-primary-300 rounded-xl overflow-hidden group bg-slate-50 dark:bg-slate-900/50">
                                <img :src="img.preview" class="w-full h-full object-cover">
                                <button type="button" @click="newGalleryImages.splice(idx, 1)" class="absolute top-1 right-1 bg-red-500 rounded-full p-1 text-white opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity duration-200" title="Remove temporary image">
                                    <i data-lucide="x" class="w-2.5 h-2.5"></i>
                                </button>
                                <span class="absolute bottom-1 right-1 bg-primary-100 text-primary-700 text-[8px] px-1 rounded font-bold">New</span>
                            </div>
                        </template>
                    </div>

                    <!-- Existing Images Display inside Modal -->
                    <div class="grid grid-cols-4 gap-2 mt-2" x-show="existingImages.length > 0">
                        <template x-for="img in existingImages" :key="img">
                            <div class="relative aspect-square border border-slate-200 dark:border-slate-700/60 rounded-xl overflow-hidden group bg-slate-100 dark:bg-slate-900">
                                <img :src="'<?= base_url() ?>' + img" class="w-full h-full object-cover">
                                <button type="button" @click="existingImages = existingImages.filter(i => i !== img)" class="absolute top-1 right-1 bg-red-500 rounded-full p-1 text-white opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i data-lucide="x" class="w-2.5 h-2.5"></i>
                                </button>
                            </div>
                        </template>
                    </div>
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
                    <button type="submit" :disabled="isLoadingSave" class="p-2 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-bold flex items-center gap-2 disabled:opacity-50">
                        <i x-show="isLoadingSave" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        <span x-text="isLoadingSave ? 'Saving...' : 'Save Product'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#prodForm').on('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);

        const alpine = Alpine.$data(document.getElementById('productsApp'));
        alpine.isLoadingSave = true;

        fd.set('colors', JSON.stringify(alpine.selectedColors));
        fd.set('sizes', JSON.stringify(alpine.selectedSizes));
        fd.set('existing_additional_images', JSON.stringify(alpine.existingImages)); // send back updated list
        
        // Append new multi-uploads from Alpine manually
        alpine.newGalleryImages.forEach(img => {
            fd.append('gallery_images[]', img.file);
        });

        $.ajax({
            url: '<?= base_url('catalog/save_product') ?>',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                alpine.isLoadingSave = false;
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function() {
                alpine.isLoadingSave = false;
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        });
    });
</script>
<?= $this->endSection() ?>