<?php
/**
 * نوار بالای فهرست محصولات: عنوان بخش فعلی، تعداد و مرتب‌سازی (پیش‌فرض، جدیدترین، نام).
 * فرم با GET به صفحه اول همین بخش می‌رود؛ ارسال خودکار با تغییر در assets/js/shop.js.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$ementash_term  = is_product_category() ? get_queried_object() : null;
$ementash_title = $ementash_term ? $ementash_term->name : __( 'همه محصولات', 'ementash' );
$ementash_found = (int) $wp_query->found_posts;
$ementash_base  = $ementash_term ? get_term_link( $ementash_term ) : ementash_shop_url();
$ementash_sort  = ementash_wc_current_orderby();
?>
<div class="em-shop__bar">
	<div>
		<h2 class="em-shop__title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<p class="em-shop__count">
			<?php
			/* translators: %s: تعداد محصولات */
			echo esc_html( sprintf( _n( '%s محصول', '%s محصول', $ementash_found, 'ementash' ), ementash_fa_num( $ementash_found ) ) );
			?>
		</p>
	</div>

	<?php if ( $ementash_found > 1 ) : ?>
		<form class="em-sort" method="get" action="<?php echo esc_url( $ementash_base ); ?>" data-em-sort>
			<label for="em-sort-select"><?php esc_html_e( 'مرتب‌سازی', 'ementash' ); ?></label>
			<select id="em-sort-select" name="orderby">
				<?php foreach ( ementash_wc_orderby_options() as $ementash_key => $ementash_label ) : ?>
					<option value="<?php echo esc_attr( $ementash_key ); ?>"<?php selected( $ementash_sort, $ementash_key ); ?>><?php echo esc_html( $ementash_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php
			// جستجوی محصول هم مرتب شود.
			if ( is_search() ) {
				printf( '<input type="hidden" name="s" value="%s">', esc_attr( get_search_query() ) );
				echo '<input type="hidden" name="post_type" value="product">';
			}
			?>
			<button type="submit" class="em-sort__go" data-em-sort-go><?php esc_html_e( 'اعمال', 'ementash' ); ?></button>
		</form>
	<?php endif; ?>
</div>
