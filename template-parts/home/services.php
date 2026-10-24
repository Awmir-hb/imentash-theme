<?php
/**
 * صفحه اصلی: خدمات (کارت‌های سرمه‌ای با عکس سمت چپ، از پست‌تایپ em_service).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_items = ementash_get_items( 'em_service' );
if ( ! $ementash_items ) {
	return;
}
$ementash_title = (string) ementash_get( 'services_title' );
$ementash_text  = (string) ementash_get( 'services_text' );
?>
<section class="em-section em-services"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-services-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title || '' !== $ementash_text ) : ?>
			<div class="em-head">
				<?php if ( '' !== $ementash_title ) : ?>
					<h2 class="em-h2" id="em-services-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $ementash_text ) : ?>
					<p class="em-head__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ul class="em-svcs">
			<?php
			foreach ( $ementash_items as $ementash_item ) :
				$ementash_link = (string) ementash_meta( $ementash_item->ID, 'link' );
				$ementash_tag  = '' !== $ementash_link ? 'a' : 'div';
				?>
				<li>
					<<?php echo esc_attr( $ementash_tag ); ?> class="em-card em-svc"<?php echo '' !== $ementash_link ? ' href="' . esc_url( $ementash_link ) . '"' : ''; ?>>
						<span class="em-card__img em-svc__img">
							<?php
							if ( has_post_thumbnail( $ementash_item ) ) {
								echo get_the_post_thumbnail(
									$ementash_item,
									'ementash-card',
									array(
										'alt'     => '',
										'loading' => 'lazy',
										'sizes'   => '(max-width: 900px) 60vw, 400px',
									)
								);
							} else {
								echo '<span class="em-ph"></span>';
							}
							?>
						</span>
						<span class="em-svc__shade" aria-hidden="true"></span>
						<span class="em-svc__icon"><?php echo ementash_icon( (string) ementash_meta( $ementash_item->ID, 'icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3 class="em-svc__title"><?php echo ementash_bdi( get_the_title( $ementash_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
						<?php if ( '' !== $ementash_link ) : ?>
							<span class="em-svc__arrow"><?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
					</<?php echo esc_attr( $ementash_tag ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
