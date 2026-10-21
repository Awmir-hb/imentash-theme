<?php
/**
 * هدر خود قالب (لوگو، منوی دسکتاپ، زبان، تلفن و دکمه منوی موبایل).
 *
 * $args: variant (overlay | grid | plain).
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

$imentash_variant = isset( $args['variant'] ) ? $args['variant'] : 'grid';
$imentash_phone   = imentash_main_phone();
?>
<header class="em-header em-header--<?php echo esc_attr( $imentash_variant ); ?>">
	<div class="em-header__in">
		<?php imentash_logo(); ?>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav class="em-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'imentash' ); ?>">
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
			<?php imentash_lang_switcher( 'short' ); ?>

			<?php if ( $imentash_phone ) : ?>
				<a href="<?php echo esc_url( imentash_tel_href( $imentash_phone ), array( 'tel' ) ); ?>" class="em-hbtn em-hbtn--phone" aria-label="<?php esc_attr_e( 'تماس تلفنی', 'imentash' ); ?>">
					<?php echo imentash_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>

			<button type="button" class="em-hbtn em-mbtn" aria-controls="em-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'باز کردن منو', 'imentash' ); ?>">
				<?php echo imentash_icon( 'menu', 'em-mbtn__wide' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo imentash_icon( 'menu-short', 'em-mbtn__short' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>
</header>
