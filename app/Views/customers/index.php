<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
    x-data="{
        customers: [],
        searchQuery: '',
        perPage: 30,
        offset: 0,
        totalRecords: 0,
        orderBy: 'name',
        orderDir: 'ASC',
        isLoading: false,

        // Modal triggers binding to update list 
        whatsappShow: false,

        async init() {
            this.loadCustomers();

            this.$watch('searchQuery', (value) => {
                this.offset = 0;
                this.loadCustomers();
            });

            window.addEventListener('open-whatsapp', (e) => {
                // this state is handled in script or whatsapp modal layer below
            });
        },

        async loadCustomers() {
            this.isLoading = true;
            try {
                const fd = new FormData();
                fd.append('draw', 1);
                fd.append('start', this.offset);
                fd.append('length', this.perPage);
                fd.append('search[value]', this.searchQuery);
                fd.append('order[0][column]', 0); 
                fd.append('columns[0][data]', this.orderBy);
                fd.append('order[0][dir]', this.orderDir);

                const res = await fetch('<?= base_url('customers/list') ?>', { method: 'POST', body: fd });
                const data = await res.json();
                this.customers = data.data;
                this.totalRecords = data.recordsFiltered;
                
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            } catch(e) { console.error('Load failed', e); }
            finally { this.isLoading = false; }
        },

        get totalPages() { return Math.ceil(this.totalRecords / this.perPage); }
    }">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Customers</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage your business connections</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button @click="openModal('bulk-whatsapp')" class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/10 hover:bg-green-100 dark:hover:bg-green-900/20 text-green-600 dark:text-green-400 rounded-xl transition-all border border-green-100 dark:border-green-800" title="Bulk WhatsApp">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span class="text-sm font-medium hidden sm:inline">Bulk WhatsApp</span>
            </button>
            <button @click="openModal('import')" class="flex items-center gap-2 px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-xl transition-all" title="Bulk Import">
                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                <span class="text-sm font-medium hidden sm:inline">Bulk Import</span>
            </button>
            <button @click="openModal('add')" class="flex items-center gap-2 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-500/30 transition-all" title="Add Customer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="text-sm font-medium hidden sm:inline">Add Customer</span>
            </button>
        </div>
    </div>

    <div class="p-6">
        <!-- Search, Limit, Sort Controls -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Search customers..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 outline-none text-sm text-slate-900 dark:text-white">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-semibold">Show</span>
                    <select x-model="perPage" @change="offset = 0; loadCustomers()" class="py-1.5 px-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-xs font-semibold text-slate-700 dark:text-white">
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-semibold">Sort</span>
                    <select x-model="orderBy" @change="offset = 0; loadCustomers()" class="py-1.5 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-xs font-semibold text-slate-700 dark:text-white">
                        <option value="name">Name</option>
                        <option value="email">Email</option>
                        <option value="joining_date">Joining Date</option>
                    </select>
                </div>
                <button @click="orderDir = (orderDir==='ASC'?'DESC':'ASC'); loadCustomers()" class="p-2 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-200 text-slate-600 dark:text-white" title="Toggle Order direction">
                    <i x-show="orderDir === 'ASC'" data-lucide="arrow-up-narrow-wide" class="w-4 h-4"></i>
                    <i x-show="orderDir === 'DESC'" data-lucide="arrow-down-wide-narrow" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Loading state -->
        <div x-show="isLoading" class="flex items-center justify-center p-12">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-primary-500"></i>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" x-show="!isLoading" x-cloak>
            <template x-for="customer in customers" :key="customer.id">
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm relative group hover:-translate-y-1 hover:shadow-xl hover:border-primary-100 dark:hover:border-primary-900 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <!-- Avatar & Name Header -->
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center font-bold text-white text-lg shadow-lg shadow-primary-500/20 flex-shrink-0" x-text="customer.name.substring(0, 1).toUpperCase()">
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-sm text-slate-800 dark:text-white truncate" x-text="customer.name"></h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-50 dark:bg-green-900/10 text-green-600 dark:text-green-400 mt-1">
                                    <span class="w-1 h-1 rounded-full bg-green-500 mr-1"></span> Active
                                </span>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="mt-5 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/40 p-2 rounded-xl border border-slate-100 dark:border-slate-800/50">
                                <i data-lucide="mail" class="w-3.5 h-3.5 text-primary-500"></i>
                                <span class="truncate font-medium" x-text="customer.email || 'No email registered'"></span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/40 p-2 rounded-xl border border-slate-100 dark:border-slate-800/50">
                                <i data-lucide="phone" class="w-3.5 h-3.5 text-green-500"></i>
                                <span class="font-semibold tracking-wide" x-text="customer.phone"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Meta & Actions Slider -->
                    <div class="mt-5">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mb-3 px-1">
                            <div class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <span x-text="customer.joining_date"></span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700/40 flex items-center justify-between">
                            <button @click="sendWA(customer.id, customer.name)" class="flex items-center justify-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl text-xs font-semibold scale-95 hover:scale-100 transition-all">
                                <i data-lucide="message-square" class="w-3.5 h-3.5"></i> Message
                            </button>
                            <div class="flex items-center gap-1">
                                <button @click="editCustomer(customer)" class="p-1.5 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-800/20 rounded-lg hover:scale-110 transition-all" title="Edit"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                                <!-- <button @click="deleteCustomer(customer.id)" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-800/20 rounded-lg hover:scale-110 transition-all" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button> -->
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination Footer -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-700/50 pt-4" x-show="totalRecords > 0">
            <p class="text-xs text-slate-500">Showing <span class="font-bold text-slate-700 dark:text-slate-300" x-text="offset + 1"></span> to <span class="font-bold text-slate-700 dark:text-slate-300" x-text="Math.min(offset + parseInt(perPage), totalRecords)"></span> of <span class="font-bold text-slate-700 dark:text-slate-300" x-text="totalRecords"></span> customers</p>
            <div class="flex items-center gap-2">
                <button @click="if(offset > 0) { offset -= parseInt(perPage); loadCustomers() }" :disabled="offset === 0" class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50">Previous</button>
                <div class="flex items-center gap-1">
                    <span class="text-xs text-slate-500">Page</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="Math.floor(offset / perPage) + 1"></span>
                    <span class="text-xs text-slate-500">of</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="totalPages"></span>
                </div>
                <button @click="if(offset + parseInt(perPage) < totalRecords) { offset += parseInt(perPage); loadCustomers() }" :disabled="offset + parseInt(perPage) >= totalRecords" class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50">Next</button>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="totalRecords === 0 && !isLoading" class="text-center p-12" x-cloak>
            <i data-lucide="users" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
            <h4 class="font-bold text-slate-700 dark:text-white">No customers found</h4>
            <p class="text-xs text-slate-500">Try adjusting your search criteria</p>
        </div>
    </div>
</div>

<!-- Customer Modal (Add/Edit) -->
<div id="customerModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modalTitle">Add New Customer</h3>
            <button onclick="closeModal('customerModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="customerForm" class="p-6 space-y-4">
            <input type="hidden" name="id" id="customerId">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-xs text-red-500">(Mandatory)</span></label>
                <input type="text" name="name" id="name" required class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address <span class="text-xs text-slate-400">(Optional)</span></label>
                <input type="email" name="email" id="email" class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone Number <span class="text-xs text-red-500">(Mandatory)</span></label>
                <div class="flex gap-2">
                    <select name="country_code" id="country_code" required class="w-1/3 py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none text-slate-900 dark:text-white">
                        <option value="91" selected>🇮🇳 +91</option>
                        <option value="1">🇺🇸 +1</option>
                        <option value="7">🇷🇺 +7</option>
                        <option value="44">🇬🇧 +44</option>
                        <option value="971">🇦🇪 +971</option>
                        <option value="65">🇸🇬 +65</option>
                        <option value="61">🇦🇺 +61</option>
                    </select>
                    <input type="text" id="phone_only" required class="flex-1 py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none text-slate-900 dark:text-white" placeholder="9876543210" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <input type="hidden" name="phone" id="phone">
                <p class="text-[10px] text-slate-400 mt-1">Country code will be added automatically.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Joining Date <span class="text-xs text-slate-400">(Optional)</span></label>
                <input type="date" name="joining_date" id="joining_date" class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none text-slate-900 dark:text-white">
            </div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('customerModal')" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary-500/30 transition-all">Save Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 p-6 overflow-hidden">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Bulk Import Customers</h3>
        <p class="text-sm text-slate-500 mb-6">Upload a CSV file with columns: Name, Email, Phone, Joining Date.</p>
        <form id="importForm" class="space-y-6">
            <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 dark:hover:bg-slate-800 dark:bg-slate-900 hover:bg-slate-100 dark:border-slate-600 dark:hover:border-slate-500">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i data-lucide="cloud-upload" class="w-8 h-8 text-slate-400 mb-2"></i>
                        <p class="mb-2 text-sm text-slate-500 dark:text-slate-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">CSV files only (MAX. 2MB)</p>
                    </div>
                    <input type="file" name="csv_file" class="hidden" accept=".csv" required id="csvInput" onchange="document.getElementById('fileName').innerText = this.files[0].name" />
                </label>
            </div>
            <p id="fileName" class="text-sm text-primary-600 font-medium text-center"></p>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 font-medium">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg">Start Import</button>
            </div>
        </form>
    </div>
</div>

<!-- WhatsApp Modal -->
<div
    x-data="{ 
        show: false, 
        isBulk: false,
        templates: [], 
        selectedTemplate: null, 
        customer: { id: '', name: '', phone: '' },
        variables: {},
        headerImage: null,
        headerPreview: null,
        isSending: false,
        
        async init() {
            const res = await fetch('<?= base_url('templates/get-approved') ?>');
            this.templates = await res.json();
            
            window.addEventListener('open-whatsapp', (e) => {
                this.customer = e.detail;
                this.isBulk = false;
                this.resetForm();
                this.show = true;
            });

            window.addEventListener('open-bulk-whatsapp', () => {
                this.isBulk = true;
                this.customer = { id: 'all', name: 'All Selected Customers' };
                this.resetForm();
                this.show = true;
            });
        },

        resetForm() {
            this.selectedTemplate = null;
            this.variables = {};
            this.headerImage = null;
            this.headerPreview = null;
        },
        
        get bodyVariables() {
            if (!this.selectedTemplate) return [];
            const matches = this.selectedTemplate.body_text.match(/\{\{(\d+)\}\}/g);
            return matches ? [...new Set(matches)] : [];
        },
        
        get hasImageHeader() {
            if (!this.selectedTemplate) return false;
            try {
                const config = JSON.parse(this.selectedTemplate.header_text);
                return config.header_type === 'IMAGE';
            } catch(e) { return false; }
        },
        
        handleFile(e) {
            const file = e.target.files[0];
            if(file) {
                this.headerImage = file;
                this.headerPreview = URL.createObjectURL(file);
            }
        },

        async send() {
            if (!this.selectedTemplate && !document.getElementById('waMessage').value) {
                Swal.fire('Error', 'Please select a template or type a message', 'error');
                return;
            }

            this.isSending = true;
            try {
                const fd = new FormData();
                if (!this.isBulk) fd.append('customer_id', this.customer.id);
                
                if (this.selectedTemplate) {
                    fd.append('template_id', this.selectedTemplate.id);
                    const params = this.bodyVariables.map(v => this.variables[v] || '');
                    fd.append('params', JSON.stringify(params));
                    
                    if (this.hasImageHeader && this.headerImage) {
                        fd.append('header_image', this.headerImage);
                    }
                } else {
                    fd.append('message', document.getElementById('waMessage').value);
                }
                
                const url = this.isBulk ? '<?= base_url('customers/bulk-whatsapp') ?>' : '<?= base_url('customers/send-whatsapp') ?>';
                const res = await fetch(url, { method: 'POST', body: fd });
                const data = await res.json();

                if (data.status === 'success') {
                    Swal.fire('Success', data.message, 'success');
                    this.show = false;
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch(e) {
                Swal.fire('Error', 'Failed to send message', 'error');
            } finally {
                this.isSending = false;
            }
        }
    }"
    x-show="show"
    class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    x-cloak>
    <div @click.away="show = false" class="bg-white dark:bg-slate-800 w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between flex-shrink-0">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="isBulk ? 'Send Bulk WhatsApp' : 'Send WhatsApp Message'"></h3>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar">
            <!-- Recipient Info -->
            <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Recipient</label>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-xs" x-text="customer.name.charAt(0)"></div>
                    <span class="font-bold text-slate-700 dark:text-slate-200" x-text="customer.name"></span>
                </div>
            </div>

            <!-- Template Selection -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Select Template</label>
                <select
                    class="block w-full py-3 px-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500"
                    @change="selectedTemplate = templates.find(t => t.id == $el.value) || null; variables = {}; headerImage = null; headerPreview = null;">
                    <option value="">Custom Message (Plain Text)</option>
                    <template x-for="tpl in templates" :key="tpl.id">
                        <option :value="tpl.id" x-text="tpl.template_name"></option>
                    </template>
                </select>
            </div>

            <!-- Dynamic Header Image -->
            <template x-if="hasImageHeader">
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Header Image</label>
                    <div class="relative h-40 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 overflow-hidden">
                        <input type="file" @change="handleFile" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                        <template x-if="headerPreview">
                            <img :src="headerPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!headerPreview">
                            <div class="text-center">
                                <i data-lucide="image" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                                <p class="text-xs text-slate-500">Click to upload header image</p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Message / Variables -->
            <div class="space-y-4">
                <template x-if="!selectedTemplate">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Message</label>
                        <textarea id="waMessage" required rows="4" class="block w-full py-3 px-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500" placeholder="Type your message here... use {name} for dynamic name"></textarea>
                    </div>
                </template>

                <template x-if="selectedTemplate">
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl">
                            <label class="block text-[10px] font-bold text-blue-500 uppercase mb-2">Template Preview</label>
                            <p class="text-xs text-slate-600 dark:text-slate-300 whitespace-pre-wrap" x-text="selectedTemplate.body_text"></p>
                        </div>

                        <div class="space-y-3" x-show="bodyVariables.length > 0">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Variables</label>
                            <div class="grid grid-cols-1 gap-3">
                                <template x-for="v in bodyVariables" :key="v">
                                    <div class="relative">
                                        <label class="absolute -top-2 left-3 px-1 bg-white dark:bg-slate-800 text-[10px] font-bold text-primary-500" x-text="'Variable ' + v"></label>
                                        <input
                                            type="text"
                                            x-model="variables[v]"
                                            placeholder="Enter value... use {name} for customer name"
                                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-sm focus:ring-2 focus:ring-primary-500">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="p-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3 flex-shrink-0">
            <button @click="show = false" class="px-6 py-2.5 text-slate-600 dark:text-slate-400 font-bold transition-all hover:text-slate-900">Cancel</button>
            <button
                @click="send()"
                :disabled="isSending"
                class="px-8 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 transition-all flex items-center gap-2 disabled:opacity-50">
                <i x-show="!isSending" data-lucide="send" class="w-4 h-4"></i>
                <i x-show="isSending" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                <span x-text="isSending ? (isBulk ? 'Processing...' : 'Sending...') : 'Send Now'"></span>
            </button>
        </div>
    </div>
</div>

<script>
    function globalReload() {
        const root = document.querySelector('[x-data]');
        if (root && root._x_dataStack) root._x_dataStack[0].loadCustomers();
    }

    function sendWA(id, name) {
        window.dispatchEvent(new CustomEvent('open-whatsapp', {
            detail: {
                id,
                name
            }
        }));
    }

    function openModal(type) {
        if (type === 'add') {
            $('#modalTitle').text('Add New Customer');
            $('#customerForm')[0].reset();
            $('#customerId').val('');
            $('#customerModal').removeClass('hidden');
        } else if (type === 'import') {
            if ($('#importModal').length) $('#importModal').removeClass('hidden');
        } else if (type === 'bulk-whatsapp') {
            window.dispatchEvent(new CustomEvent('open-bulk-whatsapp'));
        }
    }

    function closeModal(id) {
        $(`#${id}`).addClass('hidden');
    }

    function editCustomer(row) {
        $('#modalTitle').text('Edit Customer');
        $('#customerId').val(row.id);
        $('#name').val(row.name);
        $('#email').val(row.email);

        // Try to separate country code if it matches our list (simple logic)
        let phone = row.phone.toString();
        let codes = ['91', '1', '7', '44', '971', '65', '61'];
        let matched = false;

        for (let code of codes) {
            if (phone.startsWith(code)) {
                $('#country_code').val(code);
                $('#phone_only').val(phone.substring(code.length));
                matched = true;
                break;
            }
        }

        if (!matched) {
            $('#country_code').val('91');
            $('#phone_only').val(phone);
        }

        $('#joining_date').val(row.joining_date);
        $('#customerModal').removeClass('hidden');
    }

    $('#customerForm').on('submit', function(e) {
        e.preventDefault();

        // Combine phone number
        const countryCode = $('#country_code').val();
        const phoneOnly = $('#phone_only').val();
        $('#phone').val(countryCode + phoneOnly);

        const formData = $(this).serialize();
        $.post('<?= base_url('customers/save') ?>', formData, function(res) {
            if (res.status === 'success') {
                closeModal('customerModal');
                globalReload();
                Swal.fire('Success', res.message, 'success');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        });
    });

    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: '<?= base_url('customers/import') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    if (res.status === 'success') {
                        closeModal('importModal');
                        globalReload();
                        Swal.fire('Success', res.message, 'success');
                    }
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });

    function deleteCustomer(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('customers/delete') ?>', {
                    id: id
                }, function(res) {
                    globalReload();
                    Swal.fire('Deleted!', res.message, 'success');
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>