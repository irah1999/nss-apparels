<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden" x-data="{ 
    syncing: false,
    showCreateModal: false,
    async syncTemplates() {
        this.syncing = true;
        try {
            const res = await fetch('<?= base_url('templates/sync') ?>');
            const data = await res.json();
            if(data.status === 'success') {
                location.reload();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } finally {
            this.syncing = false;
        }
    }
}">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">WhatsApp Templates</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage and sync your Meta Business Templates</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="syncTemplates()" :disabled="syncing" class="flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-xl transition-all disabled:opacity-50">
                <i data-lucide="refresh-cw" class="w-4 h-4" :class="syncing ? 'animate-spin' : ''"></i>
                <span class="text-sm font-medium" x-text="syncing ? 'Syncing...' : 'Sync from Meta'"></span>
            </button>
            <button @click="showCreateModal = true" class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-500/30 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="text-sm font-medium">Create Template</span>
            </button>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($templates as $tpl): ?>
                <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 flex flex-col h-full hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-900 dark:text-white truncate" title="<?= $tpl['template_name'] ?>">
                                <?= ucwords(str_replace('_', ' ', $tpl['template_name'])) ?>
                            </h4>
                            <p class="text-[10px] text-slate-400 uppercase font-semibold"><?= $tpl['category'] ?></p>
                        </div>
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase 
                        <?= $tpl['status'] == 'APPROVED' ? 'bg-green-100 text-green-700' : ($tpl['status'] == 'REJECTED' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') ?>">
                            <?= $tpl['status'] ?>
                        </span>
                    </div>

                    <div class="flex-1 bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700 mb-4 overflow-y-auto max-h-32 custom-scrollbar">
                        <p class="text-sm text-slate-600 dark:text-slate-300 italic">"<?= $tpl['body_text'] ?>"</p>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-200 dark:border-slate-700 pt-3">
                        <div class="flex items-center gap-1">
                            <i data-lucide="globe" class="w-3 h-3"></i>
                            <?= $tpl['language'] ?>
                        </div>
                        <span>Last synced: <?= date('d M, H:i', strtotime($tpl['updated_at'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($templates)): ?>
                <div class="col-span-full py-20 flex flex-col items-center justify-center text-slate-400">
                    <i data-lucide="layout-template" class="w-16 h-16 opacity-10 mb-4"></i>
                    <p>No templates found. Click "Sync from Meta" to fetch your approved templates.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Template Modal -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden" @click.away="showCreateModal = false">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Create New Template</h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-red-500 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form action="<?= base_url('templates/create') ?>" method="POST" @submit.prevent="const fd = new FormData($el); fetch($el.action, {method: 'POST', body: fd}).then(r => r.json()).then(res => { if(res.status === 'success') { Swal.fire('Submitted', res.message, 'success').then(() => location.reload()); } else { Swal.fire('Error', res.message, 'error')} })" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Template Name (e.g. seasonal_offers)</label>
                    <input type="text" name="name" required class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Category</label>
                    <select name="category" required class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white">
                        <option value="MARKETING">Marketing</option>
                        <option value="UTILITY">Utility</option>
                        <option value="AUTHENTICATION">Authentication</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Body Text</label>
                    <textarea name="body" required rows="4" class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white" placeholder="Hello {{1}}, check out our new collection!"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Use {{1}}, {{2}} for variables.</p>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 dark:bg-slate-900/50">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 font-medium font-bold">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary-500/30 transition-all font-bold">Submit to Meta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
    }
</style>
<?= $this->endSection() ?>