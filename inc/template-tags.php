<?php
/**
 * توابع کمکی: تنظیمات، اعداد فارسی، آیکون‌ها، لوگو، شبکه‌های اجتماعی و زبان.
 *
 * @package imentash
 */

defined( 'ABSPATH' ) || exit;

/**
 * مقادیر پیش‌فرض تنظیمات قالب. فقط برای دمو؛ مقدار واقعی از Customizer می‌آید.
 */
function imentash_option_defaults() {
	return array(
		'office_isfahan_label'   => __( 'اصفهان', 'imentash' ),
		'office_tehran_label'    => __( 'تهران', 'imentash' ),
		'office_factory_label'   => __( 'کارخانه', 'imentash' ),
		'main_phone_label'       => __( 'دفتر مرکزی اصفهان', 'imentash' ),
		'quote_label'            => __( 'استعلام قیمت', 'imentash' ),
		'office_isfahan_address' => __( 'چهارباغ بالا، مجتمع پارک، پلاک ۵۳۸', 'imentash' ),
		'office_isfahan_phones'  => "031-36671919\n031-36673337",
		'office_tehran_address'  => __( 'یوسف‌آباد، خیابان فتحی شقاقی، خیابان جهان‌مهر، پلاک ۹۴، طبقه ۳، واحد ۵', 'imentash' ),
		'office_tehran_phones'   => "021-88350547\n021-88351762",
		'office_factory_address' => '',
		'office_factory_phones'  => '',
		'email'                  => '',
		'work_hours'             => '',
		'social_instagram'       => '',
		'social_linkedin'        => '',
		'social_telegram'        => '',
		'social_whatsapp'        => '',
		'footer_text'            => __( 'فعال در تولید مواد شیمیایی از سال ۱۳۶۷؛ پودر و فوم آتش‌نشانی، مواد حفاری و آنتی‌فوم.', 'imentash' ),
		'footer_copyright'       => __( 'تمامی حقوق برای شرکت ایمن تاش سپاهان محفوظ است.', 'imentash' ),
		'contact_page'           => 0,
	);
}

/**
 * خواندن یک تنظیم قالب با مقدار پیش‌فرض.
 */
function imentash_get( $key ) {
	$defaults = imentash_option_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * تبدیل ارقام لاتین به فارسی؛ فقط وقتی زبان سایت فارسی است.
 */
function imentash_fa_num( $value ) {
	$value = (string) $value;
	if ( ! apply_filters( 'imentash_use_fa_digits', 0 === strpos( get_locale(), 'fa' ) ) ) {
		return $value;
	}
	return strtr( $value, array_combine( range( 0, 9 ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ) ) );
}

/**
 * لینک tel: از شماره ذخیره‌شده (ارقام فارسی هم پذیرفته می‌شود).
 */
function imentash_tel_href( $phone ) {
	$phone = strtr( (string) $phone, array_combine( array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), range( 0, 9 ) ) );
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * شماره‌های یک دفتر (isfahan، tehran، factory)، هر خط یک شماره.
 */
function imentash_phones( $office ) {
	$raw = (string) imentash_get( 'office_' . $office . '_phones' );
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', $raw ) ) ) );
}

/**
 * شماره اصلی سایت = اولین شماره دفتر اصفهان.
 */
function imentash_main_phone() {
	$phones = imentash_phones( 'isfahan' );
	return $phones ? $phones[0] : '';
}

/**
 * چاپ لینک تلفن با ارقام فارسی.
 */
function imentash_phone_link( $phone, $class = '' ) {
	if ( '' === $phone ) {
		return;
	}
	printf(
		'<a href="%1$s"%2$s dir="ltr">%3$s</a>',
		esc_url( imentash_tel_href( $phone ), array( 'tel' ) ),
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		esc_html( imentash_fa_num( $phone ) )
	);
}

/**
 * آدرس صفحه تماس: برگه انتخاب‌شده در تنظیمات، وگرنه برگه با نامک contact.
 */
function imentash_contact_url() {
	$id = absint( imentash_get( 'contact_page' ) );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		return get_permalink( $id );
	}
	$page = get_page_by_path( 'contact' );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

/**
 * آیکون SVG خطی از assets/icons، به‌صورت inline.
 */
function imentash_icon( $name, $class = '' ) {
	static $cache = array();

	$name = sanitize_key( $name );
	if ( ! isset( $cache[ $name ] ) ) {
		$file           = IMENTASH_DIR . '/assets/icons/' . $name . '.svg';
		$svg            = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		// kses نام ویژگی‌ها را کوچک می‌کند؛ viewBox برگردانده می‌شود.
		$cache[ $name ] = str_replace( ' viewbox=', ' viewBox=', trim( wp_kses( $svg, imentash_svg_allowed() ) ) );
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

function imentash_svg_allowed() {
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
function imentash_logo( $class = '' ) {
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
function imentash_socials() {
	$labels = array(
		'instagram' => __( 'اینستاگرام', 'imentash' ),
		'linkedin'  => __( 'لینکدین', 'imentash' ),
		'telegram'  => __( 'تلگرام', 'imentash' ),
		'whatsapp'  => __( 'واتس‌اپ', 'imentash' ),
	);
	$items  = array();
	foreach ( $labels as $key => $label ) {
		$url = (string) imentash_get( 'social_' . $key );
		if ( '' !== $url ) {
			$items[ $key ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}
	return $items;
}

function imentash_social_links( $class = '' ) {
	$items = imentash_socials();
	if ( ! $items ) {
		return;
	}
	echo '<ul class="' . esc_attr( trim( 'em-soc ' . $class ) ) . '">';
	foreach ( $items as $key => $item ) {
		printf(
			'<li><a href="%1$s" aria-label="%2$s" target="_blank" rel="noopener">%3$s</a></li>',
			esc_url( $item['url'] ),
			esc_attr( $item['label'] ),
			imentash_icon( $key ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG با wp_kses پاک‌سازی شده.
		);
	}
	echo '</ul>';
}

/**
 * سوییچ زبان؛ فقط وقتی Polylang فعال است و بیش از یک زبان دارد.
 *
 * @param string $style short (فا / EN) یا full (فارسی / English).
 */
function imentash_lang_switcher( $style = 'short', $class = '' ) {
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
			$label = 'fa' === $lang['slug'] ? _x( 'فا', 'نام کوتاه زبان فارسی', 'imentash' ) : strtoupper( $lang['slug'] );
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
function imentash_page_crumbs( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$crumbs  = array(
		array(
			'label' => __( 'صفحه اصلی', 'imentash' ),
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
