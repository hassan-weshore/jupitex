<div id="thegem-blocks-wrapper">
    <div id="thegem-blocks" class="thegem-blocks-box">
        <div class="thegem-blocks-control-mode">
            <div class="thegem-blocks-control-mode-logo">
                Blocks
            </div>
            <div>
                <div class="thegem-blocks-templates-filter">
                    <input type="text" id="thegem-blocks-templates-filter" placeholder="<?=esc_html(__("Search by name","thegem"))?>">
                    <i class="fa fa-search"></i>
                </div>
            </div>

            <div>
                <label class="thegem-blocks-switch thegem-blocks-switch-mode">
                    <?= __('Multicolor', 'thegem') ?>
                    <input name="mode" <?= ($this->isDarkMode() ? 'checked' : '') ?> id="thegem-blocks-mode-input" type="checkbox"/><span class="thegem-blocks-switch-indicator"></span>
                    <?= __('Dark', 'thegem') ?>
                </label>
            </div>

            <div>
                <button id="thegem-blocks-close"></button>
            </div>
        </div>

        <div class="thegem-blocks-left-box">
            <div class="thegem-blocks-include-media">
                <label for="thegem-blocks-include-media-input">
                    <input name="include_media" <?= ($this->isIncludeMedia() ? 'checked' : '') ?> type="checkbox" id="thegem-blocks-include-media-input"><?= __('Include Media', 'thegem'); ?>
                </label>
            </div>

            <?php include '_categories.php'; ?>
        </div>

        <div class="thegem-blocks-right-box">
            <?php if (!empty($this->getTemplates())): ?>
                <div class="thegem-blocks-template-items">
                    <?php foreach (array_slice($this->getTemplates(), 0, TheGemBlocks::ITEMS_PER_PAGE) as $template) {
                        include '_item.php';
                    } ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>