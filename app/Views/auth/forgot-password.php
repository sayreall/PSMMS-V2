<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form id="forgot-form" class="auth-form" method="POST" action="<?= App\Config\App::url('forgot-password') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="you@company.com" data-validate="required|email">
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
    </div>

    <button type="submit" class="auth-button">Send Verification Code</button>

    <p class="auth-footnote"><a href="<?= App\Config\App::url('login') ?>" class="auth-link">Back to login</a></p>
</form>
