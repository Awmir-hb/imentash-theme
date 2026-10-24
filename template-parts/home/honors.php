<?php
/**
 * نوار افتخارات (em_certificate با نوع «افتخار») روی زمینه سرمه‌ای.
 * در صفحه اصلی و شورت‌کد [ementash_honors] استفاده می‌شود؛ رفتار اسلایدر در assets/js/honors.js.
 *
 * $args (اختیاری، از شورت‌کد): title و text؛ null یعنی مقدار Customizer.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_items = ementash_get_honors();
if ( ! $ementash_items ) {
	return;
}

$ementash_title = isset( $args['title'] ) ? (string) $args['title'] : (string) ementash_get( 'honors_title' );
$ementash_text  = isset( $args['text'] ) ? (string) $args['text'] : (string) ementash_get( 'honors_text' );
$ementash_uid   = wp_unique_id( 'em-honors-' );
$ementash_label = '' !== $ementash_title ? $ementash_title : __( 'افتخارات', 'ementash' );
?>
<section class="em-section em-honors" data-em-honors<?php echo '' !== $ementash_title ? ' aria-labelledby="' . esc_attr( $ementash_uid ) . '-title"' : ' aria-label="' . esc_attr( $ementash_label ) . '"'; ?>>
	<div class="em-container">
		<div class="em-head em-honors__head">
			<div>
				<?php if ( '' !== $ementash_title ) : ?>
					<h2 class="em-h2" id="<?php echo esc_attr( $ementash_uid ); ?>-title"><?php echo esc_html( $ementash_title ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $ementash_text ) : ?>
					<p class="em-head__text"><?php echo esc_html( $ementash_text ); ?></p>
				<?php endif; ?>
			</div>

			<?php // دکمه‌ها بدون JS کاری ندارند؛ honors.js نمایششان می‌دهد. ?>
			<div class="em-honors__nav" data-em-honors-nav hidden>
				<button type="button" class="em-honors__btn em-honors__prev" data-em-honors-prev aria-controls="<?php echo esc_attr( $ementash_uid ); ?>-list" aria-label="<?php esc_attr_e( 'افتخار قبلی', 'ementash' ); ?>">
					<?php echo ementash_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<button type="button" class="em-honors__btn em-honors__pause" data-em-honors-pause aria-controls="<?php echo esc_attr( $ementash_uid ); ?>-list" aria-label="<?php esc_attr_e( 'توقف حرکت خودکار', 'ementash' ); ?>" data-em-label-pause="<?php esc_attr_e( 'توقف حرکت خودکار', 'ementash' ); ?>" data-em-label-play="<?php esc_attr_e( 'ادامه حرکت خودکار', 'ementash' ); ?>">
					<?php echo ementash_icon( 'pause', 'em-honors__ic-pause' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo ementash_icon( 'play', 'em-honors__ic-play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<button type="button" class="em-honors__btn em-honors__next" data-em-honors-next aria-controls="<?php echo esc_attr( $ementash_uid ); ?>-list" aria-label="<?php esc_attr_e( 'افتخار بعدی', 'ementash' ); ?>">
					<?php echo ementash_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>

		<?php // ناحیه قابل فوکوس تا با کلیدهای جهت‌دار هم جابه‌جا شود. ?>
		<div class="em-honors__view" id="<?php echo esc_attr( $ementash_uid ); ?>-list" data-em-honors-view tabindex="0" role="region" aria-label="<?php echo esc_attr( $ementash_label ); ?>">
			<ul class="em-honors__list">
				<?php
				foreach ( $ementash_items as $ementash_item ) :
					$ementash_year  = (string) ementash_meta( $ementash_item->ID, 'year' );
					$ementash_thumb = get_post_thumbnail_id( $ementash_item );
					?>
					<li class="em-honor<?php echo $ementash_thumb ? '' : ' em-honor--text'; ?>">
						<?php if ( $ementash_thumb ) : ?>
							<div class="em-honor__img">
								<?php
								echo wp_get_attachment_image(
									$ementash_thumb,
									'medium',
									false,
									array(
										'alt'     => '',
										'loading' => 'lazy',
										'sizes'   => '240px',
									)
								);
								?>
							</div>
						<?php endif; ?>
						<h3 class="em-honor__title"><?php echo esc_html( get_the_title( $ementash_item ) ); ?></h3>
						<?php if ( '' !== $ementash_year ) : ?>
							<p class="em-honor__year"><?php echo esc_html( ementash_fa_num( $ementash_year ) ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
