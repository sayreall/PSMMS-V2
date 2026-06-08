<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? 'PSMMS' ?> | Authentication</title>
    <meta name="description" content="PSMMS Dashboard Authentication">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken ?? '') ?>">
    <link rel="icon" type="image/png" href="<?= App\Config\App::url('images/small%20icon%201.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= App\Config\App::url('images/small%20icon%201.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="<?= App\Config\App::url('css/auth.css') ?>">
</head>
<?php
$authPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
$authMode = str_contains($authPath, 'register') ? 'register' : 'login';
?>
<body class="auth-shell auth-<?= htmlspecialchars($authMode) ?>">
    <main class="auth-wrap">
        <section class="auth-visual" aria-hidden="true">
            <div class="auth-visual-slides">
                <img src="<?= App\Config\App::url('images/loginbg.png') ?>" alt="">
                <img src="<?= App\Config\App::url('images/BIDA.png') ?>" alt="">
                <img src="<?= App\Config\App::url('images/CONVERGE%20SME.png') ?>" alt="">
                <img src="<?= App\Config\App::url('images/s222s.jpg') ?>" alt="">
            </div>
            <div class="visual-grid"></div>
            <div class="visual-glow"></div>
            <div class="visual-content">
                <p class="visual-kicker">PSMMS Secure Access</p>
                <h2>Built for reliable business operations.</h2>
                <p>Unified authentication for login, recovery, and verification with a clean and focused user experience.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-card-slides" aria-hidden="true">
                    <img src="<?= App\Config\App::url('images/loginbg.png') ?>" alt="">
                    <img src="<?= App\Config\App::url('images/BIDA.png') ?>" alt="">
                    <img src="<?= App\Config\App::url('images/CONVERGE%20SME.png') ?>" alt="">
                    <img src="<?= App\Config\App::url('images/s222s.jpg') ?>" alt="">
                </div>
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
