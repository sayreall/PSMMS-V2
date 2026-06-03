<?php
$activeRoute = 'partners';
$partners = $partners ?? [];
$asmManagers = $asmManagers ?? [];
$salesCategoryOptions = $salesCategoryOptions ?? ['surf2sawa', 'fiberx', 'bida', 'sme'];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Partners</h1>
        <p class="text-lg text-slate-700 mt-1">Regional and NCR Sales MSA Partners</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Partners Users</h2>
            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" onclick="openAddPartnerModal()" aria-label="Add Subscriber">
                    <span class="manager-add-icon">+</span>
                    <span>Add Subscriber</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Company</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Sales Manager</th>
                        <th>Category</th>
                        <th>Address</th>
                        <th>MSA Type</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partners as $partner): ?>
                    <?php
                        $status = (string)($partner['status'] ?? 'active');
                        $badge = 'Approved';
                        $badgeClass = 'manager-badge manager-badge-approved';
                        if ($status === 'pending') {
                            $badge = 'Pending';
                            $badgeClass = 'manager-badge manager-badge-pending';
                        } elseif ($status === 'inactive') {
                            $badge = 'Declined';
                            $badgeClass = 'manager-badge manager-badge-declined';
                        }
                        $displayName = trim((string)($partner['company_name'] ?? 'MSA Partner'));
                        $fullName = trim(($partner['first_name'] ?? '') . ' ' . ($partner['middle_name'] ?? '') . ' ' . ($partner['last_name'] ?? ''));
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($partner['photos'])): ?>
                                <img src="<?= App\Config\App::url('uploads/' . $partner['photos']) ?>" alt="<?= htmlspecialchars($displayName) ?>" class="manager-avatar-image">
                            <?php else: ?>
                                <div class="manager-avatar"><?= strtoupper(substr($displayName, 0, 1)) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="manager-name-cell"><?= htmlspecialchars($partner['company_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($fullName ?: '-') ?></td>
                        <td><?= htmlspecialchars($partner['contact'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($partner['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(strtoupper((string)($partner['sales_manager'] ?? '-'))) ?></td>
                        <td><?= htmlspecialchars(strtoupper((string)($partner['sales_category'] ?? '-'))) ?></td>
                        <td><?= htmlspecialchars($partner['address'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(strtoupper((string)($partner['area_type'] ?? '-'))) ?></td>
                        <td>
                            <div class="manager-action-group">
                                <?php if ($status === 'pending'): ?>
                                    <?php $approveUrl = App\Config\App::url('partners/' . urlencode((string)($partner['source_type'] ?? 'partner')) . '/' . (int)($partner['row_id'] ?? $partner['id'] ?? 0) . '/approve'); ?>
                                    <button type="button" class="manager-action-icon manager-action-view" aria-label="Approve partner" onclick='openApprovePartnerModal(<?= json_encode(["approve_url" => $approveUrl, "company_name" => $displayName], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                <?php endif; ?>
                                <?php $deleteUrl = App\Config\App::url('partners/' . urlencode((string)($partner['source_type'] ?? 'partner')) . '/' . (int)($partner['row_id'] ?? $partner['id'] ?? 0) . '/delete'); ?>
                                <button type="button" class="manager-action-icon manager-action-delete" aria-label="Delete partner" onclick='openDeletePartnerModal(<?= json_encode(["delete_url" => $deleteUrl, "company_name" => $displayName], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                </button>
                                <?php
                                    $editPartner = $partner;
                                    $editPartner['update_url'] = App\Config\App::url('partners/' . urlencode((string)($partner['source_type'] ?? 'partner')) . '/' . (int)($partner['row_id'] ?? $partner['id'] ?? 0) . '/update');
                                ?>
                                <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit partner" onclick='openEditPartnerModal(<?= json_encode($editPartner, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                </button>
                                <button type="button" class="manager-action-icon manager-action-view" aria-label="View partner" onclick='openViewPartnerModal(<?= json_encode($partner, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
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
    </div>
</div>

<template id="add-partner-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add MSA Partners</h3>
            <button type="button" class="manager-modal-close" onclick="closePartnerModal()" aria-label="Close">x</button>
        </div>
        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('partners') ?>" enctype="multipart/form-data">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Sales Manager</span>
                    <select name="sales_manager">
                        <option value="">Choose Sales Manager</option>
                        <?php foreach ($asmManagers as $managerName): ?>
                            <option value="<?= htmlspecialchars($managerName) ?>"><?= htmlspecialchars(strtoupper($managerName)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="manager-modal-field"><span>Company Name</span><input type="text" name="company_name" placeholder="Enter Company Name"></label>
                <label class="manager-modal-field"><span>First Name</span><input type="text" name="first_name" placeholder="Enter First Name"></label>
                <label class="manager-modal-field"><span>Middle Name</span><input type="text" name="middle_name" placeholder="Enter Middle Name"></label>
                <label class="manager-modal-field"><span>Last Name</span><input type="text" name="last_name" placeholder="Enter Last Name"></label>
                <label class="manager-modal-field"><span>Contact</span><input type="text" name="contact" placeholder="Enter Contact" maxlength="11" inputmode="numeric"></label>
                <label class="manager-modal-field"><span>Email Address</span><input type="email" name="email" placeholder="Enter Email Address"></label>
                <label class="manager-modal-field"><span>Password</span><input type="password" name="password" placeholder="Optional password"></label>
                <label class="manager-modal-field"><span>Photos</span><input type="file" name="photos" accept="image/*"></label>
                <label class="manager-modal-field">
                    <span>Please Select MSA Type</span>
                    <select name="area_type">
                        <option value="">Choose MSA Type</option>
                        <option value="regional">REGIONAL</option>
                        <option value="ncr">NCR</option>
                    </select>
                </label>
                <label class="manager-modal-field"><span>Address</span><input type="text" name="address" placeholder="Enter House No. St. Barangay, Municipal, Province"></label>
                <label class="manager-modal-field">
                    <span>Sales Category</span>
                    <select name="sales_category">
                        <option value="">Choose Sales Category</option>
                        <?php foreach ($salesCategoryOptions as $product): ?>
                            <option value="<?= htmlspecialchars($product) ?>"><?= htmlspecialchars(strtoupper($product)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="manager-modal-field">
                    <span>Select Status</span>
                    <select name="status">
                        <option value="active">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Declined</option>
                    </select>
                </label>
            </div>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closePartnerModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="view-partner-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>View MSA Partner</h3>
            <button type="button" class="manager-modal-close" onclick="closePartnerModal()" aria-label="Close">x</button>
        </div>
        <div class="manager-view-grid">
            <div><span>Sales Manager</span><p data-vp="sales_manager"></p></div>
            <div><span>Company</span><p data-vp="company_name"></p></div>
            <div><span>First Name</span><p data-vp="first_name"></p></div>
            <div><span>Middle Name</span><p data-vp="middle_name"></p></div>
            <div><span>Last Name</span><p data-vp="last_name"></p></div>
            <div><span>Contact</span><p data-vp="contact"></p></div>
            <div><span>Email</span><p data-vp="email"></p></div>
            <div><span>MSA Type</span><p data-vp="area_type"></p></div>
            <div><span>Address</span><p data-vp="address"></p></div>
            <div><span>Sales Category</span><p data-vp="sales_category"></p></div>
            <div><span>Status</span><p data-vp="status"></p></div>
        </div>
    </div>
</template>

<template id="edit-partner-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Edit MSA Partner</h3>
            <button type="button" class="manager-modal-close" onclick="closePartnerModal()" aria-label="Close">x</button>
        </div>
        <form id="edit-partner-form" class="manager-modal-form" method="POST" action="" enctype="multipart/form-data">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Sales Manager</span>
                    <select name="sales_manager" data-ep="sales_manager">
                        <option value="">Choose Sales Manager</option>
                        <?php foreach ($asmManagers as $managerName): ?>
                            <option value="<?= htmlspecialchars($managerName) ?>"><?= htmlspecialchars(strtoupper($managerName)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="manager-modal-field"><span>Company Name</span><input type="text" name="company_name" data-ep="company_name"></label>
                <label class="manager-modal-field"><span>First Name</span><input type="text" name="first_name" data-ep="first_name"></label>
                <label class="manager-modal-field"><span>Middle Name</span><input type="text" name="middle_name" data-ep="middle_name"></label>
                <label class="manager-modal-field"><span>Last Name</span><input type="text" name="last_name" data-ep="last_name"></label>
                <label class="manager-modal-field"><span>Contact</span><input type="text" name="contact" data-ep="contact" maxlength="11" inputmode="numeric"></label>
                <label class="manager-modal-field"><span>Email</span><input type="email" name="email" data-ep="email"></label>
                <label class="manager-modal-field"><span>Password</span><input type="password" name="password" placeholder="Leave blank to keep current password"></label>
                <label class="manager-modal-field"><span>Photos</span><input type="file" name="photos" accept="image/*"></label>
                <label class="manager-modal-field">
                    <span>MSA Type</span>
                    <select name="area_type" data-ep="area_type">
                        <option value="">Choose MSA Type</option>
                        <option value="regional">REGIONAL</option>
                        <option value="ncr">NCR</option>
                    </select>
                </label>
                <label class="manager-modal-field"><span>Address</span><input type="text" name="address" data-ep="address"></label>
                <label class="manager-modal-field">
                    <span>Sales Category</span>
                    <select name="sales_category" data-ep="sales_category">
                        <option value="">Choose Sales Category</option>
                        <?php foreach ($salesCategoryOptions as $product): ?>
                            <option value="<?= htmlspecialchars($product) ?>"><?= htmlspecialchars(strtoupper($product)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="manager-modal-field">
                    <span>Status</span>
                    <select name="status" data-ep="status">
                        <option value="active">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Declined</option>
                    </select>
                </label>
            </div>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closePartnerModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="approve-partner-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Approve MSA Partner</h3>
            <button type="button" class="manager-modal-close" onclick="closePartnerModal()" aria-label="Close">x</button>
        </div>
        <form id="approve-partner-form" class="manager-modal-form" method="POST" action="">
            <?= \App\Helpers\Csrf::field(); ?>
            <p class="manager-delete-text">Approve <strong id="approve-partner-name"></strong>?</p>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Approve</button>
                <button type="button" class="manager-modal-cancel" onclick="closePartnerModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="delete-partner-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Delete MSA Partner</h3>
            <button type="button" class="manager-modal-close" onclick="closePartnerModal()" aria-label="Close">x</button>
        </div>
        <form id="delete-partner-form" class="manager-modal-form" method="POST" action="">
            <?= \App\Helpers\Csrf::field(); ?>
            <p class="manager-delete-text">Are you sure you want to delete <strong id="delete-partner-name"></strong>?</p>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit manager-delete-confirm">Delete</button>
                <button type="button" class="manager-modal-cancel" onclick="closePartnerModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    function openPartnerModal(templateId) {
        const template = document.getElementById(templateId);
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closePartnerModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openAddPartnerModal() {
        openPartnerModal('add-partner-modal-template');
    }

    function openViewPartnerModal(partner) {
        openPartnerModal('view-partner-modal-template');
        const content = document.getElementById('modal-content');
        if (!content) return;
        content.querySelectorAll('[data-vp]').forEach((node) => {
            const key = node.getAttribute('data-vp');
            node.textContent = partner[key] || '-';
        });
    }

    function openEditPartnerModal(partner) {
        openPartnerModal('edit-partner-modal-template');
        const content = document.getElementById('modal-content');
        if (!content) return;
        const form = content.querySelector('#edit-partner-form');
        if (form) {
            form.action = partner.update_url || '';
            form.addEventListener('submit', submitEditPartnerForm);
        }
        content.querySelectorAll('[data-ep]').forEach((node) => {
            const key = node.getAttribute('data-ep');
            if (node.tagName === 'SELECT') {
                node.value = partner[key] || (key === 'status' ? 'active' : '');
            } else {
                node.value = partner[key] || '';
            }
        });
    }

    function openApprovePartnerModal(partner) {
        openPartnerModal('approve-partner-modal-template');
        const form = document.getElementById('approve-partner-form');
        const nameNode = document.getElementById('approve-partner-name');
        if (form) form.action = partner.approve_url || '';
        if (nameNode) nameNode.textContent = partner.company_name || 'this partner';
    }

    function openDeletePartnerModal(partner) {
        openPartnerModal('delete-partner-modal-template');
        const form = document.getElementById('delete-partner-form');
        const nameNode = document.getElementById('delete-partner-name');
        if (form) form.action = partner.delete_url || '';
        if (nameNode) nameNode.textContent = partner.company_name || 'this partner';
    }

    async function submitEditPartnerForm(event) {
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
                alert(firstError || 'Unable to update MSA partner.');
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
