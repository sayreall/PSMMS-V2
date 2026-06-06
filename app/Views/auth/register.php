<?php
$errors = $errors ?? [];
$old = $old ?? [];
$companyEmailOld = $old['company_email'] ?? ($old['email'] ?? '');
$role = $old['role'] ?? 'asm_manager';
?>
<form id="register-form" class="auth-form auth-form-compact" method="POST" action="<?= App\Config\App::url('register') ?>" enctype="multipart/form-data" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div class="form-col">
        <label class="form-label">First name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" class="form-input" placeholder="First name" data-validate="required|min:2">
        <p class="form-error" data-error-for="first_name"><?= $errors['first_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Last name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" class="form-input" placeholder="Last name" data-validate="required|min:2">
        <p class="form-error" data-error-for="last_name"><?= $errors['last_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="Personal Email" data-validate="required|email">
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Contact number</label>
        <input type="text" name="contact_no" value="<?= htmlspecialchars($old['contact_no'] ?? '') ?>" class="form-input" placeholder="09XXXXXXXXX" data-validate="required|numeric|min:11|max:11" inputmode="numeric" maxlength="11" pattern="[0-9]{11}" autocomplete="tel">
        <p class="form-error" data-error-for="contact_no"><?= $errors['contact_no'][0] ?? '' ?></p>
    </div>

    <input type="hidden" name="company_email" value="<?= htmlspecialchars($companyEmailOld) ?>" data-copy-from="email">
    <input type="hidden" name="manager_position" value="<?= htmlspecialchars($old['manager_position'] ?? 'area_sales_manager') ?>">

    <div class="form-col">
        <label class="form-label">Sales Manager</label>
        <input type="text" name="manager_sales_manager" value="<?= htmlspecialchars($old['manager_sales_manager'] ?? '') ?>" class="form-input" placeholder="Enter Sales Manager">
        <p class="form-error" data-error-for="manager_sales_manager"><?= $errors['manager_sales_manager'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Password</label>
        <div class="password-input-wrap">
            <input id="register-password" type="password" name="password" class="form-input" placeholder="At least 8 characters" data-validate="required|min:8">
            <button type="button" class="password-toggle-btn" data-password-target="#register-password" aria-label="Show password" title="Show password">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        <p class="form-error" data-error-for="password"><?= $errors['password'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Confirm password</label>
        <div class="password-input-wrap">
            <input id="register-password-confirmation" type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" data-validate="required" data-match="#register-password">
            <button type="button" class="password-toggle-btn" data-password-target="#register-password-confirmation" aria-label="Show password" title="Show password">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>
        <p class="form-error" data-error-for="password_confirmation"><?= $errors['password_confirmation'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Requested role</label>
        <select name="role" class="form-input" data-validate="required">
            <option value="asm_manager" <?= $role === 'asm_manager' ? 'selected' : '' ?>>Manager</option>
            <option value="super_admin" <?= $role === 'super_admin' ? 'selected' : '' ?>>Admin</option>
            <option value="msa_partners" <?= $role === 'msa_partners' ? 'selected' : '' ?>>MSA Partners</option>
            <option value="inhouse_sales" <?= $role === 'inhouse_sales' ? 'selected' : '' ?>>In-house Sales</option>
            <option value="sme_sales" <?= $role === 'sme_sales' ? 'selected' : '' ?>>SME Sales</option>
        </select>
        <p class="form-error" data-error-for="role"><?= $errors['role'][0] ?? '' ?></p>
    </div>

    <button type="submit" class="auth-button form-col-full">Create account</button>

    <p class="auth-footnote form-col-full">Already have an account? <a href="<?= App\Config\App::url('login') ?>" class="auth-link">Sign in</a></p>
</form>
