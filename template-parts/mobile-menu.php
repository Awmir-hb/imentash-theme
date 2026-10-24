<?php
/**
 * منوی تمام‌صفحه موبایل (مطابق MobileMenu.dc.html). باز و بسته شدن در assets/js/main.js.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_location = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
$ementash_phone    = ementash_main_phone();
?>
<div id="em-menu" class="em-mmenu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی سایت', 'ementash' ); ?>" hidden>
	<div class="em-mmenu__top">
		<?php ementash_logo( 'em-logo--menu' ); ?>
		<button type="button" class="em-hbtn em-mmenu__close" data-em-close aria-label="<?php esc_attr_e( 'بستن منو', 'ementash' ); ?>">
			<?php echo ementash_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<?php if ( has_nav_menu( $ementash_location ) ) : ?>
		<nav class="em-mmenu__nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'ementash' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => $ementash_location,
					'container'      => false,
					'menu_class'     => 'em-mmenu__list',
					'depth'          => 1,
					'fallback_cb'    => false,
					'link_after'     => ementash_icon( 'chevron' ),
				)
			);
			?>
		</nav>
	<?php endif; ?>

	<div class="em-mmenu__foot">
		<?php ementash_lang_switcher( 'full', 'em-lang--lg' ); ?>
		<?php if ( $ementash_phone ) : ?>
			<div class="em-mmenu__label"><?php echo ementash_bdi( ementash_get( 'main_phone_label' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php ementash_phone_link( $ementash_phone, 'em-mmenu__phone' ); ?>
		<?php endif; ?>
		<?php ementash_social_links( 'em-soc--lg' ); ?>
	</div>
</div>
