<?php
$activeRoute = 'admins';
$admins = $admins ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Admin</h1>
        <p class="text-lg text-slate-700 mt-1">The Admin can manage all sales monitoring</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Admin Users</h2>

            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">Q</button>
                <button type="button" class="manager-add-btn" aria-label="Add admin" onclick="openAddAdminModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Admin</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                    <?php
                        $badge = 'Approved';
                        $badgeClass = 'manager-badge manager-badge-approved';
                        if (($admin['status'] ?? '') === 'pending') {
                            $badge = 'Pending';
                            $badgeClass = 'manager-badge manager-badge-pending';
                        } elseif (($admin['status'] ?? '') === 'inactive') {
                            $badge = 'Declined';
                            $badgeClass = 'manager-badge manager-badge-declined';
                        }
                        $fullName = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($admin['profile_picture'])): ?>
                                <img src="<?= App\Config\App::url('uploads/' . $admin['profile_picture']) ?>" alt="<?= htmlspecialchars($fullName ?: 'Admin') ?>" class="manager-avatar-image">
                            <?php else: ?>
                                <div class="manager-avatar"><?= strtoupper(substr($fullName ?: 'A', 0, 1)) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="manager-name-cell"><?= htmlspecialchars($fullName) ?></td>
                        <td><?= htmlspecialchars($admin['employee_id'] ?? '') ?></td>
                        <td><?= strtoupper(str_replace('_', ' ', htmlspecialchars($admin['position'] ?? ''))) ?></td>
                        <td><?= strtoupper(str_replace('_', ' ', htmlspecialchars($admin['department'] ?? ''))) ?></td>
                        <td>
                            <div class="manager-action-group">
                                <?php if (($admin['status'] ?? '') === 'pending'): ?>
                                <form method="POST" action="<?= App\Config\App::url('admins/' . ($admin['id'] ?? 0) . '/approve') ?>" class="inline" data-confirm="true" data-confirm-text="Approve this admin?">
                                    <?= \App\Helpers\Csrf::field() ?>
                                    <button type="submit" class="manager-action-icon manager-action-approve-icon" aria-label="Approve admin">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 6L9 17l-5-5"/>
                                        </svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-delete"
                                    aria-label="Delete"
                                    onclick='openDeleteAdminModal(<?= json_encode([
                                        'id' => $admin['id'] ?? 0,
                                        'full_name' => $fullName,
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-edit"
                                    aria-label="Edit"
                                    onclick='openEditAdminModal(<?= json_encode([
                                        'id' => $admin['id'] ?? 0,
                                        'first_name' => $admin['first_name'] ?? '',
                                        'last_name' => $admin['last_name'] ?? '',
                                        'position' => $admin['position'] ?? '',
                                        'area' => $admin['area'] ?? '',
                                        'contact_no' => $admin['contact_no'] ?? '',
                                        'employee_id' => $admin['employee_id'] ?? '',
                                        'department' => $admin['department'] ?? '',
                                        'company_email' => $admin['company_email'] ?? '',
                                        'email' => $admin['email'] ?? '',
                                        'status' => $admin['status'] ?? 'active',
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-view"
                                    aria-label="View"
                                    onclick='openViewAdminModal(<?= json_encode([
                                        'full_name' => $fullName,
                                        'position' => $admin['position'] ?? '',
                                        'area' => $admin['area'] ?? '',
                                        'contact_no' => $admin['contact_no'] ?? '',
                                        'employee_id' => $admin['employee_id'] ?? '',
                                        'department' => $admin['department'] ?? '',
                                        'company_email' => $admin['company_email'] ?? '',
                                        'email' => $admin['email'] ?? '',
                                        'status' => $admin['status'] ?? '',
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td>
                            <span class="<?= $badgeClass ?>"><?= $badge ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="manager-table-footer">
            <button class="manager-page-btn">Previous</button>
            <button class="manager-page-btn manager-page-btn-active">1</button>
            <button class="manager-page-btn">2</button>
            <button class="manager-page-btn">Next</button>
        </div>
    </div>
</div>

<template id="add-admin-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add Admin</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddAdminModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('admins') ?>" enctype="multipart/form-data">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>First Name</span>
                    <input type="text" name="first_name" placeholder="Enter First Name">
                </label>

                <label class="manager-modal-field">
                    <span>Last Name</span>
                    <input type="text" name="last_name" placeholder="Enter Last Name">
                </label>

                <label class="manager-modal-field">
                    <span>Position</span>
                    <select name="position">
                        <option value="">Select Type of Position</option>
                        <option value="dispatcher">Dispatcher</option>
                        <option value="tech_leaders">Tech Leaders</option>
                        <option value="sales_team_leader">Sales Team Leader</option>
                        <option value="validator">Validator</option>
                        <option value="accounting">Accounting</option>
                        <option value="sales_admin">Sales Admin</option>
                        <option value="backend">Backend</option>
                        <option value="marketing">Marketing</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="area_sales_manager">Area Sales Manager</option>
                        <option value="general_manager">General Manager</option>
                        <option value="product_business_manager">Product &amp; Business Manager</option>
                    </select>
                </label>

                <label class="manager-modal-field">
                    <span>Area <em>(Optional)</em></span>
                    <input type="text" name="area" placeholder="Enter Area">
                </label>

                <label class="manager-modal-field">
                    <span>Contact</span>
                    <input type="text" name="contact_no" placeholder="Enter Contact Number">
                </label>

                <label class="manager-modal-field">
                    <span>Employee ID</span>
                    <input type="text" name="employee_id" placeholder="Enter Employee ID">
                </label>

                <label class="manager-modal-field">
                    <span>Department</span>
                    <select name="department">
                        <option value="">Select Department</option>
                        <option value="operation">Operation</option>
                        <option value="sales">Sales</option>
                    </select>
                </label>

                <label class="manager-modal-field">
                    <span>Company Email</span>
                    <input type="email" name="company_email" placeholder="Enter Company Email">
                </label>

                <label class="manager-modal-field">
                    <span>Email Address</span>
                    <input type="email" name="email" placeholder="Enter Email Address">
                </label>

                <label class="manager-modal-field">
                    <span>Profile Picture</span>
                    <input type="file" name="profile_picture">
                </label>

                <label class="manager-modal-field">
                    <span>Status</span>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddAdminModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="view-admin-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>View Admin</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddAdminModal()" aria-label="Close">x</button>
        </div>
        <div class="manager-view-grid">
            <div><span>Name</span><p data-va="full_name"></p></div>
            <div><span>Position</span><p data-va="position"></p></div>
            <div><span>Area</span><p data-va="area"></p></div>
            <div><span>Contact</span><p data-va="contact_no"></p></div>
            <div><span>Employee ID</span><p data-va="employee_id"></p></div>
            <div><span>Department</span><p data-va="department"></p></div>
            <div><span>Company Email</span><p data-va="company_email"></p></div>
            <div><span>Email Address</span><p data-va="email"></p></div>
            <div><span>Status</span><p data-va="status"></p></div>
        </div>
    </div>
</template>

<template id="edit-admin-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Edit Admin</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddAdminModal()" aria-label="Close">x</button>
        </div>
        <form class="manager-modal-form" onsubmit="event.preventDefault(); closeAddAdminModal();">
            <?= \App\Helpers\Csrf::field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="manager-modal-grid">
                <label class="manager-modal-field"><span>First Name</span><input type="text" data-ea="first_name"></label>
                <label class="manager-modal-field"><span>Last Name</span><input type="text" data-ea="last_name"></label>
                <label class="manager-modal-field"><span>Position</span><input type="text" data-ea="position"></label>
                <label class="manager-modal-field"><span>Area</span><input type="text" data-ea="area"></label>
                <label class="manager-modal-field"><span>Contact</span><input type="text" data-ea="contact_no"></label>
                <label class="manager-modal-field"><span>Employee ID</span><input type="text" data-ea="employee_id"></label>
                <label class="manager-modal-field"><span>Department</span><input type="text" data-ea="department"></label>
                <label class="manager-modal-field"><span>Company Email</span><input type="email" data-ea="company_email"></label>
                <label class="manager-modal-field"><span>Email Address</span><input type="email" data-ea="email"></label>
                <label class="manager-modal-field">
                    <span>Status</span>
                    <select data-ea="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            </div>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddAdminModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="delete-admin-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Delete Admin</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddAdminModal()" aria-label="Close">x</button>
        </div>
        <p class="manager-delete-text">Are you sure you want to delete <strong data-da="full_name"></strong>?</p>
        <div class="manager-modal-actions">
            <form method="POST" data-da-form="delete" class="inline">
                <?= \App\Helpers\Csrf::field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="manager-modal-submit manager-delete-confirm">Delete</button>
            </form>
            <button type="button" class="manager-modal-cancel" onclick="closeAddAdminModal()">Cancel</button>
        </div>
    </div>
</template>

<script>
    function openAddAdminModal() {
        const template = document.getElementById('add-admin-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closeAddAdminModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openViewAdminModal(admin) {
        const template = document.getElementById('view-admin-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) {
            content.classList.add('manager-modal-shell');
            content.querySelectorAll('[data-va]').forEach((node) => {
                const key = node.getAttribute('data-va');
                node.textContent = admin[key] || '-';
            });
        }
    }

    function openEditAdminModal(admin) {
        const template = document.getElementById('edit-admin-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) {
            content.classList.add('manager-modal-shell');
            const form = content.querySelector('form.manager-modal-form');
            if (form) {
                form.action = '<?= App\Config\App::url('admins') ?>/' + encodeURIComponent(admin.id || 0);
                form.method = 'POST';
            }
            content.querySelectorAll('[data-ea]').forEach((node) => {
                const key = node.getAttribute('data-ea');
                if (node.tagName === 'SELECT') {
                    node.value = admin[key] || 'active';
                } else {
                    node.value = admin[key] || '';
                }
            });
        }
    }

    function openDeleteAdminModal(admin) {
        const template = document.getElementById('delete-admin-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) {
            content.classList.add('manager-modal-shell');
            const nameNode = content.querySelector('[data-da="full_name"]');
            if (nameNode) nameNode.textContent = admin.full_name || 'this admin';
            const deleteForm = content.querySelector('[data-da-form="delete"]');
            if (deleteForm) {
                deleteForm.action = '<?= App\Config\App::url('admins') ?>/' + encodeURIComponent(admin.id || 0);
            }
        }
    }
</script>
