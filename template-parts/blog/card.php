<?php
/**
 * کارت نوشته یا برگه با عکس ۴:۳ (صفحه اصلی، آرشیو وبلاگ، نوشته‌های مرتبط، جستجو).
 * عکس تزئینی است (alt خالی) چون عنوان کنارش آمده است.
 *
 * $args: post (WP_Post)، heading (h2|h3، پیش‌فرض h3)، sizes (ویژگی sizes تصویر).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_post = isset( $args['post'] ) ? get_post( $args['post'] ) : get_post();
if ( ! $ementash_post ) {
	return;
}
$ementash_tag   = isset( $args['heading'] ) && 'h2' === $args['heading'] ? 'h2' : 'h3';
$ementash_sizes = isset( $args['sizes'] ) ? (string) $args['sizes'] : '(max-width: 560px) 100vw, (max-width: 1000px) 50vw, 420px';

// نوشته: دسته و تاریخ. برگه (در جستجو): فقط برچسب «برگه».
if ( 'post' === $ementash_post->post_type ) {
	$ementash_cat  = ementash_post_main_cat( $ementash_post->ID );
	$ementash_chip = $ementash_cat ? $ementash_cat->name : '';
	$ementash_date = true;
} else {
	$ementash_type = get_post_type_object( $ementash_post->post_type );
	$ementash_chip = $ementash_type ? $ementash_type->labels->singular_name : '';
	$ementash_date = false;
}
?>
<li class="em-posts__item">
	<a class="em-post" href="<?php echo esc_url( get_permalink( $ementash_post ) ); ?>">
		<span class="em-post__img">
			<?php
			if ( has_post_thumbnail( $ementash_post ) ) {
				echo get_the_post_thumbnail(
					$ementash_post,
					'ementash-card',
					array(
						'alt'     => '',
						'loading' => 'lazy',
						'sizes'   => $ementash_sizes,
					)
				);
			} else {
				echo '<span class="em-ph" aria-hidden="true"></span>';
			}
			?>
		</span>
		<div class="em-post__body">
			<?php if ( '' !== $ementash_chip || $ementash_date ) : ?>
				<span class="em-post__meta">
					<?php if ( '' !== $ementash_chip ) : ?>
						<span class="em-post__cat"><?php echo ementash_bdi( $ementash_chip ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<?php if ( $ementash_date ) : ?>
						<time datetime="<?php echo esc_attr( get_the_date( 'c', $ementash_post ) ); ?>"><?php echo ementash_bdi( get_the_date( '', $ementash_post ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></time>
					<?php endif; ?>
				</span>
			<?php endif; ?>
			<<?php echo $ementash_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- فقط h2 یا h3. ?> class="em-post__title"><?php echo ementash_bdi( get_the_title( $ementash_post ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo $ementash_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<span class="em-post__more" aria-hidden="true">
				<?php echo 'post' === $ementash_post->post_type ? esc_html__( 'خواندن مقاله', 'ementash' ) : esc_html__( 'مشاهده', 'ementash' ); ?>
				<?php echo ementash_icon( 'arrow', 'em-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>
	</a>
</li>
