<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<form id="register-form" class="auth-form auth-form-compact" method="POST" action="<?= App\Config\App::url('register') ?>" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div class="form-col">
        <label class="form-label">First name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" class="form-input" placeholder="Jane" data-validate="required|min:2">
        <p class="form-error" data-error-for="first_name"><?= $errors['first_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Middle name</label>
        <input type="text" name="middle_name" value="<?= htmlspecialchars($old['middle_name'] ?? '') ?>" class="form-input" placeholder="Marie (optional)">
        <p class="form-error" data-error-for="middle_name"><?= $errors['middle_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Last name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" class="form-input" placeholder="Doe" data-validate="required|min:2">
        <p class="form-error" data-error-for="last_name"><?= $errors['last_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="you@company.com" data-validate="required|email">
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Company email</label>
        <input type="email" name="company_email" value="<?= htmlspecialchars($old['company_email'] ?? '') ?>" class="form-input" placeholder="name@yourcompany.com" data-validate="required|email">
        <p class="form-error" data-error-for="company_email"><?= $errors['company_email'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Password</label>
        <input id="register-password" type="password" name="password" class="form-input" placeholder="At least 8 characters" data-validate="required|min:8">
        <p class="form-error" data-error-for="password"><?= $errors['password'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" data-validate="required" data-match="#register-password">
        <p class="form-error" data-error-for="password_confirmation"><?= $errors['password_confirmation'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Requested role</label>
        <?php $role = $old['role'] ?? 'accounting'; ?>
        <select name="role" class="form-input" data-validate="required">
            <option value="accounting" <?= $role === 'accounting' ? 'selected' : '' ?>>Accounting</option>
            <option value="asm_manager" <?= $role === 'asm_manager' ? 'selected' : '' ?>>ASM Manager</option>
            <option value="head_manager" <?= $role === 'head_manager' ? 'selected' : '' ?>>Head Manager</option>
        </select>
        <p class="form-error" data-error-for="role"><?= $errors['role'][0] ?? '' ?></p>
    </div>

    <button type="submit" class="auth-button form-col-full">Create account</button>

    <p class="auth-footnote form-col-full">Already have an account? <a href="<?= App\Config\App::url('login') ?>" class="auth-link">Sign in</a></p>
</form>
