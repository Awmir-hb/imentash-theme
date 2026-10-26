<?php
/**
 * وبلاگ، جستجو و ۴۰۴: تعداد نوشته‌ها، دیدگاه‌ها، دامنه جستجو، مسیر صفحه، نوشته‌های مرتبط و CSS.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * آیا دیدگاه نوشته‌ها فعال است؟ پیش‌فرض خاموش (سایت کاتالوگ B2B). با فیلتر قابل تغییر.
 */
function ementash_comments_enabled() {
	return (bool) apply_filters( 'ementash_comments_enabled', false );
}

/**
 * دیدگاه و پینگ بسته؛ فرم چاپ نمی‌شود و wp-comments-post.php هم دیدگاه نمی‌پذیرد.
 */
function ementash_close_comments( $open ) {
	return ementash_comments_enabled() ? $open : false;
}
add_filter( 'comments_open', 'ementash_close_comments', 20 );
add_filter( 'pings_open', 'ementash_close_comments', 20 );

/**
 * نوع‌های پستی که جستجوی عمومی (بخش «مقاله‌ها و برگه‌ها») پوشش می‌دهد. محصولات جدا جستجو می‌شوند.
 */
function ementash_search_post_types() {
	return (array) apply_filters( 'ementash_search_post_types', array( 'post', 'page' ) );
}

/**
 * برگه‌های سیستمی ووکامرس (فروشگاه، سبد، تسویه‌حساب، حساب کاربری) در نتایج جستجو نمی‌آیند.
 */
function ementash_search_excluded_pages() {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return array();
	}
	$ids = array();
	foreach ( array( 'shop', 'cart', 'checkout', 'myaccount' ) as $page ) {
		$id = (int) wc_get_page_id( $page );
		if ( $id > 0 ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * کوئری اصلی وبلاگ و جستجو.
 * - ۹ نوشته در هر صفحه (۳ ردیف ۳تایی)، مستقل از «حداکثر نوشته‌ها در صفحه» تنظیمات خواندن.
 * - جستجوی عمومی فقط نوشته و برگه؛ ‎?post_type=product همان جستجوی ووکامرس با قالب فروشگاه است.
 */
function ementash_blog_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_search() ) {
		if ( $query->get( 'post_type' ) ) {
			return;
		}
		$query->set( 'post_type', ementash_search_post_types() );
		$query->set( 'post__not_in', ementash_search_excluded_pages() );
		$query->set( 'posts_per_page', 9 );
		return;
	}
	if ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date() ) {
		$query->set( 'posts_per_page', (int) apply_filters( 'ementash_posts_per_page', 9 ) );
	}
}
add_action( 'pre_get_posts', 'ementash_blog_query' );

/**
 * محصولات منطبق با عبارت جستجو در نام/متن یا کد محصول (SKU)؛ فقط محصولاتی که در جستجو دیده می‌شوند.
 * found_posts تعداد کل است؛ حداکثر $limit محصول برگردانده می‌شود.
 *
 * @return WP_Query
 */
function ementash_search_products( $term, $limit = 12 ) {
	$base = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => 200,
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);
	$ids  = get_posts( $base + array( 's' => $term ) );
	if ( function_exists( 'wc_get_products' ) ) {
		// ووکامرس sku را با LIKE جستجو می‌کند.
		$ids = array_merge(
			$ids,
			wc_get_products(
				array(
					'sku'    => $term,
					'status' => 'publish',
					'limit'  => 200,
					'return' => 'ids',
				)
			)
		);
	}
	$ids = array_values( array_unique( array_map( 'absint', $ids ) ) );

	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'post__in'            => $ids ? $ids : array( 0 ),
		'orderby'             => 'post__in',
		'posts_per_page'      => $limit,
		'ignore_sticky_posts' => true,
	);
	if ( function_exists( 'wc_get_product_visibility_term_ids' ) ) {
		$hidden = wc_get_product_visibility_term_ids();
		$terms  = array( $hidden['exclude-from-search'] );
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$terms[] = $hidden['outofstock'];
		}
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => array_filter( $terms ),
				'operator' => 'NOT IN',
			),
		);
	}
	return new WP_Query( $args );
}

/**
 * آدرس جستجوی کامل محصولات (قالب فروشگاه ووکامرس).
 */
function ementash_product_search_url( $term ) {
	return add_query_arg(
		array(
			's'         => rawurlencode( $term ),
			'post_type' => 'product',
		),
		home_url( '/' )
	);
}

/**
 * آدرس برگه نوشته‌ها (تنظیمات ← خواندن)؛ رشته خالی اگر تعیین نشده باشد.
 */
function ementash_blog_url() {
	$id = absint( get_option( 'page_for_posts' ) );
	return $id ? get_permalink( $id ) : '';
}

/**
 * عنوان برگه نوشته‌ها، یا «مقاله‌ها» اگر برگه‌ای تعیین نشده باشد.
 */
function ementash_blog_title() {
	$id = absint( get_option( 'page_for_posts' ) );
	return $id ? get_the_title( $id ) : __( 'مقاله‌ها', 'ementash' );
}

/**
 * دسته اصلی نوشته (اولین دسته).
 *
 * @return WP_Term|null
 */
function ementash_post_main_cat( $post_id ) {
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0] : null;
}

/**
 * مسیر صفحه وبلاگ: صفحه اصلی / مقاله‌ها / دسته‌های والد / فعلی.
 */
function ementash_blog_crumbs() {
	$crumbs = array(
		array(
			'label' => __( 'صفحه اصلی', 'ementash' ),
			'url'   => home_url( '/' ),
		),
	);
	if ( is_home() ) {
		$crumbs[] = array(
			'label' => ementash_blog_title(),
			'url'   => '',
		);
		return $crumbs;
	}
	$crumbs[] = array(
		'label' => ementash_blog_title(),
		'url'   => ementash_blog_url(),
	);

	$term = null;
	if ( is_category() || is_tag() ) {
		$term = get_queried_object();
	} elseif ( is_singular( 'post' ) ) {
		$term = ementash_post_main_cat( get_queried_object_id() );
	}
	if ( $term && is_taxonomy_hierarchical( $term->taxonomy ) ) {
		foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) ) as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $term->taxonomy );
			$crumbs[] = array(
				'label' => $ancestor->name,
				'url'   => get_term_link( $ancestor ),
			);
		}
	}
	if ( $term ) {
		$crumbs[] = array(
			'label' => $term->name,
			'url'   => is_singular() ? get_term_link( $term ) : '',
		);
	} elseif ( is_archive() ) {
		$crumbs[] = array(
			'label' => wp_strip_all_tags( get_the_archive_title() ),
			'url'   => '',
		);
	}
	if ( is_singular( 'post' ) ) {
		$crumbs[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	}
	return $crumbs;
}

/**
 * تا سه نوشته هم‌دسته؛ اگر کم بود با جدیدترین نوشته‌ها پر می‌شود.
 *
 * @return WP_Post[]
 */
function ementash_related_posts( $post_id, $limit = 3 ) {
	$base    = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'suppress_filters'    => false,
	);
	$exclude = array( $post_id );
	$posts   = array();
	$cats    = wp_get_post_categories( $post_id );
	if ( $cats ) {
		$posts = get_posts(
			$base + array(
				'posts_per_page' => $limit,
				'category__in'   => $cats,
				'post__not_in'   => $exclude,
			)
		);
	}
	if ( count( $posts ) < $limit ) {
		$exclude = array_merge( $exclude, wp_list_pluck( $posts, 'ID' ) );
		$posts   = array_merge(
			$posts,
			get_posts(
				$base + array(
					'posts_per_page' => $limit - count( $posts ),
					'post__not_in'   => $exclude,
				)
			)
		);
	}
	return $posts;
}

/**
 * آیا صفحه فعلی وبلاگ، جستجو یا ۴۰۴ با خروجی خود قالب است؟ (برای بارگذاری CSS)
 */
function ementash_is_blog_view() {
	if ( is_404() ) {
		return true;
	}
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return false;
	}
	if ( is_search() || ( is_home() && ! is_front_page() ) || is_category() || is_tag() || is_author() || is_date() ) {
		return true;
	}
	return is_singular( 'post' ) && ! ementash_is_elementor( get_queried_object_id() );
}

function ementash_blog_assets() {
	if ( ementash_is_blog_view() ) {
		wp_enqueue_style( 'ementash-blog', EMENTASH_URI . '/assets/css/blog.css', array( 'ementash-cards' ), ementash_asset_ver( 'assets/css/blog.css' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'ementash_blog_assets' );
