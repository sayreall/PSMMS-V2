<?php
$activeRoute = 'inhouse';
$inhouseUsers = $inhouseUsers ?? [];
$asmManagers = $asmManagers ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">In-House</h1>
        <p class="text-lg text-slate-700 mt-1">The Inhouse sales team leaders</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Inhouse Users</h2>

            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" onclick="openAssignProductsModal()" aria-label="Add In-House">
                    <span class="manager-add-icon">+</span>
                    <span>Add In-House</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Sales Manager</th>
                        <th>Category</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inhouseUsers as $row): ?>
                    <?php
                        $fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['middle_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                        $team = trim((string)($row['sales_manager'] ?? ''));
                        if ($team === '') {
                            $team = trim((string)($row['sales_category'] ?? '-'));
                        }

                        $badge = 'Approved';
                        $badgeClass = 'manager-badge manager-badge-approved';
                        if (($row['status'] ?? '') === 'pending') {
                            $badge = 'Pending';
                            $badgeClass = 'manager-badge manager-badge-pending';
                        } elseif (($row['status'] ?? '') === 'inactive') {
                            $badge = 'Declined';
                            $badgeClass = 'manager-badge manager-badge-declined';
                        }
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['photos'])): ?>
                                <img src="<?= App\Config\App::url('uploads/' . $row['photos']) ?>" alt="<?= htmlspecialchars($fullName ?: 'In-House') ?>" class="manager-avatar-image">
                            <?php else: ?>
                                <div class="manager-avatar"><?= strtoupper(substr($fullName ?: 'I', 0, 1)) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="manager-name-cell"><?= htmlspecialchars($fullName) ?></td>
                        <td><?= htmlspecialchars($row['contact'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(strtoupper($team)) ?></td>
                        <td><?= htmlspecialchars(strtoupper((string)($row['sales_category'] ?? '-'))) ?></td>
                        <td>
                            <div class="manager-action-group">
                                <?php if (($row['status'] ?? '') === 'pending'): ?>
                                    <?php
                                        $approveUrl = App\Config\App::url('inhouse/' . urlencode((string)($row['source_type'] ?? 'inhouse')) . '/' . (int)($row['id'] ?? 0) . '/approve');
                                    ?>
                                    <button
                                        type="button"
                                        class="manager-action-icon manager-action-view"
                                        aria-label="Approve"
                                        title="Approve In-House"
                                        data-action="approve"
                                        data-approve-url="<?= htmlspecialchars($approveUrl, ENT_QUOTES) ?>"
                                        data-name="<?= htmlspecialchars($fullName ?: 'this in-house user', ENT_QUOTES) ?>"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                <?php endif; ?>
                                <?php
                                    $deleteUrl = App\Config\App::url('inhouse/' . urlencode((string)($row['source_type'] ?? 'inhouse')) . '/' . (int)($row['id'] ?? 0) . '/delete');
                                ?>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-delete"
                                    aria-label="Delete"
                                    onclick='openDeleteInhouseModal(<?= json_encode([
                                        'id' => (int)($row['id'] ?? 0),
                                        'source_type' => (string)($row['source_type'] ?? 'inhouse'),
                                        'name' => $fullName ?: 'this in-house user',
                                        'delete_url' => $deleteUrl,
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                </button>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-edit"
                                    aria-label="Edit"
                                    onclick='openEditInhouseModal(<?= json_encode([
                                        'id' => (int)($row['id'] ?? 0),
                                        'update_url' => App\Config\App::url('inhouse/' . urlencode((string)($row['source_type'] ?? 'inhouse')) . '/' . (int)($row['id'] ?? 0) . '/update'),
                                        'source_type' => (string)($row['source_type'] ?? 'inhouse'),
                                        'first_name' => (string)($row['first_name'] ?? ''),
                                        'middle_name' => (string)($row['middle_name'] ?? ''),
                                        'last_name' => (string)($row['last_name'] ?? ''),
                                        'contact' => (string)($row['contact'] ?? ''),
                                        'email' => (string)($row['email'] ?? ''),
                                        'sales_manager' => (string)($row['sales_manager'] ?? ''),
                                        'sales_category' => (string)($row['sales_category'] ?? ''),
                                        'address' => (string)($row['address'] ?? ''),
                                        'status' => (string)($row['status'] ?? 'active'),
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                </button>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-view"
                                    aria-label="View"
                                    onclick='openViewInhouseModal(<?= json_encode([
                                        'name' => $fullName ?: '-',
                                        'email' => (string)($row['email'] ?? '-'),
                                        'contact' => (string)($row['contact'] ?? '-'),
                                        'sales_manager' => (string)($row['sales_manager'] ?? '-'),
                                        'sales_category' => (string)($row['sales_category'] ?? '-'),
                                        'address' => (string)($row['address'] ?? '-'),
                                        'status' => $badge,
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </td>
                        <td><span class="<?= $badgeClass ?>"><?= $badge ?></span></td>
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

<template id="assign-products-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add In-House</h3>
            <button type="button" class="manager-modal-close" onclick="closeAssignProductsModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('inhouse') ?>" enctype="multipart/form-data">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Select Sales Manager</span>
                    <select name="sales_manager">
                        <option value="">Choose Sales Manager</option>
                        <?php foreach ($asmManagers as $managerName): ?>
                            <option value="<?= htmlspecialchars($managerName) ?>"><?= htmlspecialchars(strtoupper($managerName)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>


                <label class="manager-modal-field">
                    <span>Select Category</span>
                    <select name="sales_category">
                        <option value="">Choose Sales Category</option>
                        <option value="fiberx">FIBER-X</option>
                        <option value="surf2sawa">SURF2SAWA</option>
                        <option value="bida">BIDA</option>
                        <option value="sme">SME</option>
                    </select>
                </label>

                <label class="manager-modal-field">
                    <span>Enter First Name</span>
                    <input type="text" name="first_name" placeholder="Enter First Name">
                </label>

                <label class="manager-modal-field">
                    <span>Enter Middle Name</span>
                    <input type="text" name="middle_name" placeholder="Enter Middle Name">
                </label>

                <label class="manager-modal-field">
                    <span>Enter Last Name</span>
                    <input type="text" name="last_name" placeholder="Enter Last Name">
                </label>

                <label class="manager-modal-field">
                    <span>Contact</span>
                    <input type="text" name="contact" placeholder="Enter Contact" maxlength="11" inputmode="numeric">
                </label>

                <label class="manager-modal-field">
                    <span>Email Address</span>
                    <input type="email" name="email" placeholder="Enter Email Address">
                </label>

                <label class="manager-modal-field">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Optional password">
                </label>

                <label class="manager-modal-field">
                    <span>Photos</span>
                    <input type="file" name="photos" accept="image/*">
                </label>

                <label class="manager-modal-field manager-modal-field-wide">
                    <span>Address</span>
                    <input type="text" name="address" placeholder="Enter Address">
                </label>

                <label class="manager-modal-field">
                    <span>Select Status</span>
                    <select name="status">
                        <option value="active" selected>Approved</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Declined</option>
                    </select>
                </label>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAssignProductsModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="inhouse-approve-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Approve In-House User</h3>
            <button type="button" class="manager-modal-close" onclick="closeInhouseApproveModal()" aria-label="Close">x</button>
        </div>

        <form id="inhouse-approve-form" class="manager-modal-form" method="POST" action="">
            <?= \App\Helpers\Csrf::field(); ?>
            <p class="text-sm text-slate-600">
                Are you sure you want to approve <strong id="inhouse-approve-name">this in-house user</strong>?
            </p>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Approve</button>
                <button type="button" class="manager-modal-cancel" onclick="closeInhouseApproveModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="inhouse-delete-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Delete In-House User</h3>
            <button type="button" class="manager-modal-close" onclick="closeInhouseDeleteModal()" aria-label="Close">x</button>
        </div>
        <form id="inhouse-delete-form" class="manager-modal-form" method="POST" action="">
            <?= \App\Helpers\Csrf::field(); ?>
            <p class="text-sm text-slate-600">
                Are you sure you want to delete <strong id="inhouse-delete-name">this in-house user</strong>?
            </p>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Delete</button>
                <button type="button" class="manager-modal-cancel" onclick="closeInhouseDeleteModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="inhouse-action-info-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3 id="inhouse-action-title">In-House Action</h3>
            <button type="button" class="manager-modal-close" onclick="closeInhouseInfoModal()" aria-label="Close">x</button>
        </div>
        <div class="manager-view-grid text-sm text-slate-600" id="inhouse-action-message">Action details.</div>
        <div class="manager-modal-actions">
            <button type="button" class="manager-modal-submit" onclick="closeInhouseInfoModal()">OK</button>
        </div>
    </div>
</template>

<template id="inhouse-edit-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Edit In-House</h3>
            <button type="button" class="manager-modal-close" onclick="closeInhouseInfoModal()" aria-label="Close">x</button>
        </div>
        <form id="inhouse-edit-form" class="manager-modal-form" method="POST" action="" enctype="multipart/form-data">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>First Name</span>
                    <input type="text" name="first_name" data-ei="first_name">
                </label>
                <label class="manager-modal-field">
                    <span>Middle Name</span>
                    <input type="text" name="middle_name" data-ei="middle_name">
                </label>
                <label class="manager-modal-field">
                    <span>Last Name</span>
                    <input type="text" name="last_name" data-ei="last_name">
                </label>
                <label class="manager-modal-field">
                    <span>Contact</span>
                    <input type="text" name="contact" data-ei="contact" maxlength="11" inputmode="numeric">
                </label>
                <label class="manager-modal-field">
                    <span>Email</span>
                    <input type="email" name="email" data-ei="email">
                </label>
                <label class="manager-modal-field">
                    <span>Sales Manager</span>
                    <select name="sales_manager" data-ei="sales_manager">
                        <option value="">Choose Sales Manager</option>
                        <?php foreach ($asmManagers as $managerName): ?>
                            <option value="<?= htmlspecialchars($managerName) ?>"><?= htmlspecialchars(strtoupper($managerName)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="manager-modal-field">
                    <span>Category</span>
                    <select name="sales_category" data-ei="sales_category">
                        <option value="">Choose Sales Category</option>
                        <option value="fiberx">FIBER-X</option>
                        <option value="surf2sawa">SURF2SAWA</option>
                        <option value="bida">BIDA</option>
                        <option value="sme">SME</option>
                    </select>
                </label>
                <label class="manager-modal-field">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Leave blank to keep current password">
                </label>
                <label class="manager-modal-field">
                    <span>Photos</span>
                    <input type="file" name="photos" accept="image/*">
                </label>
                <label class="manager-modal-field manager-modal-field-wide">
                    <span>Address</span>
                    <input type="text" name="address" data-ei="address">
                </label>
                <label class="manager-modal-field">
                    <span>Status</span>
                    <select name="status" data-ei="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            </div>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closeInhouseInfoModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<style>
    .assign-products-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        font-size: 12px;
        color: #374151;
    }
    .assign-product-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 8px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #f8fafc;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .assign-product-pill:hover {
        border-color: #93c5fd;
        background: #eff6ff;
    }
    .assign-product-pill input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: #1d9bf0;
        cursor: pointer;
    }
    .assign-product-pill:has(input[type="checkbox"]:checked) {
        border-color: #1d9bf0;
        background: #dbeafe;
        color: #0f3f7a;
    }
    @media (max-width: 640px) {
        .assign-products-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<script>
    function openAssignProductsModal() {
        const template = document.getElementById('assign-products-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closeAssignProductsModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openInhouseApproveModal(actionUrl, displayName) {
        const template = document.getElementById('inhouse-approve-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');

        const form = document.getElementById('inhouse-approve-form');
        const nameEl = document.getElementById('inhouse-approve-name');
        if (form) form.action = actionUrl;
        if (nameEl) nameEl.textContent = displayName || 'this in-house user';
    }

    function closeInhouseApproveModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openInhouseInfoModal(title, html) {
        const template = document.getElementById('inhouse-action-info-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');

        const titleEl = document.getElementById('inhouse-action-title');
        const messageEl = document.getElementById('inhouse-action-message');
        if (titleEl) titleEl.textContent = title || 'In-House Action';
        if (messageEl) messageEl.innerHTML = html || '';
    }

    function openInhouseDeleteModal(actionUrl, displayName) {
        const template = document.getElementById('inhouse-delete-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');

        const form = document.getElementById('inhouse-delete-form');
        const nameEl = document.getElementById('inhouse-delete-name');
        if (form) form.action = actionUrl;
        if (nameEl) nameEl.textContent = displayName || 'this in-house user';
    }

    function closeInhouseDeleteModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function closeInhouseInfoModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openViewInhouseModal(inhouse) {
        openInhouseInfoModal(
            'View In-House User',
            '<div><span>Name</span><p>' + (inhouse.name || '-') + '</p></div>'
            + '<div><span>Email</span><p>' + (inhouse.email || '-') + '</p></div>'
            + '<div><span>Contact</span><p>' + (inhouse.contact || '-') + '</p></div>'
            + '<div><span>Sales Manager</span><p>' + (inhouse.sales_manager || '-') + '</p></div>'
            + '<div><span>Category</span><p>' + (inhouse.sales_category || '-') + '</p></div>'
            + '<div><span>Address</span><p>' + (inhouse.address || '-') + '</p></div>'
            + '<div><span>Status</span><p>' + (inhouse.status || '-') + '</p></div>'
        );
    }

    function openEditInhouseModal(inhouse) {
        const template = document.getElementById('inhouse-edit-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) {
            content.classList.add('manager-modal-shell');
            const form = content.querySelector('#inhouse-edit-form');
            if (form) {
                form.action = inhouse.update_url || '';
                form.addEventListener('submit', submitInhouseEditForm);
            }
            content.querySelectorAll('[data-ei]').forEach((node) => {
                const key = node.getAttribute('data-ei');
                if (node.tagName === 'SELECT') {
                    node.value = inhouse[key] || (key === 'status' ? 'active' : '');
                } else {
                    node.value = inhouse[key] || '';
                }
            });
        }
    }

    function openDeleteInhouseModal(inhouse) {
        openInhouseDeleteModal(inhouse.delete_url || '', inhouse.name || 'this in-house user');
    }

    async function submitInhouseEditForm(event) {
        event.preventDefault();
        const form = event.currentTarget;
        if (!form || !form.action) return;

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: new FormData(form),
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok || payload.success === false) {
                const firstError = payload && typeof payload === 'object'
                    ? Object.values(payload).flat().find(Boolean)
                    : null;
                alert(firstError || 'Unable to update in-house user.');
                return;
            }

            window.location.href = payload.redirect || window.location.href;
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Save';
            }
        }
    }
</script>
