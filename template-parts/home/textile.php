<?php
/**
 * صفحه اصلی: محصولات کمک‌نساجی (KX-327): متن، ظرف‌های بسته‌بندی، دکمه کاتالوگ و عکس با کد بزرگ.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_title   = (string) ementash_get( 'textile_title' );
$ementash_text    = (string) ementash_get( 'textile_text' );
$ementash_packs   = ementash_get_lines( 'textile_packs' );
$ementash_image   = absint( ementash_get( 'textile_image' ) );
$ementash_badge   = (string) ementash_get( 'textile_badge' );
$ementash_catalog = absint( ementash_get( 'textile_catalog' ) );
$ementash_btn     = (string) ementash_get( 'textile_btn_label' );
$ementash_file    = $ementash_catalog ? wp_get_attachment_url( $ementash_catalog ) : '';
?>
<section class="em-section em-textile"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-textile-title"' : ''; ?>>
	<div class="em-container em-textile__in">
		<div class="em-textile__body">
			<?php if ( '' !== $ementash_title ) : ?>
				<h2 class="em-h2 em-textile__h2" id="em-textile-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $ementash_text ) : ?>
				<p class="em-textile__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( $ementash_packs ) : ?>
				<ul class="em-textile__packs">
					<?php foreach ( $ementash_packs as $ementash_pack ) : ?>
						<li><?php echo ementash_bdi( $ementash_pack ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $ementash_file && '' !== $ementash_btn ) : ?>
				<a class="em-btn em-btn--dark em-textile__btn" href="<?php echo esc_url( $ementash_file ); ?>" download><?php echo ementash_bdi( $ementash_btn ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
		</div>

		<div class="em-textile__media">
			<?php
			if ( $ementash_image ) {
				echo wp_get_attachment_image(
					$ementash_image,
					'large',
					false,
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 900px) 100vw, 640px',
					)
				);
			} else {
				echo '<span class="em-ph" aria-hidden="true"></span>';
			}
			?>
			<?php if ( '' !== $ementash_badge ) : ?>
				<?php // تزئینی؛ همین کد در متن هم آمده است. ?>
				<span class="em-textile__badge" dir="ltr" aria-hidden="true"><?php echo esc_html( $ementash_badge ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>
