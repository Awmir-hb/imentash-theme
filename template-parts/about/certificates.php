<?php
/**
 * درباره ما: گواهینامه‌ها (em_certificate با نوع «گواهینامه»). افتخارات جدا در نوار افتخارات می‌آیند.
 * اگر فایل PDF داشته باشد، کارت به آن لینک می‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_items = ementash_get_certificates( 'certificate' );
if ( ! $ementash_items ) {
	return;
}
$ementash_page  = get_the_ID();
$ementash_title = (string) ementash_meta( $ementash_page, 'about_cert_title' );
$ementash_text  = (string) ementash_meta( $ementash_page, 'about_cert_text' );
?>
<section class="em-section em-certs"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-certs-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title || '' !== $ementash_text ) : ?>
			<div class="em-head">
				<?php if ( '' !== $ementash_title ) : ?>
					<h2 class="em-h2" id="em-certs-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $ementash_text ) : ?>
					<p class="em-head__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ul class="em-certs__list">
			<?php
			foreach ( $ementash_items as $ementash_item ) :
				$ementash_name = get_the_title( $ementash_item );
				$ementash_sub  = (string) ementash_meta( $ementash_item->ID, 'subtitle' );
				$ementash_pdf  = absint( ementash_meta( $ementash_item->ID, 'pdf' ) );
				$ementash_url  = $ementash_pdf ? wp_get_attachment_url( $ementash_pdf ) : '';
				$ementash_tag  = $ementash_url ? 'a' : 'div';
				$ementash_img  = get_post_thumbnail_id( $ementash_item );
				$ementash_alt  = $ementash_img ? trim( (string) get_post_meta( $ementash_img, '_wp_attachment_image_alt', true ) ) : '';
				?>
				<li>
					<<?php echo esc_attr( $ementash_tag ); ?> class="em-cert"<?php echo $ementash_url ? ' href="' . esc_url( $ementash_url ) . '"' : ''; ?>>
						<span class="em-cert__img">
							<?php
							if ( $ementash_img ) {
								// اسکن گواهینامه محتواست: alt کتابخانه، وگرنه نام گواهینامه.
								echo wp_get_attachment_image(
									$ementash_img,
									'ementash-portrait',
									false,
									array(
										'alt'     => '' !== $ementash_alt ? $ementash_alt : $ementash_name,
										'loading' => 'lazy',
										'sizes'   => '(max-width: 560px) 100vw, (max-width: 1000px) 50vw, 300px',
									)
								);
							} else {
								echo '<span class="em-ph" aria-hidden="true"></span>';
							}
							?>
						</span>
						<h3 class="em-cert__title"><?php echo ementash_bdi( $ementash_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
						<?php if ( '' !== $ementash_sub ) : ?>
							<span class="em-cert__sub"><?php echo ementash_bdi( $ementash_sub ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
						<?php if ( $ementash_url ) : ?>
							<span class="em-cert__pdf"><?php esc_html_e( 'مشاهده فایل گواهینامه (PDF)', 'ementash' ); ?></span>
						<?php endif; ?>
					</<?php echo esc_attr( $ementash_tag ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
