<?php
$activeRoute = 'address_municipalities';
$rows = $rows ?? [];
$regions = $regions ?? [];
$provinces = $provinces ?? [];
$allProvinces = $allProvinces ?? [];
$selectedRegionId = (int)($selectedRegionId ?? 0);
$selectedProvinceId = (int)($selectedProvinceId ?? 0);
?>

<div class="space-y-5 manager-page">
    <div>
        <h1 class="text-3xl font-extrabold text-sky-600 tracking-tight uppercase">Municipality</h1>
        <p class="text-lg text-slate-700 mt-1">Location area of every municipality</p>
    </div>

    <div class="manager-table-card">
        <div class="manager-table-head">
            <h2 class="manager-table-title">Manage Municipality</h2>

            <div class="manager-table-controls">
                <form method="GET" action="<?= App\Config\App::url('address/municipalities') ?>" class="manager-table-controls" data-location-filter-form>
                    <select name="region_id" class="manager-control-input" data-region-filter>
                        <option value="">All Regions</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= (int)$region['id'] ?>" data-region-code="<?= htmlspecialchars($region['region_code'] ?? '') ?>" <?= $selectedRegionId === (int)$region['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($region['region_name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="province_id" class="manager-control-input" data-province-filter>
                        <option value="" data-province-filter-placeholder>All Provinces</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?= (int)$province['id'] ?>" <?= $selectedProvinceId === (int)$province['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($province['province'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <input type="text" placeholder="Search..." class="manager-control-input">
                <button type="button" class="manager-control-icon" aria-label="Search">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="manager-add-btn" aria-label="Add Municipality" onclick="openAddMunicipalityModal()">
                    <span class="manager-add-icon">+</span>
                    <span>Add Municipality</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="manager-table-grid" style="min-width: 100%;">
                <thead>
                    <tr>
                        <th>Region</th>
                        <th>Province</th>
                        <th>Municipality</th>
                        <th style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="manager-name-cell"><?= htmlspecialchars($row['region'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['province'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['municipality'] ?? '-') ?></td>
                            <td>
                                <div class="manager-action-group">
                                    <form method="POST" action="<?= App\Config\App::url('address/municipalities/' . (int)($row['id'] ?? 0) . '/delete') ?>" onsubmit="return confirm('Delete this municipality?');">
                                        <?= \App\Helpers\Csrf::field(); ?>
                                        <input type="hidden" name="region_id" value="<?= $selectedRegionId > 0 ? $selectedRegionId : (int)($row['region_id'] ?? 0) ?>">
                                        <input type="hidden" name="province_id" value="<?= $selectedProvinceId > 0 ? $selectedProvinceId : (int)($row['province_id'] ?? 0) ?>">
                                        <button type="submit" class="manager-action-icon manager-action-delete" aria-label="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </form>
                                    <button type="button" class="manager-action-icon manager-action-edit" aria-label="Edit" onclick='openEditMunicipalityModal(<?= (int)($row['id'] ?? 0) ?>, <?= (int)($row['province_id'] ?? 0) ?>, <?= json_encode((string)($row['municipality'] ?? '')) ?>, <?= json_encode((string)($row['municipality_code'] ?? '')) ?>)'>
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

<template id="add-municipality-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Add Municipality</h3>
            <button type="button" class="manager-modal-close" onclick="closeAddMunicipalityModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('address/municipalities') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Region</span>
                    <select name="region_id" data-modal-region-select required>
                        <option value="">Select Region</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= (int)$region['id'] ?>" data-region-code="<?= htmlspecialchars($region['region_code'] ?? '') ?>" <?= $selectedRegionId === (int)$region['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($region['region_name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="manager-modal-field">
                    <span data-modal-province-label>Province</span>
                    <select name="province_name" data-modal-province-select required>
                        <option value="">Select Region First</option>
                    </select>
                    <input type="hidden" name="province_id" data-modal-province-id-input>
                    <input type="hidden" name="province_code" data-modal-province-code-input>
                </label>
                <label class="manager-modal-field md:col-span-2" data-modal-municipality-field>
                    <span>Municipality</span>
                    <select name="municipality" data-modal-municipality-select required>
                        <option value="">Select Province First</option>
                    </select>
                    <input type="hidden" name="municipality_code" data-modal-municipality-code-input>
                    <input type="hidden" name="municipality" data-modal-ncr-city-input disabled>
                </label>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Add</button>
                <button type="button" class="manager-modal-cancel" onclick="closeAddMunicipalityModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<template id="edit-municipality-modal-template">
    <div class="manager-modal">
        <div class="manager-modal-header">
            <h3>Edit Municipality</h3>
            <button type="button" class="manager-modal-close" onclick="closeEditMunicipalityModal()" aria-label="Close">x</button>
        </div>

        <form class="manager-modal-form" method="POST" action="<?= App\Config\App::url('address/municipalities/__ID__/update') ?>">
            <?= \App\Helpers\Csrf::field(); ?>
            <div class="manager-modal-grid">
                <label class="manager-modal-field">
                    <span>Province</span>
                    <select name="province_id" data-edit-municipality-province required>
                        <option value="">Select Province</option>
                        <?php foreach ($allProvinces as $province): ?>
                            <option value="<?= (int)$province['id'] ?>">
                                <?= htmlspecialchars(($province['region'] ?? '') . ' - ' . ($province['province'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="manager-modal-field">
                    <span>Municipality Name</span>
                    <input type="text" name="municipality" data-edit-municipality-name required>
                </label>
                <label class="manager-modal-field md:col-span-2">
                    <span>Municipality Code</span>
                    <input type="text" name="municipality_code" data-edit-municipality-code>
                </label>
            </div>

            <div class="manager-modal-actions">
                <button type="submit" class="manager-modal-submit">Save</button>
                <button type="button" class="manager-modal-cancel" onclick="closeEditMunicipalityModal()">Cancel</button>
            </div>
        </form>
    </div>
</template>

<script>
    const municipalityPsgcBaseUrl = 'https://psgc.gitlab.io/api';
    const municipalityRegionCodeMap = {
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
    const addressProvinceOptions = <?= json_encode(array_map(static fn(array $province): array => [
        'id' => (int)($province['id'] ?? 0),
        'region_id' => (int)($province['region_id'] ?? 0),
        'province_code' => (string)($province['province_code'] ?? ''),
        'province' => (string)($province['province'] ?? ''),
    ], $allProvinces), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const selectedMunicipalityProvinceId = <?= $selectedProvinceId ?>;

    function getMunicipalityRegionCodes(code) {
        const normalized = String(code || '').trim().toUpperCase();
        const mapped = municipalityRegionCodeMap[normalized] || normalized;
        return Array.isArray(mapped) ? mapped : [mapped];
    }

    function isMunicipalityNcrRegion(regionSelect) {
        const option = regionSelect ? regionSelect.selectedOptions[0] : null;
        const value = String(option ? (option.dataset.regionCode || option.textContent) : '').trim().toUpperCase();
        return value === 'NCR' || value === '130000000' || value === 'NATIONAL CAPITAL REGION';
    }

    function updateMunicipalityProvinceLabel(regionSelect, label) {
        if (!label) return;
        label.textContent = isMunicipalityNcrRegion(regionSelect) ? 'City' : 'Province';
    }

    function updateProvinceFilterPlaceholder(regionSelect, provinceFilter) {
        const placeholder = provinceFilter ? provinceFilter.querySelector('[data-province-filter-placeholder]') : null;
        if (!placeholder) return;
        placeholder.textContent = isMunicipalityNcrRegion(regionSelect) ? 'All Cities' : 'All Provinces';
    }

    function findLocalProvince(regionId, provinceCode, provinceName) {
        const normalizedName = String(provinceName || '').trim().toUpperCase();
        const normalizedCode = String(provinceCode || '').trim();
        return addressProvinceOptions.find((province) => {
            return province.region_id === regionId
                && ((normalizedCode && province.province_code === normalizedCode)
                    || String(province.province || '').trim().toUpperCase() === normalizedName);
        });
    }

    async function fillProvinceSelect(select, regionSelect, selectedId = 0) {
        if (!select) return;
        const selectedRegionId = parseInt(regionSelect ? regionSelect.value || '0' : '0', 10);
        const selectedProvinceId = parseInt(selectedId || '0', 10);
        const selectedRegionOption = regionSelect ? regionSelect.selectedOptions[0] : null;
        const regionCodes = getMunicipalityRegionCodes(selectedRegionOption ? (selectedRegionOption.dataset.regionCode || selectedRegionOption.textContent) : '').filter(Boolean);
        const regionName = selectedRegionOption ? selectedRegionOption.textContent.trim() : '';
        select.innerHTML = '<option value="">Loading provinces...</option>';

        if (!selectedRegionId || !regionCodes.length) {
            select.innerHTML = '<option value="">Select Region First</option>';
            return;
        }

        try {
            const results = await Promise.all(regionCodes.map(async (regionCode) => {
                const response = await fetch(`${municipalityPsgcBaseUrl}/regions/${regionCode}/provinces/`);
                return response.ok ? await response.json() : [];
            }));
            const provinces = results.flat().sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));
            select.innerHTML = '<option value="">Select Province</option>';

            if (!provinces.length) {
                const directRegionCode = regionCodes[0] || '';
                const cityResponse = await fetch(`${municipalityPsgcBaseUrl}/regions/${directRegionCode}/cities-municipalities/`);
                const cities = cityResponse.ok ? await cityResponse.json() : [];

                cities
                    .sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')))
                    .forEach((city) => {
                        const localProvince = findLocalProvince(selectedRegionId, city.code || '', city.name || '');
                        const option = document.createElement('option');
                        option.value = String(city.name || '').toUpperCase();
                        option.textContent = city.name || '';
                        option.dataset.code = city.code || '';
                        option.dataset.directRegion = '1';
                        option.dataset.localId = String(localProvince?.id || '');
                        option.selected = selectedProvinceId > 0 && localProvince?.id === selectedProvinceId;
                        select.appendChild(option);
                    });

                if (!cities.length) {
                    select.innerHTML = '<option value="">No province or city found for this region</option>';
                }
                return;
            }

            provinces.forEach((province) => {
                const localProvince = findLocalProvince(selectedRegionId, province.code || '', province.name || '');
                const option = document.createElement('option');
                option.value = String(province.name || '').toUpperCase();
                option.textContent = province.name || '';
                option.dataset.code = province.code || '';
                option.dataset.localId = String(localProvince?.id || '');
                option.selected = selectedProvinceId > 0 && localProvince?.id === selectedProvinceId;
                select.appendChild(option);
            });
        } catch (error) {
            select.innerHTML = '<option value="">Unable to load provinces</option>';
        }
    }

    async function fillMunicipalitySelect(select, provinceSelect, regionSelect) {
        if (!select || !provinceSelect) return;
        const selectedProvinceOption = provinceSelect.selectedOptions[0];
        const selectedRegionOption = regionSelect ? regionSelect.selectedOptions[0] : null;
        const provinceCode = selectedProvinceOption ? selectedProvinceOption.dataset.code : '';
        const regionCodes = getMunicipalityRegionCodes(selectedRegionOption ? (selectedRegionOption.dataset.regionCode || selectedRegionOption.textContent) : '').filter(Boolean);
        const regionCode = regionCodes[0] || '';
        const directRegion = selectedProvinceOption ? selectedProvinceOption.dataset.directRegion === '1' : false;
        const endpoint = directRegion
            ? `${municipalityPsgcBaseUrl}/regions/${regionCode}/cities-municipalities/`
            : `${municipalityPsgcBaseUrl}/provinces/${provinceCode}/cities-municipalities/`;

        select.innerHTML = '<option value="">Loading municipalities...</option>';
        if (!provinceCode) {
            select.innerHTML = '<option value="">Select Province First</option>';
            return;
        }

        try {
            const response = await fetch(endpoint);
            const municipalities = response.ok ? await response.json() : [];
            select.innerHTML = '<option value="">Select Municipality</option>';
            municipalities.forEach((municipality) => {
                const option = document.createElement('option');
                option.value = String(municipality.name || '').toUpperCase();
                option.textContent = municipality.name || '';
                option.dataset.code = municipality.code || '';
                select.appendChild(option);
            });
            if (!municipalities.length) {
                select.innerHTML = '<option value="">No municipalities found</option>';
            }
        } catch (error) {
            select.innerHTML = '<option value="">Unable to load municipalities</option>';
        }
    }

    function syncProvinceHiddenFields(provinceSelect, provinceIdInput, provinceCodeInput) {
        const option = provinceSelect ? provinceSelect.selectedOptions[0] : null;
        if (provinceIdInput) provinceIdInput.value = option?.dataset.localId || '';
        if (provinceCodeInput) provinceCodeInput.value = option?.dataset.code || '';
    }

    function syncNcrCityValue(provinceSelect, municipalitySelect, municipalityCodeInput, ncrCityInput) {
        const option = provinceSelect ? provinceSelect.selectedOptions[0] : null;
        if (ncrCityInput) ncrCityInput.value = option ? option.value : '';
        if (municipalityCodeInput) municipalityCodeInput.value = option?.dataset.code || '';
        if (municipalitySelect) municipalitySelect.value = '';
    }

    function updateMunicipalityFieldVisibility(regionSelect, field, municipalitySelect, municipalityCodeInput, ncrCityInput, provinceSelect) {
        const isNcr = isMunicipalityNcrRegion(regionSelect);
        if (field) field.style.display = isNcr ? 'none' : '';
        if (municipalitySelect) {
            municipalitySelect.disabled = isNcr;
            municipalitySelect.required = !isNcr;
        }
        if (ncrCityInput) {
            ncrCityInput.disabled = !isNcr;
            ncrCityInput.required = isNcr;
        }
        if (isNcr) {
            syncNcrCityValue(provinceSelect, municipalitySelect, municipalityCodeInput, ncrCityInput);
        } else if (ncrCityInput) {
            ncrCityInput.value = '';
        }
    }

    (function initMunicipalityFilters() {
        const form = document.querySelector('[data-location-filter-form]');
        const regionFilter = document.querySelector('[data-region-filter]');
        const provinceFilter = document.querySelector('[data-province-filter]');
        if (!form || !regionFilter || !provinceFilter) return;

        updateProvinceFilterPlaceholder(regionFilter, provinceFilter);
        regionFilter.addEventListener('change', () => {
            provinceFilter.value = '';
            updateProvinceFilterPlaceholder(regionFilter, provinceFilter);
            form.submit();
        });
        provinceFilter.addEventListener('change', () => form.submit());
    })();

    function openAddMunicipalityModal() {
        const template = document.getElementById('add-municipality-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML);
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
        const regionSelect = content ? content.querySelector('[data-modal-region-select]') : null;
        const provinceSelect = content ? content.querySelector('[data-modal-province-select]') : null;
        const municipalitySelect = content ? content.querySelector('[data-modal-municipality-select]') : null;
        const provinceIdInput = content ? content.querySelector('[data-modal-province-id-input]') : null;
        const provinceCodeInput = content ? content.querySelector('[data-modal-province-code-input]') : null;
        const municipalityCodeInput = content ? content.querySelector('[data-modal-municipality-code-input]') : null;
        const provinceLabel = content ? content.querySelector('[data-modal-province-label]') : null;
        const municipalityField = content ? content.querySelector('[data-modal-municipality-field]') : null;
        const ncrCityInput = content ? content.querySelector('[data-modal-ncr-city-input]') : null;

        updateMunicipalityProvinceLabel(regionSelect, provinceLabel);
        updateMunicipalityFieldVisibility(regionSelect, municipalityField, municipalitySelect, municipalityCodeInput, ncrCityInput, provinceSelect);
        fillProvinceSelect(provinceSelect, regionSelect, selectedMunicipalityProvinceId).then(() => {
            syncProvinceHiddenFields(provinceSelect, provinceIdInput, provinceCodeInput);
            updateMunicipalityFieldVisibility(regionSelect, municipalityField, municipalitySelect, municipalityCodeInput, ncrCityInput, provinceSelect);
            if (!isMunicipalityNcrRegion(regionSelect)) {
                fillMunicipalitySelect(municipalitySelect, provinceSelect, regionSelect);
            }
        });
        if (regionSelect) {
            regionSelect.addEventListener('change', () => {
                updateMunicipalityProvinceLabel(regionSelect, provinceLabel);
                if (municipalitySelect) municipalitySelect.innerHTML = '<option value="">Select Province First</option>';
                if (municipalityCodeInput) municipalityCodeInput.value = '';
                updateMunicipalityFieldVisibility(regionSelect, municipalityField, municipalitySelect, municipalityCodeInput, ncrCityInput, provinceSelect);
                fillProvinceSelect(provinceSelect, regionSelect, 0).then(() => {
                    syncProvinceHiddenFields(provinceSelect, provinceIdInput, provinceCodeInput);
                    updateMunicipalityFieldVisibility(regionSelect, municipalityField, municipalitySelect, municipalityCodeInput, ncrCityInput, provinceSelect);
                });
            });
        }
        if (provinceSelect) {
            provinceSelect.addEventListener('change', () => {
                syncProvinceHiddenFields(provinceSelect, provinceIdInput, provinceCodeInput);
                if (municipalityCodeInput) municipalityCodeInput.value = '';
                updateMunicipalityFieldVisibility(regionSelect, municipalityField, municipalitySelect, municipalityCodeInput, ncrCityInput, provinceSelect);
                if (!isMunicipalityNcrRegion(regionSelect)) {
                    fillMunicipalitySelect(municipalitySelect, provinceSelect, regionSelect);
                }
            });
        }
        if (municipalitySelect && municipalityCodeInput) {
            municipalitySelect.addEventListener('change', () => {
                municipalityCodeInput.value = municipalitySelect.selectedOptions[0]?.dataset.code || '';
            });
        }
    }

    function closeAddMunicipalityModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }

    function openEditMunicipalityModal(id, provinceId, municipality, code) {
        const template = document.getElementById('edit-municipality-modal-template');
        if (!template || typeof openModal !== 'function') return;
        openModal(template.innerHTML.replace('__ID__', String(id)));
        const content = document.getElementById('modal-content');
        if (content) content.classList.add('manager-modal-shell');
        const provinceSelect = content ? content.querySelector('[data-edit-municipality-province]') : null;
        const municipalityInput = content ? content.querySelector('[data-edit-municipality-name]') : null;
        const codeInput = content ? content.querySelector('[data-edit-municipality-code]') : null;
        if (provinceSelect) provinceSelect.value = String(provinceId);
        if (municipalityInput) municipalityInput.value = municipality || '';
        if (codeInput) codeInput.value = code || '';
    }

    function closeEditMunicipalityModal() {
        const content = document.getElementById('modal-content');
        if (content) content.classList.remove('manager-modal-shell');
        if (typeof closeModal === 'function') closeModal();
    }
</script>
