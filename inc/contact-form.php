<?php
/**
 * فرم تماس: پردازش، ذخیره در em_inquiry (خصوصی) و ایمیل با wp_mail.
 *
 * یک تابع پردازش برای هر دو حالت:
 * - بدون JS: فرم به همان صفحه تماس POST می‌شود؛ موفق ← ریدایرکت (PRG)، خطا ← همان صفحه با پیام و مقادیر.
 * - با JS: admin-ajax با action=ementash_inquiry و پاسخ JSON.
 * امنیت: nonce، فیلد honeypot، محدودیت تعداد ارسال با transient بر اساس IP، sanitize همه ورودی‌ها.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * حداکثر تعداد ارسال از یک IP در بازه زمانی (قابل تغییر با فیلتر).
 */
function ementash_inquiry_limit() {
	return apply_filters(
		'ementash_inquiry_limit',
		array(
			'max'    => 3,
			'window' => 10 * MINUTE_IN_SECONDS,
		)
	);
}

/**
 * منبع‌های IP قابل انتخاب در Customizer (صفحه تماس و فرم ← منبع IP).
 *
 * چرا: محدودیت ارسال فرم بر اساس IP است. اگر سایت پشت CDN یا پروکسی معکوس باشد (Cloudflare،
 * ابر آروان، nginx جلوی سرور و...)، REMOTE_ADDR همیشه IP همان پروکسی است و همه بازدیدکننده‌ها یک نفر
 * حساب می‌شوند: بعد از چند ارسال، فرم برای همه بسته می‌شود. در این حالت هدری را انتخاب کنید که CDN
 * با IP واقعی بازدیدکننده پر می‌کند.
 *
 * هشدار: هر هدر HTTP را خود بازدیدکننده هم می‌تواند بفرستد. فقط وقتی هدر را انتخاب کنید که همه
 * ترافیک از CDN بگذرد (دسترسی مستقیم به IP سرور در فایروال بسته باشد)؛ وگرنه ربات با عوض کردن هدر
 * محدودیت را دور می‌زند. بدون CDN همان پیش‌فرض REMOTE_ADDR درست است.
 * کلیدها کوچک و با زیرخط (ementash_sanitize_select آن‌ها را با sanitize_key می‌سنجد).
 */
function ementash_ip_sources() {
	return array(
		'remote_addr'     => __( 'REMOTE_ADDR (پیش‌فرض، بدون CDN)', 'ementash' ),
		'cloudflare'      => __( 'Cloudflare: CF-Connecting-IP', 'ementash' ),
		'arvancloud'      => __( 'ابر آروان: AR-Real-IP', 'ementash' ),
		'x_real_ip'       => __( 'پروکسی معکوس (nginx): X-Real-IP', 'ementash' ),
		'x_forwarded_for' => __( 'پروکسی معکوس: آخرین مقدار X-Forwarded-For', 'ementash' ),
	);
}

/**
 * IP بازدیدکننده بر اساس منبع انتخاب‌شده. اگر هدر انتخابی نبود یا IP معتبر نداشت، REMOTE_ADDR.
 * قابل تغییر با فیلتر ementash_client_ip.
 */
function ementash_client_ip() {
	$headers = array(
		'cloudflare'      => 'HTTP_CF_CONNECTING_IP',
		'arvancloud'      => 'HTTP_AR_REAL_IP',
		'x_real_ip'       => 'HTTP_X_REAL_IP',
		'x_forwarded_for' => 'HTTP_X_FORWARDED_FOR',
	);
	$source  = (string) ementash_get( 'contact_ip_source' );
	$ip      = '';

	if ( isset( $headers[ $source ], $_SERVER[ $headers[ $source ] ] ) ) {
		$value = sanitize_text_field( wp_unslash( $_SERVER[ $headers[ $source ] ] ) );
		if ( 'x_forwarded_for' === $source ) {
			// «client, proxy1, proxy2»: آخرین مقدار را پروکسی خودمان اضافه کرده و قابل اعتماد است.
			$list  = array_map( 'trim', explode( ',', $value ) );
			$value = (string) end( $list );
		}
		$ip = filter_var( $value, FILTER_VALIDATE_IP ) ? $value : '';
	}
	if ( '' === $ip && isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$value = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		$ip    = filter_var( $value, FILTER_VALIDATE_IP ) ? $value : '';
	}
	return (string) apply_filters( 'ementash_client_ip', $ip, $source );
}

/**
 * محصول معتبر برای ?product=ID: فقط پست منتشرشده از نوع product.
 *
 * @return WP_Post|null
 */
function ementash_inquiry_product( $id ) {
	$post = get_post( absint( $id ) );
	return ( $post && 'product' === $post->post_type && 'publish' === $post->post_status ) ? $post : null;
}

/**
 * ووکامرس «product» را query var عمومی کرده است؛ ‎/contact-us/?product=108 بدون این فیلتر دنبال محصولی
 * با نامک 108 می‌گردد و ۴۰۴ می‌دهد. وقتی درخواست برای یک برگه است، این متغیر کنار گذاشته می‌شود
 * (فرم تماس مقدار را مستقیم از $_GET می‌خواند).
 */
function ementash_page_ignores_product_var( $vars ) {
	if ( ! isset( $vars['product'] ) || ( ! isset( $vars['pagename'] ) && ! isset( $vars['page_id'] ) ) ) {
		return $vars;
	}
	if ( isset( $vars['name'] ) && $vars['name'] === $vars['product'] ) {
		unset( $vars['name'] );
	}
	if ( isset( $vars['post_type'] ) && 'product' === $vars['post_type'] ) {
		unset( $vars['post_type'] );
	}
	unset( $vars['product'] );
	return $vars;
}
add_filter( 'request', 'ementash_page_ignores_product_var' );

/**
 * فیلدهای فرم: نام ورودی ← کلید ذخیره.
 */
function ementash_inquiry_inputs() {
	return array(
		'em_type'    => 'type',
		'em_name'    => 'name',
		'em_company' => 'company',
		'em_phone'   => 'phone',
		'em_email'   => 'email',
		'em_message' => 'message',
		'em_product' => 'product',
	);
}

/**
 * پردازش یک ارسال.
 *
 * @param array $raw ورودی خام ($_POST بعد از wp_unslash).
 * @return array status (ok|error)، message، errors (فیلد ← پیام)، values (مقادیر پاک‌شده برای پر کردن دوباره فرم).
 */
function ementash_handle_inquiry( $raw ) {
	$result = array(
		'status'  => 'error',
		'message' => '',
		'errors'  => array(),
		'values'  => array(),
	);

	$nonce = isset( $raw['_em_nonce'] ) ? sanitize_text_field( $raw['_em_nonce'] ) : '';
	if ( ! wp_verify_nonce( $nonce, 'ementash_inquiry' ) ) {
		$result['message'] = __( 'اعتبار فرم تمام شده است. لطفاً صفحه را دوباره باز کنید و دوباره بفرستید.', 'ementash' );
		return $result;
	}

	// honeypot: انسان این فیلد پنهان را پر نمی‌کند. ربات پاسخ موفق می‌گیرد ولی چیزی ذخیره نمی‌شود.
	if ( ! empty( $raw['em_website'] ) ) {
		$result['status']  = 'ok';
		$result['message'] = (string) ementash_get( 'contact_success' );
		return $result;
	}

	// sanitize
	$types  = ementash_inquiry_types();
	$values = array(
		'type'    => isset( $raw['em_type'] ) ? sanitize_key( $raw['em_type'] ) : '',
		'name'    => isset( $raw['em_name'] ) ? sanitize_text_field( $raw['em_name'] ) : '',
		'company' => isset( $raw['em_company'] ) ? sanitize_text_field( $raw['em_company'] ) : '',
		'phone'   => isset( $raw['em_phone'] ) ? sanitize_text_field( $raw['em_phone'] ) : '',
		'email'   => isset( $raw['em_email'] ) ? sanitize_email( $raw['em_email'] ) : '',
		'message' => isset( $raw['em_message'] ) ? sanitize_textarea_field( $raw['em_message'] ) : '',
		'product' => isset( $raw['em_product'] ) ? absint( $raw['em_product'] ) : 0,
	);
	if ( ! isset( $types[ $values['type'] ] ) ) {
		$values['type'] = (string) key( $types );
	}
	if ( $values['product'] && ! ementash_inquiry_product( $values['product'] ) ) {
		$values['product'] = 0;
	}
	$result['values'] = $values;

	// اعتبارسنجی
	$errors = array();
	if ( '' === $values['name'] ) {
		$errors['em_name'] = __( 'نام و نام خانوادگی را بنویسید.', 'ementash' );
	} elseif ( mb_strlen( $values['name'] ) > 100 ) {
		$errors['em_name'] = __( 'نام طولانی‌تر از حد مجاز است.', 'ementash' );
	}
	if ( '' === $values['company'] ) {
		$errors['em_company'] = __( 'نام شرکت را بنویسید.', 'ementash' );
	} elseif ( mb_strlen( $values['company'] ) > 150 ) {
		$errors['em_company'] = __( 'نام شرکت طولانی‌تر از حد مجاز است.', 'ementash' );
	}
	// شماره با ارقام فارسی هم پذیرفته می‌شود.
	$digits = preg_replace( '/[^0-9+]/', '', strtr( $values['phone'], array_combine( array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), range( 0, 9 ) ) ) );
	if ( '' === $values['phone'] ) {
		$errors['em_phone'] = __( 'شماره تماس را بنویسید.', 'ementash' );
	} elseif ( strlen( $digits ) < 7 || strlen( $digits ) > 15 ) {
		$errors['em_phone'] = __( 'شماره تماس معتبر نیست.', 'ementash' );
	}
	if ( isset( $raw['em_email'] ) && '' !== trim( (string) $raw['em_email'] ) && ! is_email( $values['email'] ) ) {
		$errors['em_email'] = __( 'ایمیل معتبر نیست.', 'ementash' );
	}
	if ( '' === $values['message'] ) {
		$errors['em_message'] = __( 'محصول و مقدار مورد نیاز را بنویسید.', 'ementash' );
	} elseif ( mb_strlen( $values['message'] ) > 2000 ) {
		$errors['em_message'] = __( 'متن طولانی‌تر از حد مجاز (۲۰۰۰ نویسه) است.', 'ementash' );
	}
	if ( $errors ) {
		$result['errors']  = $errors;
		$result['message'] = __( 'لطفاً موارد مشخص‌شده را اصلاح کنید.', 'ementash' );
		return $result;
	}

	// محدودیت تعداد ارسال برای هر IP.
	$ip    = ementash_client_ip();
	$limit = ementash_inquiry_limit();
	$key   = 'ementash_inq_' . md5( $ip );
	$rate  = get_transient( $key );
	$rate  = is_array( $rate ) ? $rate : array(
		'count' => 0,
		'until' => time() + (int) $limit['window'],
	);
	if ( $rate['count'] >= (int) $limit['max'] ) {
		$result['message'] = __( 'تعداد درخواست‌های ارسالی زیاد است. لطفاً چند دقیقه دیگر دوباره تلاش کنید.', 'ementash' );
		return $result;
	}

	// ذخیره
	$product = $values['product'] ? ementash_inquiry_product( $values['product'] ) : null;
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'em_inquiry',
			'post_status' => 'private',
			/* translators: 1: نوع درخواست، 2: نام */
			'post_title'  => sprintf( __( '%1$s: %2$s', 'ementash' ), $types[ $values['type'] ], $values['name'] ),
		),
		true
	);
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		$result['message'] = __( 'ثبت درخواست ممکن نشد. لطفاً دوباره تلاش کنید یا تماس بگیرید.', 'ementash' );
		return $result;
	}
	foreach ( $values as $field => $value ) {
		if ( '' !== $value && 0 !== $value ) {
			update_post_meta( $post_id, '_ementash_inquiry_' . $field, $value );
		}
	}
	if ( '' !== $ip ) {
		update_post_meta( $post_id, '_ementash_inquiry_ip', $ip );
	}

	$rate['count']++;
	set_transient( $key, $rate, max( 60, $rate['until'] - time() ) );

	// ایمیل. اگر ارسال نشد درخواست ذخیره مانده است و به کاربر خطا نشان داده نمی‌شود.
	$sent = ementash_inquiry_mail( $post_id, $values, $product );
	update_post_meta( $post_id, '_ementash_inquiry_mail', $sent ? 'sent' : 'failed' );

	$result['status']  = 'ok';
	$result['message'] = (string) ementash_get( 'contact_success' );
	$result['values']  = array();
	return $result;
}

/**
 * ایمیل درخواست به گیرنده Customizer (وگرنه ایمیل مدیر سایت).
 */
function ementash_inquiry_mail( $post_id, $values, $product ) {
	$to = sanitize_email( (string) ementash_get( 'contact_recipient' ) );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$types  = ementash_inquiry_types();
	$labels = ementash_inquiry_fields();
	$lines  = array();
	foreach ( array( 'type', 'name', 'company', 'phone', 'email', 'message' ) as $field ) {
		$value = 'type' === $field ? $types[ $values['type'] ] : $values[ $field ];
		if ( '' !== (string) $value ) {
			$lines[] = $labels[ $field ] . ': ' . $value;
		}
	}
	if ( $product ) {
		$lines[] = $labels['product'] . ': ' . get_the_title( $product ) . ' (' . get_permalink( $product ) . ')';
	}
	$lines[] = '';
	$lines[] = __( 'مشاهده در پیشخوان:', 'ementash' ) . ' ' . admin_url( 'post.php?post=' . absint( $post_id ) . '&action=edit' );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( '' !== $values['email'] && is_email( $values['email'] ) ) {
		$headers[] = 'Reply-To: ' . str_replace( array( '"', "\r", "\n" ), '', $values['name'] ) . ' <' . $values['email'] . '>';
	}

	/* translators: 1: نام سایت، 2: نوع درخواست، 3: نام */
	$subject = sprintf( __( '[%1$s] %2$s از %3$s', 'ementash' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $types[ $values['type'] ], $values['name'] );

	return (bool) wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
}

/**
 * ارسال بدون JS: پردازش روی همان صفحه تماس پیش از چاپ قالب.
 */
function ementash_inquiry_post_handler() {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) || empty( $_POST['ementash_inquiry'] ) || ! ementash_is_contact_view() ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing -- nonce در ementash_handle_inquiry.
		return;
	}
	$result = ementash_handle_inquiry( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce و sanitize داخل تابع.
	if ( 'ok' === $result['status'] ) {
		wp_safe_redirect( add_query_arg( 'em_sent', '1', get_permalink( get_queried_object_id() ) ) . '#em-form' );
		exit;
	}
	$GLOBALS['ementash_inquiry_result'] = $result;
}
add_action( 'template_redirect', 'ementash_inquiry_post_handler' );

/**
 * ارسال با JS (admin-ajax).
 */
function ementash_inquiry_ajax_handler() {
	$result = ementash_handle_inquiry( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce و sanitize داخل تابع.
	unset( $result['values'] );
	wp_send_json( $result );
}
add_action( 'wp_ajax_ementash_inquiry', 'ementash_inquiry_ajax_handler' );
add_action( 'wp_ajax_nopriv_ementash_inquiry', 'ementash_inquiry_ajax_handler' );

/**
 * CSS و JS صفحه تماس و درباره ما فقط در همان صفحه‌ها.
 */
function ementash_page_assets() {
	if ( ementash_is_about_view() ) {
		wp_enqueue_style( 'ementash-about', EMENTASH_URI . '/assets/css/about.css', array( 'ementash-main' ), ementash_asset_ver( 'assets/css/about.css' ) );
	}
	if ( ementash_is_contact_view() ) {
		wp_enqueue_style( 'ementash-contact', EMENTASH_URI . '/assets/css/contact.css', array( 'ementash-main' ), ementash_asset_ver( 'assets/css/contact.css' ) );
		wp_enqueue_script(
			'ementash-contact',
			EMENTASH_URI . '/assets/js/contact.js',
			array(),
			ementash_asset_ver( 'assets/js/contact.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ementash_page_assets' );
