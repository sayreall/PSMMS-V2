<?php
$activeRoute = 'dispatch_status';
$rows = $rows ?? [];

$styles = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@melloware/coloris@0.24.0/dist/coloris.min.css">';

$scripts = '<script src="https://cdn.jsdelivr.net/npm/@melloware/coloris@0.24.0/dist/coloris.min.js"></script>'
    . '<script>'
    . 'document.addEventListener("DOMContentLoaded", () => {'
    . '  if (window.Coloris) {'
    . '    Coloris({'
    . '      el: ".coloris",'
    . '      theme: "large",'
    . '      format: "hex",'
    . '      alpha: false,'
    . '      swatches: ["#27d5e1", "#b71c1c", "#4a148c", "#beb5b0", "#ef6c00", "#ec407a", "#00bcd4", "#ffeb3b", "#6fe6fc", "#1565c0"]'
    . '    });'
    . '  }'
    . '});'
    . 'function openAddDispatchStatusModal() {'
    . '  const template = document.getElementById("add-dispatch-status-modal-template");'
    . '  if (!template || typeof openModal !== "function") return;'
    . '  openModal(template.innerHTML);'
    . '  const content = document.getElementById("modal-content");'
    . '  if (content) {'
    . '    content.classList.add("manager-modal-shell");'
    . '    initDispatchStatusPreview(content);'
    . '  }'
    . '}'
    . 'function closeDispatchStatusModal() {'
    . '  const content = document.getElementById("modal-content");'
    . '  if (content) content.classList.remove("manager-modal-shell");'
    . '  if (typeof closeModal === "function") closeModal();'
    . '}'
    . 'function initDispatchStatusPreview(content) {'
    . '  const nameInput = content.querySelector("[name=\"dispatch_status\"]");'
    . '  const colorInput = content.querySelector("[name=\"color\"]");'
    . '  const chip = content.querySelector("[data-dispatch-preview]");'
    . '  if (!chip) return;'
    . '  const update = () => {'
    . '    const label = (nameInput && nameInput.value.trim()) || "Status";'
    . '    const color = (colorInput && colorInput.value.trim()) || "#e2e8f0";'
    . '    chip.textContent = label.toUpperCase();'
    . '    chip.style.backgroundColor = color;'
    . '    const hex = color.replace("#", "");'
    . '    if (hex.length === 6) {'
    . '      const r = parseInt(hex.slice(0, 2), 16);'
    . '      const g = parseInt(hex.slice(2, 4), 16);'
    . '      const b = parseInt(hex.slice(4, 6), 16);'
    . '      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;'
    . '      chip.style.color = luminance > 0.65 ? "#0f172a" : "#f8fafc";'
    . '    }'
    . '  };'
    . '  if (nameInput) nameInput.addEventListener("input", update);'
    . '  if (colorInput) colorInput.addEventListener("input", update);'
    . '  update();'
    . '}'
    . '</script>';
?>

<div class="space-y-5 manager-page dispatch-status-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Dispatch Status</h1>
        <p class="text-lg text-slate-700 mt-1">View and manage all dispatch statuses</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Dispatch Status</h2>
            <div class="manager-table-controls">
                <button type="button" class="manager-add-btn" aria-label="Add Dispatch Status" onclick="openAddDispatchStatusModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Status</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dispatch Status</th>
                        <th>Color</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-slate-500">No dispatch statuses yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $index => $row): ?>
                            <?php $color = $row['color'] ?? '#e2e8f0'; ?>
                            <tr>
                                <td><?= (int)($index + 1) ?></td>
                                <td class="manager-name-cell"><?= htmlspecialchars($row['dispatch_status'] ?? '-') ?></td>
                                <td>
                                    <div class="dispatch-color-cell">
                                        <span class="dispatch-color-swatch" style="--swatch-color: <?= htmlspecialchars($color) ?>"></span>
                                        <span class="dispatch-color-value"><?= htmlspecialchars(strtoupper($color)) ?></span>
                                    </div>
                                </td>
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

<template id="add-dispatch-status-modal-template">
    <div class="manager-modal dispatch-status-modal">
        <div class="manager-modal-header">
            <div>
                <h3>Add Dispatch Status</h3>
                <p>Set the label and color used across dispatch records.</p>
            </div>
            <button type="button" class="manager-modal-close" onclick="closeDispatchStatusModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('dispatch-record/dispatch-status') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="dispatch-modal-grid">
                <label class="manager-modal-field">
                    <span>Dispatch Status</span>
                    <input type="text" name="dispatch_status" placeholder="Enter Dispatch Status" required>
                </label>

                <label class="manager-modal-field dispatch-color-field">
                    <span>Color</span>
                    <input type="text" name="color" class="coloris dispatch-color-input" data-coloris value="#ffffff" placeholder="#ffffff" required>
                </label>
            </div>

            <div class="dispatch-preview">
                <span class="dispatch-preview-label">Preview</span>
                <span class="dispatch-preview-chip" data-dispatch-preview>Status</span>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeDispatchStatusModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>
