<?php
$errors = $errors ?? [];
$old = $old ?? [];
$asmManagers = $asmManagers ?? [];
$inhouseProductOptions = $inhouseProductOptions ?? ['surf2sawa', 'fiberx', 'bida', 'sme'];
$companyEmailOld = $old['company_email'] ?? '';
$companyEmailLocal = $old['company_email_local'] ?? ($companyEmailOld ? explode('@', $companyEmailOld)[0] : '');
?>
<form id="register-form" class="auth-form auth-form-compact" method="POST" action="<?= App\Config\App::url('register') ?>" enctype="multipart/form-data" data-ajax="true">
    <?= \App\Helpers\Csrf::field() ?>

    <div class="form-col" data-role-hide="inhouse_sales,msa_partners" data-role-field>
        <label class="form-label">First name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" class="form-input" placeholder="First name" data-validate="required|min:2">
        <p class="form-error" data-error-for="first_name"><?= $errors['first_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-hide="inhouse_sales,msa_partners" data-role-field>
        <label class="form-label">Middle name</label>
        <input type="text" name="middle_name" value="<?= htmlspecialchars($old['middle_name'] ?? '') ?>" class="form-input" placeholder="Middle name" data-validate="min:2">
        <p class="form-error" data-error-for="middle_name"><?= $errors['middle_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-hide="inhouse_sales,msa_partners" data-role-field>
        <label class="form-label">Last name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" class="form-input" placeholder="Last name" data-validate="required|min:2">
        <p class="form-error" data-error-for="last_name"><?= $errors['last_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-hide="super_admin,inhouse_sales,msa_partners" data-role-field>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="Personal Email" data-validate="required|email">
        <p class="form-error" data-error-for="email"><?= $errors['email'][0] ?? '' ?></p>
    </div>

    <div class="form-col form-col-full" data-role-hide="asm_manager,super_admin,inhouse_sales,msa_partners" data-role-field>
        <label class="form-label">Company email</label>
        <div class="input-suffix-wrap">
            <input type="text" name="company_email_local" value="<?= htmlspecialchars($companyEmailLocal) ?>" class="form-input" placeholder="name" data-validate="required" data-domain="paragoncorp.com.ph" data-email-target="company_email" pattern="[^@\s]+">
            <span class="input-suffix">@paragoncorp.com.ph</span>
        </div>
        <input type="hidden" name="company_email" value="<?= htmlspecialchars($companyEmailOld) ?>" data-validate="required|email">
        <p class="form-error" data-error-for="company_email"><?= $errors['company_email'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-hide="inhouse_sales,msa_partners" data-role-field>
        <label class="form-label">Contact number</label>
        <input type="text" name="contact_no" value="<?= htmlspecialchars($old['contact_no'] ?? '') ?>" class="form-input" placeholder="09XXXXXXXXX" data-validate="required|numeric|min:11|max:11" inputmode="numeric" maxlength="11" pattern="[0-9]{11}" autocomplete="tel">
        <p class="form-error" data-error-for="contact_no"><?= $errors['contact_no'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="asm_manager" data-role-field hidden>
        <label class="form-label">Position</label>
        <?php $managerPosition = $old['manager_position'] ?? ''; ?>
        <select name="manager_position" class="form-input" data-validate-base="required" data-role-only="asm_manager">
            <option value="">Select position</option>
            <option value="super_manager" <?= $managerPosition === 'super_manager' ? 'selected' : '' ?>>SUPER MANAGER</option>
            <option value="area_sales_manager" <?= $managerPosition === 'area_sales_manager' ? 'selected' : '' ?>>AREA SALES MANAGER</option>
            <option value="head_manager" <?= $managerPosition === 'head_manager' ? 'selected' : '' ?>>HEAD MANAGER</option>
        </select>
        <p class="form-error" data-error-for="manager_position"><?= $errors['manager_position'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="asm_manager" data-role-field hidden>
        <label class="form-label">Sales Manager</label>
        <input type="text" name="manager_sales_manager" value="<?= htmlspecialchars($old['manager_sales_manager'] ?? '') ?>" class="form-input" placeholder="Enter Sales Manager" data-role-only="asm_manager">
        <p class="form-error" data-error-for="manager_sales_manager"><?= $errors['manager_sales_manager'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">Select Sales Manager</label>
        <?php $inhouseSalesManager = $old['inhouse_sales_manager'] ?? ''; ?>
        <select name="inhouse_sales_manager" class="form-input" data-validate-base="required" data-role-only="inhouse_sales">
            <option value="">Choose Sales Manager</option>
            <?php foreach ($asmManagers as $managerName): ?>
                <option value="<?= htmlspecialchars($managerName) ?>" <?= $inhouseSalesManager === $managerName ? 'selected' : '' ?>>
                    <?= htmlspecialchars(strtoupper($managerName)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="form-error" data-error-for="inhouse_sales_manager"><?= $errors['inhouse_sales_manager'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">Select Category</label>
        <?php $inhouseSalesCategory = $old['inhouse_sales_category'] ?? ''; ?>
        <select name="inhouse_sales_category" class="form-input" data-validate-base="required" data-role-only="inhouse_sales">
            <option value="">Choose Product</option>
            <?php foreach ($inhouseProductOptions as $product): ?>
                <option value="<?= htmlspecialchars($product) ?>" <?= $inhouseSalesCategory === $product ? 'selected' : '' ?>>
                    <?= htmlspecialchars(strtoupper($product)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="form-error" data-error-for="inhouse_sales_category"><?= $errors['inhouse_sales_category'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">First name</label>
        <input type="text" name="inhouse_first_name" value="<?= htmlspecialchars($old['inhouse_first_name'] ?? '') ?>" class="form-input" placeholder="Enter First Name" data-validate-base="required" data-role-only="inhouse_sales">
        <p class="form-error" data-error-for="inhouse_first_name"><?= $errors['inhouse_first_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">Middle name</label>
        <input type="text" name="inhouse_middle_name" value="<?= htmlspecialchars($old['inhouse_middle_name'] ?? '') ?>" class="form-input" placeholder="Enter Middle Name" data-role-only="inhouse_sales">
        <p class="form-error" data-error-for="inhouse_middle_name"><?= $errors['inhouse_middle_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">Last name</label>
        <input type="text" name="inhouse_last_name" value="<?= htmlspecialchars($old['inhouse_last_name'] ?? '') ?>" class="form-input" placeholder="Enter Last Name" data-validate-base="required" data-role-only="inhouse_sales">
        <p class="form-error" data-error-for="inhouse_last_name"><?= $errors['inhouse_last_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">Contact</label>
        <input type="text" name="inhouse_contact" value="<?= htmlspecialchars($old['inhouse_contact'] ?? '') ?>" class="form-input" placeholder="Enter Contact" data-validate-base="required|numeric|min:11|max:11" data-role-only="inhouse_sales" inputmode="numeric" maxlength="11" pattern="[0-9]{11}">
        <p class="form-error" data-error-for="inhouse_contact"><?= $errors['inhouse_contact'][0] ?? '' ?></p>
    </div>

    <div class="form-col admin-span-full" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">Email Address</label>
        <div class="input-prefix-wrap">
            <span class="input-prefix">@</span>
            <input type="email" name="inhouse_email" value="<?= htmlspecialchars($old['inhouse_email'] ?? '') ?>" class="form-input" placeholder="Enter Email Address" data-validate-base="required|email" data-role-only="inhouse_sales">
        </div>
        <p class="form-error" data-error-for="inhouse_email"><?= $errors['inhouse_email'][0] ?? '' ?></p>
    </div>

    <div class="form-col form-col-full" data-role-only="inhouse_sales" data-role-field hidden>
        <label class="form-label">Address</label>
        <input type="text" name="inhouse_address" value="<?= htmlspecialchars($old['inhouse_address'] ?? '') ?>" class="form-input" placeholder="Enter House No. st. Barangay, Municipal, Province" data-role-only="inhouse_sales">
        <p class="form-error" data-error-for="inhouse_address"><?= $errors['inhouse_address'][0] ?? '' ?></p>
    </div>


    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Sales Manager</label>
        <?php $msaSalesManager = $old['msa_sales_manager'] ?? ''; ?>
        <select name="msa_sales_manager" class="form-input" data-validate-base="required" data-role-only="msa_partners">
            <option value="">Choose Sales Manager</option>
            <?php foreach ($asmManagers as $managerName): ?>
                <option value="<?= htmlspecialchars($managerName) ?>" <?= $msaSalesManager === $managerName ? 'selected' : '' ?>>
                    <?= htmlspecialchars(strtoupper($managerName)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="form-error" data-error-for="msa_sales_manager"><?= $errors['msa_sales_manager'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Company name</label>
        <input type="text" name="msa_company_name" value="<?= htmlspecialchars($old['msa_company_name'] ?? '') ?>" class="form-input" placeholder="Enter Company name" data-validate-base="required" data-role-only="msa_partners">
        <p class="form-error" data-error-for="msa_company_name"><?= $errors['msa_company_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">First name</label>
        <input type="text" name="msa_first_name" value="<?= htmlspecialchars($old['msa_first_name'] ?? '') ?>" class="form-input" placeholder="Enter First Name" data-validate-base="required" data-role-only="msa_partners">
        <p class="form-error" data-error-for="msa_first_name"><?= $errors['msa_first_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Middle name</label>
        <input type="text" name="msa_middle_name" value="<?= htmlspecialchars($old['msa_middle_name'] ?? '') ?>" class="form-input" placeholder="Enter Middle Name" data-role-only="msa_partners">
        <p class="form-error" data-error-for="msa_middle_name"><?= $errors['msa_middle_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Last name</label>
        <input type="text" name="msa_last_name" value="<?= htmlspecialchars($old['msa_last_name'] ?? '') ?>" class="form-input" placeholder="Enter Last Name" data-validate-base="required" data-role-only="msa_partners">
        <p class="form-error" data-error-for="msa_last_name"><?= $errors['msa_last_name'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Contact Number</label>
        <input type="text" name="msa_contact" value="<?= htmlspecialchars($old['msa_contact'] ?? '') ?>" class="form-input" placeholder="Enter Contact number" data-validate-base="required|numeric|min:11|max:11" data-role-only="msa_partners" inputmode="numeric" maxlength="11" pattern="[0-9]{11}">
        <p class="form-error" data-error-for="msa_contact"><?= $errors['msa_contact'][0] ?? '' ?></p>
    </div>

    <div class="form-col form-col-full" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Address</label>
        <input type="text" name="msa_address" value="<?= htmlspecialchars($old['msa_address'] ?? '') ?>" class="form-input" placeholder="Enter House No. st. Barangay, Municipal, Province" data-validate-base="required" data-role-only="msa_partners">
        <p class="form-error" data-error-for="msa_address"><?= $errors['msa_address'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Please Select MSA Type</label>
        <?php $msaAreaType = $old['msa_area_type'] ?? ''; ?>
        <select name="msa_area_type" class="form-input" data-validate-base="required" data-role-only="msa_partners">
            <option value="">Choose MSA Type</option>
            <option value="regional" <?= $msaAreaType === 'regional' ? 'selected' : '' ?>>REGIONAL</option>
            <option value="ncr" <?= $msaAreaType === 'ncr' ? 'selected' : '' ?>>NCR</option>
        </select>
        <p class="form-error" data-error-for="msa_area_type"><?= $errors['msa_area_type'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Select Category</label>
        <?php $msaSalesCategory = $old['msa_sales_category'] ?? ''; ?>
        <select name="msa_sales_category" class="form-input" data-validate-base="required" data-role-only="msa_partners">
            <option value="">Choose Product</option>
            <?php foreach ($inhouseProductOptions as $product): ?>
                <option value="<?= htmlspecialchars($product) ?>" <?= $msaSalesCategory === $product ? 'selected' : '' ?>>
                    <?= htmlspecialchars(strtoupper($product)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="form-error" data-error-for="msa_sales_category"><?= $errors['msa_sales_category'][0] ?? '' ?></p>
    </div>

    <div class="form-col admin-span-full" data-role-only="msa_partners" data-role-field hidden>
        <label class="form-label">Email Address</label>
        <div class="input-prefix-wrap">
            <span class="input-prefix">@</span>
            <input type="email" name="msa_email" value="<?= htmlspecialchars($old['msa_email'] ?? '') ?>" class="form-input" placeholder="Enter Email Address" data-validate-base="required|email" data-role-only="msa_partners">
        </div>
        <p class="form-error" data-error-for="msa_email"><?= $errors['msa_email'][0] ?? '' ?></p>
    </div>


    <div class="form-col" data-role-only="super_admin" data-role-field hidden>
        <label class="form-label">Position</label>
        <?php $adminPosition = $old['admin_position'] ?? ''; ?>
        <select name="admin_position" class="form-input" data-validate-base="required" data-role-only="super_admin">
            <option value="">Select Type of Position</option>
            <option value="dispatcher" <?= $adminPosition === 'dispatcher' ? 'selected' : '' ?>>Dispatcher</option>
            <option value="tech_leaders" <?= $adminPosition === 'tech_leaders' ? 'selected' : '' ?>>Tech Leaders</option>
            <option value="sales_team_leader" <?= $adminPosition === 'sales_team_leader' ? 'selected' : '' ?>>Sales Team Leader</option>
            <option value="validator" <?= $adminPosition === 'validator' ? 'selected' : '' ?>>Validator</option>
            <option value="accounting" <?= $adminPosition === 'accounting' ? 'selected' : '' ?>>Accounting</option>
            <option value="sales_admin" <?= $adminPosition === 'sales_admin' ? 'selected' : '' ?>>Sales Admin</option>
            <option value="backend" <?= $adminPosition === 'backend' ? 'selected' : '' ?>>Backend</option>
            <option value="marketing" <?= $adminPosition === 'marketing' ? 'selected' : '' ?>>Marketing</option>
            <option value="supervisor" <?= $adminPosition === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
            <option value="admin" <?= $adminPosition === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="manager" <?= $adminPosition === 'manager' ? 'selected' : '' ?>>Manager</option>
            <option value="area_sales_manager" <?= $adminPosition === 'area_sales_manager' ? 'selected' : '' ?>>Area Sales Manager</option>
            <option value="general_manager" <?= $adminPosition === 'general_manager' ? 'selected' : '' ?>>General Manager</option>
            <option value="product_business_manager" <?= $adminPosition === 'product_business_manager' ? 'selected' : '' ?>>Product &amp; Business Manager</option>
        </select>
        <p class="form-error" data-error-for="admin_position"><?= $errors['admin_position'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="super_admin" data-role-field hidden>
        <label class="form-label">Area (Optional)</label>
        <input type="text" name="admin_area" value="<?= htmlspecialchars($old['admin_area'] ?? '') ?>" class="form-input" placeholder="Enter Covered Area">
        <p class="form-error" data-error-for="admin_area"><?= $errors['admin_area'][0] ?? '' ?></p>
    </div>

    <div class="form-col form-col-full" data-role-only="super_admin" data-role-field hidden>
        <label class="form-label">Address</label>
        <input type="text" name="admin_address" value="<?= htmlspecialchars($old['admin_address'] ?? '') ?>" class="form-input" placeholder="Enter House No. st. Barangay, Municipal, Province" data-role-only="super_admin">
        <p class="form-error" data-error-for="admin_address"><?= $errors['admin_address'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="super_admin" data-role-field hidden>
        <label class="form-label">Employee ID</label>
        <input type="text" name="admin_employee_id" value="<?= htmlspecialchars($old['admin_employee_id'] ?? '') ?>" class="form-input" placeholder="Enter Employee ID" data-validate-base="required" data-role-only="super_admin" maxlength="7" pattern="PCC[0-9]{4}">
        <p class="form-error" data-error-for="admin_employee_id"><?= $errors['admin_employee_id'][0] ?? '' ?></p>
    </div>

    <div class="form-col" data-role-only="super_admin" data-role-field hidden>
        <label class="form-label">Department</label>
        <?php $adminDepartment = $old['admin_department'] ?? ''; ?>
        <select name="admin_department" class="form-input" data-validate-base="required" data-role-only="super_admin">
            <option value="">Select Department</option>
            <option value="accounting" <?= $adminDepartment === 'accounting' ? 'selected' : '' ?>>Accounting</option>
            <option value="operation" <?= $adminDepartment === 'operation' ? 'selected' : '' ?>>Operation</option>
        </select>
        <p class="form-error" data-error-for="admin_department"><?= $errors['admin_department'][0] ?? '' ?></p>
    </div>

    <div class="form-col admin-span-full" data-role-only="super_admin" data-role-field hidden>
        <label class="form-label">Company Email</label>
        <div class="input-suffix-wrap">
            <input type="text" name="company_email_local" value="<?= htmlspecialchars($companyEmailLocal) ?>" class="form-input" placeholder="name" data-validate-base="required" data-domain="paragoncorp.com.ph" data-email-target="company_email" data-role-only="super_admin" pattern="[^@\s]+">
            <span class="input-suffix">@paragoncorp.com.ph</span>
        </div>
        <input type="hidden" name="company_email" value="<?= htmlspecialchars($companyEmailOld) ?>" data-validate-base="required|email" data-role-only="super_admin">
        <p class="form-error" data-error-for="company_email"><?= $errors['company_email'][0] ?? '' ?></p>
    </div>

    <div class="form-col admin-span-full" data-role-only="super_admin" data-role-field hidden>
        <label class="form-label">Email Address</label>
        <div class="input-prefix-wrap">
            <span class="input-prefix">@</span>
            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="form-input" placeholder="Enter Email Address" data-validate-base="required|email" data-role-only="super_admin">
        </div>
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

    <div class="form-col">
        <label class="form-label">Requested role</label>
        <?php $role = $old['role'] ?? 'accounting'; ?>
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
