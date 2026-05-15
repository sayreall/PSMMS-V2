<?php use App\Config\App; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 | Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg text-center">
        <p class="text-sm font-semibold text-teal-600">403</p>
        <h1 class="text-3xl font-bold mt-2">Access denied</h1>
        <p class="mt-3 text-slate-600">You do not have permission to view this page.</p>
        <div class="mt-6">
            <a href="<?= App::url('dashboard') ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-medium hover:bg-teal-700">Back to dashboard</a>
        </div>
    </div>
</body>
</html>
