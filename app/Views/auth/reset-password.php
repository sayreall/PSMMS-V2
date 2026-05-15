<?php
$errors = $errors ?? [];
?>
<form id="reset-form" class="auth-form" method="POST" action="<?= App\Config\App::url('reset-password') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div>
        <label class="form-label">New password</label>
        <input id="reset-password" type="password" name="password" class="form-input" placeholder="At least 8 characters" data-validate="required|min:8">
        <p class="form-error" data-error-for="password"><?= $errors['password'][0] ?? '' ?></p>
    </div>

    <div>
        <label class="form-label">Confirm new password</label>
        <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" data-validate="required" data-match="#reset-password">
        <p class="form-error" data-error-for="password_confirmation"><?= $errors['password_confirmation'][0] ?? '' ?></p>
    </div>

    <button type="submit" class="auth-button">Reset password</button>

    <p class="auth-footnote"><a href="<?= App\Config\App::url('login') ?>" class="auth-link">Back to login</a></p>
</form>
