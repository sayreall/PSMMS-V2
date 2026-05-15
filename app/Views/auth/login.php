<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form id="login-form" class="auth-form" method="POST" action="<?= App\Config\App::url('login') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="you@company.com" data-validate="required|email">
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
    </div>

    <div>
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-input" placeholder="Enter password" data-validate="required|min:6">
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
