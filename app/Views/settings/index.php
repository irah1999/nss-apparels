<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">API Configuration</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage your Meta and WhatsApp API credentials</p>
        </div>

        <form action="<?= base_url('settings/save') ?>" method="POST" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($configs as $config): ?>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 capitalize">
                            <?= str_replace(['_', 'api', 'id'], [' ', 'API', 'ID'], $config['config_key']) ?>
                        </label>
                        <div class="relative">
                            <input type="text" name="configs[<?= $config['config_key'] ?>]" value="<?= $config['config_value'] ?>"
                                class="block w-full py-2.5 px-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 transition-all">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 transition-all transform active:scale-95">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Update Credentials
                </button>
            </div>
        </form>
    </div>

    <!-- Additional info card -->
    <div class="mt-8 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-2xl p-6 flex gap-4">
        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
            <i data-lucide="info" class="w-6 h-6"></i>
        </div>
        <div>
            <h4 class="text-lg font-bold text-blue-900 dark:text-blue-300">Meta/WhatsApp Integration</h4>
            <p class="text-sm text-blue-700 dark:text-blue-400 mt-1 leading-relaxed">
                Ensure you have set up a Meta App and enabled the WhatsApp product. You will need the Phone Number ID and System User Access Token to send messages via the Cloud API.
            </p>
            <a href="https://developers.facebook.com" target="_blank" class="inline-flex items-center gap-1 text-sm font-bold text-blue-600 hover:underline mt-4">
                Meta Developer Portal <i data-lucide="external-link" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>