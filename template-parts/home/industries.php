<?php
/**
 * صفحه اصلی: صنایع (کارت‌های تصویری از پست‌تایپ em_industry).
 * دسکتاپ: شبکه ۴ستونه. موبایل: اسلایدر افقی با اسکرول بومی.
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
<section class="em-section em-section--soft em-industries"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-industries-title"' : ''; ?>>
	<div class="em-container">
		<?php if ( '' !== $ementash_title ) : ?>
			<div class="em-head">
				<h2 class="em-h2" id="em-industries-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			</div>
		<?php endif; ?>

		<ul class="em-inds">
			<?php
			foreach ( $ementash_items as $ementash_item ) :
				$ementash_link = (string) ementash_meta( $ementash_item->ID, 'link' );
				$ementash_tag  = '' !== $ementash_link ? 'a' : 'div';
				?>
				<li class="em-inds__item">
					<<?php echo esc_attr( $ementash_tag ); ?> class="em-card em-ind"<?php echo '' !== $ementash_link ? ' href="' . esc_url( $ementash_link ) . '"' : ''; ?>>
						<span class="em-card__img">
							<?php
							if ( has_post_thumbnail( $ementash_item ) ) {
								echo get_the_post_thumbnail(
									$ementash_item,
									'ementash-card',
									array(
										'alt'     => '',
										'loading' => 'lazy',
										'sizes'   => '(max-width: 767px) 250px, (max-width: 900px) 50vw, 330px',
									)
								);
							} else {
								echo '<span class="em-ph"></span>';
							}
							?>
						</span>
						<span class="em-card__shade" aria-hidden="true"></span>
						<div class="em-ind__foot">
							<h3 class="em-ind__title"><?php echo ementash_bdi( get_the_title( $ementash_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
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
