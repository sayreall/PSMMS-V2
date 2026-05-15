<?php
$pagination = $pagination ?? [];
$totalPages = (int) ($pagination['pages'] ?? 1);
$currentPage = (int) ($pagination['page'] ?? 1);

if ($totalPages <= 1) {
    return;
}

$params = $_GET;
?>

<nav class="flex items-center justify-between mt-6" aria-label="Pagination">
    <p class="text-sm text-slate-500">Page <?= $currentPage ?> of <?= $totalPages ?></p>
    <div class="flex items-center gap-2">
        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
            <?php $params['page'] = $page; ?>
            <a href="<?= App\Config\App::url('users') . '?' . http_build_query($params) ?>"
               class="px-3 py-1.5 rounded-lg text-sm <?= $page === $currentPage ? 'bg-primary-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <?= $page ?>
            </a>
        <?php endfor; ?>
    </div>
</nav>
