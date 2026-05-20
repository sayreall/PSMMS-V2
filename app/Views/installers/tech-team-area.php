<?php
$activeRoute = 'tech_team_area';
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Tech Team Area</h1>
        <p class="text-lg text-slate-700 mt-1">Manage tech team area assignments.</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Tech Team Area List</h2>
            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add team area" onclick="openAddTechTeamAreaModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Team Area</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>Area</th>
                        <th>Team</th>
                        <th>Action</th>
                        <th>Validation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sampleRows = [
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'approved'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'pending'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'declined'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'declined'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'approved'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'approved'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'pending'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'pending'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'pending'],
                        ['area' => 'QUEZON CITY', 'team' => '10', 'validation' => 'declined'],
                    ];
                    ?>
                    <?php foreach ($sampleRows as $row): ?>
                        <?php
                        $validation = strtolower($row['validation']);
                        $validationClass = 'manager-badge manager-badge-pending';
                        if ($validation === 'approved') {
                            $validationClass = 'manager-badge manager-badge-approved';
                        } elseif ($validation === 'declined') {
                            $validationClass = 'manager-badge manager-badge-declined';
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['area']) ?></td>
                            <td><?= htmlspecialchars($row['team']) ?></td>
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

<template id="add-tech-team-area-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add Team Area</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddTechTeamAreaModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" onsubmit="event.preventDefault(); closeAddTechTeamAreaModal();">
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Team Name</span>
                    <input type="text" placeholder="Enter Team Name" required>
                </label>
                <label class="manager-modal-field">
                    <span>Assigned Area</span>
                    <input type="text" placeholder="Enter Assigned Area" required>
                </label>
                <label class="manager-modal-field">
                    <span>Lead</span>
                    <input type="text" placeholder="Enter Team Lead" required>
                </label>
                <label class="manager-modal-field">
                    <span>Status</span>
                    <select required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            </div>
            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddTechTeamAreaModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    function openAddTechTeamAreaModal() {
        const template = document.getElementById('add-tech-team-area-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
    }

    function closeAddTechTeamAreaModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }
</script>
