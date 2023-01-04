<div class="thegem-blocks-template-item<?= ($this->isFavorite($template['id']) ? ' is-favorite' : ''); ?>"
     data-category="<?= esc_html(implode(',', $template['category'])); ?>"
     data-id="<?= esc_html($template['id']); ?>"
     data-unique-id="<?= esc_html($template['unique_id']); ?>"
     data-name="<?= esc_html($template['title']) ?>"
>
    <div class="thegem-blocks-template-item-inner">
        <div class="thegem-blocks-template-item-image">
            <img src="<?= esc_html($this->getPreviewImage($template['image'])) ; ?>" alt="<?= esc_html($template['title']); ?>">
            <div class="thegem-blocks-template-item-actions">
                <a class="thegem-blocks-template-item-add" href="#"><i class="tgb-icon-arrow-down"></i><?= __('Insert', 'thegem')?></a>
                <a class="thegem-blocks-template-item-preview" href="<?= esc_url($template['link']); ?>" target="_blank"><i class="tgb-icon-search"></i><?= __('Preview', 'thegem')?></a>
                <a class="thegem-blocks-template-item-favorite" href="#"><i class="tgb-icon-star-outline"></i></a>
            </div>
        </div>
        <div class="thegem-blocks-template-item-head">
            <div class="thegem-blocks-template-item-title"><?= esc_html($template['title']); ?></div>
            <?= ($template['type'] == TheGemBlocks::TEMPLATE_TYPE_NEW ? '<span>'.__('New', 'thegem').'</span>' : '') ?>
        </div>
    </div>
</div>