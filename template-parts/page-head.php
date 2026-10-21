<?php
/**
 * سربرگ سرمه‌ای صفحات داخلی: مسیر صفحه، تیتر و توضیح کوتاه.
 *
 * $args: title (string)، desc (string، اختیاری)، crumbs (آرایه‌ای از label/url، اختیاری).
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

$imentash_title  = isset( $args['title'] ) ? (string) $args['title'] : '';
$imentash_desc   = isset( $args['desc'] ) ? (string) $args['desc'] : '';
$imentash_crumbs = isset( $args['crumbs'] ) && is_array( $args['crumbs'] ) ? $args['crumbs'] : array();
?>
<section class="em-pagehead">
	<div class="em-pagehead__in">
		<?php if ( $imentash_crumbs ) : ?>
			<nav class="em-crumbs" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'imentash' ); ?>">
				<?php foreach ( $imentash_crumbs as $imentash_i => $imentash_crumb ) : ?>
					<?php if ( $imentash_i > 0 ) : ?>
						<span class="em-crumbs__sep" aria-hidden="true">/</span>
					<?php endif; ?>
					<?php if ( ! empty( $imentash_crumb['url'] ) ) : ?>
						<a href="<?php echo esc_url( $imentash_crumb['url'] ); ?>"><?php echo esc_html( $imentash_crumb['label'] ); ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( $imentash_crumb['label'] ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<h1 class="em-pagehead__title"><?php echo esc_html( $imentash_title ); ?></h1>

		<?php if ( '' !== $imentash_desc ) : ?>
			<p class="em-pagehead__desc"><?php echo esc_html( $imentash_desc ); ?></p>
		<?php endif; ?>
	</div>
</section>
