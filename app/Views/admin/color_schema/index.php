<div class="top-action-bar">
    <div>
        <h1 class="page-title">Color Schema & Theme Configuration</h1>
        <div class="page-subtitle">Configure CSS custom properties (:root variables) for live frontend branding, colors, gradients, and typography.</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="submit" form="colorSchemaForm" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save All Theme Properties
        </button>
    </div>
</div>

<form id="colorSchemaForm" action="<?= base_url('admin/color-schema/update') ?>" method="POST">
    <?= csrf_field() ?>

    <?php
    $categoryMeta = [
        'semantic' => [
            'title' => 'Semantic Theme Tokens',
            'desc'  => 'Key application surface tokens, card gradients, text colors, and CTA highlights mapped across the frontend.',
            'open'  => true,
        ],
        'primary-1' => [
            'title' => 'Primary 1 Palette (Deep Teal / Brand Dark)',
            'desc'  => '10-step primary brand scale from lightest tint (--primary-1-1) to deepest background tone (--primary-1-10).',
            'open'  => false,
        ],
        'primary-2' => [
            'title' => 'Primary 2 Palette (Accent Blue)',
            'desc'  => '10-step accent blue scale used for buttons, interactive highlights, glows, and badges.',
            'open'  => false,
        ],
        'neutral' => [
            'title' => 'Neutral Palette (Whites, Grays & Blacks)',
            'desc'  => '13-step neutral gray scale from pure white (--neutral-1) to solid black (--neutral-13).',
            'open'  => false,
        ],
        'fonts' => [
            'title' => 'Typography & Fonts',
            'desc'  => 'Font family stacks used for titles, headings, serif accents, and body texts.',
            'open'  => false,
        ],
        'spacing' => [
            'title' => 'Layout & Dimensions',
            'desc'  => 'Container max width, responsive desktop padding, and border radius variables.',
            'open'  => false,
        ],
    ];
    ?>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php foreach ($categoryMeta as $catKey => $catInfo): ?>
            <?php $props = $groupedProperties[$catKey] ?? []; ?>
            <?php if (empty($props)) continue; ?>

            <div class="cms-card" style="margin-bottom: 0;">
                <div class="card-title-bar" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleCat('cat_<?= esc($catKey) ?>')">
                    <div>
                        <span class="card-title-text" style="font-size: 15px; font-weight: 700; color: var(--text-strong);"><?= esc($catInfo['title']) ?></span>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;"><?= esc($catInfo['desc']) ?></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="badge" style="background: var(--bg-subtle); color: var(--primary-1-7); font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px;">
                            <?= count($props) ?> variables
                        </span>
                        <svg id="icon_cat_<?= esc($catKey) ?>" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="transition: transform 0.2s ease; transform: <?= $catInfo['open'] ? 'rotate(180deg)' : 'rotate(0deg)' ?>;">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </div>

                <div id="cat_<?= esc($catKey) ?>" class="cms-card-body" style="display: <?= $catInfo['open'] ? 'block' : 'none' ?>; padding: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px;">
                        <?php foreach ($props as $p): ?>
                            <?php
                                $val = $p['css_value'];
                                $valType = $p['value_type'];
                                $isHex = (bool)preg_match('/^#[0-9a-fA-F]{3,8}$/', $val);
                                $isRgba = (bool)preg_match('/^rgba?\(/i', $val);
                                $isGradient = (bool)preg_match('/gradient/i', $val);
                                $pId = (int)$p['id'];
                            ?>
                            <div style="background: var(--bg-page); border: 1px solid var(--line); border-radius: var(--radius-control); padding: 14px 16px; display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                    <span style="font-weight: 700; font-size: 13.5px; color: var(--text-strong);">
                                        <?= esc($p['label'] ?: $p['css_property']) ?>
                                    </span>
                                    <code style="font-size: 11.5px; color: var(--primary-1-6); background: rgba(11,81,108,0.08); padding: 2px 6px; border-radius: 4px;">
                                        <?= esc($p['css_property']) ?>
                                    </code>
                                </div>

                                <?php if ($valType === 'solid' || $valType === 'gradient'): ?>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <!-- Color Picker / Swatch Preview -->
                                        <?php if ($isHex): ?>
                                            <input type="color"
                                                   class="color-picker-input"
                                                   value="<?= esc($val) ?>"
                                                   oninput="document.getElementById('input_val_<?= $pId ?>').value = this.value; document.getElementById('preview_<?= $pId ?>').style.background = this.value;"
                                                   style="width: 38px; height: 38px; padding: 2px; border: 1px solid var(--line); border-radius: 6px; cursor: pointer; flex-shrink: 0;">
                                        <?php else: ?>
                                            <div id="preview_<?= $pId ?>"
                                                 style="width: 38px; height: 38px; border-radius: 6px; border: 1px solid var(--line); background: <?= esc($val) ?>; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);">
                                            </div>
                                        <?php endif; ?>

                                        <input type="text"
                                               id="input_val_<?= $pId ?>"
                                               name="properties[<?= $pId ?>][value]"
                                               class="form-control"
                                               value="<?= esc($val) ?>"
                                               style="font-family: monospace; font-size: 13px;"
                                               oninput="const pv = document.getElementById('preview_<?= $pId ?>'); if(pv) pv.style.background = this.value;">

                                        <select name="properties[<?= $pId ?>][type]" class="form-control" style="width: 105px; flex-shrink: 0; font-size: 12px;">
                                            <option value="solid" <?= $valType === 'solid' ? 'selected' : '' ?>>Solid</option>
                                            <option value="gradient" <?= $valType === 'gradient' ? 'selected' : '' ?>>Gradient</option>
                                        </select>
                                    </div>
                                <?php else: ?>
                                    <!-- Text / Dimension Property (e.g. font-family, container width) -->
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <input type="text"
                                               name="properties[<?= $pId ?>][value]"
                                               class="form-control"
                                               value="<?= esc($val) ?>"
                                               style="font-family: monospace; font-size: 13px;">
                                        <input type="hidden" name="properties[<?= $pId ?>][type]" value="<?= esc($valType) ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top: 24px; padding: 18px 24px; background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-card); display: flex; justify-content: flex-end; gap: 12px;">
        <button type="submit" class="btn btn-primary">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Color Schema
        </button>
    </div>
</form>

<script>
function toggleCat(catId) {
    const el = document.getElementById(catId);
    const icon = document.getElementById('icon_' + catId);
    if (!el) return;
    if (el.style.display === 'none') {
        el.style.display = 'block';
        if (icon) icon.style.transform = 'rotate(180deg)';
    } else {
        el.style.display = 'none';
        if (icon) icon.style.transform = 'rotate(0deg)';
    }
}
</script>
