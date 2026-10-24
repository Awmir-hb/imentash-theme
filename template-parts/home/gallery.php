<?php
/**
 * صفحه اصلی: گالری کارخانه (۵ عکس از Customizer؛ عکس اول بزرگ). موبایل: اسلایدر افقی.
 * متن جایگزین هر عکس از کتابخانه رسانه خوانده می‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_title = (string) ementash_get( 'gallery_title' );
$ementash_label = (string) ementash_get( 'gallery_link_label' );
$ementash_url   = (string) ementash_get( 'gallery_link_url' );
?>
<section class="em-section em-gallery"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-gallery-title"' : ''; ?>>
	<div class="em-container">
		<div class="em-head">
			<?php if ( '' !== $ementash_title ) : ?>
				<h2 class="em-h2" id="em-gallery-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $ementash_label && '' !== $ementash_url ) : ?>
				<a class="em-link em-gallery__all" href="<?php echo esc_url( $ementash_url ); ?>"><?php echo ementash_bdi( $ementash_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
		</div>

		<ul class="em-gal">
			<?php
			for ( $ementash_n = 1; $ementash_n <= 5; $ementash_n++ ) :
				$ementash_id = absint( ementash_get( 'gallery_' . $ementash_n ) );
				?>
				<li class="em-gal__item em-gal__item--<?php echo esc_attr( $ementash_n ); ?>">
					<span class="em-gal__img">
						<?php
						if ( $ementash_id ) {
							// عکس اول دو برابر بقیه است.
							echo wp_get_attachment_image(
								$ementash_id,
								1 === $ementash_n ? 'large' : 'ementash-card',
								false,
								array(
									'loading' => 'lazy',
									'sizes'   => 1 === $ementash_n ? '(max-width: 767px) 300px, (max-width: 900px) 100vw, 640px' : '(max-width: 767px) 300px, (max-width: 900px) 50vw, 320px',
								)
							);
						} else {
							echo '<span class="em-ph" aria-hidden="true"></span>';
						}
						?>
					</span>
				</li>
			<?php endfor; ?>
		</ul>
	</div>
</section>
