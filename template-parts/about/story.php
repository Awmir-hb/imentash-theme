<?php
/**
 * درباره ما: متن شرکت = محتوای خود برگه (ویرایشگر)، عکس = متای «عکس کنار متن شرکت»،
 * و نقل‌قول اختیاری زیر متن.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_page    = get_the_ID();
$ementash_content = trim( (string) get_the_content() );
$ementash_image   = absint( ementash_meta( $ementash_page, 'about_story_image' ) );
$ementash_quote   = trim( (string) ementash_meta( $ementash_page, 'about_quote' ) );
$ementash_name    = (string) ementash_meta( $ementash_page, 'about_quote_name' );
$ementash_role    = (string) ementash_meta( $ementash_page, 'about_quote_role' );
if ( '' === $ementash_content && ! $ementash_image && '' === $ementash_quote ) {
	return;
}
?>
<section class="em-section em-story">
	<div class="em-container em-story__in<?php echo $ementash_image ? '' : ' em-story__in--full'; ?>">
		<div class="em-story__body">
			<?php if ( '' !== $ementash_content ) : ?>
				<div class="em-entry em-story__text">
					<?php echo ementash_bdi_html( apply_filters( 'the_content', $ementash_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی the_content؛ فقط bdi اضافه می‌شود. ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $ementash_quote ) : ?>
				<figure class="em-quote">
					<blockquote class="em-quote__text"><p><?php echo ementash_bdi( $ementash_quote ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></blockquote>
					<?php if ( '' !== $ementash_name || '' !== $ementash_role ) : ?>
						<figcaption class="em-quote__by">
							<?php if ( '' !== $ementash_name ) : ?>
								<span class="em-quote__name"><?php echo ementash_bdi( $ementash_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
							<?php if ( '' !== $ementash_role ) : ?>
								<span class="em-quote__role"><?php echo ementash_bdi( $ementash_role ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>

		<?php if ( $ementash_image ) : ?>
			<div class="em-story__img">
				<?php
				echo wp_get_attachment_image(
					$ementash_image,
					'large',
					false,
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 900px) 100vw, 560px',
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
