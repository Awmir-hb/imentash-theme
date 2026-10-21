<?php
/**
 * منوی تمام‌صفحه موبایل (مطابق MobileMenu.dc.html). باز و بسته شدن در assets/js/main.js.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

$imentash_location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
$imentash_phone    = imentash_main_phone();
?>
<div id="em-menu" class="em-mmenu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی سایت', 'imentash' ); ?>" hidden>
	<div class="em-mmenu__top">
		<?php imentash_logo( 'em-logo--menu' ); ?>
		<button type="button" class="em-hbtn em-mmenu__close" data-em-close aria-label="<?php esc_attr_e( 'بستن منو', 'imentash' ); ?>">
			<?php echo imentash_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<?php if ( has_nav_menu( $imentash_location ) ) : ?>
		<nav class="em-mmenu__nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'imentash' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => $imentash_location,
					'container'      => false,
					'menu_class'     => 'em-mmenu__list',
					'depth'          => 1,
					'fallback_cb'    => false,
					'link_after'     => imentash_icon( 'chevron' ),
				)
			);
			?>
		</nav>
	<?php endif; ?>

	<div class="em-mmenu__foot">
		<?php imentash_lang_switcher( 'full', 'em-lang--lg' ); ?>
		<?php if ( $imentash_phone ) : ?>
			<div class="em-mmenu__label"><?php echo esc_html( imentash_get( 'main_phone_label' ) ); ?></div>
			<?php imentash_phone_link( $imentash_phone, 'em-mmenu__phone' ); ?>
		<?php endif; ?>
		<?php imentash_social_links( 'em-soc--lg' ); ?>
	</div>
</div>
