<?php
/**
 * بارگذاری ماژول‌های قالب ایمن تاش.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

define( 'EMENTASH_DIR', get_template_directory() );
define( 'EMENTASH_URI', get_template_directory_uri() );

require EMENTASH_DIR . '/inc/template-tags.php';
require EMENTASH_DIR . '/inc/setup.php';
require EMENTASH_DIR . '/inc/elementor.php';
require EMENTASH_DIR . '/inc/enqueue.php';
require EMENTASH_DIR . '/inc/customizer.php';
require EMENTASH_DIR . '/inc/cpt.php';
require EMENTASH_DIR . '/inc/metaboxes.php';
require EMENTASH_DIR . '/inc/honors.php';
require EMENTASH_DIR . '/inc/woocommerce.php';
