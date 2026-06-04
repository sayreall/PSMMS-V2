<?php
$activeRoute = 'address_province';
$rows = $rows ?? [];
$regions = $regions ?? [];
$selectedRegionId = (int)($selectedRegionId ?? 0);
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Province</h1>
        <p class="text-lg text-slate-700 mt-1">Location area of every province</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Province</h2>

            <div class="manager-table-controls">
                <form method="GET" action="<?= App\Config\App::url('address/province') ?>" class="manager-table-controls">
                    <select name="region_id" class="manager-control-input" onchange="this.form.submit()">
                        <option value="">All Regions</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= (int)$region['id'] ?>" data-region-code="<?= htmlspecialchars($region['region_code'] ?? '') ?>" <?= $selectedRegionId === (int)$region['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($region['region_name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add Province" onclick="openAddProvinceModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Province</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid" style="min-width: 100%;">
                <thead>
                    <tr>
                        <th>Region</th>
                        <th>Province</th>
                        <th style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="manager-name-cell"><?= htmlspecialchars($row['region'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['province'] ?? '-') ?></td>
                            <td>
                                <div class="manager-action-group">
                                    <form method="POST" action="<?= App\Config\App::url('address/province/' . (int)($row['id'] ?? 0) . '/delete') ?>" onsubmit="return confirm('Delete this province?');">
                                        <?= \App\Helpers\Csrf::field(); ?>
                                        <input type="hidden" name="region_id" value="<?= $selectedRegionId > 0 ? $selectedRegionId : (int)($row['region_id'] ?? 0) ?>">
                                        <button type="submit" class="manager-action-icon manager-action-delete" aria-label="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </form>
                                    <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit" onclick='openEditProvinceModal(<?= (int)($row['id'] ?? 0) ?>, <?= (int)($row['region_id'] ?? 0) ?>, <?= json_encode((string)($row['province'] ?? '')) ?>, <?= json_encode((string)($row['province_code'] ?? '')) ?>)'>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="manager-table-footer">
            <button class="manager-page-btn">Previous</button>
            <button class="manager-page-btn manager-page-btn-active">1</button>
            <button class="manager-page-btn">2</button>
            <button class="manager-page-btn">Next</button>
        </div>
    </div>
</div>

<template id="add-province-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Add Province</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddProvinceModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('address/province') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <label class="manager-modal-field">
                <span>Region</span>
                <select name="region_id" required>
                    <option value="">Select Region</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= (int)$region['id'] ?>" data-region-code="<?= htmlspecialchars($region['region_code'] ?? '') ?>" <?= $selectedRegionId === (int)$region['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($region['region_name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="manager-modal-field">
                <span data-province-field-label>Province Name</span>
                <select name="province_name" data-province-api-select required>
                    <option value="">Select Region First</option>
                </select>
                <input type="hidden" name="province_code" data-province-code-input>
            </label>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddProvinceModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="edit-province-modal-template">
    <div class="manager-modal manager-modal-compact">
        <div class="manager-modal-header">
            <h3>Edit Province</h3>
            <button type="button" class="manager-modal-close" onclick="closeEditProvinceModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('address/province/__ID__/update') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <label class="manager-modal-field">
                <span>Region</span>
                <select name="region_id" data-edit-province-region required>
                    <option value="">Select Region</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= (int)$region['id'] ?>">
                            <?= htmlspecialchars($region['region_name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="manager-modal-field">
                <span>Province Name</span>
                <input type="text" name="province_name" data-edit-province-name required>
            </label>
            <label class="manager-modal-field">
                <span>Province Code</span>
                <input type="text" name="province_code" data-edit-province-code>
            </label>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closeEditProvinceModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    const provincePsgcBaseUrl = 'https://psgc.gitlab.io/api';
    const provinceRegionCodeMap = {
        'NCLZ': ['140000000', '010000000', '020000000', '030000000'],
        'NORTH CENTRAL LUZON': ['140000000', '010000000', '020000000', '030000000'],
        'SLB': ['040000000', '170000000', '050000000'],
        'SOUTH LUZON BICOL': ['040000000', '170000000', '050000000'],
        'VIZMIN': ['060000000', '070000000', '080000000', '090000000', '100000000', '110000000', '120000000', '160000000', '150000000'],
        'VISAYAS MINDANAO': ['060000000', '070000000', '080000000', '090000000', '100000000', '110000000', '120000000', '160000000', '150000000'],
        'REGION I': '010000000',
        'REGION II': '020000000',
        'REGION III': '030000000',
        'REGION IV-A': '040000000',
        'REGION V': '050000000',
        'REGION VI': '060000000',
        'REGION VII': '070000000',
        'REGION VIII': '080000000',
        'REGION IX': '090000000',
        'REGION X': '100000000',
        'REGION XI': '110000000',
        'REGION XII': '120000000',
        'NCR': '130000000',
        'NATIONAL CAPITAL REGION': '130000000',
        'CAR': '140000000',
        'CORDILLERA ADMINISTRATIVE REGION': '140000000',
        'ILOCOS REGION': '010000000',
        'CAGAYAN VALLEY': '020000000',
        'CENTRAL LUZON': '030000000',
        'CALABARZON': '040000000',
        'BICOL REGION': '050000000',
        'WESTERN VISAYAS': '060000000',
        'CENTRAL VISAYAS': '070000000',
        'EASTERN VISAYAS': '080000000',
        'ZAMBOANGA PENINSULA': '090000000',
        'NORTHERN MINDANAO': '100000000',
        'DAVAO REGION': '110000000',
        'SOCCSKSARGEN': '120000000',
        'BARMM': '150000000',
        'BANGSAMORO AUTONOMOUS REGION IN MUSLIM MINDANAO': '150000000',
        'REGION XIII': '160000000',
        'CARAGA': '160000000',
        'MIMAROPA': '170000000',
        'MIMAROPA REGION': '170000000'
    };

    function getProvinceRegionCodes(code) {
        const normalized = String(code || '').trim().toUpperCase();
        const mapped = provinceRegionCodeMap[normalized] || normalized;
        return Array.isArray(mapped) ? mapped : [mapped];
    }

    function isNcrRegion(regionSelect) {
        const option = regionSelect ? regionSelect.selectedOptions[0] : null;
        const value = String(option ? (option.dataset.regionCode || option.textContent) : '').trim().toUpperCase();
        return value === 'NCR' || value === '130000000' || value === 'NATIONAL CAPITAL REGION';
    }

    function updateProvinceFieldLabel(regionSelect, label) {
        if (!label) return;
        label.textContent = isNcrRegion(regionSelect) ? 'City' : 'Province Name';
    }

    async function loadProvinceOptions(regionSelect, provinceSelect, codeInput) {
        if (!regionSelect || !provinceSelect) return;
        updateProvinceFieldLabel(regionSelect, provinceSelect.closest('label')?.querySelector('[data-province-field-label]'));
        const option = regionSelect.selectedOptions[0];
        const regionCodes = getProvinceRegionCodes(option ? (option.dataset.regionCode || option.textContent) : '').filter(Boolean);
        provinceSelect.innerHTML = '<option value="">Loading provinces...</option>';
        if (codeInput) codeInput.value = '';

        if (!regionCodes.length) {
            provinceSelect.innerHTML = '<option value="">Select Region First</option>';
            return;
        }

        try {
            const results = await Promise.all(regionCodes.map(async (regionCode) => {
                const response = await fetch(`${provincePsgcBaseUrl}/regions/${regionCode}/provinces/`);
                return response.ok ? await response.json() : [];
            }));
            const provinces = results.flat().sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));
            provinceSelect.innerHTML = '<option value="">Select Province</option>';

            if (!provinces.length) {
                const directRegionCode = regionCodes[0] || '';
                const cityResponse = await fetch(`${provincePsgcBaseUrl}/regions/${directRegionCode}/cities-municipalities/`);
                const cities = cityResponse.ok ? await cityResponse.json() : [];

                cities
                    .sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')))
                    .forEach((city) => {
                        const provinceOption = document.createElement('option');
                        provinceOption.value = String(city.name || '').toUpperCase();
                        provinceOption.textContent = city.name || '';
                        provinceOption.dataset.code = city.code || '';
                        provinceSelect.appendChild(provinceOption);
                    });

                if (!cities.length) {
                    provinceSelect.innerHTML = '<option value="">No province or city found for this region</option>';
                }
                return;
            }

            provinces.forEach((province) => {
                const provinceOption = document.createElement('option');
                provinceOption.value = String(province.name || '').toUpperCase();
                provinceOption.textContent = province.name || '';
                provinceOption.dataset.code = province.code || '';
                provinceSelect.appendChild(provinceOption);
            });
        } catch (error) {
            provinceSelect.innerHTML = '<option value="">Unable to load provinces</option>';
        }
    }

    function openAddProvinceModal() {
        const template = document.getElementById('add-province-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
        const regionSelect = content ? content.querySelector('select[name="region_id"]') : null;
        const provinceSelect = content ? content.querySelector('[data-province-api-select]') : null;
        const codeInput = content ? content.querySelector('[data-province-code-input]') : null;
        const fieldLabel = content ? content.querySelector('[data-province-field-label]') : null;
        updateProvinceFieldLabel(regionSelect, fieldLabel);
        loadProvinceOptions(regionSelect, provinceSelect, codeInput);
        if (regionSelect) {
            regionSelect.addEventListener('change', () => {
                updateProvinceFieldLabel(regionSelect, fieldLabel);
                loadProvinceOptions(regionSelect, provinceSelect, codeInput);
            });
        }
        if (provinceSelect && codeInput) {
            provinceSelect.addEventListener('change', () => {
                codeInput.value = provinceSelect.selectedOptions[0]?.dataset.code || '';
            });
        }
    }

    function closeAddProvinceModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openEditProvinceModal(id, regionId, province, code) {
        const template = document.getElementById('edit-province-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML.replace('__ID__', String(id)));
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
        const regionSelect = content ? content.querySelector('[data-edit-province-region]') : null;
        const provinceInput = content ? content.querySelector('[data-edit-province-name]') : null;
        const codeInput = content ? content.querySelector('[data-edit-province-code]') : null;
        if (regionSelect) regionSelect.value = String(regionId);
        if (provinceInput) provinceInput.value = province || '';
        if (codeInput) codeInput.value = code || '';
    }

    function closeEditProvinceModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }
</script>
