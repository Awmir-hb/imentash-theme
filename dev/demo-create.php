<?php
/**
 * ساخت داده دمو برای تست محلی (فقط CLI). همه موارد نامک em-demo-… و متای _em_demo دارند.
 * اجرای دوباره چیز تکراری نمی‌سازد. حذف: dev/demo-cleanup.php
 *
 * php -c <php.ini سایت Local> dev/demo-create.php [--static-front]
 *   --static-front: برای تست برگه نوشته‌ها، «صفحه اول» موقتاً یک برگه ایستا می‌شود (مقادیر قبلی در
 *                   گزینه em_demo_backup ذخیره و هنگام پاک‌سازی برگردانده می‌شوند).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || PHP_SAPI === 'cli' || exit;

$_SERVER['HTTP_HOST']   = 'ementash.local';
$_SERVER['REQUEST_URI'] = '/';
require dirname( __DIR__, 4 ) . '/wp-load.php';

/**
 * پست/ترم موجود با این نامک، یا ساخت آن.
 */
function em_demo_post( $slug, $args ) {
	$found = get_posts(
		array(
			'name'        => $slug,
			'post_type'   => $args['post_type'],
			'post_status' => 'any',
			'numberposts' => 1,
		)
	);
	if ( $found ) {
		return $found[0]->ID;
	}
	$id = wp_insert_post( $args + array( 'post_name' => $slug ), true );
	if ( is_wp_error( $id ) ) {
		fwrite( STDERR, $slug . ': ' . $id->get_error_message() . "\n" );
		return 0;
	}
	update_post_meta( $id, '_em_demo', 1 );
	echo "+ {$args['post_type']} #$id $slug\n";
	return $id;
}

function em_demo_term( $slug, $name, $tax, $desc = '' ) {
	$t = get_term_by( 'slug', $slug, $tax );
	if ( $t ) {
		return (int) $t->term_id;
	}
	$r = wp_insert_term( $name, $tax, array( 'slug' => $slug, 'description' => $desc ) );
	if ( is_wp_error( $r ) ) {
		fwrite( STDERR, $slug . ': ' . $r->get_error_message() . "\n" );
		return 0;
	}
	update_term_meta( $r['term_id'], '_em_demo', 1 );
	echo "+ $tax #{$r['term_id']} $slug\n";
	return (int) $r['term_id'];
}

/**
 * تصویر ساده ۴:۳ با GD (گرادیان سرمه‌ای و خطوط شبکه) به‌عنوان پیوست.
 */
function em_demo_image( $slug, $hue ) {
	$found = get_posts( array( 'name' => $slug, 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1 ) );
	if ( $found ) {
		return $found[0]->ID;
	}
	$w  = 1200;
	$h  = 900;
	$im = imagecreatetruecolor( $w, $h );
	for ( $y = 0; $y < $h; $y++ ) {
		$k = $y / $h;
		imageline( $im, 0, $y, $w, $y, imagecolorallocate( $im, (int) ( 11 + 30 * $k + $hue ), (int) ( 21 + 50 * $k ), (int) ( 80 + 90 * $k ) ) );
	}
	$line = imagecolorallocatealpha( $im, 143, 180, 255, 110 );
	for ( $x = 0; $x < $w; $x += 60 ) {
		imageline( $im, $x, 0, $x, $h, $line );
	}
	for ( $y = 0; $y < $h; $y += 60 ) {
		imageline( $im, 0, $y, $w, $y, $line );
	}
	imagefilledellipse( $im, (int) ( $w * 0.65 ), (int) ( $h * 0.55 ), 420, 420, imagecolorallocatealpha( $im, 143, 180, 255, 90 ) );
	$upload = wp_upload_dir();
	$file   = trailingslashit( $upload['path'] ) . $slug . '.jpg';
	imagejpeg( $im, $file, 82 );
	imagedestroy( $im );

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id = wp_insert_attachment(
		array(
			'post_title'     => $slug,
			'post_name'      => $slug,
			'post_mime_type' => 'image/jpeg',
			'post_status'    => 'inherit',
		),
		$file
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	update_post_meta( $id, '_wp_attachment_image_alt', 'تصویر نمونه' );
	update_post_meta( $id, '_em_demo', 1 );
	echo "+ attachment #$id $slug\n";
	return $id;
}

$imgs = array( em_demo_image( 'em-demo-img-1', 0 ), em_demo_image( 'em-demo-img-2', 40 ), em_demo_image( 'em-demo-img-3', 80 ) );

/* ---------- وبلاگ ---------- */
$cats = array(
	em_demo_term( 'em-demo-guides', 'راهنمای فنی', 'category', 'راهنمای انتخاب و استفاده از مواد اطفای حریق و شیمیایی.' ),
	em_demo_term( 'em-demo-news', 'اخبار شرکت', 'category' ),
	em_demo_term( 'em-demo-safety', 'ایمنی آتش', 'category' ),
);
$sub  = em_demo_term( 'em-demo-foam-guides', 'فوم‌ها', 'category' );
wp_update_term( $sub, 'category', array( 'parent' => $cats[0] ) );
$tags = array( 'em-demo-foam' => 'فوم AFFF', 'em-demo-powder' => 'پودر ABC', 'em-demo-iso' => 'ISO 9001' );

$content = '<!-- wp:paragraph --><p>این یک نوشته نمونه برای بررسی قالب است. فوم‌های AFFF و پودر ABC در کلاس‌های مختلف آتش کاربرد دارند و انتخاب درست آن‌ها به نوع سوخت بستگی دارد. <a href="#">لینک نمونه</a> داخل متن.</p><!-- /wp:paragraph -->'
	. '<!-- wp:heading --><h2>انتخاب ماده اطفا</h2><!-- /wp:heading -->'
	. '<!-- wp:list --><ul><li>آتش جامدات (کلاس A)</li><li>آتش مایعات قابل اشتعال (کلاس B)</li><li>آتش گازها (کلاس C)</li></ul><!-- /wp:list -->'
	. '<!-- wp:quote --><blockquote class="wp-block-quote"><p>نگهداری درست کپسول به اندازه انتخاب درست آن مهم است.</p></blockquote><!-- /wp:quote -->'
	. '<!-- wp:heading {"level":3} --><h3>جدول مقایسه</h3><!-- /wp:heading -->'
	. '<!-- wp:table --><figure class="wp-block-table"><table><thead><tr><th>محصول</th><th>کلاس</th><th>غلظت</th><th>بسته‌بندی</th><th>استاندارد</th><th>کاربرد اصلی در صنعت</th></tr></thead><tbody><tr><td>AFFF 3%</td><td>B</td><td>3%</td><td>بشکه ۲۲۰ لیتری</td><td>EN 1568-3</td><td>پالایشگاه و انبار سوخت</td></tr><tr><td>ABC 40%</td><td>A, B, C</td><td>40%</td><td>کیسه ۲۵ کیلوگرمی</td><td>EN 615</td><td>کپسول‌های دستی و چرخ‌دار</td></tr></tbody></table></figure><!-- /wp:table -->'
	. '<!-- wp:paragraph --><p>پاراگراف پایانی با عددهای 1367 و ۱۹۰۰۰ و کد KX-327.</p><!-- /wp:paragraph -->';

$titles = array(
	'راهنمای انتخاب فوم AFFF برای مخازن سوخت',
	'تفاوت پودر ABC و BC در کپسول‌های آتش‌نشانی',
	'دریافت گواهی ISO 9001 برای خط تولید فوم',
	'نگهداری و شارژ دوره‌ای کپسول‌های پودری',
	'کاربرد ضدکف‌ها در صنایع نساجی',
	'حضور ایمن تاش در نمایشگاه ایمنی و آتش‌نشانی',
	'کلاس‌های آتش و ماده اطفای مناسب هرکدام',
	'فوم مقاوم در برابر الکل (AR-AFFF) چیست؟',
	'افزایش ظرفیت تولید به ۱۹ هزار تن در سال',
	'استاندارد EN 1568 و آزمون‌های فوم',
	'ایمنی انبار مواد شیمیایی در کارخانه',
);
$post_cats = array( array( $cats[0], $sub ), array( $cats[0] ), array( $cats[1] ), array( $cats[2] ), array( $cats[0] ), array( $cats[1] ), array( $cats[2] ), array( $sub ), array( $cats[1] ), array( $cats[0] ), array( $cats[2] ) );
foreach ( $titles as $i => $title ) {
	$n  = $i + 1;
	$id = em_demo_post(
		'em-demo-post-' . $n,
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => $content,
			'post_date'    => gmdate( 'Y-m-d H:i:s', time() - $n * DAY_IN_SECONDS * 6 ),
		)
	);
	if ( $id ) {
		wp_set_post_categories( $id, $post_cats[ $i ] );
		wp_set_post_tags( $id, array_values( array_slice( $tags, $i % 3, 2 ) ) );
		if ( $i % 4 !== 3 ) {
			set_post_thumbnail( $id, $imgs[ $i % 3 ] );
		}
	}
}
// نامک برچسب‌ها
foreach ( $tags as $slug => $name ) {
	$t = get_term_by( 'name', $name, 'post_tag' );
	if ( $t && $t->slug !== $slug ) {
		wp_update_term( $t->term_id, 'post_tag', array( 'slug' => $slug ) );
		update_term_meta( $t->term_id, '_em_demo', 1 );
	}
}

/* ---------- محصولات ---------- */
if ( class_exists( 'WC_Product_Simple' ) ) {
	$pcats = array(
		em_demo_term( 'em-demo-pcat-foam', 'فوم‌های آتش‌نشانی', 'product_cat', 'انواع فوم AFFF، AR-AFFF و پروتئینی.' ),
		em_demo_term( 'em-demo-pcat-powder', 'پودرهای اطفا', 'product_cat', 'پودر ABC و BC برای کپسول‌های دستی و چرخ‌دار.' ),
		em_demo_term( 'em-demo-pcat-textile', 'کمک‌نساجی', 'product_cat' ),
	);
	$products = array(
		array( 'فوم AFFF سه درصد', 'AF-3', 0 ),
		array( 'فوم AFFF شش درصد', 'AF-6', 0 ),
		array( 'فوم AR-AFFF مقاوم در برابر الکل', 'AR-36', 0 ),
		array( 'پودر ABC چهل درصد', 'PO-40ABC', 1 ),
		array( 'پودر ABC نود درصد', 'PO-90ABC', 1 ),
		array( 'پودر BC', 'PO-600A', 1 ),
		array( 'رطوبت‌دهنده سریع', 'KX-327', 2 ),
		array( 'ضدکف صنعتی', 'AF-100', 2 ),
	);
	foreach ( $products as $i => $p ) {
		$slug = 'em-demo-product-' . ( $i + 1 );
		if ( get_page_by_path( $slug, OBJECT, 'product' ) ) {
			continue;
		}
		$product = new WC_Product_Simple();
		$product->set_name( $p[0] );
		$product->set_slug( $slug );
		$product->set_status( 'publish' );
		$product->set_sku( $p[1] );
		$product->set_category_ids( array( $pcats[ $p[2] ] ) );
		$product->set_description( '<p>توضیحات نمونه محصول ' . $p[0] . '.</p>' );
		$product->set_short_description( '<p>خلاصه نمونه برای ' . $p[0] . '.</p>' );
		if ( $i % 3 !== 2 ) {
			$product->set_image_id( $imgs[ $i % 3 ] );
		}
		$id = $product->save();
		update_post_meta( $id, '_em_demo', 1 );
		echo "+ product #$id $slug\n";
	}
}

/* ---------- برگه نوشته‌ها (اختیاری) ---------- */
if ( in_array( '--static-front', $argv, true ) ) {
	$home = em_demo_post( 'em-demo-home', array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'صفحه اصلی (دمو)' ) );
	$blog = em_demo_post( 'em-demo-blog', array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'مقاله‌ها', 'post_excerpt' => 'راهنماهای فنی، اخبار و دانش ایمنی آتش از تیم ایمن تاش.' ) );
	if ( false === get_option( 'em_demo_backup' ) ) {
		add_option(
			'em_demo_backup',
			array(
				'show_on_front'  => get_option( 'show_on_front' ),
				'page_on_front'  => get_option( 'page_on_front' ),
				'page_for_posts' => get_option( 'page_for_posts' ),
			),
			'',
			false
		);
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );
	update_option( 'page_for_posts', $blog );
	echo "~ show_on_front=page, page_on_front=$home, page_for_posts=$blog (backup: em_demo_backup)\n";
}

echo "done\n";
