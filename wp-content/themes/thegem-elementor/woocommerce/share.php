<?php
/**
 * Share template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.13
 */
?>

<div class="yith-wcwl-share">
    <div class="yith-wcwl-share-title"><?php $share_title = 'Share your wishlist on:'; echo $share_title ?></div>
    <ul>
        <?php if( $share_twitter_enabled ): ?>
            <li style="list-style-type: none; display: inline-block;">
                <a target="_blank" class="twitter" href="<?php echo esc_url('https://twitter.com/share?url='.$share_link_url.'&amp;text='.$share_twitter_summary); ?>" title="<?php esc_attr_e( 'Twitter', 'yith-woocommerce-wishlist' ) ?>"></a>
            </li>
        <?php endif; ?>

        <?php if( $share_facebook_enabled ): ?>
            <li style="list-style-type: none; display: inline-block;">
                <a target="_blank" class="facebook" href="<?php echo esc_url('https://www.facebook.com/sharer.php?s=100&amp;p%5Btitle%5D='.$share_link_title.'&amp;p%5Burl%5D='.$share_link_url.'&amp;p%5Bsummary%5D='.$share_summary.'&amp;p%5Bimages%5D%5B0%5D='.$share_image_url); ?>" title="<?php esc_attr_e( 'Facebook', 'yith-woocommerce-wishlist' ) ?>"></a>
            </li>
        <?php endif; ?>

        <?php if( $share_pinterest_enabled ): ?>
            <li style="list-style-type: none; display: inline-block;">
                <a target="_blank" class="pinterest" href="<?php echo esc_url('http://pinterest.com/pin/create/button/?url='.$share_link_url.'&amp;description='.$share_summary.'&amp;media='.$share_image_url); ?>" title="<?php esc_attr_e( 'Pinterest', 'yith-woocommerce-wishlist' ) ?>" onclick="window.open(this.href); return false;"></a>
            </li>
        <?php endif; ?>

        <?php if( $share_email_enabled ): ?>
            <li style="list-style-type: none; display: inline-block;">
                <a class="email" href="<?php echo esc_url('mailto:?subject='.urlencode( apply_filters( 'yith_wcwl_email_share_subject', esc_html__( 'I wanted you to see this site', 'thegem' ) ) ).'&amp;body='.apply_filters( 'yith_wcwl_email_share_body', $share_link_url ).'&amp;title='.$share_link_title); ?>" title="<?php esc_attr_e( 'Email', 'yith-woocommerce-wishlist' ) ?>"></a>
            </li>
        <?php endif; ?>
    </ul>
</div>
