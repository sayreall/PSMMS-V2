<?php
$activeRoute = 'managers';
$managers = $managers ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Managers</h1>
        <p class="text-lg text-slate-700 mt-1">The manager can manage all sales monitoring</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Manager Users</h2>

            <div class="manager-table-controls">
                <input type="text" placeholder="Jan 10, 2026 - Feb 10, 2026" class="manager-control-input">
                <select class="manager-control-select" aria-label="Filter status">
                    <option>All Managers</option>
                    <option>Approved</option>
                    <option>Pending</option>
                    <option>Declined</option>
                </select>
                <button type="button" class="manager-add-btn" onclick="openAddManagerModal()" aria-label="Add manager">
                    <span class="manager-add-icon">+</span>
                    <span>Add Manager</span>
                </button>
                <button class="manager-control-icon" aria-label="Expand table">[]</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Sales Manager</th>
                        <th>Position</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Company Email</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($managers as $manager): ?>
                    <?php
                        $badge = 'Approved';
                        $badgeClass = 'manager-badge manager-badge-approved';
                        if (($manager['status'] ?? '') === 'pending') {
                            $badge = 'Pending';
                            $badgeClass = 'manager-badge manager-badge-pending';
                        } elseif (($manager['status'] ?? '') === 'inactive') {
                            $badge = 'Declined';
                            $badgeClass = 'manager-badge manager-badge-declined';
                        }
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($manager['photos'])): ?>
                                <img
                                    src="<?= App\Config\App::url('uploads/' . $manager['photos']) ?>"
                                    alt="<?= htmlspecialchars($manager['manager_name'] ?? 'Manager') ?>"
                                    class="manager-avatar-image"
                                >
                            <?php else: ?>
                                <div class="manager-avatar">
                                    <?= strtoupper(substr($manager['manager_name'] ?? 'U', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="manager-name-cell"><?= htmlspecialchars($manager['manager_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($manager['sales_manager'] ?? $manager['manager_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($manager['position'] ?? '') ?></td>
                        <td><?= htmlspecialchars($manager['contact'] ?? $manager['contact_no'] ?? '') ?></td>
                        <td><?= htmlspecialchars($manager['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($manager['company_email'] ?? '') ?></td>
                        <td>
                            <div class="manager-action-group">
                                <?php if (($manager['status'] ?? '') === 'pending'): ?>
                                <form method="POST" action="<?= App\Config\App::url('managers/' . ($manager['source_type'] ?? 'manager') . '/' . ($manager['row_id'] ?? $manager['id'] ?? 0) . '/approve') ?>" class="inline" data-confirm="true" data-confirm-text="Approve this manager?">
                                    <?= \App\Helpers\Csrf::field() ?>
                                    <button type="submit" class="manager-action-icon manager-action-approve-icon" aria-label="Approve manager">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 6L9 17l-5-5"/>
                                        </svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-edit"
                                    aria-label="Edit manager"
                                    onclick='openEditManagerModal(<?= json_encode([
                                        'id' => $manager['id'] ?? 0,
                                        'row_id' => $manager['row_id'] ?? $manager['id'] ?? 0,
                                        'source_type' => $manager['source_type'] ?? 'manager',
                                        'manager_name' => $manager['manager_name'] ?? '',
                                        'first_name' => $manager['first_name'] ?? '',
                                        'middle_name' => $manager['middle_name'] ?? '',
                                        'last_name' => $manager['last_name'] ?? '',
                                        'sales_manager' => $manager['sales_manager'] ?? $manager['manager_name'] ?? '',
                                        'position' => $manager['position'] ?? '',
                                        'contact' => $manager['contact'] ?? $manager['contact_no'] ?? '',
                                        'company_email' => $manager['company_email'] ?? '',
                                        'email' => $manager['email'] ?? '',
                                        'status' => $manager['status'] ?? 'active',
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-delete"
                                    aria-label="Delete manager"
                                    onclick='openDeleteManagerModal(<?= json_encode([
                                        'id' => $manager['id'] ?? 0,
                                        'row_id' => $manager['row_id'] ?? $manager['id'] ?? 0,
                                        'source_type' => $manager['source_type'] ?? 'manager',
                                        'manager_name' => $manager['manager_name'] ?? '',
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18"/>
                                        <path d="M8 6V4h8v2"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="manager-action-icon manager-action-view"
                                    aria-label="View manager"
                                    onclick='openViewManagerModal(<?= json_encode([
                                        'id' => $manager['id'] ?? 0,
                                        'manager_name' => $manager['manager_name'] ?? '',
                                        'first_name' => $manager['first_name'] ?? '',
                                        'middle_name' => $manager['middle_name'] ?? '',
                                        'last_name' => $manager['last_name'] ?? '',
                                        'sales_manager' => $manager['sales_manager'] ?? $manager['manager_name'] ?? '',
                                        'position' => $manager['position'] ?? '',
                                        'contact' => $manager['contact'] ?? $manager['contact_no'] ?? '',
                                        'company_email' => $manager['company_email'] ?? '',
                                        'email' => $manager['email'] ?? '',
                                        'status' => $manager['status'] ?? 'active',
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td>
                            <span class="<?= $badgeClass ?>">
                                <?= $badge ?>
                            </span>
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

<template id="add-manager-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add Manager</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddManagerModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('managers') ?>" enctype="multipart/form-data">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>First Name</span>
                    <input type="text" name="first_name" placeholder="Enter First Name">
                </label>

                <label class="manager-modal-field">
                    <span>Middle Name</span>
                    <input type="text" name="middle_name" placeholder="Enter Middle Name">
                </label>

                <label class="manager-modal-field">
                    <span>Last Name</span>
                    <input type="text" name="last_name" placeholder="Enter Last Name">
                </label>

                <label class="manager-modal-field">
                    <span>Sales Manager</span>
                    <input type="text" name="sales_manager" placeholder="Enter Sales Manager">
                </label>

                <label class="manager-modal-field">
                    <span>Position</span>
                    <select name="position">
                        <option value="">Type of Manager Position</option>
                        <option value="super_manager">Super Manager</option>
                        <option value="area_sales_manager">Area Sales Manager</option>
                        <option value="head_manager">Head Manager</option>
                    </select>
                </label>

                <label class="manager-modal-field">
                    <span>Contact</span>
                    <input type="text" name="contact" placeholder="Enter Contact Number">
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
                    <span>Password <em>(Optional)</em></span>
                    <input type="password" name="password" placeholder="At least 8 characters">
                </label>

                <label class="manager-modal-field">
                    <span>Photos</span>
                    <input type="file" name="photos" accept="image/*">
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
                <button type="button" class="manager-modal-cancel" onclick="closeAddManagerModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="view-manager-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>View Manager</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddManagerModal()" aria-label="Close">x</button>
        </div>
        <div class="manager-view-grid">
            <div><span>Name</span><p data-vm="manager_name"></p></div>
            <div><span>First Name</span><p data-vm="first_name"></p></div>
            <div><span>Middle Name</span><p data-vm="middle_name"></p></div>
            <div><span>Last Name</span><p data-vm="last_name"></p></div>
            <div><span>Sales Manager</span><p data-vm="sales_manager"></p></div>
            <div><span>Position</span><p data-vm="position"></p></div>
            <div><span>Contact</span><p data-vm="contact"></p></div>
            <div><span>Company Email</span><p data-vm="company_email"></p></div>
            <div><span>Email Address</span><p data-vm="email"></p></div>
            <div><span>Status</span><p data-vm="status"></p></div>
        </div>
    </div>
</template>

<template id="edit-manager-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Edit Manager</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddManagerModal()" aria-label="Close">x</button>
        </div>
        <form class="manager-modal-form" method="POST">
            <?= \App\Helpers\Csrf::field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>First Name</span>
                    <input type="text" name="first_name" data-em="first_name">
                </label>
                <label class="manager-modal-field">
                    <span>Middle Name</span>
                    <input type="text" name="middle_name" data-em="middle_name">
                </label>
                <label class="manager-modal-field">
                    <span>Last Name</span>
                    <input type="text" name="last_name" data-em="last_name">
                </label>
                <label class="manager-modal-field">
                    <span>Sales Manager</span>
                    <input type="text" name="sales_manager" data-em="sales_manager">
                </label>
                <label class="manager-modal-field">
                    <span>Position</span>
                    <select name="position" data-em="position">
                        <option value="">Type of Manager Position</option>
                        <option value="super_manager">Super Manager</option>
                        <option value="area_sales_manager">Area Sales Manager</option>
                        <option value="head_manager">Head Manager</option>
                    </select>
                </label>
                <label class="manager-modal-field">
                    <span>Contact</span>
                    <input type="text" name="contact" data-em="contact">
                </label>
                <label class="manager-modal-field">
                    <span>Company Email</span>
                    <input type="email" name="company_email" data-em="company_email">
                </label>
                <label class="manager-modal-field">
                    <span>Email Address</span>
                    <input type="email" name="email" data-em="email">
                </label>
                <label class="manager-modal-field">
                    <span>Status</span>
                    <select name="status" data-em="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            </div>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddManagerModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="delete-manager-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Delete Manager</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddManagerModal()" aria-label="Close">x</button>
        </div>
        <p class="manager-delete-text">Are you sure you want to delete <strong data-dm="manager_name"></strong>?</p>
        <div class="manager-modal-actions">
            <form method="POST" data-dm-form="delete" class="inline">
                <?= \App\Helpers\Csrf::field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="manager-modal-submit manager-delete-confirm">Delete</button>
            </form>
            <button type="button" class="manager-modal-cancel" onclick="closeAddManagerModal()">Cancel</button>
        </div>
    </div>
</template>

<script>
    function openAddManagerModal() {
        const template = document.getElementById('add-manager-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closeAddManagerModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openViewManagerModal(manager) {
        const template = document.getElementById('view-manager-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) {
            content.classList.add('manager-modal-shell');
            content.querySelectorAll('[data-vm]').forEach((node) => {
                const key = node.getAttribute('data-vm');
                node.textContent = manager[key] || '-';
            });
        }
    }

    function openEditManagerModal(manager) {
        const template = document.getElementById('edit-manager-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) {
            content.classList.add('manager-modal-shell');
            const form = content.querySelector('form.manager-modal-form');
            if (form) {
                const source = manager.source_type || 'manager';
                const rowId = manager.row_id || manager.id || 0;
                form.action = '<?= App\Config\App::url('managers') ?>/' + encodeURIComponent(source) + '/' + encodeURIComponent(rowId);
                form.method = 'POST';
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) submitButton.disabled = true;

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

                        const contentType = response.headers.get('content-type') || '';
                        if (contentType.includes('application/json')) {
                            const payload = await response.json();
                            if (payload.success) {
                                window.location.href = payload.redirect || '<?= App\Config\App::url('managers') ?>';
                                return;
                            }
                        }

                        window.location.reload();
                    } catch (error) {
                        window.location.reload();
                    } finally {
                        if (submitButton) submitButton.disabled = false;
                    }
                });
            }
            content.querySelectorAll('[data-em]').forEach((node) => {
                const key = node.getAttribute('data-em');
                if (node.tagName === 'SELECT') {
                    node.value = manager[key] || (key === 'status' ? 'active' : '');
                    return;
                }
                node.value = manager[key] || '';
            });
        }
    }

    function openDeleteManagerModal(manager) {
        const template = document.getElementById('delete-manager-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) {
            content.classList.add('manager-modal-shell');
            const nameNode = content.querySelector('[data-dm="manager_name"]');
            if (nameNode) nameNode.textContent = manager.manager_name || 'this manager';
            const deleteForm = content.querySelector('[data-dm-form="delete"]');
            if (deleteForm) {
                const source = manager.source_type || 'manager';
                const rowId = manager.row_id || manager.id || 0;
                deleteForm.action = '<?= App\Config\App::url('managers') ?>/' + encodeURIComponent(source) + '/' + encodeURIComponent(rowId);
            }
        }
    }
</script>
