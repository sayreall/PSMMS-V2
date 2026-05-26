<?php
$activeRoute = 'tech_data';
$rows = $rows ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Tech Data</h1>
        <p class="text-lg text-slate-700 mt-1">Manage installer technical data records.</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Tech Data List</h2>
            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add tech data" onclick="openAddTechDataModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Tech Data</span>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Installer</th>
                        <th>Full Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Area</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-slate-500">No tech data found.</td>
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
                                $initial = strtoupper(substr((string)($row['full_name'] ?? $row['installer_name'] ?? 'U'), 0, 1));
                            ?>
                            <tr>
                                <td>
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold"><?= htmlspecialchars($initial) ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['installer_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['full_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['type'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['category'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['area'] ?? '-') ?></td>
                                <td>
                                    <div class="manager-action-group">
                                        <button type="button" class="manager-action-icon manager-action-delete" aria-label="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                        <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
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
    </div>
</div>

<template id="add-tech-data-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add Tech Data</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddTechDataModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('installers/tech-data') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Installer Name</span>
                    <input type="text" name="installer_name" placeholder="Installer Code" required>
                </label>

                <label class="manager-modal-field">
                    <span>Full Name</span>
                    <input type="text" name="full_name" placeholder="Last Name, First Name" required>
                </label>

                <label class="manager-modal-field">
                    <span>Type</span>
                    <select name="type" required>
                        <option value="">-- SELECT TYPE --</option>
                        <option value="INHOUSE">INHOUSE</option>
                        <option value="PARTNER">PARTNER</option>
                    </select>
                </label>

                <label class="manager-modal-field">
                    <span>Category</span>
                    <select name="category" required>
                        <option value="">-- SELECT CATEGORY --</option>
                        <option value="GREEN PASTURE">GREEN PASTURE</option>
                        <option value="SURF2SAWA">SURF2SAWA</option>
                        <option value="FIBER-X">FIBER-X</option>
                    </select>
                </label>

                <label class="manager-modal-field">
                    <span>Area</span>
                    <select name="area" required>
                        <option value="">-- SELECT AREA --</option>
                        <option value="MANILA">MANILA</option>
                        <option value="QUEZON CITY">QUEZON CITY</option>
                        <option value="PASIG">PASIG</option>
                    </select>
                </label>

                <label class="manager-modal-field">
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
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300">
                        <input type="checkbox" name="check_surf2sawa" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">SURF2SAWA</span>
                    </label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300">
                        <input type="checkbox" name="check_fiberx" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">FIBER-X</span>
                    </label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300">
                        <input type="checkbox" name="check_bida" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">BIDA</span>
                    </label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg cursor-pointer hover:border-primary-300">
                        <input type="checkbox" name="check_sme" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">SME</span>
                    </label>
                </div>
            </div>
            <input type="hidden" name="status" value="active">

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddTechDataModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    function openAddTechDataModal() {
        const template = document.getElementById('add-tech-data-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closeAddTechDataModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }
</script>
