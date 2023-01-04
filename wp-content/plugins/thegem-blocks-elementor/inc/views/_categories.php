<?php if (!empty($this->getCategories())): ?>
    <ul class="thegem-blocks-categories-list">
        <li>
            <a data-name="all" href="#">
                <i class="tgb-icon-presentation"></i><?= __('All', 'thegem'); ?>
                <span><?= esc_html($this->getCountTotalTemplates()) ?></span>
            </a>
        </li>
        <li>
            <a data-name="favorite" href="#">
                <i class="tgb-icon-star-outline"></i><?= __('My Favorites', 'thegem'); ?>
                <span class="favorite-cnt"><?= esc_html(count($this->getFavorites())); ?></span>
            </a>
        </li>
        <?php foreach ($this->getCategories() as $category): ?>
            <?php if ($category['name'] == 'custom-title' && !$this->isCustomPostTitle) continue; ?>
            <li>
                <a href="#"
                   data-name="<?= esc_html($category['name']); ?>"
                   data-count-dark="<?= esc_html($category['count_dark']) ?>"
                   data-count-multicolor="<?= esc_html($category['count_multicolor']) ?>"
                >
                    <?= esc_html($category['title']); ?>
                    <span><?= esc_html($category[$this->isDarkMode() ? 'count_dark' : 'count_multicolor']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>