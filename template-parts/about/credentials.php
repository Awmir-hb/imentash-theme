<?php
/**
 * درباره ما: اعتبار = سربرگ با شمارنده‌ها + ویترین گواهینامه‌ها + ردیف نشان‌ها.
 * نوار افتخارات بخش جدای بعدی است (همان template-parts/home/honors.php صفحه اصلی؛ page-about.php).
 *
 * - ویترین: گواهینامه‌های «نمایش: ویترین»؛ هم‌گروه‌ها یک کارت (ementash_certificate_cards). کلیک ← لایت‌باکس
 *   با همه سندهای کارت (assets/js/about.js). بدون JS، کارت به عکس بزرگ سند اول لینک است.
 * - نشان‌ها: «نمایش: نشان»؛ اگر PDF دارد لینک به PDF.
 * - شمارنده‌ها از «دسته» گواهینامه‌ها و تعداد افتخارات؛ صفرها چاپ نمی‌شوند.
 * اگر هیچ‌کدام نباشد بخش چاپ نمی‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_certs  = ementash_get_certificates( 'certificate' );
$ementash_honors = ementash_get_honors();
if ( ! $ementash_certs && ! $ementash_honors ) {
	return;
}
$ementash_page   = get_the_ID();
$ementash_title  = (string) ementash_meta( $ementash_page, 'about_cert_title' );
$ementash_text   = (string) ementash_meta( $ementash_page, 'about_cert_text' );
$ementash_cards  = ementash_certificate_cards( $ementash_certs );
$ementash_badges = array_values( array_filter( $ementash_certs, static fn( $c ) => 'badge' === ementash_meta( $c->ID, 'display' ) ) );

// شمارنده‌ها: هر دسته گواهینامه + افتخارات.
$ementash_stats = array();
foreach ( ementash_certificate_categories() as $ementash_cat => $ementash_label ) {
	$ementash_stats[ $ementash_label ] = count( array_filter( $ementash_certs, static fn( $c ) => ementash_meta( $c->ID, 'category' ) === $ementash_cat ) );
}
$ementash_stats[ __( 'افتخار', 'ementash' ) ] = count( $ementash_honors );
$ementash_stats                               = array_filter( $ementash_stats );

/**
 * توضیح کوتاه + لینک PDF یک سند (زیر عکس لایت‌باکس).
 */
$ementash_caption = static function ( $item ) {
	$sub = (string) ementash_meta( $item->ID, 'subtitle' );
	$pdf = absint( ementash_meta( $item->ID, 'pdf' ) );
	echo '<figcaption class="em-lb__cap"><strong>' . ementash_bdi( get_the_title( $item ) ) . '</strong>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( '' !== $sub ) {
		echo '<span>' . ementash_bdi( $sub ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( $pdf && wp_get_attachment_url( $pdf ) ) {
		printf( '<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url( wp_get_attachment_url( $pdf ) ), esc_html__( 'فایل PDF گواهینامه', 'ementash' ) );
	}
	echo '</figcaption>';
};
?>
<section class="em-section em-creds" id="em-credentials"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-creds-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title || '' !== $ementash_text || $ementash_stats ) : ?>
			<div class="em-creds__head">
				<div class="em-creds__intro">
					<?php if ( '' !== $ementash_title ) : ?>
						<h2 class="em-h2" id="em-creds-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $ementash_text ) : ?>
						<p class="em-head__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
				</div>
				<?php if ( $ementash_stats ) : ?>
					<ul class="em-creds__stats">
						<?php foreach ( $ementash_stats as $ementash_label => $ementash_count ) : ?>
							<li class="em-creds__stat">
								<span class="em-creds__num"><?php echo esc_html( ementash_fa_num( $ementash_count ) ); ?></span>
								<span class="em-creds__label"><?php echo esc_html( $ementash_label ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $ementash_cards ) : ?>
			<ul class="em-certs__list">
				<?php
				foreach ( $ementash_cards as $ementash_i => $ementash_card ) :
					$ementash_items = $ementash_card['items'];
					$ementash_n     = count( $ementash_items );
					$ementash_img   = get_post_thumbnail_id( $ementash_items[0] );
					$ementash_full  = $ementash_img ? wp_get_attachment_image_url( $ementash_img, 'large' ) : '';
					$ementash_tpl   = 'em-lb-' . ( $ementash_i + 1 );
					$ementash_sub   = $ementash_n > 1
						/* translators: %s: تعداد سندهای یک کارت گواهینامه */
						? sprintf( _n( '%s سند', '%s سند', $ementash_n, 'ementash' ), ementash_fa_num( $ementash_n ) )
						: (string) ementash_meta( $ementash_items[0]->ID, 'subtitle' );
					$ementash_tag   = $ementash_full ? 'a' : 'div';
					?>
					<li>
						<<?php echo esc_attr( $ementash_tag ); ?> class="em-cert"<?php echo $ementash_full ? ' href="' . esc_url( $ementash_full ) . '" data-em-lb="' . esc_attr( $ementash_tpl ) . '" aria-haspopup="dialog"' : ''; ?>>
							<span class="em-cert__img">
								<?php
								if ( $ementash_img ) {
									echo wp_get_attachment_image(
										$ementash_img,
										'medium_large', // بدون برش: پروانه‌های افقی کامل در کادر (object-fit: contain).
										false,
										array(
											'alt'     => '', // عنوان کارت کنار عکس هست؛ متن جایگزین تکراری نشود.
											'loading' => 'lazy',
											'sizes'   => '(max-width: 560px) 50vw, (max-width: 1000px) 33vw, 240px',
										)
									);
								} else {
									echo '<span class="em-ph" aria-hidden="true"></span>';
								}
								?>
								<?php if ( $ementash_n > 1 ) : ?>
									<span class="em-cert__count" aria-hidden="true"><?php echo esc_html( ementash_fa_num( $ementash_n ) ); ?></span>
								<?php endif; ?>
							</span>
							<h3 class="em-cert__title"><?php echo ementash_bdi( $ementash_card['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
							<?php if ( '' !== $ementash_sub ) : ?>
								<span class="em-cert__sub"><?php echo ementash_bdi( $ementash_sub ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
						</<?php echo esc_attr( $ementash_tag ); ?>>
						<?php if ( $ementash_full ) : ?>
							<template id="<?php echo esc_attr( $ementash_tpl ); ?>">
								<?php
								foreach ( $ementash_items as $ementash_item ) :
									$ementash_iimg = get_post_thumbnail_id( $ementash_item );
									if ( ! $ementash_iimg ) {
										continue;
									}
									$ementash_ialt = trim( (string) get_post_meta( $ementash_iimg, '_wp_attachment_image_alt', true ) );
									?>
									<figure class="em-lb__fig">
										<?php
										echo wp_get_attachment_image(
											$ementash_iimg,
											'large',
											false,
											array(
												'alt'   => '' !== $ementash_ialt ? $ementash_ialt : get_the_title( $ementash_item ),
												'sizes' => '(max-width: 900px) 100vw, 900px',
											)
										);
										$ementash_caption( $ementash_item );
										?>
									</figure>
								<?php endforeach; ?>
							</template>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<dialog class="em-lb" aria-label="<?php esc_attr_e( 'نمایش بزرگ گواهینامه', 'ementash' ); ?>">
				<div class="em-lb__bar">
					<span class="em-lb__pos" aria-live="polite"></span>
					<button type="button" class="em-lb__btn em-lb__close" aria-label="<?php esc_attr_e( 'بستن', 'ementash' ); ?>"><?php echo ementash_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				</div>
				<div class="em-lb__stage"></div>
				<div class="em-lb__nav">
					<button type="button" class="em-lb__btn em-lb__prev" aria-label="<?php esc_attr_e( 'سند قبلی', 'ementash' ); ?>"><?php echo ementash_icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<button type="button" class="em-lb__btn em-lb__next" aria-label="<?php esc_attr_e( 'سند بعدی', 'ementash' ); ?>"><?php echo ementash_icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				</div>
			</dialog>
		<?php endif; ?>

		<?php if ( $ementash_badges ) : ?>
			<ul class="em-badges">
				<?php
				foreach ( $ementash_badges as $ementash_item ) :
					$ementash_pdf = absint( ementash_meta( $ementash_item->ID, 'pdf' ) );
					$ementash_url = $ementash_pdf ? wp_get_attachment_url( $ementash_pdf ) : '';
					$ementash_tag = $ementash_url ? 'a' : 'span';
					$ementash_img = get_post_thumbnail_id( $ementash_item );
					$ementash_sub = (string) ementash_meta( $ementash_item->ID, 'subtitle' );
					?>
					<li>
						<<?php echo esc_attr( $ementash_tag ); ?> class="em-badge"<?php echo $ementash_url ? ' href="' . esc_url( $ementash_url ) . '" target="_blank" rel="noopener"' : ''; ?>>
							<span class="em-badge__img">
								<?php
								if ( $ementash_img ) {
									echo wp_get_attachment_image(
										$ementash_img,
										'medium',
										false,
										array(
											'alt'     => '',
											'loading' => 'lazy',
											'sizes'   => '96px',
										)
									);
								} else {
									echo ementash_icon( 'award' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<span class="em-badge__body">
								<span class="em-badge__name"><?php echo ementash_bdi( get_the_title( $ementash_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php if ( '' !== $ementash_sub ) : ?>
									<span class="em-badge__sub"><?php echo ementash_bdi( $ementash_sub ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<?php endif; ?>
							</span>
							<?php if ( $ementash_url ) : ?>
								<span class="em-badge__pdf"><?php esc_html_e( 'PDF', 'ementash' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(در زبانه جدید)', 'ementash' ); ?></span></span>
							<?php endif; ?>
						</<?php echo esc_attr( $ementash_tag ); ?>>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
