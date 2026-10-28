<?php
/**
 * صنایع: برگه‌هایی با قالب «صنعت» (templates/industry.php) و اتصال محصولات به آن‌ها.
 *
 * صنعت از دسته محصول جداست: هر محصول در صفحه ویرایشش، در کادر «مناسب برای این صنایع»،
 * برگه‌های صنعت را تیک می‌زند. هر انتخاب یک ردیف متای _ementash_industry (شناسه برگه) است
 * تا برگه صنعت محصولاتش را مستقیم با meta_query پیدا کند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * فایل قالب برگه صنعت.
 */
function ementash_industry_template() {
	return 'templates/industry.php';
}

/**
 * برگه‌های صنعت (منتشرشده یا پیش‌نویس) به ترتیب menu_order.
 *
 * @return WP_Post[]
 */
function ementash_industry_pages() {
	return get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => -1,
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => ementash_industry_template(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

/**
 * محصولات منتشرشده و قابل نمایش در فهرست که برای این صنعت تیک خورده‌اند.
 *
 * @return WP_Post[]
 */
function ementash_industry_products( $page_id ) {
	$page_id = absint( $page_id );
	if ( ! $page_id || ! post_type_exists( 'product' ) ) {
		return array();
	}
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => '_ementash_industry',
				'value' => $page_id,
				'type'  => 'NUMERIC',
			),
		),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	);
	if ( function_exists( 'wc_get_product_visibility_term_ids' ) ) {
		$hidden            = wc_get_product_visibility_term_ids();
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => array( $hidden['exclude-from-catalog'] ),
				'operator' => 'NOT IN',
			),
		);
	}
	return get_posts( $args );
}

/* ---------- کادر «مناسب برای این صنایع» در صفحه ویرایش محصول ---------- */

function ementash_industry_add_meta_box() {
	add_meta_box(
		'ementash-industries',
		__( 'مناسب برای این صنایع', 'ementash' ),
		'ementash_industry_render_meta_box',
		'product',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes_product', 'ementash_industry_add_meta_box' );

function ementash_industry_render_meta_box( $post ) {
	$pages = ementash_industry_pages();
	wp_nonce_field( 'ementash_industries', 'ementash_industries_nonce' );

	if ( ! $pages ) {
		echo '<p class="description">' . esc_html__( 'هنوز برگه‌ای با قالب «صنعت» ساخته نشده است.', 'ementash' ) . '</p>';
		return;
	}

	$selected = array_map( 'absint', get_post_meta( $post->ID, '_ementash_industry' ) );
	echo '<ul class="em-mb__checks">';
	foreach ( $pages as $page ) {
		printf(
			'<li><label><input type="checkbox" name="ementash_industries[]" value="%1$d"%2$s> %3$s</label></li>',
			(int) $page->ID,
			checked( in_array( (int) $page->ID, $selected, true ), true, false ),
			esc_html( get_the_title( $page ) )
		);
	}
	echo '</ul>';
	echo '<p class="description">' . esc_html__( 'محصول در برگه صنایع تیک‌خورده نمایش داده می‌شود. صنعت از دسته محصول جداست.', 'ementash' ) . '</p>';
}

function ementash_industry_save( $post_id ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	$nonce = isset( $_POST['ementash_industries_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ementash_industries_nonce'] ) ) : '';
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ementash_industries' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$posted = isset( $_POST['ementash_industries'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ementash_industries'] ) ) : array();
	// فقط شناسه برگه‌های صنعت پذیرفته می‌شود.
	$valid = array_intersect( array_unique( $posted ), array_map( 'intval', wp_list_pluck( ementash_industry_pages(), 'ID' ) ) );

	delete_post_meta( $post_id, '_ementash_industry' );
	foreach ( $valid as $page_id ) {
		add_post_meta( $post_id, '_ementash_industry', (int) $page_id );
	}
}
add_action( 'save_post_product', 'ementash_industry_save' );

/**
 * وقتی برگه صنعت برای همیشه حذف شد، اتصال محصولات به آن هم پاک شود.
 */
function ementash_industry_cleanup( $post_id ) {
	if ( 'page' === get_post_type( $post_id ) ) {
		delete_metadata( 'post', 0, '_ementash_industry', (int) $post_id, true );
	}
}
add_action( 'before_delete_post', 'ementash_industry_cleanup' );

/* ---------- نمایش ---------- */

/**
 * شورت‌کد [ementash_industry_products] برای برگه صنعتی که با المنتور ساخته شده است.
 * id اختیاری؛ پیش‌فرض برگه فعلی. اگر محصولی نباشد چیزی چاپ نمی‌شود.
 */
function ementash_industry_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'ementash_industry_products' );
	$id   = absint( $atts['id'] ) ? absint( $atts['id'] ) : get_the_ID();
	ob_start();
	get_template_part( 'template-parts/industry/products', null, array( 'page_id' => $id ) );
	return ob_get_clean();
}
add_shortcode( 'ementash_industry_products', 'ementash_industry_shortcode' );

/**
 * CSS کارت محصول در برگه صنعت (برگه المنتوری فقط وقتی شورت‌کد دارد).
 */
function ementash_industry_assets() {
	if ( ! is_page() ) {
		return;
	}
	$post = get_queried_object();
	if ( is_page_template( ementash_industry_template() ) || ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'ementash_industry_products' ) ) ) {
		wp_enqueue_style( 'ementash-cards' );
	}
}
add_action( 'wp_enqueue_scripts', 'ementash_industry_assets', 20 );
