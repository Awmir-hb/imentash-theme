<?php
/**
 * درباره ما: تحقیق و توسعه. عکس (متای برگه) کنار تیتر، متن و فهرست حوزه‌ها (ردیف‌های متاباکس).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_page  = get_the_ID();
$ementash_items = (array) ementash_meta( $ementash_page, 'about_rd_items' );
$ementash_title = (string) ementash_meta( $ementash_page, 'about_rd_title' );
$ementash_text  = (string) ementash_meta( $ementash_page, 'about_rd_text' );
$ementash_image = absint( ementash_meta( $ementash_page, 'about_rd_image' ) );
if ( ! $ementash_items && '' === $ementash_text ) {
	return;
}
?>
<section class="em-section em-rd" id="em-rd"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-rd-title"' : ''; ?>>
	<div class="em-container em-rd__in<?php echo $ementash_image ? '' : ' em-rd__in--full'; ?>">
		<?php if ( $ementash_image ) : ?>
			<div class="em-rd__img">
				<?php
				echo wp_get_attachment_image(
					$ementash_image,
					'large',
					false,
					array(
						'loading' => 'lazy',
						'sizes'   => '(max-width: 900px) 100vw, 600px',
					)
				);
				?>
			</div>
		<?php endif; ?>

		<div class="em-rd__body">
			<?php if ( '' !== $ementash_title ) : ?>
				<h2 class="em-h2 em-rd__h2" id="em-rd-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $ementash_text ) : ?>
				<p class="em-rd__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( $ementash_items ) : ?>
				<ul class="em-rd__list">
					<?php foreach ( $ementash_items as $ementash_item ) : ?>
						<li class="em-rd__item">
							<?php if ( ! empty( $ementash_item['icon'] ) ) : ?>
								<span class="em-rd__icon"><?php echo ementash_icon( (string) $ementash_item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
							<?php echo ementash_bdi( isset( $ementash_item['title'] ) ? $ementash_item['title'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
