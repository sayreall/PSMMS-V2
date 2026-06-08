<?php
$errors = $errors ?? [];
$old = $old ?? [];
$role = $old['role'] ?? 'asm_manager';
$companyEmailLocal = $old['company_email_local'] ?? '';
if ($companyEmailLocal === '' && !empty($old['company_email'])) {
    $companyEmailLocal = preg_replace('/@paragoncorp\.com\.ph$/i', '', (string)$old['company_email']);
}
$activeRoleSection = ($role === 'super_admin' || $role === 'accounting')
    ? 'admin'
    : ($role === 'inhouse_sales'
        ? 'inhouse'
        : ($role === 'msa_partners' ? 'msa' : 'manager'));
$formRoleClass = $activeRoleSection === 'admin'
    ? ' role-admin'
    : ($activeRoleSection === 'inhouse'
        ? ' role-inhouse'
        : ($activeRoleSection === 'msa' ? ' role-msa' : ''));

$roleSectionAttrs = static function (string $sections, string $activeRoleSection, string $extraClass = ''): string
{
    $sectionList = preg_split('/\s+/', trim($sections)) ?: [];
    $classes = trim('form-col role-section ' . $extraClass . (in_array($activeRoleSection, $sectionList, true) ? '' : ' is-hidden'));

    return 'class="' . htmlspecialchars($classes) . '" data-role-section="' . htmlspecialchars($sections) . '"';
};
?>
<form id="register-form" class="auth-form auth-form-compact<?= $formRoleClass ?>" method="POST" action="<?= App\Config\App::url('register') ?>" enctype="multipart/form-data" data-ajax="true">
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
        <label class="form-label">Contact number</label>
        <input type="text" name="contact_no" value="<?= htmlspecialchars($old['contact_no'] ?? '') ?>" class="form-input" placeholder="09XXXXXXXXX" data-validate="required|numeric|min:11|max:11" inputmode="numeric" maxlength="11" pattern="[0-9]{11}" autocomplete="tel">
        <p class="form-error" data-error-for="contact_no"><?= $errors['contact_no'][0] ?? '' ?></p>
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

    <div <?= $roleSectionAttrs('manager', $activeRoleSection) ?>>
        <label class="form-label">Position</label>
        <select name="manager_position" class="form-input" data-validate-role="manager|required">
            <option value="">Select Type of Position</option>
            <option value="super_manager" <?= ($old['manager_position'] ?? '') === 'super_manager' ? 'selected' : '' ?>>Super Manager</option>
            <option value="area_sales_manager" <?= ($old['manager_position'] ?? 'area_sales_manager') === 'area_sales_manager' ? 'selected' : '' ?>>Area Sales Manager</option>
            <option value="head_manager" <?= ($old['manager_position'] ?? '') === 'head_manager' ? 'selected' : '' ?>>Head Manager</option>
        </select>
        <p class="form-error" data-error-for="manager_position"><?= $errors['manager_position'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('manager', $activeRoleSection) ?>>
        <label class="form-label">Sales Manager</label>
        <input type="text" name="manager_sales_manager" value="<?= htmlspecialchars($old['manager_sales_manager'] ?? '') ?>" class="form-input" placeholder="Enter Sales Manager">
        <p class="form-error" data-error-for="manager_sales_manager"><?= $errors['manager_sales_manager'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('admin', $activeRoleSection) ?>>
        <label class="form-label">Position</label>
        <select name="admin_position" class="form-input" data-validate-role="admin|required">
            <option value="">Select Type of Position</option>
            <option value="dispatcher" <?= ($old['admin_position'] ?? '') === 'dispatcher' ? 'selected' : '' ?>>Dispatcher</option>
            <option value="tech_leaders" <?= ($old['admin_position'] ?? '') === 'tech_leaders' ? 'selected' : '' ?>>Tech Leaders</option>
            <option value="sales_team_leader" <?= ($old['admin_position'] ?? '') === 'sales_team_leader' ? 'selected' : '' ?>>Sales Team Leader</option>
            <option value="validator" <?= ($old['admin_position'] ?? '') === 'validator' ? 'selected' : '' ?>>Validator</option>
            <option value="accounting" <?= ($old['admin_position'] ?? '') === 'accounting' ? 'selected' : '' ?>>Accounting</option>
            <option value="sales_admin" <?= ($old['admin_position'] ?? '') === 'sales_admin' ? 'selected' : '' ?>>Sales Admin</option>
            <option value="backend" <?= ($old['admin_position'] ?? '') === 'backend' ? 'selected' : '' ?>>Backend</option>
            <option value="marketing" <?= ($old['admin_position'] ?? '') === 'marketing' ? 'selected' : '' ?>>Marketing</option>
            <option value="supervisor" <?= ($old['admin_position'] ?? '') === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
            <option value="admin" <?= ($old['admin_position'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
        <p class="form-error" data-error-for="admin_position"><?= $errors['admin_position'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('admin', $activeRoleSection) ?>>
        <label class="form-label">Department</label>
        <select name="admin_department" class="form-input" data-validate-role="admin|required">
            <option value="">Select Department</option>
            <option value="operation" <?= ($old['admin_department'] ?? '') === 'operation' ? 'selected' : '' ?>>Operation</option>
            <option value="accounting" <?= ($old['admin_department'] ?? '') === 'accounting' ? 'selected' : '' ?>>Accounting</option>
        </select>
        <p class="form-error" data-error-for="admin_department"><?= $errors['admin_department'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('admin', $activeRoleSection) ?>>
        <label class="form-label">Employee ID</label>
        <input type="text" name="admin_employee_id" value="<?= htmlspecialchars($old['admin_employee_id'] ?? '') ?>" class="form-input" placeholder="Enter Employee ID" data-validate-role="admin|required">
        <p class="form-error" data-error-for="admin_employee_id"><?= $errors['admin_employee_id'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('admin', $activeRoleSection) ?>>
        <label class="form-label">Address</label>
        <input type="text" name="admin_address" value="<?= htmlspecialchars($old['admin_address'] ?? '') ?>" class="form-input" placeholder="Enter Address">
        <p class="form-error" data-error-for="admin_address"><?= $errors['admin_address'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('inhouse msa', $activeRoleSection) ?>>
        <label class="form-label">Sales Manager</label>
        <input type="text" name="sales_manager" value="<?= htmlspecialchars($old['sales_manager'] ?? '') ?>" class="form-input" placeholder="Enter Sales Manager" data-validate-role="inhouse,msa|required">
        <p class="form-error" data-error-for="sales_manager"><?= $errors['sales_manager'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('msa', $activeRoleSection) ?>>
        <label class="form-label">Company name</label>
        <input type="text" name="company_name" value="<?= htmlspecialchars($old['company_name'] ?? '') ?>" class="form-input" placeholder="Enter Company Name" data-validate-role="msa|required">
        <p class="form-error" data-error-for="company_name"><?= $errors['company_name'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('inhouse msa', $activeRoleSection) ?>>
        <label class="form-label">Sales category</label>
        <input type="text" name="sales_category" value="<?= htmlspecialchars($old['sales_category'] ?? '') ?>" class="form-input" placeholder="Enter Sales Category" data-validate-role="inhouse,msa|required">
        <p class="form-error" data-error-for="sales_category"><?= $errors['sales_category'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('msa', $activeRoleSection) ?>>
        <label class="form-label">Area type</label>
        <input type="text" name="area_type" value="<?= htmlspecialchars($old['area_type'] ?? '') ?>" class="form-input" placeholder="Enter Area Type" data-validate-role="msa|required">
        <p class="form-error" data-error-for="area_type"><?= $errors['area_type'][0] ?? '' ?></p>
    </div>

    <div <?= $roleSectionAttrs('inhouse msa', $activeRoleSection, 'admin-span-full') ?>>
        <label class="form-label">Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($old['address'] ?? '') ?>" class="form-input" placeholder="Enter Address" data-validate-role="inhouse,msa|required">
        <p class="form-error" data-error-for="address"><?= $errors['address'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Company Email</label>
        <div class="input-suffix-wrap">
            <input type="text" name="company_email_local" value="<?= htmlspecialchars($companyEmailLocal) ?>" class="form-input" placeholder="name" data-email-target="company_email" data-domain="paragoncorp.com.ph">
            <span class="input-suffix">@paragoncorp.com.ph</span>
        </div>
        <input type="hidden" name="company_email" value="<?= htmlspecialchars($old['company_email'] ?? '') ?>">
        <p class="form-error" data-error-for="company_email"><?= $errors['company_email'][0] ?? '' ?></p>
    </div>

    <div class="form-col">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="Enter Email Address" data-validate="required|email">
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
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

    <button type="submit" class="auth-button form-col-full">Create account</button>

    <p class="auth-footnote form-col-full">Already have an account? <a href="<?= App\Config\App::url('login') ?>" class="auth-link">Sign in</a></p>
</form>
