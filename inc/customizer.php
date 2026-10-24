<?php
/**
 * تنظیمات قالب در Customizer (پنل «ایمن تاش»).
 *
 * همه فیلدها فقط در ementash_customizer_schema() تعریف می‌شوند؛ کنترل‌ها،
 * مقادیر پیش‌فرض ementash_get() و ثبت رشته‌ها در Polylang از همین آرایه ساخته می‌شوند.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

/**
 * سکشن‌های صفحه اصلی برای کلیدهای روشن/خاموش Customizer. کلید = نام template-part در template-parts/home.
 * ترتیب نمایش در front-page.php تعیین می‌شود.
 */
function ementash_home_sections() {
	return array(
		'hero'            => __( 'هیرو', 'ementash' ),
		'about'           => __( 'درباره ما و آمار', 'ementash' ),
		'honors'          => __( 'افتخارات', 'ementash' ),
		'industries'      => __( 'صنایع', 'ementash' ),
		'clients'         => __( 'مشتریان', 'ementash' ),
		'services'        => __( 'خدمات', 'ementash' ),
		'products'        => __( 'محصولات', 'ementash' ),
		'production-line' => __( 'خط تولید', 'ementash' ),
		'gallery'         => __( 'گالری کارخانه', 'ementash' ),
		'textile'         => __( 'کمک‌نساجی', 'ementash' ),
		'blog'            => __( 'مقاله‌ها', 'ementash' ),
	);
}

/**
 * کلید تنظیم روشن/خاموش یک سکشن صفحه اصلی.
 */
function ementash_section_key( $id ) {
	return 'home_' . str_replace( '-', '_', $id ) . '_show';
}

/**
 * فیلد روشن/خاموش که بالای هر سکشن صفحه اصلی می‌آید.
 */
function ementash_toggle_field( $id ) {
	return array(
		ementash_section_key( $id ) => array(
			'type'    => 'checkbox',
			'label'   => __( 'نمایش این سکشن در صفحه اصلی', 'ementash' ),
			'default' => true,
		),
	);
}

/**
 * تعریف همه سکشن‌ها و فیلدهای Customizer.
 *
 * نوع فیلدها: text، textarea، lines (هر خط یک مورد)، url، email، checkbox، select،
 * page، image، video، media (تصویر یا ویدئو)، file (PDF). رسانه‌ها شناسه پیوست ذخیره می‌شوند.
 */
function ementash_customizer_schema() {
	$offices = array(
		'isfahan' => array(
			'name'    => __( 'دفتر اصفهان', 'ementash' ),
			'label'   => __( 'اصفهان', 'ementash' ),
			'address' => __( 'چهارباغ بالا، مجتمع پارک، پلاک ۵۳۸', 'ementash' ),
			'phones'  => "031-36671919\n031-36673337",
		),
		'tehran'  => array(
			'name'    => __( 'دفتر تهران', 'ementash' ),
			'label'   => __( 'تهران', 'ementash' ),
			'address' => __( 'یوسف‌آباد، خیابان فتحی شقاقی، خیابان جهان‌مهر، پلاک ۹۴، طبقه ۳، واحد ۵', 'ementash' ),
			'phones'  => "021-88350547\n021-88351762",
		),
		'factory' => array(
			'name'    => __( 'کارخانه', 'ementash' ),
			'label'   => __( 'کارخانه', 'ementash' ),
			'address' => '',
			'phones'  => '',
		),
	);

	$contact = array(
		'main_phone_label' => array(
			'type'        => 'text',
			'label'       => __( 'برچسب شماره اصلی', 'ementash' ),
			'description' => __( 'شماره اصلی سایت اولین شماره دفتر اصفهان است.', 'ementash' ),
			'default'     => __( 'دفتر مرکزی اصفهان', 'ementash' ),
		),
		'quote_label'      => array(
			'type'    => 'text',
			'label'   => __( 'متن دکمه استعلام قیمت', 'ementash' ),
			'default' => __( 'استعلام قیمت', 'ementash' ),
		),
		'email'            => array(
			'type'    => 'email',
			'label'   => __( 'ایمیل', 'ementash' ),
			'default' => '',
		),
		'work_hours'       => array(
			'type'    => 'text',
			'label'   => __( 'ساعات کاری', 'ementash' ),
			'default' => '',
		),
	);
	foreach ( $offices as $key => $office ) {
		$contact[ 'office_' . $key . '_label' ]   = array(
			'type'    => 'text',
			/* translators: %s: نام دفتر */
			'label'   => sprintf( __( '%s: برچسب کوتاه', 'ementash' ), $office['name'] ),
			'default' => $office['label'],
		);
		$contact[ 'office_' . $key . '_address' ] = array(
			'type'    => 'textarea',
			/* translators: %s: نام دفتر */
			'label'   => sprintf( __( '%s: آدرس', 'ementash' ), $office['name'] ),
			'default' => $office['address'],
		);
		$contact[ 'office_' . $key . '_phones' ]  = array(
			'type'        => 'lines',
			/* translators: %s: نام دفتر */
			'label'       => sprintf( __( '%s: تلفن‌ها', 'ementash' ), $office['name'] ),
			'description' => __( 'هر خط یک شماره.', 'ementash' ),
			'default'     => $office['phones'],
		);
	}

	$social = array();
	foreach (
		array(
			'instagram' => __( 'اینستاگرام', 'ementash' ),
			'linkedin'  => __( 'لینکدین', 'ementash' ),
			'telegram'  => __( 'تلگرام', 'ementash' ),
			'whatsapp'  => __( 'واتس‌اپ', 'ementash' ),
		) as $key => $label
	) {
		$social[ 'social_' . $key ] = array(
			'type'    => 'url',
			'label'   => $label,
			'default' => '',
		);
	}

	$stat_defaults = array(
		array( '۱۳۶۷', __( 'سال تأسیس', 'ementash' ), 'calendar' ),
		array( '۱۹٬۰۰۰', __( 'تن در سال', 'ementash' ), 'factory' ),
		array( '۳۶', __( 'محصول در ۷ دسته', 'ementash' ), 'grid' ),
		array( 'ISO', __( 'و TUV NORD، گواهینامه بین‌المللی', 'ementash' ), 'shield' ),
	);
	$stats         = array();
	foreach ( $stat_defaults as $i => $stat ) {
		$n = $i + 1;
		/* translators: %d: شماره آیتم */
		$prefix                          = sprintf( __( 'آمار %d', 'ementash' ), $n );
		$stats[ 'stat_' . $n . '_number' ] = array(
			'type'    => 'text',
			'label'   => $prefix . ': ' . __( 'عدد', 'ementash' ),
			'default' => $stat[0],
		);
		$stats[ 'stat_' . $n . '_label' ]  = array(
			'type'    => 'text',
			'label'   => $prefix . ': ' . __( 'برچسب', 'ementash' ),
			'default' => $stat[1],
		);
		$stats[ 'stat_' . $n . '_icon' ]   = array(
			'type'    => 'select',
			'label'   => $prefix . ': ' . __( 'آیکون', 'ementash' ),
			'choices' => ementash_icon_choices( 'stat' ),
			'default' => $stat[2],
		);
	}

	$step_defaults = array(
		array( 'T-101 / 102', __( 'مخازن مواد اولیه', 'ementash' ), __( 'نگهداری و آماده‌سازی مواد اولیه پیش از فرآوری.', 'ementash' ) ),
		array( 'C-201', __( 'فرآوری و اختلاط', 'ementash' ), __( 'تولید بر اساس فرمول‌های واحد تحقیق و توسعه، با ظرفیت ۱۹ هزار تن در سال.', 'ementash' ) ),
		array( 'PI-01', __( 'کنترل و آزمون', 'ementash' ), __( 'آزمون در آزمایشگاه همکار با استاندارد ۱۷۰۲۵ و امکان حضور خریدار در تست کارخانه.', 'ementash' ) ),
		array( 'S-301', __( 'بسته‌بندی و ارسال', 'ementash' ), __( 'بسته‌بندی و پلمب سفارشی ظروف، و ارسال اضطراری فوم و پودر.', 'ementash' ) ),
	);
	$line          = ementash_toggle_field( 'production-line' ) + array(
		'pl_title' => array(
			'type'    => 'text',
			'label'   => __( 'تیتر', 'ementash' ),
			'default' => __( 'خط تولید ما', 'ementash' ),
		),
		'pl_text'  => array(
			'type'    => 'textarea',
			'label'   => __( 'متن', 'ementash' ),
			'default' => __( 'از مخزن مواد اولیه تا بشکه‌ای که پلمب می‌شود؛ هر مرحله در کارخانه اصفهان کنترل می‌شود.', 'ementash' ),
		),
	);
	foreach ( $step_defaults as $i => $step ) {
		$n = $i + 1;
		/* translators: %d: شماره مرحله */
		$prefix                       = sprintf( __( 'مرحله %d', 'ementash' ), $n );
		$line[ 'pl_' . $n . '_code' ]  = array(
			'type'        => 'text',
			'label'       => $prefix . ': ' . __( 'کد', 'ementash' ),
			'description' => __( 'روی نقشه خط تولید هم نوشته می‌شود.', 'ementash' ),
			'default'     => $step[0],
		);
		$line[ 'pl_' . $n . '_title' ] = array(
			'type'    => 'text',
			'label'   => $prefix . ': ' . __( 'عنوان', 'ementash' ),
			'default' => $step[1],
		);
		$line[ 'pl_' . $n . '_text' ]  = array(
			'type'    => 'textarea',
			'label'   => $prefix . ': ' . __( 'توضیح', 'ementash' ),
			'default' => $step[2],
		);
	}

	$gallery = ementash_toggle_field( 'gallery' ) + array(
		'gallery_title' => array(
			'type'    => 'text',
			'label'   => __( 'تیتر', 'ementash' ),
			'default' => __( 'نمایی از کارخانه', 'ementash' ),
		),
	);
	for ( $n = 1; $n <= 5; $n++ ) {
		$gallery[ 'gallery_' . $n ] = array(
			'type'        => 'image',
			/* translators: %d: شماره عکس */
			'label'       => sprintf( __( 'عکس %d', 'ementash' ), $n ),
			'description' => 1 === $n ? __( 'متن جایگزین (alt) هر عکس از کتابخانه رسانه خوانده می‌شود.', 'ementash' ) : '',
			'default'     => 0,
		);
	}
	$gallery += array(
		'gallery_link_label' => array(
			'type'    => 'text',
			'label'   => __( 'متن لینک گالری کامل', 'ementash' ),
			'default' => __( 'گالری کامل تصاویر', 'ementash' ),
		),
		'gallery_link_url'   => array(
			'type'        => 'url',
			'label'       => __( 'آدرس گالری کامل', 'ementash' ),
			'description' => __( 'اگر خالی باشد لینک نمایش داده نمی‌شود.', 'ementash' ),
			'default'     => '',
		),
	);

	return array(
		'contact'                 => array(
			'title'  => __( 'تماس و دفاتر', 'ementash' ),
			'fields' => $contact,
		),
		'social'                  => array(
			'title'       => __( 'شبکه‌های اجتماعی', 'ementash' ),
			'description' => __( 'فقط شبکه‌هایی که لینک دارند نمایش داده می‌شوند.', 'ementash' ),
			'fields'      => $social,
		),
		'footer'                  => array(
			'title'  => __( 'فوتر', 'ementash' ),
			'fields' => array(
				'footer_text'      => array(
					'type'    => 'textarea',
					'label'   => __( 'متن کوتاه معرفی', 'ementash' ),
					'default' => __( 'فعال در تولید مواد شیمیایی از سال ۱۳۶۷؛ پودر و فوم آتش‌نشانی، مواد حفاری و آنتی‌فوم.', 'ementash' ),
				),
				'footer_copyright' => array(
					'type'    => 'text',
					'label'   => __( 'متن کپی‌رایت', 'ementash' ),
					'default' => __( 'تمامی حقوق برای شرکت ایمن تاش سپاهان محفوظ است.', 'ementash' ),
				),
			),
		),
		'pages'                   => array(
			'title'  => __( 'برگه‌ها', 'ementash' ),
			'fields' => array(
				'contact_page' => array(
					'type'        => 'page',
					'label'       => __( 'برگه تماس با ما', 'ementash' ),
					'description' => __( 'اگر انتخاب نشود برگه با نامک contact استفاده می‌شود.', 'ementash' ),
					'default'     => 0,
				),
				'about_page'   => array(
					'type'        => 'page',
					'label'       => __( 'برگه درباره ما', 'ementash' ),
					'description' => __( 'اگر انتخاب نشود برگه با نامک about استفاده می‌شود.', 'ementash' ),
					'default'     => 0,
				),
			),
		),
		'home_hero'               => array(
			'title'  => __( 'صفحه اصلی: هیرو', 'ementash' ),
			'fields' => ementash_toggle_field( 'hero' ) + array(
				'hero_media'        => array(
					'type'        => 'media',
					'label'       => __( 'ویدئو یا گیف پس‌زمینه (دسکتاپ)', 'ementash' ),
					'description' => __( 'ویدئوی MP4/WebM بی‌صدا یا تصویر GIF.', 'ementash' ),
					'default'     => 0,
				),
				'hero_media_mobile' => array(
					'type'        => 'video',
					'label'       => __( 'ویدئوی عمودی موبایل', 'ementash' ),
					'description' => __( 'اگر خالی باشد پوستر نمایش داده می‌شود.', 'ementash' ),
					'default'     => 0,
				),
				'hero_poster'       => array(
					'type'        => 'image',
					'label'       => __( 'تصویر پوستر', 'ementash' ),
					'description' => __( 'تا بارگذاری ویدئو و برای کاربرانی که حرکت را کم کرده‌اند نمایش داده می‌شود.', 'ementash' ),
					'default'     => 0,
				),
				'hero_eyebrow'      => array(
					'type'        => 'text',
					'label'       => __( 'برچسب کوچک بالای تیتر', 'ementash' ),
					'description' => __( 'اگر خالی باشد نمایش داده نمی‌شود.', 'ementash' ),
					'default'     => __( 'شرکت دانش‌بنیان ایمن تاش سپاهان (سی‌پیکو)', 'ementash' ),
				),
				'hero_title'        => array(
					'type'        => 'lines',
					'label'       => __( 'تیتر', 'ementash' ),
					'description' => __( 'هر خط، یک خط تیتر.', 'ementash' ),
					'default'     => __( 'بزرگ‌ترین تولیدکننده مواد شیمیایی', 'ementash' ),
				),
				'hero_words_lead'   => array(
					'type'    => 'text',
					'label'   => __( 'متن پیش از کلمات چرخان', 'ementash' ),
					'default' => __( 'تولیدکننده‌ی', 'ementash' ),
				),
				'hero_words'        => array(
					'type'        => 'lines',
					'label'       => __( 'کلمات چرخان', 'ementash' ),
					'description' => __( 'هر خط یک مورد.', 'ementash' ),
					'default'     => __( "آتش‌نشانی\nحفاری\nآنتی‌فوم\nدیفومر", 'ementash' ),
				),
				'hero_text'         => array(
					'type'    => 'textarea',
					'label'   => __( 'متن', 'ementash' ),
					'default' => __( 'تولید مواد شیمیایی تخصصی متناسب با نیاز عملیاتی صنایع، با ظرفیت ۱۹ هزار تن در سال.', 'ementash' ),
				),
				'hero_btn1_label'   => array(
					'type'    => 'text',
					'label'   => __( 'دکمه اول: متن', 'ementash' ),
					'default' => __( 'مشاهده محصولات', 'ementash' ),
				),
				'hero_btn1_url'     => array(
					'type'        => 'url',
					'label'       => __( 'دکمه اول: لینک', 'ementash' ),
					'description' => __( 'اگر خالی باشد به فروشگاه (فهرست محصولات) می‌رود.', 'ementash' ),
					'default'     => '',
				),
				'hero_btn2_label'   => array(
					'type'    => 'text',
					'label'   => __( 'دکمه دوم: متن', 'ementash' ),
					'default' => __( 'استعلام قیمت', 'ementash' ),
				),
				'hero_btn2_url'     => array(
					'type'        => 'url',
					'label'       => __( 'دکمه دوم: لینک', 'ementash' ),
					'description' => __( 'اگر خالی باشد به صفحه تماس می‌رود.', 'ementash' ),
					'default'     => '',
				),
			),
		),
		'home_about'              => array(
			'title'  => __( 'صفحه اصلی: درباره ما', 'ementash' ),
			'fields' => ementash_toggle_field( 'about' ) + array(
				'home_about_eyebrow'   => array(
					'type'    => 'text',
					'label'   => __( 'برچسب بالای متن', 'ementash' ),
					'default' => __( 'درباره ما', 'ementash' ),
				),
				'home_about_text'      => array(
					'type'    => 'textarea',
					'label'   => __( 'متن', 'ementash' ),
					'default' => __( 'شرکت دانش‌بنیان ایمن تاش سپاهان (سی‌پیکو)، عضو گروه صنعتی تاش، از سال ۱۳۶۷ با تکیه بر دانش فنی و تجربه صنعتی فعالیت می‌کند. تولید مواد شیمیایی آتش‌نشانی را آغاز کرد و پس از آن به تولید مواد شیمیایی حفاری، آنتی‌فوم و دیفومر رسید. امروز با یکی از بزرگ‌ترین مراکز تحقیق و توسعه بخش خصوصی، محصولاتش را به صنایع نفت، گاز، پتروشیمی، فولاد و هواپیمایی عرضه و به چند کشور صادر می‌کند.', 'ementash' ),
				),
				'home_about_image'     => array(
					'type'    => 'image',
					'label'   => __( 'عکس', 'ementash' ),
					'default' => 0,
				),
				'home_about_btn_label' => array(
					'type'    => 'text',
					'label'   => __( 'متن دکمه', 'ementash' ),
					'default' => __( 'بیشتر درباره ما', 'ementash' ),
				),
				'home_about_url'       => array(
					'type'        => 'url',
					'label'       => __( 'لینک دکمه', 'ementash' ),
					'description' => __( 'اگر خالی باشد به برگه درباره ما می‌رود.', 'ementash' ),
					'default'     => '',
				),
			),
		),
		'stats'                   => array(
			'title'       => __( 'آمار', 'ementash' ),
			'description' => __( 'در صفحه اصلی و صفحه درباره ما نمایش داده می‌شود. آیتم بدون عدد نمایش داده نمی‌شود.', 'ementash' ),
			'fields'      => $stats,
		),
		'home_honors'             => array(
			'title'       => __( 'صفحه اصلی: افتخارات', 'ementash' ),
			'description' => __( 'کارت‌ها از «گواهینامه‌ها»یی خوانده می‌شوند که نوعشان «افتخار» است. در المنتور با شورت‌کد [ementash_honors].', 'ementash' ),
			'fields'      => ementash_toggle_field( 'honors' ) + array(
				'honors_title' => array(
					'type'    => 'text',
					'label'   => __( 'تیتر', 'ementash' ),
					'default' => __( 'افتخارات', 'ementash' ),
				),
				'honors_text'  => array(
					'type'    => 'textarea',
					'label'   => __( 'متن (اختیاری)', 'ementash' ),
					'default' => '',
				),
			),
		),
		'home_industries'         => array(
			'title'       => __( 'صفحه اصلی: صنایع', 'ementash' ),
			'description' => __( 'کارت‌ها از بخش «صنایع» در پیشخوان خوانده می‌شوند.', 'ementash' ),
			'fields'      => ementash_toggle_field( 'industries' ) + array(
				'industries_title' => array(
					'type'    => 'text',
					'label'   => __( 'تیتر', 'ementash' ),
					'default' => __( 'ما در صنایع زیر فعال هستیم', 'ementash' ),
				),
			),
		),
		'home_clients'            => array(
			'title'       => __( 'صفحه اصلی: مشتریان', 'ementash' ),
			'description' => __( 'لوگوها از بخش «مشتریان» در پیشخوان خوانده می‌شوند.', 'ementash' ),
			'fields'      => ementash_toggle_field( 'clients' ) + array(
				'clients_title' => array(
					'type'    => 'text',
					'label'   => __( 'تیتر', 'ementash' ),
					'default' => __( 'مشتریان ما', 'ementash' ),
				),
			),
		),
		'home_services'           => array(
			'title'       => __( 'صفحه اصلی: خدمات', 'ementash' ),
			'description' => __( 'کارت‌ها از بخش «خدمات» در پیشخوان خوانده می‌شوند.', 'ementash' ),
			'fields'      => ementash_toggle_field( 'services' ) + array(
				'services_title' => array(
					'type'    => 'text',
					'label'   => __( 'تیتر', 'ementash' ),
					'default' => __( 'خدمات ما', 'ementash' ),
				),
				'services_text'  => array(
					'type'    => 'textarea',
					'label'   => __( 'متن', 'ementash' ),
					'default' => __( 'خدمات قابل ارائه توسط شرکت ایمن تاش سپاهان', 'ementash' ),
				),
			),
		),
		'home_products'           => array(
			'title'       => __( 'صفحه اصلی: محصولات', 'ementash' ),
			'description' => __( 'دسته‌های سطح اول ووکامرس با تصویر و توضیح خودشان نمایش داده می‌شوند.', 'ementash' ),
			'fields'      => ementash_toggle_field( 'products' ) + array(
				'products_title'      => array(
					'type'    => 'text',
					'label'   => __( 'تیتر', 'ementash' ),
					'default' => __( 'محصولات', 'ementash' ),
				),
				'products_link_label' => array(
					'type'        => 'text',
					'label'       => __( 'متن لینک فهرست کامل', 'ementash' ),
					'description' => __( '{count} با تعداد محصولات منتشرشده جایگزین می‌شود.', 'ementash' ),
					// %s در پیش‌فرض‌ها مجاز نیست: get_theme_mod() آن را با sprintf و آدرس قالب پر می‌کند.
					'default'     => __( 'فهرست کامل {count} محصول', 'ementash' ),
				),
			),
		),
		'home_production_line'    => array(
			'title'  => __( 'صفحه اصلی: خط تولید', 'ementash' ),
			'fields' => $line,
		),
		'home_gallery'            => array(
			'title'  => __( 'صفحه اصلی: گالری کارخانه', 'ementash' ),
			'fields' => $gallery,
		),
		'home_textile'            => array(
			'title'  => __( 'صفحه اصلی: کمک‌نساجی', 'ementash' ),
			'fields' => ementash_toggle_field( 'textile' ) + array(
				'textile_title'     => array(
					'type'    => 'text',
					'label'   => __( 'تیتر', 'ementash' ),
					'default' => __( 'محصولات کمک‌نساجی', 'ementash' ),
				),
				'textile_text'      => array(
					'type'    => 'textarea',
					'label'   => __( 'متن', 'ementash' ),
					'default' => __( 'رطوبت‌دهنده سریع KX-327 برای صنایع نساجی.', 'ementash' ),
				),
				'textile_packs'     => array(
					'type'        => 'lines',
					'label'       => __( 'ظرف‌های بسته‌بندی', 'ementash' ),
					'description' => __( 'هر خط یک مورد.', 'ementash' ),
					'default'     => __( "بشکه ۶۰ لیتری\nبشکه ۲۲۰ لیتری\nمخزن ۱۰۰۰ لیتری", 'ementash' ),
				),
				'textile_image'     => array(
					'type'    => 'image',
					'label'   => __( 'عکس', 'ementash' ),
					'default' => 0,
				),
				'textile_badge'     => array(
					'type'        => 'text',
					'label'       => __( 'کد بزرگ روی عکس', 'ementash' ),
					'description' => __( 'اگر خالی باشد نمایش داده نمی‌شود.', 'ementash' ),
					'default'     => 'KX-327',
				),
				'textile_catalog'   => array(
					'type'        => 'file',
					'label'       => __( 'فایل کاتالوگ', 'ementash' ),
					'description' => __( 'اگر خالی باشد دکمه دانلود نمایش داده نمی‌شود.', 'ementash' ),
					'default'     => 0,
				),
				'textile_btn_label' => array(
					'type'    => 'text',
					'label'   => __( 'متن دکمه کاتالوگ', 'ementash' ),
					'default' => __( 'دریافت کاتالوگ KX-327', 'ementash' ),
				),
			),
		),
		'home_blog'               => array(
			'title'       => __( 'صفحه اصلی: مقاله‌ها', 'ementash' ),
			'description' => __( 'چهار نوشته آخر وبلاگ نمایش داده می‌شود.', 'ementash' ),
			'fields'      => ementash_toggle_field( 'blog' ) + array(
				'blog_title'      => array(
					'type'    => 'text',
					'label'   => __( 'تیتر', 'ementash' ),
					'default' => __( 'مقاله‌ها و دانش فنی', 'ementash' ),
				),
				'blog_text'       => array(
					'type'    => 'textarea',
					'label'   => __( 'متن', 'ementash' ),
					'default' => __( 'راهنمای انتخاب و استفاده از فوم، پودر و مواد شیمیایی صنعتی، از تیم فنی ایمن تاش.', 'ementash' ),
				),
				'blog_link_label' => array(
					'type'    => 'text',
					'label'   => __( 'متن لینک همه مقاله‌ها', 'ementash' ),
					'default' => __( 'همه مقاله‌ها', 'ementash' ),
				),
			),
		),
	);
}

/**
 * همه فیلدها به‌صورت تخت: کلید ← تعریف فیلد.
 */
function ementash_customizer_fields() {
	static $fields = null;
	if ( null === $fields ) {
		$fields = array();
		foreach ( ementash_customizer_schema() as $section ) {
			$fields += $section['fields'];
		}
	}
	return $fields;
}

/**
 * تابع پاک‌سازی مناسب هر نوع فیلد.
 */
function ementash_customizer_sanitizer( $type ) {
	switch ( $type ) {
		case 'textarea':
		case 'lines':
			return 'sanitize_textarea_field';
		case 'url':
			return 'esc_url_raw';
		case 'email':
			return 'sanitize_email';
		case 'checkbox':
			return 'ementash_sanitize_checkbox';
		case 'select':
			return 'ementash_sanitize_select';
		case 'page':
		case 'image':
		case 'video':
		case 'media':
		case 'file':
			return 'absint';
		default:
			return 'sanitize_text_field';
	}
}

function ementash_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * فقط مقدارهای موجود در لیست انتخاب پذیرفته می‌شوند.
 */
function ementash_sanitize_select( $value, $setting ) {
	$fields  = ementash_customizer_fields();
	$choices = isset( $fields[ $setting->id ]['choices'] ) ? $fields[ $setting->id ]['choices'] : array();
	$value   = sanitize_key( $value );
	return isset( $choices[ $value ] ) ? $value : $setting->default;
}

function ementash_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'ementash',
		array(
			'title'    => __( 'ایمن تاش', 'ementash' ),
			'priority' => 30,
		)
	);

	$mimes = array(
		'image' => 'image',
		'video' => 'video',
		'file'  => 'application',
		'media' => '',
	);

	$priority = 10;
	foreach ( ementash_customizer_schema() as $section_id => $section ) {
		$section_id = 'ementash_' . $section_id;
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => $section['title'],
				'description' => isset( $section['description'] ) ? $section['description'] : '',
				'panel'       => 'ementash',
				'priority'    => $priority,
			)
		);
		$priority += 5;

		foreach ( $section['fields'] as $key => $field ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => $field['default'],
					'sanitize_callback' => ementash_customizer_sanitizer( $field['type'] ),
				)
			);

			$args = array(
				'label'       => $field['label'],
				'description' => isset( $field['description'] ) ? $field['description'] : '',
				'section'     => $section_id,
			);

			if ( isset( $mimes[ $field['type'] ] ) ) {
				$wp_customize->add_control(
					new WP_Customize_Media_Control( $wp_customize, $key, $args + array( 'mime_type' => $mimes[ $field['type'] ] ) )
				);
				continue;
			}

			$types = array(
				'lines' => 'textarea',
				'page'  => 'dropdown-pages',
			);
			$args['type'] = isset( $types[ $field['type'] ] ) ? $types[ $field['type'] ] : $field['type'];
			if ( 'select' === $field['type'] ) {
				$args['choices'] = $field['choices'];
			}
			if ( 'url' === $field['type'] ) {
				$args['input_attrs'] = array( 'dir' => 'ltr' );
			}
			$wp_customize->add_control( $key, $args );
		}
	}
}
add_action( 'customize_register', 'ementash_customize_register' );

/**
 * رشته‌های متنی Customizer در Polylang (زبان‌ها ← ترجمه رشته‌ها) ثبت می‌شوند تا نسخه انگلیسی ترجمه داشته باشد.
 */
function ementash_register_pll_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}
	foreach ( ementash_customizer_fields() as $key => $field ) {
		if ( ! in_array( $field['type'], array( 'text', 'textarea', 'lines' ), true ) ) {
			continue;
		}
		$value = (string) get_theme_mod( $key, $field['default'] );
		if ( '' !== $value ) {
			pll_register_string( $key, $value, 'ementash', 'text' !== $field['type'] );
		}
	}
}
add_action( 'admin_init', 'ementash_register_pll_strings' );
