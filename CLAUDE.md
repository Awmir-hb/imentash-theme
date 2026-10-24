# قالب اختصاصی ایمن تاش سپاهان (ementash)

قالب کلاسیک وردپرس (PHP، نه بلاک‌تم) برای سایت ementash.com، با ووکامرس در حالت کاتالوگ.
بدون قالب آماده و **بدون افزونه ACF**. صفحات پیش‌فرض با PHP خود قالب ساخته می‌شوند، ولی هر بخش و هر صفحه باید با Elementor / Elementor Pro قابل جایگزینی باشد (بخش «سازگاری با المنتور»).

## فایل‌های مرجع طراحی
پوشه `design/` **نسخه اولیه** تأییدشده است و ممکن است بعداً زیاد عوض شود؛ برای همین کد باید ماژولار باشد (بخش «ماژولار بودن»). این فایل‌ها کد قابل استفاده نیستند. HTML مرجع هستند با سینتکس قالب‌سازی ابزار طراحی:
- `{{name}}` یعنی مقدار پویا، `<sc-for list=...>` یعنی حلقه، `<sc-if value=...>` یعنی شرط.
- داده‌های نمونه در تگ `<script type="text/x-dc">` انتهای هر فایل است.
- CSS داخل `<helmet><style>` منبع اصلی رنگ، فاصله و انیمیشن است. مقادیر را منتقل کن، ولی به‌صورت CSS variable و با کلاس‌های پیشوند‌دار `em-`.
- فونت IBM Plex Sans Arabic که در طراحی آمده **استفاده نشود**.

| فایل طراحی | قالب وردپرس |
|---|---|
| Industrial.dc.html | front-page.php (صفحه اصلی دسکتاپ) |
| MobileHome.dc.html, MobileMenu.dc.html | نسخه موبایل همان صفحه اصلی، با media query، نه صفحه جدا |
| Products.dc.html | woocommerce/archive-product.php و taxonomy-product_cat |
| Product.dc.html | woocommerce/single-product.php |
| About.dc.html | page-about.php |
| Contact.dc.html | page-contact.php |

اگر بین طراحی و این فایل تناقض بود، این فایل معتبر است. اگر چیزی در هیچ‌کدام نبود، **بپرس، حدس نزن.**

## قوانین ثابت
- زبان سایت فارسی، `dir="rtl"`. همه رشته‌ها با `__()`/`esc_html__()` و text domain `ementash`، تا نسخه انگلیسی بعداً با Polylang اضافه شود.
- هیچ متن، عدد یا عکس مشتری hardcode نشود. همه از پنل (Customizer، CPT، ووکامرس، متاباکس) بیاید. مقدار پیش‌فرض فقط برای دمو.
- **آدرس‌های فعلی سایت نباید عوض شوند**: `/product/...` و `/product-category/...` و آدرس نوشته‌ها و برگه‌ها همان بماند (سئو).
- قالب متا تگ سئو و schema تولید نکند. این کار افزونه سئوی سایت است. فقط `title-tag` و HTML معنایی درست.
- بدون jQuery در کد قالب. JS خالص، کوچک و با `defer`.
- فونت‌ها **نهایی** هستند: Vazirmatn برای متن، Readex Pro برای تیترها (هر دو OFL، variable، woff2). self-host در `assets/fonts`، نه Google Fonts، چون در ایران کند یا مسدود است. IBM Plex استفاده نشود.
- پیشوند همه کلاس‌ها، idها و data-attributeهای قالب در HTML/CSS/JS: `em-` (مثل `em-header`، `em-container`). پیشوند توابع PHP: `ementash_`.
- `prefers-reduced-motion` برای همه انیمیشن‌ها رعایت شود (نمونه در CSS طراحی هست).
- تصاویر با `wp_get_attachment_image()` با سایزهای ثبت‌شده و `loading="lazy"`، به‌جز تصویر هیرو.

## امنیت (اجباری)
- هر ورودی: `sanitize_text_field`, `sanitize_email`, `absint`, `esc_url_raw`, `wp_kses_post` بسته به نوع.
- هر خروجی: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`.
- هر فرم و ذخیره متاباکس: nonce + `current_user_can` + چک autosave.
- فرم تماس: nonce، فیلد honeypot، محدودیت ارسال با transient بر اساس IP، ذخیره درخواست در CPT خصوصی `em_inquiry` + ارسال ایمیل با `wp_mail`. هیچ کوئری SQL خام.
- `defined('ABSPATH') || exit;` بالای هر فایل PHP.

## توکن‌های طراحی (در `:root`)
```
--navy-900:#08113F   /* هدر، فوتر، سکشن‌های تیره */
--navy-800:#0B1550   /* کارت خدمات، خط تولید */
--blue:#00007F       /* رنگ برند، دکمه و لینک؛ فقط روی زمینه روشن */
--blue-hover:#1B1BA8 /* hover رنگ برند */
--blue-light:#8FB4FF /* آبی روی زمینه‌های تیره (هیرو، هدر، فوتر، منو) */
--bg-soft:#EEF2FA    /* سکشن‌های روشن یکی‌درمیان */
--ink:#0A1033        /* متن اصلی */
--muted:#4E5878
--line:#C9D0E6
```
- پدینگ سکشن‌ها `64px 40px`، کانتینر `max-width:1320px`. موبایل: پدینگ کناری ۲۰ پیکسل.
- بدون رنگ قرمز یا نارنجی در کل سایت.
- تیترهای بزرگ (هیرو و عنوان سکشن‌ها) با Readex Pro در وزن Light (۳۰۰)، از توکن `--fw-display`.
- توکن‌های تکمیلی (رنگ‌های روی زمینه تیره، خطوط، فونت‌ها، فاصله‌ها، گردی‌ها) همه در `:root` فایل `assets/css/main.css` تعریف می‌شوند.

## ماژولار بودن (اجباری)
طرح اولیه ممکن است زیاد عوض شود، پس:
- هر بخش صفحه یک template-part جدا (`template-parts/...`). قالب‌های اصلی فقط بخش‌ها را صدا می‌زنند.
- **همه رنگ‌ها، فونت‌ها و فاصله‌ها از CSS variable** خوانده شوند. هیچ رنگ hex/rgba خارج از `:root` نوشته نشود.
- **هیچ متنی hardcode نشود.** محتوای مشتری از پنل (Customizer، CPT، ووکامرس، متاباکس) و برچسب‌های رابط کاربری با `__()`.

## ساختار قالب
```
ementash/
  style.css  functions.php  screenshot.png
  header.php  footer.php  front-page.php  page.php  page-about.php  page-contact.php
  single.php  archive.php  index.php  404.php  search.php
  templates/full-width.php   // قالب برگه «تمام‌عرض»
  inc/
    setup.php          // theme supports, menus, image sizes
    elementor.php      // ثبت locationها، تشخیص صفحه المنتوری
    enqueue.php        // css/js، فقط هرجا لازم است
    customizer.php     // تنظیمات عمومی و صفحه اصلی
    cpt.php            // پست‌تایپ‌ها
    metaboxes.php      // متاباکس‌های بومی
    woocommerce.php    // حالت کاتالوگ و تب‌ها
    contact-form.php   // هندلر فرم
    template-tags.php  // توابع کمکی (اعداد فارسی، آیکون‌ها)
  template-parts/  site-header.php  site-footer.php  mobile-menu.php  mobile-bar.php  page-head.php  entry-content.php  loop.php
  template-parts/home/  hero.php about.php industries.php clients.php services.php products.php production-line.php gallery.php textile.php blog.php
  woocommerce/  archive-product.php  single-product.php  content-product.php
  assets/  css/  js/  fonts/  img/  icons/ (SVG خطی)
```

## منبع داده هر بخش

**Customizer** (پنل «ایمن تاش»)
- عمومی: لوگو (custom-logo)، تلفن‌ها، آدرس دفاتر اصفهان، تهران و کارخانه، ایمیل، ساعات کاری، لینک شبکه‌های اجتماعی، متن کوتاه فوتر.
- هیرو: ویدئو یا گیف دسکتاپ، ویدئوی عمودی موبایل، تصویر پوستر، تیتر، کلمات چرخان (هر خط یکی)، متن، دو دکمه (متن و لینک). دکمه دوم پیش‌فرض «استعلام قیمت» به صفحه تماس است.
- درباره ما در صفحه اصلی: متن، عکس، لینک دکمه «بیشتر درباره ما».
- آمار: ۴ آیتم (عدد، برچسب، آیکون از لیست ثابت).
- بخش کمک‌نساجی KX-327: متن، عکس، فایل کاتالوگ.
- روشن یا خاموش کردن هر سکشن صفحه اصلی.

**پست‌تایپ‌ها** (`show_in_rest` روشن، بدون صفحه عمومی به‌جز جایی که گفته شده)
- `em_service`: خدمات (عنوان، تصویر شاخص، آیکون از لیست ثابت truck/lab/chat/train، لینک).
- `em_industry`: صنایع (عنوان، تصویر، لینک). لینک‌ها به برگه‌های فعلی صنایع در سایت می‌روند.
- `em_client`: مشتریان (نام، لوگو) برای نوار چرخان.
- `em_timeline`: خط زمانی درباره ما (سال، عنوان، متن).
- `em_certificate`: گواهینامه‌ها (عنوان، تصویر، فایل PDF اختیاری).
- `em_inquiry`: درخواست‌های فرم تماس (خصوصی، فقط در پیشخوان).
- ترتیب نمایش با `menu_order`.

**ووکامرس**
- حالت کاتالوگ: بدون قیمت، سبد خرید، تسویه حساب یا حساب کاربری. ریدایرکت cart و checkout به فروشگاه.
- دسته‌ها: تصویر و توضیح بومی `product_cat`. بخش محصولات صفحه اصلی: دسته‌های سطح اول، با **کلیک** (نه هاور) عوض می‌شوند و هر دسته عکس خودش را دارد.
- جدول مشخصات فنی = ویژگی‌های محصول (attributes).
- کلاس آتش (A, B, C, D) = ویژگی سراسری `pa_fire_class`.
- تب «کاربردها» و «بسته‌بندی» = دو متاباکس با `wp_editor`. تب «معرفی» = توضیحات محصول.
- دکمه «استعلام قیمت این محصول» به صفحه تماس با `?product=ID` می‌رود و فرم نام محصول را از پیش پر می‌کند.
- **دیتاشیت ندارد.** دکمه دانلود دیتاشیت نساز.

**وبلاگ**: نوشته‌های بومی وردپرس، کارت‌های هم‌اندازه با نسبت 4/3.

## سازگاری با المنتور (Elementor و Elementor Pro)
- **Theme Builder:** locationهای اصلی (`header`، `footer`، `single`، `archive`) در `inc/elementor.php` ثبت می‌شوند. هر قالب اول `elementor_theme_do_location()` را صدا می‌زند (از طریق `ementash_elementor_location()`). فقط اگر Theme Builder آن location را نگرفته بود، خروجی خود قالب چاپ شود:
  - `header.php` ← `header`، `footer.php` ← `footer`
  - `single.php`، `page.php`، `404.php` ← `single`
  - `archive.php`، `search.php` ← `archive`
  - `woocommerce/single-product.php` ← `single`، `woocommerce/archive-product.php` ← `archive` (تا Woo Builder المنتور پرو بتواند جایگزین کند)
- اگر هدر قالب با هدر المنتور جایگزین شد، منوی تمام‌صفحه موبایل قالب هم چاپ نشود. اگر فوتر جایگزین شد، نوار ثابت پایین موبایل هم چاپ نشود.
- **هر صفحه‌ای که با المنتور ساخته شده** (حتی صفحه اصلی، نوشته، محصول) فقط `the_content()` را نشان بدهد، نه template-partهای قالب (حتی سربرگ صفحه). تشخیص با `ementash_is_elementor()` که `is_built_with_elementor()` سند المنتور را چک می‌کند. `front-page.php` هم باید همین چک را اول انجام بدهد.
- قالب برگه «تمام‌عرض» (`templates/full-width.php`): header و footer قالب + محتوای بدون کانتینر.
- استایل‌های قالب نباید ویجت‌های المنتور را بشکنند: selectorها محدود به کلاس‌های `em-`. استایل سراسری فقط برای `body` و موارد خیلی پایه.

## روش کار
- هر بار فقط یک فاز. آخر هر فاز خلاصه بده چه فایل‌هایی ساخته یا عوض شد و چه چیزی باید دستی تست شود.
- قبل از شروع فاز جدید، اگر چیزی مبهم است، سؤال بپرس.
- کد تمیز، با کامنت فارسی کوتاه فقط جایی که منطق واضح نیست.
- بعد از هر فاز: `php -l` روی فایل‌های تغییرکرده.

## فازها
1. اسکلت قالب، توکن‌ها، فونت‌ها، header و footer (دسکتاپ و منوی تمام‌صفحه موبایل)، نوار ثابت پایین موبایل.
2. Customizer + CPTها + متاباکس‌ها.
3. صفحه اصلی، سکشن به سکشن، مطابق Industrial.dc.html و MobileHome.dc.html.
4. ووکامرس: حالت کاتالوگ، آرشیو و دسته، صفحه محصول.
5. درباره ما، تماس با ما و هندلر فرم.
6. وبلاگ، 404، جستجو، بررسی ریسپانسیو، دسترس‌پذیری و سرعت.
