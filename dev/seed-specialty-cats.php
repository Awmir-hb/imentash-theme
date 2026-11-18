<?php
/**
 * «دسته ششم» جدول _source/report/product-industry-draft.csv (تصمیم کارفرما، ۱۴۰۵/۰۸/۲۷):
 * - دسته textile-auxiliaries ← «مواد شیمیائی تخصصی کمک نساجی»؛ محصول KX-327 ← «وتینگ ایجنت تخصصی صنایع نساجی».
 * - دسته جدید سطح اول «محلول خاموش کننده آتش کلاس K»؛ محصول 303 ← «وت کمیکال Wet Chemical fire Ext1000000»
 *   و فقط در همین دسته (از فوم آتش‌نشانی / شیمیایی بیرون می‌آید). نامک محصول‌ها عوض نمی‌شود.
 * - آیکون دسته‌ها (وقتی عکس نیست): نساجی lab، کلاس K powder (کپسول). عکس دسته بعداً از پیشخوان.
 * دوباره اجرا شدن تکراری نمی‌سازد (دسته با نامک پیدا می‌شود).
 *
 * php -c <php.ini سایت Local> dev/seed-specialty-cats.php            ← فقط گزارش
 * php -c <php.ini سایت Local> dev/seed-specialty-cats.php --confirm  ← نوشتن
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

$confirm = in_array( '--confirm', $argv, true );

$cats = array(
	'textile-auxiliaries'                => array(
		'name'    => 'مواد شیمیائی تخصصی کمک نساجی',
		'icon'    => 'lab',
		'desc'    => 'مواد کمکی تخصصی فرآیندهای نساجی: رنگرزی، سفیدگری، چاپ و تکمیل.',
		'product' => 133,
		'title'   => 'وتینگ ایجنت تخصصی صنایع نساجی',
	),
	'class-k-fire-extinguishing-solution' => array(
		'name'    => 'محلول خاموش کننده آتش کلاس K',
		'icon'    => 'powder',
		'desc'    => 'محلول خاموش‌کننده Wet Chemical برای آتش روغن‌ها و چربی‌های پخت‌وپز (کلاس K).',
		'product' => 303,
		'title'   => 'وت کمیکال Wet Chemical fire Ext1000000',
	),
);

$admins = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
	)
);
wp_set_current_user( $admins[0]->ID );

echo $confirm ? "نوشتن:\n" : "گزارش (بدون --confirm چیزی نوشته نمی‌شود):\n";
foreach ( $cats as $slug => $c ) {
	$term    = get_term_by( 'slug', $slug, 'product_cat' );
	$product = wc_get_product( $c['product'] );
	if ( ! $product ) {
		exit( "محصول {$c['product']} پیدا نشد.\n" );
	}
	printf( "  دسته %s: %s ← «%s» (آیکون %s)\n", $slug, $term ? '#' . $term->term_id . ' «' . $term->name . '»' : 'ساخت', $c['name'], $c['icon'] );
	printf( "  محصول #%d: «%s» ← «%s»؛ دسته‌ها: %s ← فقط این دسته\n", $c['product'], $product->get_name(), $c['title'], implode( '، ', wp_list_pluck( get_the_terms( $c['product'], 'product_cat' ) ?: array(), 'name' ) ) );
	if ( ! $confirm ) {
		continue;
	}

	if ( $term ) {
		$term_id = (int) $term->term_id;
		wp_update_term( $term_id, 'product_cat', array( 'name' => $c['name'] ) );
	} else {
		$res = wp_insert_term(
			$c['name'],
			'product_cat',
			array(
				'slug'        => $slug,
				'description' => $c['desc'],
			)
		);
		if ( is_wp_error( $res ) ) {
			exit( $res->get_error_message() . "\n" );
		}
		$term_id = (int) $res['term_id'];
		update_term_meta( $term_id, '_em_source', 'client:category-6' );
	}
	update_term_meta( $term_id, 'ementash_icon', $c['icon'] );

	$product->set_name( $c['title'] );
	$product->set_category_ids( array( $term_id ) );
	$product->save();
}
echo $confirm ? "انجام شد.\n" : '';
