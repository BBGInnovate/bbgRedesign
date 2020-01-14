<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package bbgRedesign
 */
require 'inc/bbg-functions-assemble.php';
get_header();
?>

<div class="outer-container" style="margin-bottom: 30px;">
	<div class="inner-container">
		<h1><span style="color: #900;">404!</span> That page can’t be found.</h1>
	</div>
</div>

<main id="main" role="main">
	<section class="outer-container">
		<div class="inner-container">
			<p class="lead-in">But here are some recent USAGM highlights from around the world.</p>
			<?php
				/* translators: %1$s: smiley */
				$archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives.', 'bbginnovate' ), convert_smilies( ':)' ) ) . '</p>';
				the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
			?>
		</div>
	</section>
</main>

<?php get_footer(); ?>