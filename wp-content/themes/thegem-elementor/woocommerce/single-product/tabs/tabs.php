<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see	 https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $thegem_product_data;

$isSidebar = false;
$sidebar_data = thegem_get_output_page_settings(get_the_ID());
if(is_active_sidebar('shop-sidebar') && $sidebar_data['sidebar_show']) {
	$isSidebar = true;
}
$page_layout = $thegem_product_data['product_page_layout'];
$desc_review_layout = $thegem_product_data['product_page_desc_review_layout'];
$desc_review_style = $thegem_product_data['product_page_desc_review_layout_tabs_style'];
$isFullWidth = $thegem_product_data['product_page_layout_fullwidth'];
$isVerticalTabs = $thegem_product_data['product_page_desc_review_layout_tabs_style'] == 'vertical';

switch ($desc_review_layout) {
	case 'tabs':
		$tabs_class = 'thegem-tabs--'.$thegem_product_data['product_page_desc_review_layout_tabs_style'];
		$tabs_full_class = 'thegem-tabs--fullwidth';
		$tabs_position_horizontal_class = !$isFullWidth ? 'thegem-tabs__nav--'.$thegem_product_data['product_page_desc_review_layout_tabs_alignment'] : 'thegem-tabs__nav--center';
		$tabs_position_vertical_class = 'thegem-tabs__nav--'.$thegem_product_data['product_page_desc_review_layout_tabs_alignment'];
		$tabsPosition = $thegem_product_data['product_page_desc_review_layout_tabs_style'] == 'horizontal' ? $tabs_position_horizontal_class : $tabs_position_vertical_class;
		break;
	case 'accordion':
		$acc_class = 'thegem-accordion--'.$thegem_product_data['product_page_desc_review_layout_acc_position'];
		$acc_full_class = 'thegem-accordion--fullwidth';
		$acc_position = $thegem_product_data['product_page_desc_review_layout_acc_position'];
		break;
	case 'one_by_one':
		$desc_review_back['description'] = $thegem_product_data['product_page_desc_review_layout_one_by_one_description_background'];
		$desc_review_back['additional_information'] = $thegem_product_data['product_page_desc_review_layout_one_by_one_additional_info_background'];
		$desc_review_back['reviews'] = $thegem_product_data['product_page_desc_review_layout_one_by_one_reviews_background'];
		$isColorBack = $desc_review_back['description'] != '' || $desc_review_back['additional_information'] != '' || $desc_review_back['reviews'] != '';
		break;
}


/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if (!empty($product_tabs)): ?>
	<div class="gem-tta-container woocommerce-tabs wc-tabs-wrapper gem-woocommerce-tabs" data-vc-action="collapse">
		<?php if ((!empty($product_tabs) && $page_layout == 'legacy') || (!empty($product_tabs) && $page_layout != 'legacy' && $desc_review_layout == 'tabs' && $desc_review_style == 'legacy')): wp_enqueue_script( 'thegem_tabs_script' ); wp_enqueue_style( 'gem-tta_style' ); wp_enqueue_script( 'thegem-tabs' ); wp_enqueue_style( 'thegem-tabs' ); ?>
			<div class="gem-tta-general gem-tta gem-tta-tabs gem-tta-color-thegem gem-tta-style-classic gem-tta-shape-square gem-tta-spacing-5 gem-tta-tabs-position-top gem-tta-controls-align-left">
				<div class="gem-tta-tabs-container">
					<ul class="gem-tta-tabs-list">
					<?php $is_first = true; foreach ( $product_tabs as $key => $product_tab ) : ?>
						<li class="gem-tta-tab<?php if($is_first) { echo ' gem-tta-active'; $is_first= false; } ?>" data-vc-tab>
							<a href="#tab-<?php echo esc_attr( $key ); ?>" data-vc-tabs data-vc-container=".gem-tta">
								<span class="gem-tta-title-text"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
				<div class="gem-tta-panels-container">
					<div class="gem-tta-panels">
						<?php $is_first = true; foreach ( $product_tabs as $key => $product_tab ) : ?>
							<div class="gem-tta-panel<?php if($is_first) { echo ' gem-tta-active'; $is_first= false; } ?>" id="tab-<?php echo esc_attr( $key ); ?>" data-vc-content=".gem-tta-panel-body">
								<div class="gem-tta-panel-heading">
									<h4 class="gem-tta-panel-title">
										<a href="#tab-<?php echo esc_attr( $key ); ?>" data-vc-accordion data-vc-container=".gem-tta-container">
											<span class="gem-tta-title-text"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?></span>
										</a>
									</h4>
								</div>
								<div class="gem-tta-panel-body">
									<?php if ( isset( $product_tab['callback'] ) ) {
										call_user_func( $product_tab['callback'], $key, $product_tab );
									}?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($product_tabs) && $page_layout != 'legacy' && $desc_review_layout == 'tabs' && $desc_review_style != 'legacy'): ?>
			<div class="thegem-tabs <?=$tabs_class?> <?=$isFullWidth && !$isSidebar ? $tabs_full_class : null?>" data-type="<?=$desc_review_style?>">
				<div class="thegem-tabs__nav <?=$tabsPosition?>" <?php if ($isFullWidth && !$isSidebar && !$isVerticalTabs): ?>style="width: 100vw; left: calc(50% - 50vw);"<?php endif;?>>
					<div class="thegem-tabs__nav-list">
						<?php $is_first = true; foreach ( $product_tabs as $key => $product_tab ): ?>
							<div class="thegem-tabs__nav-item <?php if($is_first) { echo 'thegem-tabs__nav-item--active'; $is_first = false; } ?>" data-id="thegem-<?php echo esc_attr( $key ); ?>">
								<span><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="thegem-tabs__nav-line">
						<div class="thegem-tabs__nav-slide"></div>
					</div>
				</div>
				<div class="thegem-tabs__body">
					<?php $is_first = true; foreach ( $product_tabs as $key => $product_tab ): ?>
						<div class="thegem-accordion__item thegem-accordion__item--tab-view">
							<div class="thegem-accordion__item-title <?php if($is_first) { echo 'thegem-accordion__item--active'; } ?>" data-id="thegem-<?php echo esc_attr( $key ); ?>">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
							</div>
							<div id="thegem-<?php echo esc_attr( $key ); ?>" class="thegem-accordion__item-body" style="display: <?php if($is_first) { echo 'block'; $is_first = false; } ?>" >
								<?php if ( isset( $product_tab['callback'] ) ) {
									call_user_func( $product_tab['callback'], $key, $product_tab );
								}?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($product_tabs) && $page_layout != 'legacy' && $desc_review_layout == 'accordion'): ?>
			<div class="thegem-accordion <?=$acc_class?> <?=$isFullWidth && !$isSidebar ? $acc_full_class : null?>">
				<?php $is_first = true; foreach ( $product_tabs as $key => $product_tab ): ?>
					<div class="thegem-accordion__item">
						<div class="thegem-accordion__item-title <?php if($is_first && $acc_position != 'next_to_gallery') { echo 'thegem-accordion__item--active'; } ?>" data-id="thegem-<?php echo esc_attr( $key ); ?>">
							<span><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?></span>
						</div>
						<div id="thegem-<?php echo esc_attr( $key ); ?>" class="thegem-accordion__item-body" style="<?php if($is_first && $acc_position != 'next_to_gallery') { echo 'display: block;'; $is_first = false; } ?>">
							<?php if ( isset( $product_tab['callback'] ) ) {
								call_user_func( $product_tab['callback'], $key, $product_tab );
							}?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($product_tabs) && $page_layout != 'legacy' && $desc_review_layout == 'one_by_one'): ?>
			<div class="thegem-one-by-one <?=!$isSidebar ? 'fullwidth-block' : null?>" <?php if (!$isSidebar): ?>style="width: 100vw; left: calc(50% - 50vw);"<?php endif;?>>
				<?php foreach ( $product_tabs as $key => $product_tab ): ?>
					<div id="thegem-<?php echo esc_attr( $key ); ?>" class="thegem-one-by-one__item <?=!$isColorBack ? 'thegem-one-by-one__item--separator' : null?>" <?php if (esc_attr($desc_review_back[$key]) != ''): ?>style="background-color: <?=esc_attr($desc_review_back[$key])?>;"<?php endif;?>>
						<div class="<?=!$isSidebar ? 'container' : 'thegem-one-by-one__container'?>">
							<?php if ($key != 'reviews'): ?>
								<div class="thegem-one-by-one__item-title">
									<h4 class="light"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?></h4>
								</div>
							<?php endif; ?>
							<div class="thegem-one-by-one__item-body">
								<?php if ( isset( $product_tab['callback'] ) ) {
									call_user_func( $product_tab['callback'], $key, $product_tab );
								}?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_product_after_tabs' ); ?>
	</div>
<?php endif; ?>
