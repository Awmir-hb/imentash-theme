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
					<h2 class="em-h2" id="<?php echo esc_attr( $ementash_uid ); ?>-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $ementash_text ) : ?>
					<p class="em-head__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php
	// نوار پیوسته (مثل نوار مشتریان): فهرست دو بار چاپ می‌شود تا حرکت بی‌درز باشد.
	// با هاور یا فوکوس می‌ایستد؛ با کاهش حرکت ثابت و با اسکرول افقی، و نسخه تکراری پنهان.
	ob_start();
	?>
			<ul class="em-honors__list">
				<?php
				foreach ( $ementash_items as $ementash_item ) :
					$ementash_year  = (string) ementash_meta( $ementash_item->ID, 'year' );
					$ementash_thumb = get_post_thumbnail_id( $ementash_item );
					$ementash_name  = get_the_title( $ementash_item );
					?>
					<li class="em-honor<?php echo $ementash_thumb ? '' : ' em-honor--text'; ?>">
						<?php if ( $ementash_thumb ) : ?>
							<?php
							// ویترین: نور پشت تندیس و سایه پایه با CSS. لینک به عکس بزرگ؛ honors.js آن را در پنجره باز می‌کند.
							$ementash_zoom = wp_get_attachment_image_url( $ementash_thumb, 'large' );
							/* translators: %s: عنوان افتخار */
							$ementash_zlabel = sprintf( __( 'نمایش بزرگ‌تر: %s', 'ementash' ), $ementash_name );
							?>
							<a class="em-honor__stage" href="<?php echo esc_url( $ementash_zoom ); ?>" data-em-honor-zoom data-em-caption="<?php echo esc_attr( trim( $ementash_name . ' ' . ementash_fa_num( $ementash_year ) ) ); ?>" aria-label="<?php echo esc_attr( $ementash_zlabel ); ?>">
								<?php
								echo wp_get_attachment_image(
									$ementash_thumb,
									'medium',
									false,
									array(
										'alt'     => '',
										'loading' => 'lazy',
										'sizes'   => '260px',
										'class'   => 'em-honor__img',
									)
								);
								echo ementash_icon( 'search', 'em-honor__zoom' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</a>
						<?php else : ?>
							<?php // بدون عکس: نشان خطی ثابت تا همه کارت‌ها هم‌شکل باشند. ?>
							<span class="em-honor__stage em-honor__stage--emblem" aria-hidden="true"><?php echo ementash_icon( 'award', 'em-honor__emblem' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
						<h3 class="em-honor__title"><?php echo ementash_bdi( $ementash_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
						<?php $ementash_sub = (string) ementash_meta( $ementash_item->ID, 'subtitle' ); // شرکت دیگر گروه تاش، یا توضیح کوتاه ?>
						<?php if ( '' !== $ementash_sub ) : ?>
							<p class="em-honor__sub"><?php echo ementash_bdi( $ementash_sub ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						<?php endif; ?>
						<?php if ( '' !== $ementash_year ) : ?>
							<p class="em-honor__year"><?php echo ementash_bdi( $ementash_year, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
	<?php
	$ementash_list = (string) ob_get_clean();
	// نسخه تکراری: پنهان از صفحه‌خوان و بیرون از ترتیب Tab
	$ementash_copy = str_replace(
		array( '<ul class="em-honors__list">', '<a class="em-honor__stage"' ),
		array( '<ul class="em-honors__list" aria-hidden="true" data-em-copy>', '<a class="em-honor__stage" tabindex="-1"' ),
		$ementash_list
	);
	?>
	<div class="em-honors__view" style="--em-honors-n:<?php echo esc_attr( count( $ementash_items ) ); ?>">
		<div class="em-honors__track">
			<?php
			echo $ementash_list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی escape‌شده همین فایل.
			echo $ementash_copy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>

	<?php // پنجره عکس بزرگ؛ فقط با JS استفاده می‌شود (بدون JS لینک خود عکس را باز می‌کند). ?>
	<dialog class="em-hzoom" data-em-hzoom aria-label="<?php echo esc_attr( $ementash_label ); ?>">
		<button type="button" class="em-hzoom__close" data-em-hzoom-close aria-label="<?php esc_attr_e( 'بستن', 'ementash' ); ?>"><?php echo ementash_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		<img class="em-hzoom__img" src="data:," alt="">
		<p class="em-hzoom__caption" data-em-hzoom-caption></p>
	</dialog>
</section>
