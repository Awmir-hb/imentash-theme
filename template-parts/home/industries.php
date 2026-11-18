<?php
/**
 * صفحه اصلی: صنایع (کارت‌های تصویری از پست‌تایپ em_industry).
 * دسکتاپ: شبکه (بنتو یا ۳/۴ ستون بسته به تعداد)، تعداد محصولات هر صنعت روی کارت. موبایل: اسلایدر افقی با اسکرول بومی.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_items = ementash_get_items( 'em_industry' );
if ( ! $ementash_items ) {
	return;
}
$ementash_title = (string) ementash_get( 'industries_title' );
?>
<section class="em-section em-section--steel em-industries" id="em-industries"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-industries-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title ) : ?>
			<div class="em-head">
				<h2 class="em-h2" id="em-industries-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			</div>
		<?php endif; ?>

		<?php
		// چیدمان دسکتاپ طوری که ردیف آخر تک نماند:
		// بنتو (کارت اول ۲×۲ در شبکه ۴ستونه) وقتی تعداد ۵، ۹، ۱۳... است؛ وگرنه مضرب ۳ ← ۳ ستون، بقیه ۴ ستون.
		$ementash_n     = count( $ementash_items );
		$ementash_bento = $ementash_n >= 5 && 1 === $ementash_n % 4;
		$ementash_cols  = ! $ementash_bento && 0 !== $ementash_n % 4 && 0 === $ementash_n % 3 ? 3 : 4;
		?>
		<ul class="em-inds<?php echo $ementash_bento ? ' em-inds--bento' : ''; ?>" style="--em-ind-cols:<?php echo esc_attr( $ementash_cols ); ?>">
			<?php
			foreach ( $ementash_items as $ementash_i => $ementash_item ) :
				$ementash_link  = (string) ementash_meta( $ementash_item->ID, 'link' );
				$ementash_tag   = '' !== $ementash_link ? 'a' : 'div';
				$ementash_count = '' !== $ementash_link ? ementash_industry_product_count( $ementash_link ) : 0;
				$ementash_big   = $ementash_bento && 0 === $ementash_i;
				?>
				<li class="em-inds__item<?php echo $ementash_big ? ' em-inds__item--big' : ''; ?>">
					<<?php echo esc_attr( $ementash_tag ); ?> class="em-card em-ind"<?php echo '' !== $ementash_link ? ' href="' . esc_url( $ementash_link ) . '"' : ''; ?>>
						<span class="em-card__img">
							<?php
							if ( has_post_thumbnail( $ementash_item ) ) {
								echo get_the_post_thumbnail(
									$ementash_item,
									$ementash_big ? 'large' : 'ementash-card',
									array(
										'alt'     => '',
										'loading' => 'lazy',
										'sizes'   => $ementash_big ? '(max-width: 767px) 250px, (max-width: 900px) 50vw, 660px' : '(max-width: 767px) 250px, (max-width: 900px) 50vw, 330px',
									)
								);
							} else {
								echo '<span class="em-ph"></span>';
							}
							?>
						</span>
						<span class="em-card__shade" aria-hidden="true"></span>
						<div class="em-ind__foot">
							<div class="em-ind__text">
								<h3 class="em-ind__title"><?php echo ementash_bdi( get_the_title( $ementash_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
								<?php if ( $ementash_count ) : ?>
									<p class="em-ind__count">
										<?php
										/* translators: %s: تعداد محصول */
										echo esc_html( sprintf( _n( '%s محصول', '%s محصول', $ementash_count, 'ementash' ), ementash_fa_num( $ementash_count ) ) );
										?>
									</p>
								<?php endif; ?>
								<?php if ( '' !== $ementash_link ) : ?>
									<?php // فقط دیدنی؛ خود کارت لینک است و عنوانش را صفحه‌خوان می‌خواند. ?>
									<span class="em-ind__more" aria-hidden="true"><?php esc_html_e( 'مشاهده محصولات', 'ementash' ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( '' !== $ementash_link ) : ?>
								<span class="em-ind__arrow"><?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
						</div>
					</<?php echo esc_attr( $ementash_tag ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="em-rail-hint"><?php esc_html_e( 'برای دیدن همه صنایع، کارت‌ها را به چپ بکشید.', 'ementash' ); ?></p>
	</div>
</section>
