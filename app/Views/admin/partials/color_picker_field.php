<?php
/**
 * Dual-Mode Color Picker Field Partial
 *
 * Variables required:
 * - $fieldName: string (e.g. 'colors[--navbar-cta-bg]')
 * - $fieldId: string (e.g. 'color_navbar_cta_bg')
 * - $currentValue: string (hex or color string, e.g. '#ffffff')
 * - $label: string (e.g. 'CTA Button Background')
 * - $palette: array of ['label' => ..., 'token' => ..., 'value' => ...]
 * - $cssProp: string (e.g. '--navbar-cta-bg')
 */
$currentValue = $currentValue ?? '#ffffff';
$isHex = (bool)preg_match('/^#[0-9a-fA-F]{3,8}$/', $currentValue);
$pickerHex = $isHex ? $currentValue : '#ffffff';
?>
<div class="color-picker-control-group" id="group_<?= esc($fieldId) ?>" style="background: var(--bg-surface, #fff); border: 1px solid var(--line, #e2e8f0); border-radius: 8px; padding: 12px 14px; margin-bottom: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px;">
        <label for="<?= esc($fieldId) ?>" style="font-size: 13px; font-weight: 600; color: var(--text-strong, #0f172a); margin-bottom: 0;">
            <?= esc($label) ?>
        </label>
        <?php if (!empty($cssProp)): ?>
            <code style="font-size: 11px; color: var(--primary-1-6, #0b516c); background: rgba(11,81,108,0.08); padding: 1px 6px; border-radius: 4px;">
                <?= esc($cssProp) ?>
            </code>
        <?php endif; ?>
    </div>

    <!-- Mode Selector Tabs -->
    <div style="display: flex; gap: 4px; margin-bottom: 10px; background: var(--bg-page, #f8fafc); padding: 3px; border-radius: 6px; width: fit-content;">
        <button type="button" 
                id="btn_mode_scheme_<?= esc($fieldId) ?>" 
                onclick="switchColorMode('<?= esc($fieldId) ?>', 'scheme')"
                class="btn-mode-tab active"
                style="padding: 3px 10px; font-size: 11.5px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; background: var(--primary-1-6, #0b516c); color: #ffffff; transition: all 0.2s ease;">
            Theme Scheme
        </button>
        <button type="button" 
                id="btn_mode_custom_<?= esc($fieldId) ?>" 
                onclick="switchColorMode('<?= esc($fieldId) ?>', 'custom')"
                class="btn-mode-tab"
                style="padding: 3px 10px; font-size: 11.5px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; background: transparent; color: var(--text-muted, #64748b); transition: all 0.2s ease;">
            Custom Color
        </button>
    </div>

    <!-- Mode 1: Theme Scheme Dropdown + Swatch -->
    <div id="panel_scheme_<?= esc($fieldId) ?>" style="display: flex; gap: 8px; align-items: center;">
        <div id="swatch_preview_<?= esc($fieldId) ?>" 
             style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid var(--line, #cbd5e1); background: <?= esc($currentValue) ?>; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.06);">
        </div>
        <select class="form-control" 
                style="font-size: 12.5px;"
                onchange="applySchemeColor('<?= esc($fieldId) ?>', this.value)">
            <option value="">-- Choose from Theme Palette --</option>
            <?php foreach ($palette as $item): ?>
                <option value="<?= esc($item['value']) ?>" <?= (strtolower(trim($currentValue)) === strtolower(trim($item['value']))) ? 'selected' : '' ?>>
                    <?= esc($item['label']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Mode 2: Custom Color Input + Picker -->
    <div id="panel_custom_<?= esc($fieldId) ?>" style="display: none; gap: 8px; align-items: center;">
        <input type="color" 
               id="picker_<?= esc($fieldId) ?>" 
               value="<?= esc($pickerHex) ?>" 
               style="width: 38px; height: 38px; padding: 2px; border: 1px solid var(--line, #cbd5e1); border-radius: 6px; cursor: pointer; flex-shrink: 0;"
               oninput="syncFromPicker('<?= esc($fieldId) ?>', this.value)">
        <input type="text" 
               id="hex_<?= esc($fieldId) ?>" 
               value="<?= esc($currentValue) ?>" 
               placeholder="#ffffff"
               class="form-control"
               style="font-family: monospace; font-size: 13px;"
               oninput="syncFromHex('<?= esc($fieldId) ?>', this.value)">
    </div>

    <!-- Hidden final input submitted with form -->
    <input type="hidden" 
           id="<?= esc($fieldId) ?>" 
           name="<?= esc($fieldName) ?>" 
           value="<?= esc($currentValue) ?>"
           data-prop="<?= esc($cssProp ?? '') ?>">
</div>
