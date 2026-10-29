<?php
/**
 * درباره ما: خط زمانی (پست‌تایپ em_timeline) روی زمینه سرمه‌ای. تیتر از متاباکس برگه.
 *
 * @package ementash
 */

defined( 'ABSPATH' ) || exit;

$ementash_items = ementash_get_items( 'em_timeline' );
if ( ! $ementash_items ) {
	return;
}
$ementash_title = (string) ementash_meta( get_the_ID(), 'about_timeline_title' );
?>
<section class="em-section em-timeline"<?php echo '' !== $ementash_title ? ' aria-labelledby="em-timeline-title"' : ''; ?>>
	<div class="em-container em-timeline__in">
		<?php if ( '' !== $ementash_title ) : ?>
			<h2 class="em-h2 em-timeline__h2" id="em-timeline-title"><?php echo ementash_bdi( $ementash_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<?php endif; ?>
		<ol class="em-tl">
			<?php
			foreach ( $ementash_items as $ementash_item ) :
				$ementash_year = (string) ementash_meta( $ementash_item->ID, 'year' );
				$ementash_text = (string) ementash_meta( $ementash_item->ID, 'text' );
				?>
				<li class="em-tl__item<?php echo ementash_meta( $ementash_item->ID, 'key' ) ? ' em-tl__item--key' : ''; ?>">
					<span class="em-tl__dot" aria-hidden="true"></span>
					<?php if ( '' !== $ementash_year ) : ?>
						<span class="em-tl__year"><?php echo ementash_bdi( $ementash_year, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<h3 class="em-tl__title"><?php echo ementash_bdi( get_the_title( $ementash_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
					<?php if ( '' !== $ementash_text ) : ?>
						<p class="em-tl__text"><?php echo ementash_bdi( $ementash_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
