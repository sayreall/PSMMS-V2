<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? 'PSMMS' ?> | Authentication</title>
    <meta name="description" content="PSMMS Dashboard Authentication">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? '') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= App\Config\App::url('css/auth.css') ?>">
</head>
<body class="auth-shell">
    <main class="auth-wrap">
        <section class="auth-visual" aria-hidden="true">
            <div class="visual-grid"></div>
            <div class="visual-glow"></div>
            <div class="visual-content">
                <p class="visual-kicker">PSMMS Secure Access</p>
                <h2>Built for reliable campus operations.</h2>
                <p>Unified authentication for login, recovery, and verification with a clean and focused user experience.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <h1><?= $title ?? 'Welcome back' ?></h1>
                <p class="auth-subtitle"><?= $subtitle ?? 'Sign in to continue.' ?></p>

                <?php
                $flashMsg = \App\Helpers\Session::getFlash('message');
                $flashType = \App\Helpers\Session::getFlash('message_type') ?? 'info';
                if ($flashMsg):
                ?>
                <div class="flash flash-<?= htmlspecialchars($flashType) ?>">
                    <?= htmlspecialchars($flashMsg) ?>
                </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </section>
    </main>

    <script src="<?= App\Config\App::url('js/auth.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $scripts ?? '' ?>
</body>
</html>
