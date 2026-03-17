<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{
    categories: [],
    totalRecords: 0,
    search: '',
    limit: 30,
    offset: 0,
    orderBy: 'id',
    orderDir: 'DESC',
    isLoading: false,
    imagePreview: null,

    async loadCategories() {
        this.isLoading = true;
        try {
            const fd = new FormData();
            fd.append('search', this.search);
            fd.append('limit', this.limit);
            fd.append('offset', this.offset);
            fd.append('orderBy', this.orderBy);
            fd.append('orderDir', this.orderDir);

            const res = await fetch('<?= base_url('catalog/categories_list') ?>', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'success') {
                this.categories = data.categories;
                this.totalRecords = data.totalRecords;
            }
        } catch (e) { console.error(e); }
        this.isLoading = false;
        setTimeout(() => lucide.createIcons(), 50);
    },

    init() {
        this.loadCategories();
        this.$watch('search', () => { this.offset = 0; this.loadCategories(); });
        this.$watch('limit', () => { this.offset = 0; this.loadCategories(); });
        this.$watch('orderBy', () => { this.offset = 0; this.loadCategories(); });
        this.$watch('orderDir', () => { this.offset = 0; this.loadCategories(); });
    },

    previewImage(e) {
        const file = e.target.files[0];
        if (file) {
            const r = new FileReader();
            r.onload = (evt) => { this.imagePreview = evt.target.result; };
            r.readAsDataURL(file);
        }
    },

    openModal(cat = null) {
        if (cat) {
            $('#catId').val(cat.id);
            $('#catName').val(cat.name);
            $('#catStatus').val(cat.status);
            this.imagePreview = cat.image ? '<?= base_url() ?>' + cat.image : null;
            $('#modalTitle').text('Edit Category');
        } else {
            $('#catForm')[0].reset();
            $('#catId').val('');
            this.imagePreview = null;
            $('#modalTitle').text('Add Category');
        }
        $('#catModal').removeClass('hidden');
    },

    deleteCategory(id) {
        Swal.fire({
            title: 'Delete Category?',
            text: 'Are you sure you want to delete this category?',
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('catalog/delete_category') ?>', { id: id }, (res) => {
                    if (res.status === 'success') {
                        Swal.fire('Deleted!', res.message, 'success');
                        this.loadCategories();
                    }
                });
            }
        });
    }
}">

    <!-- Filter Bar Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold font-serif text-slate-900 dark:text-white">Product Categories</h1>
            <p class="text-xs text-slate-500">Search and organize your dynamic marketplace display</p>
        </div>
        <button @click="openModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-500/30 transition-all font-bold text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Category
        </button>
    </div>

    <!-- Toolbar Filters -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="relative w-full md:w-64">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="search" class="w-4 h-4"></i></span>
            <input type="text" x-model.debounce.300ms="search" placeholder="Search categories..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border rounded-lg outline-none text-sm border-slate-200 dark:border-slate-700">
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <select x-model="limit" class="text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 outline-none">
                <option value="15">15 per page</option>
                <option value="30">30 per page</option>
                <option value="50">50 per page</option>
            </select>
            <select x-model="orderBy" class="text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 outline-none">
                <option value="id">Order by ID</option>
                <option value="name">Order by Name</option>
            </select>
            <select x-model="orderDir" class="text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 outline-none">
                <option value="DESC">Descending</option>
                <option value="ASC">Ascending</option>
            </select>
        </div>
    </div>

    <!-- Category Card Grid -->
    <div class="relative min-h-[400px]">

        <!-- Loader -->
        <div x-show="isLoading" class="absolute inset-0 bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm z-30 flex items-center justify-center">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-primary-500"></i>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4" x-show="!isLoading" x-cloak>
            <template x-for="cat in categories" :key="cat.id">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all relative group flex flex-col">
                    <div class="aspect-square bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden relative">
                        <img x-show="cat.image" :src="'<?= base_url() ?>' + cat.image" class="w-full h-full object-cover">
                        <i x-show="!cat.image" data-lucide="folder" class="w-8 h-8 text-slate-300"></i>
                    </div>

                    <div class="p-3 text-center border-t border-slate-100 dark:border-slate-700/50 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white truncate" x-text="cat.name"></h4>
                            <span class="inline-block px-2 py-0.5 mt-1 rounded-full text-[9px] font-bold" :class="cat.status === 'active' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'" x-text="cat.status"></span>
                        </div>

                        <!-- Action Buttons row -->
                        <div class="mt-3 pt-3 border-t border-slate-50 dark:border-slate-700/30 flex items-center justify-center gap-1">
                            <button @click="openModal(cat)" class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/10" title="Edit"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                            <a :href="'<?= base_url('catalog/products/') ?>' + cat.id" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-50" title="View Items"><i data-lucide="eye" class="w-4 h-4"></i></a>
                            <!-- <button @click="deleteCategory(cat.id)" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button> -->
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty state -->
        <div x-show="!isLoading && categories.length == 0" class="py-20 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
            <i data-lucide="folders" class="w-12 h-12 text-slate-300 mx-auto mb-2"></i>
            <h3 class="text-sm font-bold text-slate-700 dark:text-white">No Categories Found</h3>
        </div>

    </div>

    <!-- Pagination Toolbar -->
    <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4" x-show="categories.length > 0">
        <div class="text-xs text-slate-500">
            Showing <span class="font-bold" x-text="offset + 1"></span> to
            <span class="font-bold" x-text="Math.min(offset + categories.length, totalRecords)"></span>
            of <span class="font-bold" x-text="totalRecords"></span> records
        </div>

        <div class="flex items-center gap-2">
            <button @click="offset = Math.max(0, offset - limit); loadCategories()" :disabled="offset <= 0" class="px-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 border rounded-lg text-slate-600 disabled:opacity-50 font-bold">Prev</button>
            <button @click="offset = offset + limit; loadCategories()" :disabled="offset + categories.length >= totalRecords" class="px-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-800 border rounded-lg text-slate-600 disabled:opacity-50 font-bold">Next</button>
        </div>
    </div>

    <!-- Modal Layout -->
    <div id="catModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-bold text-slate-800 dark:text-white" id="modalTitle">Add Category</h3>
                <button onclick="$('#catModal').addClass('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="catForm" class="p-6 space-y-4">
                <input type="hidden" name="id" id="catId">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Category Name</label>
                    <input type="text" name="name" id="catName" required class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 rounded-lg outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Banner Image</label>
                    <input type="file" name="image" accept="image/*" @change="previewImage($event)" class="w-full mt-1 p-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 rounded-lg text-xs">
                    <div class="mt-2 w-full h-40 bg-slate-100 dark:bg-slate-900/50 rounded-xl flex items-center justify-center overflow-hidden border">
                        <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                        <i x-show="!imagePreview" data-lucide="image" class="w-8 h-8 text-slate-300"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <select name="status" id="catStatus" class="w-full mt-1 p-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 rounded-lg text-sm">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="$('#catModal').addClass('hidden')" class="text-sm text-slate-500 font-bold">Cancel</button>
                    <button type="submit" class="p-2 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-bold">Save</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    $('#catForm').on('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $.ajax({
            url: '<?= base_url('catalog/save_category') ?>',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        $('#catModal').addClass('hidden');
                        document.querySelector('[x-data]').__x.$data.loadCategories();
                    });
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>