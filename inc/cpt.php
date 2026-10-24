<?php
/**
 * پست‌تایپ‌های قالب: خدمات، صنایع، مشتریان، خط زمانی، گواهینامه‌ها و درخواست‌های فرم تماس.
 *
 * هیچ‌کدام صفحه عمومی ندارند؛ محتوا فقط داخل قالب‌ها نمایش داده می‌شود. ترتیب با menu_order.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * تعریف پست‌تایپ‌های نمایشی (به‌جز em_inquiry).
 */
function ementash_post_types() {
	return array(
		'em_service'     => array(
			'singular' => __( 'خدمت', 'ementash' ),
			'plural'   => __( 'خدمات', 'ementash' ),
			'icon'     => 'dashicons-admin-tools',
			'supports' => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'thumb'    => __( 'تصویر کارت', 'ementash' ),
		),
		'em_industry'    => array(
			'singular' => __( 'صنعت', 'ementash' ),
			'plural'   => __( 'صنایع', 'ementash' ),
			'icon'     => 'dashicons-building',
			'supports' => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'thumb'    => __( 'تصویر کارت', 'ementash' ),
		),
		'em_client'      => array(
			'singular' => __( 'مشتری', 'ementash' ),
			'plural'   => __( 'مشتریان', 'ementash' ),
			'icon'     => 'dashicons-groups',
			'supports' => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'thumb'    => __( 'لوگو', 'ementash' ),
		),
		'em_timeline'    => array(
			'singular' => __( 'رویداد', 'ementash' ),
			'plural'   => __( 'خط زمانی', 'ementash' ),
			'icon'     => 'dashicons-backup',
			'supports' => array( 'title', 'page-attributes', 'custom-fields' ),
			'thumb'    => '',
		),
		'em_certificate' => array(
			'singular' => __( 'گواهینامه', 'ementash' ),
			'plural'   => __( 'گواهینامه‌ها', 'ementash' ),
			'icon'     => 'dashicons-awards',
			'supports' => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'thumb'    => __( 'تصویر گواهینامه', 'ementash' ),
		),
	);
}

function ementash_register_post_types() {
	$position = 21;
	foreach ( ementash_post_types() as $type => $def ) {
		$labels = array(
			'name'               => $def['plural'],
			'singular_name'      => $def['singular'],
			'menu_name'          => $def['plural'],
			'all_items'          => $def['plural'],
			/* translators: %s: نام مفرد */
			'add_new'            => sprintf( __( 'افزودن %s', 'ementash' ), $def['singular'] ),
			/* translators: %s: نام مفرد */
			'add_new_item'       => sprintf( __( 'افزودن %s', 'ementash' ), $def['singular'] ),
			/* translators: %s: نام مفرد */
			'edit_item'          => sprintf( __( 'ویرایش %s', 'ementash' ), $def['singular'] ),
			/* translators: %s: نام مفرد */
			'new_item'           => sprintf( __( '%s جدید', 'ementash' ), $def['singular'] ),
			/* translators: %s: نام جمع */
			'search_items'       => sprintf( __( 'جستجوی %s', 'ementash' ), $def['plural'] ),
			'not_found'          => __( 'موردی پیدا نشد.', 'ementash' ),
			'not_found_in_trash' => __( 'موردی در زباله‌دان نیست.', 'ementash' ),
		);
		if ( $def['thumb'] ) {
			$labels['featured_image']        = $def['thumb'];
			/* translators: %s: نام تصویر شاخص */
			$labels['set_featured_image']    = sprintf( __( 'انتخاب %s', 'ementash' ), $def['thumb'] );
			/* translators: %s: نام تصویر شاخص */
			$labels['remove_featured_image'] = sprintf( __( 'حذف %s', 'ementash' ), $def['thumb'] );
		}

		register_post_type(
			$type,
			array(
				'labels'              => $labels,
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'hierarchical'        => false,
				'menu_icon'           => $def['icon'],
				'menu_position'       => $position++,
				'supports'            => $def['supports'],
			)
		);
	}

	register_post_type(
		'em_inquiry',
		array(
			'labels'              => array(
				'name'          => __( 'درخواست‌ها', 'ementash' ),
				'singular_name' => __( 'درخواست', 'ementash' ),
				'menu_name'     => __( 'درخواست‌های تماس', 'ementash' ),
				'all_items'     => __( 'درخواست‌ها', 'ementash' ),
				'edit_item'     => __( 'جزئیات درخواست', 'ementash' ),
				'search_items'  => __( 'جستجوی درخواست‌ها', 'ementash' ),
				'not_found'     => __( 'هنوز درخواستی ثبت نشده است.', 'ementash' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			// داده شخصی است؛ در REST منتشر نمی‌شود.
			'show_in_rest'        => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			// درخواست فقط از فرم سایت ساخته می‌شود.
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
		)
	);
}
add_action( 'init', 'ementash_register_post_types' );

/**
 * انواع درخواست فرم تماس.
 */
function ementash_inquiry_types() {
	return array(
		'price'     => __( 'استعلام قیمت', 'ementash' ),
		'consult'   => __( 'مشاوره فنی', 'ementash' ),
		'emergency' => __( 'ارسال اضطراری', 'ementash' ),
	);
}

/**
 * فیلدهای ذخیره‌شده هر درخواست (متای _ementash_inquiry_*).
 */
function ementash_inquiry_fields() {
	return array(
		'type'    => __( 'نوع درخواست', 'ementash' ),
		'name'    => __( 'نام و نام خانوادگی', 'ementash' ),
		'company' => __( 'نام شرکت', 'ementash' ),
		'phone'   => __( 'شماره تماس', 'ementash' ),
		'email'   => __( 'ایمیل', 'ementash' ),
		'product' => __( 'محصول', 'ementash' ),
		'message' => __( 'محصول و مقدار مورد نیاز', 'ementash' ),
		'ip'      => __( 'آی‌پی', 'ementash' ),
	);
}

function ementash_inquiry_meta( $post_id, $field ) {
	return (string) get_post_meta( $post_id, '_ementash_inquiry_' . $field, true );
}

/**
 * ترتیب پیش‌فرض فهرست پیشخوان پست‌تایپ‌های نمایشی بر اساس menu_order.
 */
function ementash_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || $query->get( 'orderby' ) ) {
		return;
	}
	if ( array_key_exists( (string) $query->get( 'post_type' ), ementash_post_types() ) ) {
		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			)
		);
	}
}
add_action( 'pre_get_posts', 'ementash_admin_order' );

/**
 * ستون‌های تصویر و ترتیب در فهرست پیشخوان.
 */
function ementash_admin_columns( $columns ) {
	$screen = get_current_screen();
	$out    = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key && $screen && post_type_supports( $screen->post_type, 'thumbnail' ) ) {
			$out['em_thumb'] = __( 'تصویر', 'ementash' );
		}
		$out[ $key ] = $label;
	}
	$out['em_order'] = __( 'ترتیب', 'ementash' );
	return $out;
}

function ementash_admin_column_content( $column, $post_id ) {
	if ( 'em_thumb' === $column ) {
		echo get_the_post_thumbnail( $post_id, array( 48, 48 ) );
	} elseif ( 'em_order' === $column ) {
		echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
	}
}

/**
 * ستون‌های فهرست درخواست‌ها.
 */
function ementash_inquiry_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'title'      => __( 'عنوان', 'ementash' ),
		'em_type'    => __( 'نوع', 'ementash' ),
		'em_name'    => __( 'نام', 'ementash' ),
		'em_company' => __( 'شرکت', 'ementash' ),
		'em_phone'   => __( 'تلفن', 'ementash' ),
		'date'       => $columns['date'],
	);
}

function ementash_inquiry_column_content( $column, $post_id ) {
	if ( 'em_type' === $column ) {
		$types = ementash_inquiry_types();
		$type  = ementash_inquiry_meta( $post_id, 'type' );
		echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : $type );
	} elseif ( in_array( $column, array( 'em_name', 'em_company', 'em_phone' ), true ) ) {
		echo esc_html( ementash_inquiry_meta( $post_id, substr( $column, 3 ) ) );
	}
}

function ementash_register_admin_columns() {
	foreach ( array_keys( ementash_post_types() ) as $type ) {
		add_filter( "manage_{$type}_posts_columns", 'ementash_admin_columns' );
		add_action( "manage_{$type}_posts_custom_column", 'ementash_admin_column_content', 10, 2 );
	}
	add_filter( 'manage_em_inquiry_posts_columns', 'ementash_inquiry_columns' );
	add_action( 'manage_em_inquiry_posts_custom_column', 'ementash_inquiry_column_content', 10, 2 );
}
add_action( 'admin_init', 'ementash_register_admin_columns' );

/**
 * درخواست‌ها فقط‌خواندنی‌اند: «ویرایش سریع» حذف می‌شود.
 */
function ementash_inquiry_row_actions( $actions, $post ) {
	if ( 'em_inquiry' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'] );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'ementash_inquiry_row_actions', 10, 2 );
