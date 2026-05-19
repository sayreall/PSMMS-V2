<?php
$activeRoute = 'partners';
$partners = $partners ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Partners</h1>
        <p class="text-lg text-slate-700 italic mt-1">Regional and NCR Sales MSA Partners</p>
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
                        <th>Username</th>
                        <th>Contact No.</th>
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
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($partner['profile_picture'])): ?>
                                <img src="<?= App\Config\App::url('uploads/' . $partner['profile_picture']) ?>" alt="<?= htmlspecialchars($displayName) ?>" class="manager-avatar-image">
                            <?php else: ?>
                                <div class="manager-avatar"><?= strtoupper(substr($displayName, 0, 1)) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="manager-name-cell"><?= htmlspecialchars($partner['company_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($partner['username'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($partner['contact_no'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($partner['address'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(strtoupper((string)($partner['msa_type'] ?? '-'))) ?></td>
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
                                <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit partner" onclick='openEditPartnerModal(<?= json_encode($partner, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
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
                <label class="manager-modal-field"><span>Company Name</span><input type="text" name="company_name" placeholder="Enter Company Name"></label>
                <label class="manager-modal-field"><span>Username</span><input type="text" name="username" placeholder="Enter Username"></label>
                <label class="manager-modal-field"><span>Contact</span><input type="text" name="contact_no" placeholder="Enter Contact"></label>
                <label class="manager-modal-field"><span>Address</span><input type="text" name="address" placeholder="Enter House No. St. Barangay, Municipal, Province"></label>
                <label class="manager-modal-field"><span>Installer</span><input type="text" name="installer" placeholder="Enter Installer"></label>
                <label class="manager-modal-field">
                    <span>Please Select MSA Type</span>
                    <select name="msa_type">
                        <option value="">Choose MSA Type</option>
                        <option value="regional">REGIONAL</option>
                        <option value="ncr">NCR</option>
                    </select>
                </label>
                <label class="manager-modal-field"><span>Email Address</span><input type="email" name="email" placeholder="Enter Email Address"></label>
                <label class="manager-modal-field"><span>Profile Picture</span><input type="file" name="profile_picture"></label>
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
            <div><span>Company</span><p data-vp="company_name"></p></div>
            <div><span>Username</span><p data-vp="username"></p></div>
            <div><span>Contact</span><p data-vp="contact_no"></p></div>
            <div><span>Address</span><p data-vp="address"></p></div>
            <div><span>Installer</span><p data-vp="installer"></p></div>
            <div><span>MSA Type</span><p data-vp="msa_type"></p></div>
            <div><span>Email</span><p data-vp="email"></p></div>
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
        <form class="manager-modal-form" onsubmit="event.preventDefault(); closePartnerModal();">
            <div class="manager-modal-grid">
                <label class="manager-modal-field"><span>Company Name</span><input type="text" data-ep="company_name"></label>
                <label class="manager-modal-field"><span>Username</span><input type="text" data-ep="username"></label>
                <label class="manager-modal-field"><span>Contact</span><input type="text" data-ep="contact_no"></label>
                <label class="manager-modal-field"><span>Address</span><input type="text" data-ep="address"></label>
                <label class="manager-modal-field"><span>Installer</span><input type="text" data-ep="installer"></label>
                <label class="manager-modal-field"><span>MSA Type</span><input type="text" data-ep="msa_type"></label>
                <label class="manager-modal-field"><span>Email</span><input type="email" data-ep="email"></label>
                <label class="manager-modal-field">
                    <span>Status</span>
                    <select data-ep="status">
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
        content.querySelectorAll('[data-ep]').forEach((node) => {
            const key = node.getAttribute('data-ep');
            if (node.tagName === 'SELECT') {
                node.value = partner[key] || 'active';
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
</script>
