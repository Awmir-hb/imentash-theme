<?php
/**
 * نوشته‌های مرتبط زیر نوشته وبلاگ: تا سه نوشته هم‌دسته (یا جدیدترین‌ها).
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_posts = ementash_related_posts( get_the_ID() );
if ( ! $ementash_posts ) {
	return;
}
?>
<section class="em-section em-section--soft em-brelated" aria-labelledby="em-brelated-title">
	<div class="em-container">
		<h2 class="em-h2 em-brelated__title" id="em-brelated-title"><?php esc_html_e( 'مقاله‌های مرتبط', 'ementash' ); ?></h2>
		<ul class="em-posts">
			<?php
			foreach ( $ementash_posts as $ementash_post ) {
				get_template_part( 'template-parts/blog/card', null, array( 'post' => $ementash_post ) );
			}
			?>
		</ul>
	</div>
</section>
