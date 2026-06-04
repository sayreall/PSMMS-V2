<?php
$activeRoute = 'address_region';
$rows = $rows ?? [];
$philippineRegions = $philippineRegions ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Region</h1>
        <p class="text-lg text-slate-700 mt-1">Location area of every region</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Region</h2>

            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add Region" onclick="openAddRegionModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Region</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid" style="min-width: 100%;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th style="width: 160px;">Code</th>
                        <th style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="manager-name-cell"><?= htmlspecialchars($row['name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['code'] ?? '-') ?></td>
                            <td>
                                <div class="manager-action-group">
                                    <form method="POST" action="<?= App\Config\App::url('address/region/' . (int)($row['id'] ?? 0) . '/delete') ?>" onsubmit="return confirm('Delete this region?');">
                                        <?= \App\Helpers\Csrf::field(); ?>
                                        <button type="submit" class="manager-action-icon manager-action-delete" aria-label="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </form>
                                    <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit" onclick='openEditRegionModal(<?= (int)($row['id'] ?? 0) ?>, <?= json_encode((string)($row['name'] ?? '')) ?>, <?= json_encode((string)($row['code'] ?? '')) ?>)'>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                    </button>
                                </div>
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

<template id="add-region-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Add Region</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddRegionModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('address/region') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <label class="manager-modal-field">
                <span>Region Name</span>
                <select name="region_name" data-region-name-select required>
                    <option value="">Select Philippine Region</option>
                    <?php foreach ($philippineRegions as $region): ?>
                        <option value="<?= htmlspecialchars($region['name']) ?>" data-code="<?= htmlspecialchars($region['code']) ?>">
                            <?= htmlspecialchars($region['code'] . ' - ' . $region['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="region_code" data-region-code-input>
            </label>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddRegionModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="edit-region-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Edit Region</h3>
            <button type="button" class="manager-modal-close" onclick="closeEditRegionModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('address/region/__ID__/update') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <label class="manager-modal-field">
                <span>Region Name</span>
                <select name="region_name" data-edit-region-name-select required>
                    <option value="">Select Region</option>
                    <?php foreach ($philippineRegions as $region): ?>
                        <option value="<?= htmlspecialchars($region['name']) ?>" data-code="<?= htmlspecialchars($region['code']) ?>">
                            <?= htmlspecialchars($region['code'] . ' - ' . $region['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="region_code" data-edit-region-code-input>
            </label>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closeEditRegionModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    function openAddRegionModal() {
        const template = document.getElementById('add-region-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
        const select = content ? content.querySelector('[data-region-name-select]') : null;
        const codeInput = content ? content.querySelector('[data-region-code-input]') : null;
        if (select && codeInput) {
            select.addEventListener('change', () => {
                codeInput.value = select.selectedOptions[0]?.dataset.code || '';
            });
        }
    }

    function closeAddRegionModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openEditRegionModal(id, name, code) {
        const template = document.getElementById('edit-region-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML.replace('__ID__', String(id)));
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
        const select = content ? content.querySelector('[data-edit-region-name-select]') : null;
        const codeInput = content ? content.querySelector('[data-edit-region-code-input]') : null;
        if (select && codeInput) {
            select.value = name;
            codeInput.value = code || select.selectedOptions[0]?.dataset.code || '';
            select.addEventListener('change', () => {
                codeInput.value = select.selectedOptions[0]?.dataset.code || '';
            });
        }
    }

    function closeEditRegionModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }
</script>
