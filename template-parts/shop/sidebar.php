<?php
/**
 * سایدبار فروشگاه: دسته‌ها (لینک واقعی /product-category/)، کادر کاتالوگ و کادر مشاوره.
 * دسته‌های سطح اول همیشه؛ زیردسته‌ها فقط زیر دسته فعال (و والدهایش).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_current = is_product_category() ? get_queried_object() : null;
// دسته فعلی و همه والدهایش: زیردسته‌های همین‌ها باز می‌شوند.
$ementash_open = $ementash_current ? array_merge( array( (int) $ementash_current->term_id ), array_map( 'intval', get_ancestors( $ementash_current->term_id, 'product_cat', 'taxonomy' ) ) ) : array();

/**
 * فهرست دسته‌های یک سطح؛ زیرشاخه دسته باز به‌صورت تودرتو.
 */
$ementash_render = static function ( $parent ) use ( &$ementash_render, $ementash_current, $ementash_open ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $parent,
			'hide_empty' => true,
			'orderby'    => 'menu_order',
			'exclude'    => array( absint( get_option( 'default_product_cat' ) ) ),
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}
	printf( '<ul class="em-fcats%s">', $parent ? ' em-fcats--sub' : '' );
	foreach ( $terms as $term ) {
		$count   = get_term_meta( $term->term_id, 'product_count_product_cat', true );
		$count   = '' !== $count ? (int) $count : (int) $term->count;
		$current = $ementash_current && (int) $ementash_current->term_id === (int) $term->term_id;
		$open    = in_array( (int) $term->term_id, $ementash_open, true );

		echo '<li>';
		printf(
			'<a class="em-fcat%1$s" href="%2$s"%3$s><span>%4$s</span><span class="em-fcat__count">%5$s</span></a>',
			$current ? ' em-fcat--on' : ( $open ? ' em-fcat--open' : '' ),
			esc_url( get_term_link( $term ) ),
			$current ? ' aria-current="page"' : '',
			ementash_bdi( $term->name ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( ementash_fa_num( $count ) )
		);
		if ( $open ) {
			$ementash_render( $term->term_id );
		}
		echo '</li>';
	}
	echo '</ul>';
};

$ementash_all     = is_shop() && ! is_search();
$ementash_total   = (int) wp_count_posts( 'product' )->publish;
$ementash_catalog = absint( ementash_get( 'shop_catalog_file' ) );
$ementash_phone   = ementash_main_phone();
?>
<aside class="em-shop__side">
	<nav class="em-sbox em-sbox--cats" aria-labelledby="em-fcats-title">
		<h2 class="em-sbox__title" id="em-fcats-title"><?php esc_html_e( 'دسته‌بندی محصولات', 'ementash' ); ?></h2>
		<ul class="em-fcats">
			<li>
				<a class="em-fcat<?php echo $ementash_all ? ' em-fcat--on' : ''; ?>" href="<?php echo esc_url( ementash_shop_url() ); ?>"<?php echo $ementash_all ? ' aria-current="page"' : ''; ?>>
					<span><?php esc_html_e( 'همه محصولات', 'ementash' ); ?></span>
					<span class="em-fcat__count"><?php echo esc_html( ementash_fa_num( $ementash_total ) ); ?></span>
				</a>
			</li>
		</ul>
		<?php $ementash_render( 0 ); ?>
	</nav>

	<?php if ( $ementash_catalog ) : ?>
		<div class="em-sbox em-sbox--catalog">
			<?php echo ementash_icon( 'quote', 'em-sbox__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<h2 class="em-sbox__title"><?php echo ementash_bdi( ementash_get( 'shop_catalog_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<?php if ( '' !== (string) ementash_get( 'shop_catalog_text' ) ) : ?>
				<p class="em-sbox__text"><?php echo ementash_bdi( ementash_get( 'shop_catalog_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<a class="em-sbox__btn" href="<?php echo esc_url( wp_get_attachment_url( $ementash_catalog ) ); ?>" download><?php echo ementash_bdi( ementash_get( 'shop_catalog_btn' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	<?php endif; ?>

	<?php if ( $ementash_phone && '' !== (string) ementash_get( 'shop_help_title' ) ) : ?>
		<div class="em-sbox em-sbox--help">
			<h2 class="em-sbox__title"><?php echo ementash_bdi( ementash_get( 'shop_help_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<?php if ( '' !== (string) ementash_get( 'shop_help_text' ) ) : ?>
				<p class="em-sbox__text"><?php echo ementash_bdi( ementash_get( 'shop_help_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php ementash_phone_link( $ementash_phone, 'em-sbox__phone' ); ?>
		</div>
	<?php endif; ?>
</aside>
