<?php
$activeRoute = 'dispatch_remarks';
$rows = $rows ?? [];

$scripts = '<script>'
    . 'function openAddDispatchRemarksModal() {'
    . '  const template = document.getElementById("add-dispatch-remarks-modal-template");'
    . '  if (!template || typeof openModal !== "function") return;'
    . '  openModal(template.innerHTML);'
    . '  const content = document.getElementById("modal-content");'
    . '  if (content) {'
    . '    content.classList.add("manager-modal-shell");'
    . '    initDispatchRemarksPreview(content);'
    . '  }'
    . '}'
    . 'function closeDispatchRemarksModal() {'
    . '  const content = document.getElementById("modal-content");'
    . '  if (content) content.classList.remove("manager-modal-shell");'
    . '  if (typeof closeModal === "function") closeModal();'
    . '}'
    . 'function initDispatchRemarksPreview(content) {'
    . '  const input = content.querySelector("[name=\"dispatch_remarks\"]");'
    . '  const chip = content.querySelector("[data-remarks-preview]");'
    . '  if (!chip) return;'
    . '  const update = () => {'
    . '    const label = (input && input.value.trim()) || "Remarks";'
    . '    chip.textContent = label.toUpperCase();'
    . '  };'
    . '  if (input) input.addEventListener("input", update);'
    . '  update();'
    . '}'
    . '</script>';
?>

<div class="space-y-5 manager-page dispatch-remarks-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Dispatch Remarks</h1>
        <p class="text-lg text-slate-700 mt-1">For monitoring of dispatcher in every status</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Dispatch Remarks</h2>
            <div class="manager-table-controls">
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add Dispatch Remarks" onclick="openAddDispatchRemarksModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dispatch Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-slate-500">No dispatch remarks yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $index => $row): ?>
                            <tr>
                                <td><?= (int)($index + 1) ?></td>
                                <td class="manager-name-cell"><?= htmlspecialchars($row['dispatch_remarks'] ?? '-') ?></td>
                                <td>
                                    <div class="manager-action-group">
                                        <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                        </button>
                                        <button type="button" class="manager-action-icon manager-action-delete" aria-label="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<template id="add-dispatch-remarks-modal-template">
    <div class="manager-modal dispatch-remarks-modal">
        <div class="manager-modal-header">
            <div>
                <h3>Add Dispatch Remarks</h3>
                <p>Keep the remark short and consistent for reporting.</p>
            </div>
            <button type="button" class="manager-modal-close" onclick="closeDispatchRemarksModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('dispatch-record/dispatch-remarks') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Dispatch Remarks</span>
                    <input type="text" name="dispatch_remarks" placeholder="Enter Dispatch Remarks" required>
                </label>
            </div>

            <div class="dispatch-preview">
                <span class="dispatch-preview-label">Preview</span>
                <span class="dispatch-preview-chip" data-remarks-preview>Remarks</span>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeDispatchRemarksModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>
