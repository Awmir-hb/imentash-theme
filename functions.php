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
