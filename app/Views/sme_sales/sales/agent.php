<?php
$activeRoute = 'sales_agent';
$rows = $rows ?? [];
$categories = $categories ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Sales Agent</h1>
        <p class="text-lg text-slate-700 italic mt-1">Subs agents in every Inhouse and MSA Partners</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Sales Agent</h2>

            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add Sales Agent" onclick="openAddSalesAgentModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Sales Agent</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Sales Category</th>
                        <th>Agent Name</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                            $badge = 'Approved';
                            $badgeClass = 'manager-badge manager-badge-approved';
                            if (($row['validation'] ?? '') === 'pending') {
                                $badge = 'Pending';
                                $badgeClass = 'manager-badge manager-badge-pending';
                            } elseif (($row['validation'] ?? '') === 'declined') {
                                $badge = 'Declined';
                                $badgeClass = 'manager-badge manager-badge-declined';
                            }
                        ?>
                        <tr>
                            <td class="manager-name-cell"><?= htmlspecialchars($row['sales_category'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['agent_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars(strtoupper($row['status'] ?? '-')) ?></td>
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

<template id="add-sales-agent-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add Sales Agent</h3>
            <button type="button" class="manager-modal-close" onclick="closeSalesAgentModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('sales-agent') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Select Sales Category</span>
                    <select name="sales_category_id">
                        <option value="">Choose Sales Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int)($category['id'] ?? 0) ?>">
                                <?= htmlspecialchars($category['sales_category'] ?? '-') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="manager-modal-field">
                    <span>Agent Name</span>
                    <input type="text" name="agent_name" placeholder="Enter Last Name, First Name, Middle Name">
                </label>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeSalesAgentModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    function openAddSalesAgentModal() {
        const template = document.getElementById('add-sales-agent-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closeSalesAgentModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }
</script>
