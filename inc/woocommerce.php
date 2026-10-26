<?php
/**
 * ووکامرس در حالت کاتالوگ: بدون قیمت، خرید، سبد، تسویه‌حساب و حساب کاربری.
 * به‌علاوه تنظیمات آرشیو (تعداد، ترتیب)، ویژگی سراسری pa_fire_class و توابع کمکی قالب‌های محصول.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* ---------- حالت کاتالوگ ---------- */

// هیچ محصولی قابل خرید نیست؛ دکمه افزودن به سبد در هیچ قالب، ویجت یا بلوکی ساخته نمی‌شود.
add_filter( 'woocommerce_is_purchasable', '__return_false' );
add_filter( 'woocommerce_variation_is_purchasable', '__return_false' );
add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string' );

// قیمت هیچ‌جا نمایش داده نمی‌شود.
add_filter( 'woocommerce_get_price_html', '__return_empty_string' );
add_filter( 'woocommerce_variable_price_html', '__return_empty_string' );
add_filter( 'woocommerce_variation_price_html', '__return_empty_string' );
add_filter( 'woocommerce_show_variation_price', '__return_false' );

/**
 * حذف خروجی‌های پیش‌فرض ووکامرس که در قالب‌های افزونه‌ها یا المنتور صدا زده می‌شوند.
 */
function ementash_wc_remove_shop_parts() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
}
// روی init تا در شورت‌کدها، بلوک‌ها و محتوایی که پیش از wp ساخته می‌شود هم اعمال شود.
add_action( 'init', 'ementash_wc_remove_shop_parts', 99 );

/**
 * شورت‌کدهای دکمه خرید ووکامرس خروجی ندارند.
 */
function ementash_wc_disable_cart_shortcodes() {
	foreach ( array( 'add_to_cart', 'add_to_cart_url' ) as $tag ) {
		remove_shortcode( $tag );
		add_shortcode( $tag, '__return_empty_string' );
	}
}
add_action( 'init', 'ementash_wc_disable_cart_shortcodes', 99 );

/**
 * بلوک‌های قیمت، دکمه خرید، سبد، مینی‌سبد و تسویه‌حساب هیچ خروجی ندارند.
 */
function ementash_wc_hide_blocks( $content, $block ) {
	$blocked = array(
		'woocommerce/product-price',
		'woocommerce/product-button',
		'woocommerce/add-to-cart-form',
		'woocommerce/product-add-to-cart',
		'woocommerce/mini-cart',
		'woocommerce/cart',
		'woocommerce/checkout',
		'woocommerce/customer-account',
		'woocommerce/price-filter',
		'woocommerce/filter-wrapper',
	);
	return isset( $block['blockName'] ) && in_array( $block['blockName'], $blocked, true ) ? '' : $content;
}
add_filter( 'render_block', 'ementash_wc_hide_blocks', 10, 2 );

/**
 * ویجت‌های کلاسیک سبد و فیلتر قیمت ثبت نمی‌شوند.
 */
function ementash_wc_unregister_widgets() {
	unregister_widget( 'WC_Widget_Cart' );
	unregister_widget( 'WC_Widget_Price_Filter' );
}
add_action( 'widgets_init', 'ementash_wc_unregister_widgets', 15 );

/**
 * سبد خرید، تسویه‌حساب و حساب کاربری به فروشگاه ریدایرکت می‌شوند.
 */
function ementash_wc_redirect_shop_pages() {
	if ( is_cart() || is_checkout() || is_account_page() ) {
		wp_safe_redirect( ementash_shop_url(), 302 );
		exit;
	}
}
add_action( 'template_redirect', 'ementash_wc_redirect_shop_pages' );

/**
 * قالب ظاهر خودش را دارد: CSS پیش‌فرض و اسکریپت‌های سبد ووکامرس بارگذاری نمی‌شوند.
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * در حالت کاتالوگ اسکریپت‌های سبد، تسویه‌حساب و ردیابی منبع سفارش کاری ندارند. با حذف آن‌ها jQuery هم
 * (اگر افزونه دیگری لازمش نداشته باشد) بارگذاری نمی‌شود. اسکریپت‌های قالب به jQuery وابسته نیستند.
 * گالری محصول ووکامرس هم استفاده نمی‌شود (قالب گالری خودش را دارد).
 */
function ementash_wc_dequeue_scripts() {
	$handles = array(
		'wc-cart-fragments',
		'wc-add-to-cart',
		'woocommerce',
		'wc-single-product',
		'wc-jquery-blockui',
		'jquery-blockui',
		'wc-js-cookie',
		'js-cookie',
		'sourcebuster-js',
		'wc-order-attribution',
	);
	foreach ( $handles as $handle ) {
		wp_dequeue_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'ementash_wc_dequeue_scripts', 99 );

/* ---------- آرشیو: تعداد و ترتیب ---------- */

// ۹ محصول در هر صفحه (۳ ردیف ۳تایی مطابق طراحی).
add_filter(
	'loop_shop_per_page',
	static function () {
		return 9;
	},
	20
);

/**
 * فقط سه ترتیب: پیش‌فرض (ترتیب دستی پیشخوان)، جدیدترین، نام. قیمت، محبوبیت و امتیاز حذف می‌شوند.
 */
function ementash_wc_orderby_options() {
	return array(
		'menu_order' => __( 'پیش‌فرض', 'ementash' ),
		'date'       => __( 'جدیدترین', 'ementash' ),
		'title'      => __( 'نام محصول', 'ementash' ),
	);
}
add_filter( 'woocommerce_catalog_orderby', 'ementash_wc_orderby_options' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'ementash_wc_orderby_options' );
add_filter(
	'woocommerce_default_catalog_orderby',
	static function () {
		return 'menu_order';
	}
);

/**
 * ترتیب فعلی آرشیو؛ مقدار ناشناخته یعنی پیش‌فرض.
 */
function ementash_wc_current_orderby() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- فقط خواندن ترتیب نمایش.
	$value = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';
	return array_key_exists( $value, ementash_wc_orderby_options() ) ? $value : 'menu_order';
}

/* ---------- کلاس آتش (ویژگی سراسری pa_fire_class) ---------- */

/**
 * ساخت ویژگی «کلاس آتش» با مقادیر A تا D، فقط اگر وجود نداشته باشد (هنگام فعال‌سازی قالب).
 * برچسب هر کلاس (جامدات، مایعات...) توضیح همان مقدار است و در پیشخوان قابل ویرایش است.
 */
function ementash_wc_ensure_fire_class() {
	if ( ! function_exists( 'wc_create_attribute' ) ) {
		return;
	}
	if ( ! wc_attribute_taxonomy_id_by_name( 'fire_class' ) ) {
		$id = wc_create_attribute(
			array(
				'name'         => __( 'کلاس آتش', 'ementash' ),
				'slug'         => 'fire_class',
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			)
		);
		if ( is_wp_error( $id ) ) {
			return;
		}
	}
	if ( ! taxonomy_exists( 'pa_fire_class' ) ) {
		register_taxonomy( 'pa_fire_class', 'product', array( 'hierarchical' => false ) );
	}
	$classes = array(
		'A' => __( 'جامدات', 'ementash' ),
		'B' => __( 'مایعات', 'ementash' ),
		'C' => __( 'گازها', 'ementash' ),
		'D' => __( 'فلزات', 'ementash' ),
	);
	foreach ( $classes as $name => $label ) {
		if ( ! term_exists( strtolower( $name ), 'pa_fire_class' ) ) {
			wp_insert_term(
				$name,
				'pa_fire_class',
				array(
					'slug'        => strtolower( $name ),
					'description' => $label,
				)
			);
		}
	}
}
add_action( 'after_switch_theme', 'ementash_wc_ensure_fire_class' );

/**
 * کلاس‌های آتش یک محصول: آرایه‌ای از name (A) و label (جامدات).
 */
function ementash_wc_fire_classes( $product_id ) {
	$terms = taxonomy_exists( 'pa_fire_class' ) ? wc_get_product_terms( $product_id, 'pa_fire_class', array( 'fields' => 'all' ) ) : array();
	$out   = array();
	foreach ( $terms as $term ) {
		$out[] = array(
			'name'  => $term->name,
			'label' => wp_strip_all_tags( $term->description ),
		);
	}
	return $out;
}

/* ---------- توابع کمکی قالب‌های محصول ---------- */

/**
 * لینک «استعلام قیمت این محصول»: صفحه تماس با ?product=ID (فرم در فاز ۵ نام محصول را پر می‌کند).
 */
function ementash_product_inquiry_url( $product_id ) {
	return add_query_arg( 'product', absint( $product_id ), ementash_contact_url() );
}

/**
 * دسته اصلی محصول (اولین دسته؛ دسته پیش‌فرض «دسته‌بندی نشده» فقط اگر تنها دسته باشد).
 *
 * @return WP_Term|null
 */
function ementash_product_main_cat( $product_id ) {
	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	$default = absint( get_option( 'default_product_cat' ) );
	foreach ( $terms as $term ) {
		if ( (int) $term->term_id !== $default ) {
			return $term;
		}
	}
	return $terms[0];
}

/**
 * مسیر صفحه برای فروشگاه، دسته و محصول: صفحه اصلی / محصولات / دسته‌های والد / فعلی.
 */
function ementash_shop_crumbs() {
	$crumbs = array(
		array(
			'label' => __( 'صفحه اصلی', 'ementash' ),
			'url'   => home_url( '/' ),
		),
	);
	$shop   = array(
		'label' => get_the_title( wc_get_page_id( 'shop' ) ) ? get_the_title( wc_get_page_id( 'shop' ) ) : __( 'محصولات', 'ementash' ),
		'url'   => ementash_shop_url(),
	);

	if ( is_shop() && ! is_search() ) {
		$shop['url'] = '';
		$crumbs[]    = $shop;
		return $crumbs;
	}
	$crumbs[] = $shop;

	$term = null;
	if ( is_product_category() ) {
		$term = get_queried_object();
	} elseif ( is_product() ) {
		$term = ementash_product_main_cat( get_the_ID() );
	}
	if ( $term ) {
		foreach ( array_reverse( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) ) as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, 'product_cat' );
			$crumbs[] = array(
				'label' => $ancestor->name,
				'url'   => get_term_link( $ancestor ),
			);
		}
		$crumbs[] = array(
			'label' => $term->name,
			'url'   => is_product() ? get_term_link( $term ) : '',
		);
	}
	if ( is_product() ) {
		$crumbs[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	}
	return $crumbs;
}

/**
 * CSS و JS فروشگاه فقط در صفحه‌های ووکامرس (نه صفحه‌ای که با المنتور ساخته شده).
 */
function ementash_wc_assets() {
	if ( ! is_woocommerce() || ( is_product() && ementash_is_elementor( get_queried_object_id() ) ) ) {
		return;
	}
	wp_enqueue_style( 'ementash-shop', EMENTASH_URI . '/assets/css/shop.css', array( 'ementash-cards' ), ementash_asset_ver( 'assets/css/shop.css' ) );
	wp_enqueue_script(
		'ementash-shop',
		EMENTASH_URI . '/assets/js/shop.js',
		array(),
		ementash_asset_ver( 'assets/js/shop.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ementash_wc_assets' );
