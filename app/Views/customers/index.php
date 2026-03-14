<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Customers</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage your business connections</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openModal('import')" class="flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-xl transition-all">
                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                <span class="text-sm font-medium">Bulk Import</span>
            </button>
            <button @click="openModal('add')" class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-500/30 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="text-sm font-medium">Add Customer</span>
            </button>
        </div>
    </div>

    <style>
        @media screen and (max-width: 767px) {
            #customersTable thead {
                display: none;
            }

            #customersTable,
            #customersTable tbody,
            #customersTable tr,
            #customersTable td {
                display: block;
                width: 100%;
            }

            #customersTable tr {
                margin-bottom: 1.5rem;
                border: 1px solid #e5e7eb;
                border-radius: 1rem;
                padding: 0.5rem;
                background: white;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            }

            .dark #customersTable tr {
                border-color: #374151;
                background: #1e293b;
            }

            #customersTable td {
                text-align: right;
                padding: 0.75rem 1rem;
                position: relative;
                border: none !important;
                min-height: 2.5rem;
                color: #1e293b;
                font-weight: 500;
            }

            .dark #customersTable td {
                color: #f8fafc;
            }

            #customersTable td:before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                font-size: 0.7rem;
                letter-spacing: 0.025em;
            }

            #customersTable td.text-center {
                text-align: center;
                border-top: 1px solid #f1f5f9 !important;
                margin-top: 0.5rem;
                padding-top: 1rem;
                display: flex;
                justify-content: center;
                gap: 4px;
            }

            .dark #customersTable td.text-center {
                border-top-color: #334155 !important;
            }
        }

        .action-btn {
            padding: 0.6rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>

    <div class="p-6">
        <div class="mb-6 flex justify-end">
            <button @click="openModal('bulk-whatsapp')" class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-lg shadow-green-500/30 transition-all">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span class="text-sm font-medium">Send Bulk WhatsApp Message</span>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table id="customersTable" class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Phone</th>
                        <th class="px-6 py-4">Joining Date</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
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
<div id="whatsappModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Send WhatsApp Message</h3>
            <button onclick="closeModal('whatsappModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="whatsappForm" class="p-6 space-y-4">
            <input type="hidden" name="customer_id" id="waCustomerId">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">To</label>
                <input type="text" id="waCustomerName" readonly class="block w-full py-2 px-3 bg-slate-100 dark:bg-slate-700 border-none rounded-lg text-slate-500 dark:text-slate-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Select Template</label>
                <select id="waTemplate" class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white" onchange="updateMessage()">
                    <option value="">Custom Message</option>
                    <option value="Welcome to NSS Business! We are glad to have you.">Welcome Template</option>
                    <option value="Hello! This is a reminder about your account status.">Reminder Template</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Message</label>
                <textarea name="message" id="waMessage" required rows="4" class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white" placeholder="Type your message here..."></textarea>
            </div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('whatsappModal')" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 font-medium">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Send Now
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let table;
    $(document).ready(function() {
        table = $('#customersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('customers/list') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'name',
                    className: 'px-6 py-4 font-medium text-slate-900 dark:text-white whitespace-nowrap'
                },
                {
                    data: 'email',
                    className: 'px-6 py-4'
                },
                {
                    data: 'phone',
                    className: 'px-6 py-4'
                },
                {
                    data: 'joining_date',
                    className: 'px-6 py-4'
                },
                {
                    data: null,
                    orderable: false,
                    className: 'px-6 py-4 text-center flex justify-center gap-2',
                    render: function(data, type, row) {
                        return `
                            <button onclick="sendWA(${row.id}, '${row.name}')" class="action-btn text-green-600 hover:bg-green-50 dark:hover:bg-green-900/10" title="WhatsApp">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                            </button>
                            <button onclick="editCustomer(${JSON.stringify(row).replace(/"/g, '&quot;')})" class="action-btn text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/10" title="Edit">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteCustomer(${row.id})" class="action-btn text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10" title="Delete">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        `;
                    }
                }
            ],
            createdRow: function(row, data, dataIndex) {
                const labels = ['Name', 'Email', 'Phone', 'Joining Date', 'Actions'];
                $(row).find('td').each(function(index) {
                    $(this).attr('data-label', labels[index]);
                });
            },
            drawCallback: function() {
                lucide.createIcons();
            },
            language: {
                search: "",
                searchPlaceholder: "Search customers...",
                lengthMenu: "_MENU_",
                paginate: {
                    previous: "<",
                    next: ">"
                }
            },
            responsive: false
        });

        // Add proper styling to DataTable search input
        $('.dataTables_filter input').addClass('bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 outline-none mb-4');
        $('.dataTables_length select').addClass('bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 outline-none ml-2 mr-2');
    });

    function openModal(type) {
        if (type === 'add') {
            $('#modalTitle').text('Add New Customer');
            $('#customerForm')[0].reset();
            $('#customerId').val('');
            $('#customerModal').removeClass('hidden');
        } else if (type === 'import') {
            $('#importModal').removeClass('hidden');
        } else if (type === 'bulk-whatsapp') {
            Swal.fire({
                title: 'Send Bulk Message?',
                text: 'This will send messages to all customers in chunks.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ea5e9',
                confirmButtonText: 'Yes, Send All'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Start bulk processing logic
                }
            });
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

    function sendWA(id, name) {
        $('#waCustomerId').val(id);
        $('#waCustomerName').val(name);
        $('#whatsappModal').removeClass('hidden');
    }

    function updateMessage() {
        const template = $('#waTemplate').val();
        if (template) $('#waMessage').val(template);
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
                table.ajax.reload();
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
                    closeModal('importModal');
                    table.ajax.reload();
                    Swal.fire('Success', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });

    $('#whatsappForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post('<?= base_url('customers/send-whatsapp') ?>', formData, function(res) {
            closeModal('whatsappModal');
            Swal.fire('Sent', 'WhatsApp message triggered successfully', 'success');
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
                    table.ajax.reload();
                    Swal.fire('Deleted!', res.message, 'success');
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>