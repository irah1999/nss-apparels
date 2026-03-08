<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">User Management</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Control who can access the system</p>
        </div>
        <button onclick="openUserModal('add')" class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-500/30 transition-all">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span class="text-sm font-medium">Add System User</span>
        </button>
    </div>

    <style>
        @media screen and (max-width: 767px) {
            #usersTable thead {
                display: none;
            }

            #usersTable,
            #usersTable tbody,
            #usersTable tr,
            #usersTable td {
                display: block;
                width: 100%;
            }

            #usersTable tr {
                margin-bottom: 1rem;
                border: 1px solid #e5e7eb;
                border-radius: 1rem;
                padding: 0.5rem;
                background: white;
            }

            .dark #usersTable tr {
                border-color: #374151;
                background: #1e293b;
            }

            #usersTable td {
                text-align: right;
                padding: 0.5rem 1rem;
                position: relative;
                border: none !important;
            }

            #usersTable td:before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                font-weight: 600;
                color: #64748b;
            }

            #usersTable td.text-center {
                text-align: center;
                border-top: 1px solid #f1f5f9 !important;
                margin-top: 0.5rem;
                padding-top: 1rem;
            }

            .dark #usersTable td.text-center {
                border-top-color: #334155 !important;
            }
        }

        .action-btn {
            padding: 0.5rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
    </style>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="usersTable" class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-900">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white" data-label="Name"><?= $user['name'] ?></td>
                            <td class="px-6 py-4" data-label="Email"><?= $user['email'] ?></td>
                            <td class="px-6 py-4" data-label="Role">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= $user['role'] == 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4" data-label="Status">
                                <div class="flex items-center gap-1.5 md:justify-start justify-end">
                                    <div class="w-2 h-2 rounded-full <?= $user['status'] ? 'bg-green-500' : 'bg-slate-300' ?>"></div>
                                    <span class="text-xs"><?= $user['status'] ? 'Active' : 'Inactive' ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center" data-label="Actions">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)" class="action-btn text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/10">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="deleteUser(<?= $user['id'] ?>)" class="action-btn text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="userModalTitle">Add System User</h3>
            <button onclick="closeUserModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="userForm" class="p-6 space-y-4">
            <input type="hidden" name="id" id="userId">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                <input type="text" name="name" id="userName" required class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                <input type="email" name="email" id="userEmail" required class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password <span id="pwdLabel" class="text-xs italic text-slate-400"></span></label>
                <input type="password" name="password" id="userPassword" class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Role</label>
                <select name="role" id="userRole" required class="block w-full py-2 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg outline-none text-slate-900 dark:text-white">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="closeUserModal()" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 font-medium">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg shadow-lg shadow-primary-500/30 transition-all">Save User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUserModal(type) {
        if (type === 'add') {
            $('#userModalTitle').text('Add System User');
            $('#userForm')[0].reset();
            $('#userId').val('');
            $('#userPassword').attr('required', true);
            $('#pwdLabel').text('(Required)');
        }
        $('#userModal').removeClass('hidden');
        lucide.createIcons();
    }

    function closeUserModal() {
        $('#userModal').addClass('hidden');
    }

    function editUser(user) {
        $('#userModalTitle').text('Edit System User');
        $('#userId').val(user.id);
        $('#userName').val(user.name);
        $('#userEmail').val(user.email);
        $('#userRole').val(user.role);
        $('#userPassword').attr('required', false);
        $('#pwdLabel').text('(Leave blank to keep same)');
        $('#userModal').removeClass('hidden');
    }

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post('<?= base_url('users/save') ?>', formData, function(res) {
            if (res.status === 'success') {
                closeUserModal();
                Swal.fire({
                    title: 'Success',
                    text: res.message,
                    icon: 'success'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        });
    });

    function deleteUser(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This user will lose access!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete user'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('users/delete') ?>', {
                    id: id
                }, function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Deleted!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>