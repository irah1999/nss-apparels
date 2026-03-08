<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Cards -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-semibold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg">+12.5%</span>
        </div>
        <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Customers</h3>
        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1"><?= number_format($total_customers) ?></p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-lg">Active</span>
        </div>
        <h3 class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Users</h3>
        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1"><?= number_format($total_users) ?></p>
    </div>

    <!-- More cards can be added here -->
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Business Overview</h3>
            <select class="text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 outline-none">
                <option>Last 7 Days</option>
                <option>Last 30 Days</option>
            </select>
        </div>
        <div class="h-64 flex items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl italic text-slate-400">
            Chart Placeholder - Add Chart.js here for visually appealing stats
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Recent Activities</h3>
        <ul class="space-y-4">
            <li class="flex items-start gap-4">
                <div class="w-2 h-2 mt-2 bg-primary-500 rounded-full"></div>
                <div>
                    <p class="text-sm dark:text-white text-slate-900 font-medium">Bulk WhatsApp Sent</p>
                    <p class="text-xs text-slate-500">100 messages sent successfully</p>
                    <p class="text-[10px] text-slate-400 mt-1">2 hours ago</p>
                </div>
            </li>
            <li class="flex items-start gap-4">
                <div class="w-2 h-2 mt-2 bg-green-500 rounded-full"></div>
                <div>
                    <p class="text-sm dark:text-white text-slate-900 font-medium">New Customer Added</p>
                    <p class="text-xs text-slate-500">John Doe was added to the list</p>
                    <p class="text-[10px] text-slate-400 mt-1">5 hours ago</p>
                </div>
            </li>
        </ul>
    </div>
</div>
<?= $this->endSection() ?>