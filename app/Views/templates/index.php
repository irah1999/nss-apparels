<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{ 
    syncing: false,
    showCreateModal: false,
    isUploading: false,
    isSubmitting: false,
    
    // Form State
    selectedFile: null,
    form: {
        name: '',
        category: 'MARKETING',
        language: 'en_US',
        header_type: 'NONE',
        header_handle: '',
        header_preview: '',
        body: '',
        footer: '',
        button_text: '',
        button_url: ''
    },
    
    // Body Variables Handling
    get bodyVariables() {
        const matches = this.form.body.match(/\{\{(\d+)\}\}/g) || [];
        return [...new Set(matches)].sort();
    },
    variableExamples: {},

    async syncTemplates() {
        this.syncing = true;
        try {
            const res = await fetch('<?= base_url('templates/sync') ?>');
            const data = await res.json();
            if(data.status === 'success') {
                Swal.fire('Success', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } finally {
            this.syncing = false;
        }
    },

    handleFileSelect(el) {
        const file = el.files[0];
        if(!file) return;
        this.selectedFile = file;
        this.form.header_preview = URL.createObjectURL(file);
    },

    async submitTemplate() {
        if(!this.form.name || !this.form.body) {
            Swal.fire('Wait', 'Template name and body are required', 'warning');
            return;
        }

        this.isSubmitting = true;

        try {
            // Step 1: Upload media IF needed and handle NOT yet obtained
            if (this.form.header_type === 'IMAGE' && this.selectedFile) {
                this.isUploading = true;
                const fdMedia = new FormData();
                fdMedia.append('file', this.selectedFile);
                
                const resMedia = await fetch('<?= base_url('templates/uploadMedia') ?>', { method: 'POST', body: fdMedia });
                const dataMedia = await resMedia.json();
                
                if (dataMedia.status === 'success') {
                    this.form.header_url = dataMedia.url;
                } else {
                    throw new Error(dataMedia.message || 'Media upload failed');
                }
                this.isUploading = false;
            }

            // Step 2: Create Template
            const fd = new FormData();
            Object.keys(this.form).forEach(key => fd.append(key, this.form[key]));
            
            // Add examples
            this.bodyVariables.forEach(v => {
                const num = v.replace(/[\{\}]/g, '');
                fd.append('body_examples[]', this.variableExamples[v] || 'Example');
            });

            const res = await fetch('<?= base_url('templates/create') ?>', { method: 'POST', body: fd });
            const data = await res.json();
            
            if(data.status === 'success') {
                Swal.fire('Success', 'Template submitted to Meta for review', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        } finally {
            this.isSubmitting = false;
            this.isUploading = false;
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="layout-template" class="text-primary-500"></i>
                WhatsApp Templates
            </h1>
            <p class="text-slate-500 dark:text-slate-400">Create and manage your marketing & utility message templates</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="syncTemplates()" :disabled="syncing" class="px-4 py-2 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-white rounded-xl transition-all flex items-center gap-2 disabled:opacity-50">
                <i data-lucide="refresh-cw" class="w-4 h-4" :class="syncing ? 'animate-spin' : ''"></i>
                <span x-text="syncing ? 'Syncing...' : 'Sync from Meta'"></span>
            </button>
            <button @click="showCreateModal = true" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span class="font-semibold">Create Template</span>
            </button>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($templates as $tpl): ?>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-xl transition-all group border-b-4 <?= $tpl['status'] == 'APPROVED' ? 'border-b-green-500' : 'border-b-amber-500' ?>">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $tpl['status'] == 'APPROVED' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                        <?= $tpl['status'] ?>
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase"><?= $tpl['category'] ?></span>
                </div>
                <div class="p-5 space-y-3">
                    <h3 class="font-bold text-slate-900 dark:text-white truncate" title="<?= $tpl['template_name'] ?>">
                        <?= ucwords(str_replace('_', ' ', $tpl['template_name'])) ?>
                    </h3>
                    <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl text-sm text-slate-600 dark:text-slate-300 min-h-[100px] flex flex-col justify-center">
                        <p class="line-clamp-4 italic">"<?= $tpl['body_text'] ?>"</p>
                    </div>
                </div>
                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500">
                    <div class="flex items-center gap-1">
                        <i data-lucide="globe" class="w-3 h-4"></i>
                        <?= $tpl['language'] ?>
                    </div>
                    <span>ID: <?= substr($tpl['meta_template_id'] ?? 'N/A', -8) ?></span>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($templates)): ?>
            <div class="col-span-full py-24 flex flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl">
                <i data-lucide="layout-template" class="w-20 h-20 opacity-10 mb-6"></i>
                <h3 class="text-lg font-bold">No Templates Found</h3>
                <p>Sync with Meta to fetch your templates or create a new one.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Create Modal Overhaul -->
    <div x-show="showCreateModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md" x-cloak>

        <div class="bg-white dark:bg-slate-800 w-full max-w-5xl h-[90vh] rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row" @click.away="showCreateModal = false">

            <!-- Form Side -->
            <div class="flex-1 flex flex-col border-r border-slate-200 dark:border-slate-700 overflow-y-auto custom-scrollbar">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between sticky top-0 bg-white dark:bg-slate-800 z-10">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Create WhatsApp Template</h2>
                    <button @click="showCreateModal = false" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full transition-colors">
                        <i data-lucide="x" class="w-6 h-6 text-slate-500"></i>
                    </button>
                </div>

                <div class="p-8 space-y-8">
                    <!-- General Info -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Template Name</label>
                            <input type="text" x-model="form.name" placeholder="seasonal_sale_banner" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none text-slate-900 dark:text-white">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Category</label>
                            <select x-model="form.category" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white">
                                <option value="MARKETING">Marketing (Offers, News)</option>
                                <option value="UTILITY">Utility (Order alerts, OTP)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Header Options -->
                    <div class="space-y-4">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Header Content (Optional)</label>
                        <div class="flex gap-4">
                            <button @click="form.header_type = 'NONE'" :class="form.header_type == 'NONE' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-slate-900 text-slate-500 border-slate-200 dark:border-slate-700'" class="flex-1 py-3 rounded-xl border font-bold text-sm transition-all">None</button>
                            <button @click="form.header_type = 'IMAGE'" :class="form.header_type == 'IMAGE' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-slate-900 text-slate-500 border-slate-200 dark:border-slate-700'" class="flex-1 py-3 rounded-xl border font-bold text-sm transition-all">Image</button>
                        </div>

                        <div x-show="form.header_type == 'IMAGE'" class="transition-all">
                            <div class="relative group h-40 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 transition-colors pointer-events-auto">
                                <input type="file" @change="handleFileSelect($el)" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                                <div x-show="!form.header_preview" class="text-center">
                                    <i data-lucide="image-plus" class="w-8 h-8 mx-auto text-slate-400 mb-2"></i>
                                    <p class="text-xs text-slate-500 px-4">Click and select the offer image</p>
                                </div>
                                <img x-show="form.header_preview" :src="form.header_preview" class="absolute inset-0 w-full h-full object-cover rounded-2xl">
                                <div x-show="isUploading" class="absolute inset-0 bg-white/80 dark:bg-slate-800/80 flex items-center justify-center rounded-2xl">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-primary-600"></i>
                                        <p class="text-[10px] font-bold text-primary-600">UPLOADING TO META...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Body Content -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Body Text</label>
                            <span class="text-[10px] text-primary-500 font-bold px-2 py-0.5 bg-primary-50 dark:bg-primary-900/30 rounded">REQUIRED</span>
                        </div>
                        <textarea x-model="form.body" rows="6" class="w-full p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none text-slate-900 dark:text-white resize-none" placeholder="Hello {{1}},\n\nNew Offer from NSS Apparels!\n\nProduct: {{2}}\nDiscount: {{3}}\nPrice: {{4}}\n\nGrab the deal before it ends!"></textarea>

                        <!-- Variable Examples -->
                        <div x-show="bodyVariables.length > 0" class="space-y-3 bg-amber-50 dark:bg-amber-900/10 p-4 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                            <h4 class="text-xs font-bold text-amber-700 dark:text-amber-500 flex items-center gap-2">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                Meta Requires Examples for variables:
                            </h4>
                            <div class="grid grid-cols-2 gap-3">
                                <template x-for="v in bodyVariables" :key="v">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase" x-text="'Value for ' + v"></label>
                                        <input type="text" x-model="variableExamples[v]" placeholder="Example value" class="w-full px-3 py-2 text-xs bg-white dark:bg-slate-800 border-none rounded shadow-sm outline-none">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="grid grid-cols-2 gap-8 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <!-- Footer -->
                        <div class="space-y-4">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Footer Text</label>
                            <input type="text" x-model="form.footer" placeholder="NSS Apparels Wholesale Clothing" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                        </div>
                        <!-- Button -->
                        <div class="space-y-4">
                            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Website Button</label>
                            <div class="space-y-2">
                                <input type="text" x-model="form.button_text" placeholder="View Website" class="w-full px-4 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                                <input type="url" x-model="form.button_url" placeholder="https://nssapparels.shop" class="w-full px-4 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto p-8 bg-slate-50 dark:bg-slate-900/20 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-4">
                    <button @click="showCreateModal = false" class="px-6 py-2 text-slate-600 dark:text-slate-400 font-bold">Cancel</button>
                    <button @click="submitTemplate()" :disabled="isSubmitting || isUploading" class="px-10 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-xl shadow-primary-500/30 disabled:opacity-50 flex items-center gap-2">
                        <i x-show="isSubmitting" data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        <span x-text="isSubmitting ? 'Submitting...' : 'Submit to Meta'"></span>
                    </button>
                </div>
            </div>

            <!-- Preview Side (The "WOW" factor) -->
            <div class="w-full lg:w-[380px] bg-slate-100 dark:bg-slate-900/50 p-6 flex flex-col items-center justify-center relative bg-[url('https://w0.peakpx.com/wallpaper/508/606/HD-wallpaper-whatsapp-l-background-doodle-pattern-thumbnail.jpg')] bg-repeat">
                <div class="absolute inset-0 bg-slate-200/40 dark:bg-slate-950/40 backdrop-blur-[2px]"></div>

                <p class="relative z-10 text-[10px] font-bold text-slate-500 mb-6 uppercase tracking-widest bg-white/80 dark:bg-slate-800/80 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700">Live Preview</p>

                <!-- WhatsApp Bubble -->
                <div class="relative z-10 w-full bg-white dark:bg-[#202c33] rounded-2xl shadow-2xl overflow-hidden max-w-[320px] transform hover:scale-105 transition-transform duration-500">
                    <!-- Media Header -->
                    <div x-show="form.header_type == 'IMAGE'" class="h-40 bg-slate-200 dark:bg-slate-800 overflow-hidden relative">
                        <img x-show="form.header_preview" :src="form.header_preview" class="w-full h-full object-cover">
                        <div x-show="!form.header_preview" class="flex flex-col items-center justify-center h-full text-slate-400">
                            <i data-lucide="image" class="w-10 h-10 opacity-20"></i>
                        </div>
                    </div>

                    <div class="p-4 space-y-2">
                        <!-- Body -->
                        <div class="text-[13.5px] leading-[1.6] text-slate-800 dark:text-[#e9edef] whitespace-pre-wrap"
                            x-text="form.body ? form.body.replace(/\{\{(\d+)\}\}/g, (match) => variableExamples[match] || match) : 'Template body will appear here...'"></div>

                        <!-- Footer -->
                        <div x-show="form.footer" class="text-[11.5px] text-slate-500 dark:text-[#8696a0]" x-text="form.footer"></div>

                        <!-- Buttons -->
                        <div x-show="form.button_text" class="pt-2 border-t border-slate-100 dark:border-[#2f3b43]">
                            <div class="flex items-center justify-center gap-2 py-2 text-primary-600 dark:text-blue-400 text-sm font-medium">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                <span x-text="form.button_text"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Bubble "tail" -->
                    <div class="absolute left-[-8px] top-6 w-0 h-0 border-t-[8px] border-t-white dark:border-t-[#202c33] border-l-[8px] border-l-transparent"></div>
                </div>

                <div class="mt-8 relative z-10 px-6 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900/50 rounded-2xl max-w-[300px]">
                    <p class="text-[11px] leading-relaxed text-amber-800 dark:text-amber-400 text-center font-medium">
                        Approval typically takes 1 minute to 24 hours. Once <span class="font-bold">APPROVED</span>, you can use it in campaigns.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
    }

    [x-cloak] {
        display: none !important;
    }
</style>
<?= $this->endSection() ?>