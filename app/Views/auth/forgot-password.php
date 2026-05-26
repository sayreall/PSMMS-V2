<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form id="forgot-form" class="auth-form" method="POST" action="<?= App\Config\App::url('forgot-password') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div>
        <label class="form-label">Email</label>
        <div class="input-icon-wrap">
            <span class="input-icon input-icon-left" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="M3 7l9 6 9-6"></path>
                </svg>
            </span>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="you@company.com" data-validate="required|email">
        </div>
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
    </div>

    <button type="submit" class="auth-button">Send Verification Code</button>

    <p class="auth-footnote"><a href="<?= App\Config\App::url('login') ?>" class="auth-link">Back to login</a></p>
</form>
