<?php
/**
 * توابع کمکی: تنظیمات، اعداد فارسی، آیکون‌ها، لوگو، شبکه‌های اجتماعی و زبان.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * مقادیر پیش‌فرض تنظیمات قالب (از تعریف فیلدها در inc/customizer.php). فقط برای دمو.
 */
function ementash_option_defaults() {
	return wp_list_pluck( ementash_customizer_fields(), 'default' );
}

/**
 * خواندن یک تنظیم قالب با مقدار پیش‌فرض. متن‌ها در صورت فعال بودن Polylang ترجمه می‌شوند.
 */
function ementash_get( $key ) {
	$fields = ementash_customizer_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return get_theme_mod( $key, '' );
	}
	$value = get_theme_mod( $key, $fields[ $key ]['default'] );
	if ( function_exists( 'pll__' ) && is_string( $value ) && '' !== $value && in_array( $fields[ $key ]['type'], array( 'text', 'textarea', 'lines' ), true ) ) {
		$value = pll__( $value );
	}
	return $value;
}

/**
 * تنظیمی از نوع «هر خط یک مورد» به‌صورت آرایه.
 */
function ementash_get_lines( $key ) {
	// پرچم u لازم است؛ بدون آن \R بایت 0x85 داخل حروف فارسی (مثل «م») را خط جدید حساب می‌کند.
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', (string) ementash_get( $key ) ) ), 'strlen' ) );
}

/**
 * آیا سکشن صفحه اصلی در Customizer روشن است؟ (شناسه‌ها در ementash_home_sections)
 */
function ementash_section_enabled( $id ) {
	return (bool) ementash_get( ementash_section_key( $id ) );
}

/**
 * تبدیل ارقام لاتین به فارسی؛ فقط وقتی زبان سایت فارسی است.
 */
function ementash_fa_num( $value ) {
	$value = (string) $value;
	if ( ! apply_filters( 'ementash_use_fa_digits', 0 === strpos( get_locale(), 'fa' ) ) ) {
		return $value;
	}
	return strtr( $value, array_combine( range( 0, 9 ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ) ) );
}

/**
 * لینک tel: از شماره ذخیره‌شده (ارقام فارسی هم پذیرفته می‌شود).
 */
function ementash_tel_href( $phone ) {
	$phone = strtr( (string) $phone, array_combine( array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), range( 0, 9 ) ) );
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * شماره‌های یک دفتر (isfahan، tehran، factory)، هر خط یک شماره.
 */
function ementash_phones( $office ) {
	return ementash_get_lines( 'office_' . $office . '_phones' );
}

/**
 * شماره اصلی سایت = اولین شماره دفتر اصفهان.
 */
function ementash_main_phone() {
	$phones = ementash_phones( 'isfahan' );
	return $phones ? $phones[0] : '';
}

/**
 * چاپ لینک تلفن با ارقام فارسی.
 */
function ementash_phone_link( $phone, $class = '' ) {
	if ( '' === $phone ) {
		return;
	}
	printf(
		'<a href="%1$s"%2$s dir="ltr">%3$s</a>',
		esc_url( ementash_tel_href( $phone ), array( 'tel' ) ),
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		esc_html( ementash_fa_num( $phone ) )
	);
}

/**
 * شناسه برگه ویژه (contact یا about): برگه انتخاب‌شده در تنظیمات، وگرنه برگه با همان نامک.
 */
function ementash_page_id( $slug ) {
	$id = absint( ementash_get( $slug . '_page' ) );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		return $id;
	}
	$page = get_page_by_path( $slug );
	return $page ? (int) $page->ID : 0;
}

/**
 * آدرس صفحه تماس؛ اگر برگه‌ای پیدا نشد صفحه اصلی.
 */
function ementash_contact_url() {
	$id = ementash_page_id( 'contact' );
	return $id ? get_permalink( $id ) : home_url( '/' );
}

/**
 * آدرس صفحه درباره ما؛ اگر برگه‌ای پیدا نشد صفحه اصلی.
 */
function ementash_about_url() {
	$id = ementash_page_id( 'about' );
	return $id ? get_permalink( $id ) : home_url( '/' );
}

/**
 * آیکون SVG خطی از assets/icons، به‌صورت inline.
 */
function ementash_icon( $name, $class = '' ) {
	static $cache = array();

	$name = sanitize_key( $name );
	if ( ! isset( $cache[ $name ] ) ) {
		$file           = EMENTASH_DIR . '/assets/icons/' . $name . '.svg';
		$svg            = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		// kses نام ویژگی‌ها را کوچک می‌کند؛ viewBox برگردانده می‌شود.
		$cache[ $name ] = str_replace( ' viewbox=', ' viewBox=', trim( wp_kses( $svg, ementash_svg_allowed() ) ) );
	}
	if ( '' === $cache[ $name ] ) {
		return '';
	}

	return preg_replace(
		'/^<svg\b/',
		'<svg class="' . esc_attr( trim( 'ic ' . $class ) ) . '" aria-hidden="true" focusable="false"',
		$cache[ $name ],
		1
	);
}

/**
 * لیست ثابت آیکون‌هایی که در پنل قابل انتخاب‌اند (فایل‌ها در assets/icons).
 *
 * @param string $set stat (آمار)، service (خدمات) یا card (کارت‌های درباره ما).
 */
function ementash_icon_choices( $set ) {
	$sets = array(
		'stat'    => array(
			'calendar' => __( 'تقویم', 'ementash' ),
			'factory'  => __( 'کارخانه', 'ementash' ),
			'grid'     => __( 'دسته‌ها', 'ementash' ),
			'shield'   => __( 'سپر', 'ementash' ),
		),
		'service' => array(
			'truck' => __( 'کامیون', 'ementash' ),
			'lab'   => __( 'آزمایشگاه', 'ementash' ),
			'chat'  => __( 'گفتگو', 'ementash' ),
			'train' => __( 'آموزش', 'ementash' ),
		),
		'card'    => array(
			'foam'     => __( 'فوم', 'ementash' ),
			'powder'   => __( 'پودر (کپسول)', 'ementash' ),
			'antifoam' => __( 'ضدکف', 'ementash' ),
			'drill'    => __( 'حفاری', 'ementash' ),
			'concrete' => __( 'بتن', 'ementash' ),
			'lab'      => __( 'آزمایشگاه', 'ementash' ),
			'message'  => __( 'گفتگو', 'ementash' ),
			'check'    => __( 'تیک', 'ementash' ),
			'scale'    => __( 'ترازو', 'ementash' ),
			'lock'     => __( 'قفل', 'ementash' ),
			'shield'   => __( 'سپر', 'ementash' ),
		),
	);
	return isset( $sets[ $set ] ) ? $sets[ $set ] : array();
}

function ementash_svg_allowed() {
	$shape = array(
		'd'         => true,
		'x'         => true,
		'y'         => true,
		'cx'        => true,
		'cy'        => true,
		'r'         => true,
		'rx'        => true,
		'ry'        => true,
		'width'     => true,
		'height'    => true,
		'points'    => true,
		'x1'        => true,
		'x2'        => true,
		'y1'        => true,
		'y2'        => true,
		'transform' => true,
	);
	return array(
		'svg'      => array(
			'xmlns'   => true,
			'viewbox' => true,
			'width'   => true,
			'height'  => true,
		),
		'g'        => array( 'transform' => true ),
		'path'     => $shape,
		'rect'     => $shape,
		'circle'   => $shape,
		'ellipse'  => $shape,
		'line'     => $shape,
		'polyline' => $shape,
	);
}

/**
 * لوگوی سایت داخل قرص سفید؛ اگر لوگو تنظیم نشده باشد نام سایت نمایش داده می‌شود.
 */
function ementash_logo( $class = '' ) {
	$logo_id = absint( get_theme_mod( 'custom_logo' ) );
	$name    = get_bloginfo( 'name' );

	echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="' . esc_attr( trim( 'em-logo ' . $class ) ) . '" rel="home">';
	if ( $logo_id ) {
		echo wp_get_attachment_image(
			$logo_id,
			'medium',
			false,
			array(
				'class'    => 'em-logo__img',
				'alt'      => $name,
				'loading'  => 'eager',
				'decoding' => 'async',
			)
		);
	} else {
		echo '<span class="em-logo__text">' . esc_html( $name ) . '</span>';
	}
	echo '</a>';
}

/**
 * شبکه‌های اجتماعی پرشده در تنظیمات.
 */
function ementash_socials() {
	$labels = array(
		'instagram' => __( 'اینستاگرام', 'ementash' ),
		'linkedin'  => __( 'لینکدین', 'ementash' ),
		'telegram'  => __( 'تلگرام', 'ementash' ),
		'whatsapp'  => __( 'واتس‌اپ', 'ementash' ),
	);
	$items  = array();
	foreach ( $labels as $key => $label ) {
		$url = (string) ementash_get( 'social_' . $key );
		if ( '' !== $url ) {
			$items[ $key ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}
	return $items;
}

function ementash_social_links( $class = '' ) {
	$items = ementash_socials();
	if ( ! $items ) {
		return;
	}
	echo '<ul class="' . esc_attr( trim( 'em-soc ' . $class ) ) . '">';
	foreach ( $items as $key => $item ) {
		printf(
			'<li><a href="%1$s" aria-label="%2$s" target="_blank" rel="noopener">%3$s</a></li>',
			esc_url( $item['url'] ),
			esc_attr( $item['label'] ),
			ementash_icon( $key ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG با wp_kses پاک‌سازی شده.
		);
	}
	echo '</ul>';
}

/**
 * سوییچ زبان؛ فقط وقتی Polylang فعال است و بیش از یک زبان دارد.
 *
 * @param string $style short (فا / EN) یا full (فارسی / English).
 */
function ementash_lang_switcher( $style = 'short', $class = '' ) {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}
	$langs = pll_the_languages(
		array(
			'raw'           => 1,
			'hide_if_empty' => 0,
		)
	);
	if ( ! is_array( $langs ) || count( $langs ) < 2 ) {
		return;
	}

	echo '<div class="' . esc_attr( trim( 'em-lang ' . $class ) ) . '">';
	foreach ( $langs as $lang ) {
		if ( 'full' === $style ) {
			$label = $lang['name'];
		} else {
			$label = 'fa' === $lang['slug'] ? _x( 'فا', 'نام کوتاه زبان فارسی', 'ementash' ) : strtoupper( $lang['slug'] );
		}
		printf(
			'<a href="%1$s" lang="%2$s" hreflang="%2$s"%3$s>%4$s</a>',
			esc_url( $lang['url'] ),
			esc_attr( $lang['slug'] ),
			! empty( $lang['current_lang'] ) ? ' aria-current="true"' : '',
			esc_html( $label )
		);
	}
	echo '</div>';
}

/**
 * مسیر صفحه (breadcrumb) برای برگه‌ها: صفحه اصلی / والدها / برگه فعلی.
 */
function ementash_page_crumbs( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$crumbs  = array(
		array(
			'label' => __( 'صفحه اصلی', 'ementash' ),
			'url'   => home_url( '/' ),
		),
	);
	foreach ( array_reverse( get_post_ancestors( $post_id ) ) as $ancestor ) {
		$crumbs[] = array(
			'label' => get_the_title( $ancestor ),
			'url'   => get_permalink( $ancestor ),
		);
	}
	$crumbs[] = array(
		'label' => get_the_title( $post_id ),
		'url'   => '',
	);
	return $crumbs;
}
