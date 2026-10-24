<?php
/**
 * سربرگ سرمه‌ای صفحات داخلی: مسیر صفحه، تیتر و توضیح کوتاه.
 *
 * $args: title (string)، desc (string، اختیاری)، crumbs (آرایه‌ای از label/url، اختیاری).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_title  = isset( $args['title'] ) ? (string) $args['title'] : '';
$ementash_desc   = isset( $args['desc'] ) ? (string) $args['desc'] : '';
$ementash_crumbs = isset( $args['crumbs'] ) && is_array( $args['crumbs'] ) ? $args['crumbs'] : array();
?>
<section class="em-pagehead">
	<div class="em-pagehead__in">
		<?php if ( $ementash_crumbs ) : ?>
			<nav class="em-crumbs" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'ementash' ); ?>">
				<?php foreach ( $ementash_crumbs as $ementash_i => $ementash_crumb ) : ?>
					<?php if ( $ementash_i > 0 ) : ?>
						<span class="em-crumbs__sep" aria-hidden="true">/</span>
					<?php endif; ?>
					<?php if ( ! empty( $ementash_crumb['url'] ) ) : ?>
						<a href="<?php echo esc_url( $ementash_crumb['url'] ); ?>"><?php echo ementash_bdi( $ementash_crumb['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo ementash_bdi( $ementash_crumb['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<h1 class="em-pagehead__title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>

		<?php if ( '' !== $ementash_desc ) : ?>
			<p class="em-pagehead__desc"><?php echo ementash_bdi( $ementash_desc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
	</div>
</section>
