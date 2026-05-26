<?php
$activeRoute = 'asm_name';
$rows = $rows ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">ASM NAMES</h1>
        <p class="text-lg text-slate-700 mt-1">The sales manager is manage the team leader and agent for In-House</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage ASM Names</h2>
            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add sales manager" onclick="openAddAsmNameModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Sales Manager</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-slate-500">No ASM name records found.</td>
                        </tr>
                    <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $validation = strtolower((string)($row['validation_status'] ?? 'pending'));
                        $validationClass = 'manager-badge manager-badge-pending';
                        if ($validation === 'approved') {
                            $validationClass = 'manager-badge manager-badge-approved';
                        } elseif ($validation === 'declined') {
                            $validationClass = 'manager-badge manager-badge-declined';
                        }
                        ?>
                        <tr>
                            <td>
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                    <?= strtoupper(substr((string)($row['name'] ?? 'U'), 0, 1)) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['name'] ?? '-') ?></td>
                            <td>
                                <div class="manager-action-group">
                                    <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                    </button>
                                    <button type="button" class="manager-action-icon manager-action-delete" aria-label="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    </button>
                                    <button type="button" class="manager-action-icon manager-action-view" aria-label="View">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td><span class="<?= $validationClass ?>"><?= ucfirst($validation) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
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

<template id="add-asm-name-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add Sales Manager</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddAsmNameModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('asm/name') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field md:col-span-2">
                    <span>Sales Manager</span>
                    <input type="text" name="name" placeholder="Enter Sales Manager" required>
                </label>
                <label class="manager-modal-field md:col-span-2">
                    <span>Validation</span>
                    <select name="validation_status" required>
                        <option value="approved">Approved</option>
                        <option value="pending" selected>Pending</option>
                        <option value="declined">Declined</option>
                    </select>
                </label>
            </div>

            <div class="mt-4">
                <p class="text-sm font-medium text-slate-700 mb-2">Check Status</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300 transition-colors">
                        <input type="checkbox" name="check_surf2sawa" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">SURF2SAWA</span>
                    </label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300 transition-colors">
                        <input type="checkbox" name="check_fiberx" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">FIBER-X</span>
                    </label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300 transition-colors">
                        <input type="checkbox" name="check_bida" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">BIDA</span>
                    </label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300 transition-colors">
                        <input type="checkbox" name="check_sme" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">SME</span>
                    </label>
                </div>
            </div>

            <input type="hidden" name="status" value="active">
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddAsmNameModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    function openAddAsmNameModal() {
        const template = document.getElementById('add-asm-name-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closeAddAsmNameModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }
</script>
