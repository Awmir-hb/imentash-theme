<?php
/**
 * فوتر خود قالب: معرفی کوتاه، دسترسی سریع، دفاتر.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_about = (string) ementash_get( 'footer_text' );
$ementash_copy  = (string) ementash_get( 'footer_copyright' );
?>
<footer class="em-footer">
	<div class="em-footer__in">
		<div class="em-footer__brand">
			<?php ementash_logo( 'em-logo--footer' ); ?>
			<?php if ( '' !== $ementash_about ) : ?>
				<p class="em-footer__about"><?php echo esc_html( $ementash_about ); ?></p>
			<?php endif; ?>
			<?php ementash_social_links( 'em-footer__soc' ); ?>
		</div>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="em-footer__links" aria-labelledby="em-footer-links-title">
				<h2 class="em-footer__title" id="em-footer-links-title"><?php esc_html_e( 'دسترسی سریع', 'ementash' ); ?></h2>
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
			<h2 class="em-footer__title"><?php esc_html_e( 'تماس', 'ementash' ); ?></h2>
			<?php
			foreach ( array( 'isfahan', 'tehran' ) as $ementash_key ) :
				$ementash_label   = (string) ementash_get( 'office_' . $ementash_key . '_label' );
				$ementash_address = (string) ementash_get( 'office_' . $ementash_key . '_address' );
				$ementash_phones  = ementash_phones( $ementash_key );
				if ( '' === $ementash_address && ! $ementash_phones ) {
					continue;
				}
				?>
				<div class="em-footer__office">
					<?php if ( '' !== $ementash_label ) : ?>
						<span class="em-footer__label"><?php echo esc_html( $ementash_label ); ?>:</span>
					<?php endif; ?>
					<?php echo esc_html( $ementash_address ); ?><?php echo ( '' !== $ementash_address && $ementash_phones ) ? esc_html__( '، ', 'ementash' ) : ''; ?>
					<?php
					if ( $ementash_phones ) {
						ementash_phone_link( $ementash_phones[0] );
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( '' !== $ementash_copy ) : ?>
		<div class="em-footer__bottom"><?php echo esc_html( $ementash_copy ); ?></div>
	<?php endif; ?>
</footer>
