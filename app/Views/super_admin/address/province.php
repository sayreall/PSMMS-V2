<?php
$activeRoute = 'address_province';
$rows = $rows ?? [];
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Province</h1>
        <p class="text-lg text-slate-700 mt-1">Location area of every province</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Province</h2>

            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add Province">
                    <span class="manager-add-icon">+</span>
                    <span>Add Province</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid" style="min-width: 100%;">
                <thead>
                    <tr>
                        <th>Region</th>
                        <th>Province</th>
                        <th style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="manager-name-cell"><?= htmlspecialchars($row['region'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['province'] ?? '-') ?></td>
                            <td>
                                <div class="manager-action-group">
                                    <button type="button" class="manager-action-icon manager-action-delete" aria-label="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    </button>
                                    <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit">
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
