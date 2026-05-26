<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form id="login-form" class="auth-form" method="POST" action="<?= App\Config\App::url('login') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div>
        <label class="form-label">Company email</label>
        <div class="input-icon-wrap">
            <span class="input-icon input-icon-left" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="M3 7l9 6 9-6"></path>
                </svg>
            </span>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="name@paragoncorp.com.ph" data-validate="required|email">
        </div>
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
    </div>

    <div>
        <label class="form-label">Password</label>
        <div class="password-input-wrap has-left-icon">
            <span class="input-icon input-icon-left" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 2l-2 2m-7.61 7.61a5 5 0 1 1-7.07 7.07 5 5 0 0 1 7.07-7.07z"></path>
                    <path d="M14.5 6.5l3 3"></path>
                    <path d="M12.5 8.5l3 3"></path>
                    <path d="M10.5 10.5l3 3"></path>
                </svg>
            </span>
            <input id="login-password" type="password" name="password" class="form-input" placeholder="Enter password" data-validate="required|min:6">
            <button type="button" class="password-toggle-btn" data-password-target="#login-password" aria-label="Show password" title="Show password">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        <p class="form-error" data-error-for="password"><?= $errors['password'][0] ?? '' ?></p>
    </div>

    <div class="row-between">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a href="<?= App\Config\App::url('forgot-password') ?>" class="auth-link">Forgot Password?</a>
    </div>

    <button type="submit" class="auth-button">Sign In</button>

    <div class="auth-divider">or continue with</div>

    <p class="auth-footnote">Don't have an account? <a href="<?= App\Config\App::url('register') ?>" class="auth-link">Sign up</a></p>
</form>
