<?php
/**
 * فیلتر دسته‌های وبلاگ (لینک، نه JS): «همه» + دسته‌های سطح اول که نوشته دارند.
 * در برگه نوشته‌ها و صفحه دسته نمایش داده می‌شود؛ با کمتر از دو دسته چاپ نمی‌شود.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_home() && ! is_category() ) {
	return;
}

$ementash_cats = get_categories(
	array(
		'parent'     => 0,
		'hide_empty' => true,
		'orderby'    => 'name',
	)
);
if ( count( $ementash_cats ) < 2 ) {
	return;
}

$ementash_all = ementash_blog_url();
// دسته فعلی، یا دسته سطح اولِ زیردسته فعلی.
$ementash_current = 0;
if ( is_category() ) {
	$ementash_ancestors = get_ancestors( get_queried_object_id(), 'category', 'taxonomy' );
	$ementash_current   = $ementash_ancestors ? (int) end( $ementash_ancestors ) : get_queried_object_id();
}
?>
<nav class="em-bcats" aria-label="<?php esc_attr_e( 'دسته‌بندی مقاله‌ها', 'ementash' ); ?>">
	<ul class="em-bcats__list">
		<?php if ( '' !== $ementash_all ) : ?>
			<li><a class="em-bcat" href="<?php echo esc_url( $ementash_all ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'همه', 'ementash' ); ?></a></li>
		<?php endif; ?>
		<?php
		foreach ( $ementash_cats as $ementash_cat ) :
			// خود دسته فعلی «page»؛ والدِ زیردسته فعلی «true».
			$ementash_on = (int) $ementash_cat->term_id === $ementash_current ? ( $ementash_current === get_queried_object_id() ? 'page' : 'true' ) : '';
			?>
			<li><a class="em-bcat" href="<?php echo esc_url( get_category_link( $ementash_cat ) ); ?>"<?php echo $ementash_on ? ' aria-current="' . esc_attr( $ementash_on ) . '"' : ''; ?>><?php echo ementash_bdi( $ementash_cat->name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>
