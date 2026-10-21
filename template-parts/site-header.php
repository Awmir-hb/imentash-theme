<?php
/**
 * هدر خود قالب (لوگو، منوی دسکتاپ، زبان، تلفن و دکمه منوی موبایل).
 *
 * $args: variant (overlay | grid | plain).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_variant = isset( $args['variant'] ) ? $args['variant'] : 'grid';
$ementash_phone   = ementash_main_phone();
?>
<header class="em-header em-header--<?php echo esc_attr( $ementash_variant ); ?>">
	<div class="em-header__in">
		<?php ementash_logo(); ?>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav class="em-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'ementash' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'em-nav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="em-header__tools">
			<?php ementash_lang_switcher( 'short' ); ?>

			<?php if ( $ementash_phone ) : ?>
				<a href="<?php echo esc_url( ementash_tel_href( $ementash_phone ), array( 'tel' ) ); ?>" class="em-hbtn em-hbtn--phone" aria-label="<?php esc_attr_e( 'تماس تلفنی', 'ementash' ); ?>">
					<?php echo ementash_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>

			<button type="button" class="em-hbtn em-mbtn" aria-controls="em-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'باز کردن منو', 'ementash' ); ?>">
				<?php echo ementash_icon( 'menu', 'em-mbtn__wide' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo ementash_icon( 'menu-short', 'em-mbtn__short' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>
</header>
