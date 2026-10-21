<?php
/**
 * فوتر خود قالب: معرفی کوتاه، دسترسی سریع، دفاتر.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

$imentash_about = (string) imentash_get( 'footer_text' );
$imentash_copy  = (string) imentash_get( 'footer_copyright' );
?>
<footer class="em-footer">
	<div class="em-footer__in">
		<div class="em-footer__brand">
			<?php imentash_logo( 'em-logo--footer' ); ?>
			<?php if ( '' !== $imentash_about ) : ?>
				<p class="em-footer__about"><?php echo esc_html( $imentash_about ); ?></p>
			<?php endif; ?>
			<?php imentash_social_links( 'em-footer__soc' ); ?>
		</div>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="em-footer__links" aria-labelledby="em-footer-links-title">
				<h2 class="em-footer__title" id="em-footer-links-title"><?php esc_html_e( 'دسترسی سریع', 'imentash' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'em-footer__menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="em-footer__contact">
			<h2 class="em-footer__title"><?php esc_html_e( 'تماس', 'imentash' ); ?></h2>
			<?php
			foreach ( array( 'isfahan', 'tehran' ) as $imentash_key ) :
				$imentash_label   = (string) imentash_get( 'office_' . $imentash_key . '_label' );
				$imentash_address = (string) imentash_get( 'office_' . $imentash_key . '_address' );
				$imentash_phones  = imentash_phones( $imentash_key );
				if ( '' === $imentash_address && ! $imentash_phones ) {
					continue;
				}
				?>
				<div class="em-footer__office">
					<?php if ( '' !== $imentash_label ) : ?>
						<span class="em-footer__label"><?php echo esc_html( $imentash_label ); ?>:</span>
					<?php endif; ?>
					<?php echo esc_html( $imentash_address ); ?><?php echo ( '' !== $imentash_address && $imentash_phones ) ? esc_html__( '، ', 'imentash' ) : ''; ?>
					<?php
					if ( $imentash_phones ) {
						imentash_phone_link( $imentash_phones[0] );
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( '' !== $imentash_copy ) : ?>
		<div class="em-footer__bottom"><?php echo esc_html( $imentash_copy ); ?></div>
	<?php endif; ?>
</footer>
