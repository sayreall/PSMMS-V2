<?php
$errors = $errors ?? [];
?>
<form id="reset-form" class="auth-form" method="POST" action="<?= App\Config\App::url('reset-password') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div>
        <label class="form-label">New password</label>
        <div class="password-input-wrap">
            <input id="reset-password" type="password" name="password" class="form-input" placeholder="At least 8 characters" data-validate="required|min:8">
            <button type="button" class="password-toggle-btn" data-password-target="#reset-password" aria-label="Show password" title="Show password">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        <p class="form-error" data-error-for="password"><?= $errors['password'][0] ?? '' ?></p>
    </div>

    <div>
        <label class="form-label">Confirm new password</label>
        <div class="password-input-wrap">
            <input id="reset-password-confirmation" type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" data-validate="required" data-match="#reset-password">
            <button type="button" class="password-toggle-btn" data-password-target="#reset-password-confirmation" aria-label="Show password" title="Show password">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        <p class="form-error" data-error-for="password_confirmation"><?= $errors['password_confirmation'][0] ?? '' ?></p>
    </div>

    <button type="submit" class="auth-button">Reset password</button>

    <p class="auth-footnote"><a href="<?= App\Config\App::url('login') ?>" class="auth-link">Back to login</a></p>
</form>
