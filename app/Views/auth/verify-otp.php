<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form id="otp-form" class="auth-form" method="POST" action="<?= App\Config\App::url('verify-otp') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div>
        <label class="form-label">Verification code</label>
        <input type="text" name="otp" value="<?= htmlspecialchars($old['otp'] ?? '') ?>" class="form-input" placeholder="6-digit code" data-validate="required|min:6" maxlength="6" inputmode="numeric">
        <p class="form-error" data-error-for="otp"><?= $errors['otp'][0] ?? '' ?></p>
    </div>

    <button type="submit" class="auth-button">Verify code</button>

    <p class="auth-footnote">Wrong email? <a href="<?= App\Config\App::url('forgot-password') ?>" class="auth-link">Try again</a></p>
</form>
