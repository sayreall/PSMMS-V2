<?php
$flash = $flash ?? ['message' => null, 'type' => 'success'];
if (empty($flash['message'])) {
    return;
}

$styles = [
    'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'error' => 'bg-rose-50 text-rose-700 border-rose-200',
    'info' => 'bg-blue-50 text-blue-700 border-blue-200',
    'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
];
$style = $styles[$flash['type']] ?? $styles['success'];
?>

<div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= $style ?>">
    <?= htmlspecialchars($flash['message']) ?>
</div>
