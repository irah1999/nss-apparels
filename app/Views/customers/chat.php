<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col h-[calc(100vh-140px)] bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden"
    x-data="{ 
        selectedCustomer: null,
        searchQuery: '',
        messages: [],
        newMessage: '',
        isSending: false,
        attachments: [],
        selectedTemplate: '',
        showEmoji: false,
        showAddModal: false,
        showBulkModal: false,
        openImportModal: false,
        newCustomerName: '',
        newCustomerCountryCode: '+91', // Default to India
        newCustomerPhone: '',
        newCustomerEmail: '',
        isAddingCustomer: false,

        async init() {
            const urlParams = new URLSearchParams(window.location.search);
            const customerId = urlParams.get('customer_id');

            if (customerId) {
                const res = await fetch('<?= base_url('api/whatsapp/get-customer') ?>/' + customerId);
                if (res.ok) {
                    const customer = await res.json();
                    this.selectCustomer(customer);
                }
            }
        },
        
        async selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.messages = [];
            this.attachments = [];
            this.newMessage = '';
            this.stopPolling();
            await this.loadHistory();
            this.startPolling();
            this.$nextTick(() => {
                const container = this.$refs.messageContainer;
                if (container) container.scrollTop = container.scrollHeight;
                if (window.lucide) lucide.createIcons();
            });

            // Mark as read in background
            fetch('<?= base_url('api/whatsapp/mark-read') ?>/' + customer.id, { method: 'POST' });
        },

        formatPhoneNumber(phone) {
            // Remove any non-numeric characters for simple display if needed
            return phone.replace(/\D/g, '');
        },

        async loadHistory() {
            if (!this.selectedCustomer) return;
            try {
                const response = await fetch(`<?= base_url('customers/history') ?>/${this.selectedCustomer.id}`);
                const newMessages = await response.json();
                // Only scroll to bottom if new messages arrived
                const hadNewMsg = newMessages.length > this.messages.length;
                this.messages = newMessages;
                if (hadNewMsg) {
                    this.$nextTick(() => {
                        const container = this.$refs.messageContainer;
                        if (container) container.scrollTop = container.scrollHeight;
                        if (window.lucide) lucide.createIcons();
                    });
                }
            } catch (e) {
                console.error('Failed to load history', e);
            }
        },

        startPolling() {
            if (this._pollInterval) clearInterval(this._pollInterval);
            this._pollInterval = setInterval(() => {
                if (this.selectedCustomer) this.loadHistory();
            }, 5000); // Every 5 seconds
        },

        stopPolling() {
            if (this._pollInterval) clearInterval(this._pollInterval);
        },

        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            if (this.attachments.length + files.length > 3) {
                Swal.fire('Limit Reached', 'You can upload maximum 3 files.', 'warning');
            }
            this.attachments = [...this.attachments, ...files].slice(0, 3);
        },

        removeAttachment(index) {
            this.attachments.splice(index, 1);
        },

        async sendMessage() {
            if (!this.selectedCustomer || (!this.newMessage && this.attachments.length === 0 && !this.selectedTemplate)) return;
            
            this.isSending = true;
            const formData = new FormData();
            formData.append('customer_id', this.selectedCustomer.id);
            formData.append('message', this.newMessage);
            formData.append('template_id', this.selectedTemplate);
            this.attachments.forEach(file => {
                formData.append('attachment[]', file);
            });

            try {
                const response = await fetch('<?= base_url('customers/send-chat') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.status === 'success') {
                    this.newMessage = '';
                    this.selectedTemplate = '';
                    this.attachments = [];
                    this.$refs.fileInput.value = '';
                    await this.loadHistory();
                    this.$nextTick(() => {
                        const container = this.$refs.messageContainer;
                        container.scrollTop = container.scrollHeight;
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Failed to send message', 'error');
            } finally {
                this.isSending = false;
            }
        },

        formatTime(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
     }">

    <!-- Top Bar -->
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-green-100 dark:bg-green-900/20 flex items-center justify-center">
                <i data-lucide="message-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Customer Chat</h2>
                <p class="text-xs text-slate-400">WhatsApp 24-hour conversations</p>
            </div>
        </div>
        <a href="<?= base_url('customers') ?>" class="flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-lg text-sm font-medium transition-colors w-fit">
            <i data-lucide="users" class="w-4 h-4"></i>
            Customers
        </a>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar: Customer List -->
        <div class="w-full sm:w-80 md:w-96 border-r border-slate-200 dark:border-slate-700 flex flex-col bg-slate-50 dark:bg-slate-800/50"
            :class="selectedCustomer ? 'hidden md:flex' : 'flex'">
            <!-- Search -->
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text"
                        x-model="searchQuery"
                        placeholder="Search customers..."
                        class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all dark:text-white">
                </div>
            </div>

            <!-- List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <?php foreach ($customers as $customer): ?>
                    <div @click="selectCustomer(<?= htmlspecialchars(json_encode($customer)) ?>)"
                        class="flex items-center gap-3 p-4 border-b border-slate-100 dark:border-slate-700/50 hover:bg-white dark:hover:bg-slate-700 cursor-pointer transition-colors"
                        :class="selectedCustomer?.id == <?= $customer['id'] ?> ? 'bg-white dark:bg-slate-700 border-l-4 border-l-primary-500 shadow-sm' : ''"
                        x-show="!searchQuery || '<?= addslashes(strtolower($customer['name'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= $customer['phone'] ?>'.includes(searchQuery)">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold shrink-0">
                                <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                            </div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h4 class="font-semibold text-slate-900 dark:text-white truncate"><?= $customer['name'] ?></h4>
                                <span class="text-[10px] text-slate-400 shrink-0">Now</span>
                            </div>
                            <p class="text-sm text-slate-500 truncate"><?= $customer['phone'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-slate-100/30 dark:bg-slate-900/10 relative"
            x-show="selectedCustomer"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">

            <template x-if="selectedCustomer">
                <div class="flex flex-col h-full">
                    <!-- Chat Header -->
                    <div class="px-6 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button @click="selectedCustomer = null" class="md:hidden p-2 -ml-2 text-slate-500 hover:text-slate-900 dark:hover:text-white">
                                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                            </button>
                            <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold" x-text="selectedCustomer.name.substring(0, 1).toUpperCase()"></div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white leading-tight" x-text="selectedCustomer.name"></h3>
                                <p class="text-xs text-green-500 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Online
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <button class="p-2 text-slate-400 hover:text-primary-600 transition-colors"><i data-lucide="phone" class="w-5 h-5"></i></button>
                            <button class="p-2 text-slate-400 hover:text-primary-600 transition-colors"><i data-lucide="video" class="w-5 h-5"></i></button>
                            <button class="p-2 text-slate-400 hover:text-primary-600 transition-colors"><i data-lucide="more-vertical" class="w-5 h-5"></i></button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar" x-ref="messageContainer">
                        <template x-for="msg in messages" :key="msg.id">
                            <div class="flex" :class="msg.direction === 'outbound' ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[75%] group relative">
                                    <div :class="msg.direction === 'outbound' ? 'bg-primary-600 text-white rounded-2xl rounded-tr-none' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-2xl rounded-tl-none border border-slate-100 dark:border-slate-700'"
                                        class="p-3 shadow-sm relative">

                                        <!-- Attachment Rendering -->
                                        <template x-if="msg.attachment">
                                            <div class="mb-2 space-y-2">
                                                <template x-for="(file, idx) in JSON.parse(msg.attachment)" :key="idx">
                                                    <div>
                                                        <template x-if="JSON.parse(msg.attachment_type)[idx] && JSON.parse(msg.attachment_type)[idx].startsWith('image/')">
                                                            <img :src="'<?= base_url('uploads/whatsapp') ?>/' + file" class="rounded-lg max-h-64 cursor-pointer hover:opacity-90 w-full object-cover" @click="window.open('<?= base_url('uploads/whatsapp') ?>/' + file)">
                                                        </template>
                                                        <template x-if="JSON.parse(msg.attachment_type)[idx] && !JSON.parse(msg.attachment_type)[idx].startsWith('image/')">
                                                            <a :href="'<?= base_url('uploads/whatsapp') ?>/' + file" target="_blank" class="flex items-center gap-2 p-2 bg-black/10 rounded-lg hover:bg-black/20 transition-colors text-inherit decoration-none">
                                                                <i data-lucide="file-text" class="w-5 h-5 opacity-70 border-none"></i>
                                                                <span class="text-xs font-medium truncate" x-text="file"></span>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <p class="text-sm whitespace-pre-wrap leading-relaxed font-semibold mb-1" x-text="msg.message"></p>
                                        <div class="flex items-center justify-end gap-1 mt-1 opacity-70">
                                            <span class="text-[10px]" x-text="formatTime(msg.sent_at)"></span>
                                            <template x-if="msg.direction === 'outbound'">
                                                <i :data-lucide="msg.read_status === 'read' ? 'check-check' : 'check'" :class="msg.read_status === 'read' ? 'text-blue-300' : 'text-slate-300'" class="w-3 h-3"></i>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Skeleton/Empty State if no messages -->
                        <template x-if="messages.length === 0">
                            <div class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                    <i data-lucide="message-square" class="w-10 h-10 opacity-20"></i>
                                </div>
                                <h4 class="font-medium text-slate-600 dark:text-slate-300">No conversation yet</h4>
                                <p class="text-sm max-w-xs mt-1">Start chatting with <span x-text="selectedCustomer.name"></span> by sending a message below.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">

                        <!-- Attachment Previews -->
                        <div x-show="attachments.length > 0" class="mb-3 flex flex-wrap gap-2" x-cloak>
                            <template x-for="(file, index) in attachments" :key="index">
                                <div class="p-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center gap-3 shrink-0">
                                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded flex items-center justify-center">
                                        <i data-lucide="file" class="text-primary-600 w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium dark:text-white truncate max-w-[100px]" x-text="file.name"></p>
                                    </div>
                                    <button @click="removeAttachment(index)" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-full text-slate-400">
                                        <i data-lucide="x" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-end gap-3">
                            <div class="flex items-center gap-1 shrink-0 mb-1 relative">
                                <!-- Emoji Picker -->
                                <div class="relative">
                                    <button type="button" @click="showEmoji = !showEmoji" class="p-2 text-slate-400 hover:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/10 rounded-full transition-colors" title="Emoji">
                                        <i data-lucide="smile" class="w-6 h-6"></i>
                                    </button>
                                    <div x-show="showEmoji" @click.away="showEmoji = false" x-cloak
                                        class="absolute bottom-12 left-0 z-50 shadow-2xl rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700"
                                        style="width: 320px;">
                                        <emoji-picker id="emojiPicker" class="light" @emoji-click="newMessage += $event.detail.unicode; showEmoji = false;"></emoji-picker>
                                    </div>
                                </div>

                                <!-- Send Image Button -->
                                <button @click="$refs.imageInput.click()" class="flex items-center gap-2 p-2 text-slate-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/10 rounded-xl transition-all relative border border-slate-200 dark:border-slate-700" :class="attachments.filter(f => f.type.startsWith('image/')).length > 0 ? 'text-primary-600 bg-primary-50 border-primary-500' : ''" title="Send Image">
                                    <i data-lucide="image" class="w-5 h-5"></i>
                                    <input type="file" x-ref="imageInput" @change="handleFileSelect" class="hidden" accept="image/*" multiple>
                                </button>

                                <!-- Send Video Button -->
                                <button @click="$refs.videoInput.click()" class="flex items-center gap-2 p-2 text-slate-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/10 rounded-xl transition-all relative border border-slate-200 dark:border-slate-700" :class="attachments.filter(f => f.type.startsWith('video/')).length > 0 ? 'text-primary-600 bg-primary-50 border-primary-500' : ''" title="Send Video">
                                    <i data-lucide="video" class="w-5 h-5"></i>
                                    <input type="file" x-ref="videoInput" @change="handleFileSelect" class="hidden" accept="video/mp4,video/3gpp" multiple>
                                </button>

                                <!-- Send File Button -->
                                <button @click="$refs.fileInput.click()" class="flex items-center gap-2 p-2 text-slate-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/10 rounded-xl transition-all relative border border-slate-200 dark:border-slate-700" :class="attachments.filter(f => !f.type.startsWith('image/') && !f.type.startsWith('video/')).length > 0 ? 'text-primary-600 bg-primary-50 border-primary-500' : ''" title="Send Document">
                                    <i data-lucide="paperclip" class="w-5 h-5"></i>
                                    <input type="file" x-ref="fileInput" @change="handleFileSelect" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" multiple>
                                </button>
                            </div>
                            <div class="flex-1 relative">
                                <textarea x-model="newMessage"
                                    @keydown.enter.prevent="if(!isSending) sendMessage()"
                                    rows="1"
                                    placeholder="Type a message..."
                                    class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-2xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition-all dark:text-white resize-none max-h-32 overflow-y-auto custom-scrollbar"></textarea>
                            </div>
                            <button @click="sendMessage()"
                                :disabled="isSending || (!newMessage && attachments.length === 0)"
                                class="p-3 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-2xl transition-all shadow-md active:scale-95 shrink-0 mb-0.5">
                                <i x-show="!isSending" data-lucide="send" class="w-6 h-6"></i>
                                <i x-show="isSending" data-lucide="loader-2" class="w-6 h-6 animate-spin"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Loading State overlay if messageContainer isn't ready -->
            <div x-show="!selectedCustomer" class="absolute inset-0 bg-slate-50 dark:bg-slate-900/50 flex flex-col items-center justify-center p-8 text-center text-slate-400 z-10">
                <div class="w-24 h-24 bg-primary-100 dark:bg-primary-900/20 rounded-full flex items-center justify-center mb-6 animate-pulse">
                    <i data-lucide="messages-square" class="w-12 h-12 text-primary-500 opacity-50"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-700 dark:text-white mb-2">Welcome to NSS Chat</h3>
                <p class="max-w-md text-slate-500 dark:text-slate-400">Select a customer from the left to start messaging. You can send images, PDFs, and documents directly to their WhatsApp.</p>
                <div class="mt-8 flex gap-4">
                    <div class="flex items-center gap-2 text-xs font-medium"><span class="w-2 h-2 bg-green-500 rounded-full"></span> Secure</div>
                    <div class="flex items-center gap-2 text-xs font-medium"><span class="w-2 h-2 bg-blue-500 rounded-full"></span> Fast</div>
                    <div class="flex items-center gap-2 text-xs font-medium"><span class="w-2 h-2 bg-purple-500 rounded-full"></span> Reliable</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (Add Customer) -->
    <div x-show="showAddModal"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="showAddModal = false">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add New Customer</h3>
                <button @click="showAddModal = false" class="p-1 text-slate-400 hover:text-red-500"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="<?= base_url('customers/save') ?>" method="POST" @submit.prevent="const fd = new FormData($el); const fullPhone = fd.get('country_code') + fd.get('phone_only'); fd.set('phone', fullPhone); fetch($el.action, {method: 'POST', body: fd}).then(r => r.json()).then(res => { if(res.status === 'success') { location.reload(); } else { Swal.fire('Error', res.message, 'error')} })">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name <span class="text-xs text-red-500">(Mandatory)</span></label>
                        <input type="text" name="name" required class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone (WhatsApp) <span class="text-xs text-red-500">(Mandatory)</span></label>
                        <div class="flex gap-2">
                            <select name="country_code" required class="w-1/3 px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none dark:text-white">
                                <option value="91" selected>🇮🇳 +91 (IN)</option>
                                <option value="1">🇺🇸 +1 (US)</option>
                                <option value="7">🇷🇺 +7 (RU)</option>
                                <option value="44">🇬🇧 +44 (UK)</option>
                                <option value="971">🇦🇪 +971 (UAE)</option>
                                <option value="65">🇸🇬 +65 (SG)</option>
                                <option value="61">🇦🇺 +61 (AU)</option>
                            </select>
                            <input type="text" name="phone_only" required placeholder="9876543210" class="flex-1 px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <input type="hidden" name="phone" id="full_phone_hidden">
                        <p class="text-[10px] text-slate-400 mt-1">Country code will be added automatically (e.g. 91xxxxxxxxxx)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email <span class="text-xs text-slate-400">(Optional)</span></label>
                        <input type="email" name="email" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Joining Date <span class="text-xs text-slate-400">(Optional)</span></label>
                        <input type="date" name="joining_date" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none dark:text-white">
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 text-slate-500 hover:text-slate-700">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow-md transition-all active:scale-95">Create Customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Message Modal -->
    <div x-show="showBulkModal"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.away="showBulkModal = false">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Send Bulk WhatsApp</h3>
                <button @click="showBulkModal = false" class="p-1 text-slate-400 hover:text-red-500"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="<?= base_url('customers/bulk-whatsapp') ?>" method="POST" @submit.prevent="Swal.fire({title: 'Are you sure?', text: 'Sending bulk messages to all customers.', icon: 'warning', showCancelButton: true}).then(result => { if(result.isConfirmed) { const fd = new FormData($el); fetch($el.action, {method: 'POST', body: fd}).then(r => r.json()).then(res => { if(res.status === 'success') { showBulkModal = false; Swal.fire('Success', res.message, 'success'); } else { Swal.fire('Error', res.message, 'error')} }) } })">
                <div class="p-6 space-y-4">
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-xl flex items-start gap-3">
                        <i data-lucide="alert-circle" class="text-amber-600 w-5 h-5 shrink-0 mt-0.5"></i>
                        <p class="text-sm text-amber-800 dark:text-amber-300">Bulk messaging will send this content to <strong><?= count($customers) ?> active customers</strong>. Ensure your API limits allow this.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Use Approved Template (Marketing)</label>
                        <select name="template_id" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none dark:text-white" onchange="const t = this.options[this.selectedIndex].getAttribute('data-body'); if(t) { this.form.message.value = t; }">
                            <option value="">No Template (Custom Text)</option>
                            <?php foreach ($templates as $tpl): ?>
                                <option value="<?= $tpl['id'] ?>" data-body="<?= htmlspecialchars($tpl['body_text']) ?>"><?= ucwords(str_replace('_', ' ', $tpl['template_name'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[10px] text-amber-500 mt-1 font-semibold">Note: Templates are processed faster and avoid 24-hr blocks.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Message Content</label>
                        <textarea name="message" required rows="6" placeholder="Hi {{name}}, welcome to NSS Business..." class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none dark:text-white resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Attachment (Image/PDF/Doc)</label>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <button type="button" @click="$refs.bulkImageInput.click()" class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all group">
                                <i data-lucide="image" class="w-5 h-5 text-slate-400 group-hover:text-primary-500"></i>
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-primary-600">Add Images</span>
                                <input type="file" x-ref="bulkImageInput" name="attachment[]" class="hidden" accept="image/*" multiple @change="$el.closest('.space-y-4').querySelector('.file-names').innerText = Array.from($el.files).map(f => f.name).join(', ')">
                            </button>
                            <button type="button" @click="$refs.bulkFileInput.click()" class="flex items-center justify-center gap-2 p-3 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all group">
                                <i data-lucide="file-text" class="w-5 h-5 text-slate-400 group-hover:text-primary-500"></i>
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-primary-600">Add Files</span>
                                <input type="file" x-ref="bulkFileInput" name="attachment[]" class="hidden" accept=".pdf,.doc,.docx" multiple @change="$el.closest('.space-y-4').querySelector('.file-names').innerText = Array.from($el.files).map(f => f.name).join(', ')">
                            </button>
                        </div>
                        <p class="file-names text-sm font-bold text-primary-500 truncate text-center"></p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3">
                    <button type="button" @click="showBulkModal = false" class="px-4 py-2 text-slate-500 hover:text-slate-700">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow-md transition-all active:scale-95">Send for all Customers</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="openImportModal"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.away="openImportModal = false">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Bulk Import Customers</h3>
                <button @click="openImportModal = false" class="p-1 text-slate-400 hover:text-red-500"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="<?= base_url('customers/import') ?>" method="POST" @submit.prevent="const fd = new FormData($el); fetch($el.action, {method: 'POST', body: fd}).then(r => r.json()).then(res => { if(res.status === 'success') { location.reload(); } else { Swal.fire('Error', res.message, 'error')} })">
                <div class="p-6 space-y-4">
                    <p class="text-sm text-slate-500 mb-6">Upload a CSV file with columns: Name, Email, Phone, Joining Date.</p>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 dark:hover:bg-slate-800 dark:bg-slate-900 hover:bg-slate-100 dark:border-slate-600 dark:hover:border-slate-500">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="cloud-upload" class="w-8 h-8 text-slate-400 mb-2"></i>
                                <p class="mb-2 text-sm text-slate-500 dark:text-slate-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">CSV files only (MAX. 2MB)</p>
                            </div>
                            <input type="file" name="csv_file" class="hidden" accept=".csv" required onchange="$el.closest('form').querySelector('#chatFileName').innerText = this.files[0].name" />
                        </label>
                    </div>
                    <p id="chatFileName" class="text-sm text-primary-600 font-medium text-center"></p>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3">
                    <button type="button" @click="openImportModal = false" class="px-4 py-2 text-slate-500 hover:text-slate-700">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow-md transition-all active:scale-95">Start Import</button>
                </div>
            </form>
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
        background: #475569;
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        // Alpine data is structured in x-data above
    });

    // Connect emoji picker to textarea
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const picker = document.getElementById('emojiPicker');
            if (picker) {
                picker.addEventListener('emoji-click', event => {
                    const emoji = event.detail.unicode;
                    const ta = document.querySelector('textarea[placeholder="Type a message..."]');
                    if (!ta) return;
                    const start = ta.selectionStart ?? ta.value.length;
                    const end = ta.selectionEnd ?? ta.value.length;
                    ta.value = ta.value.slice(0, start) + emoji + ta.value.slice(end);
                    ta.selectionStart = ta.selectionEnd = start + emoji.length;
                    ta.dispatchEvent(new Event('input'));
                    ta.focus();
                    // Close picker via Alpine
                    const root = ta.closest('[x-data]');
                    if (root && root._x_dataStack) root._x_dataStack[0].showEmoji = false;
                });
            }
        }, 800);
    });
</script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<?= $this->endSection() ?>