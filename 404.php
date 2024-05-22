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

<main id="main" role="main">
	<section class="outer-container">
		<div class="grid-container">
			<div class="inner-container">
				<h2><span style="color: #900; font-size: 2em;">404!</span></h2>
				<h1>Sorry, the page you are looking for doesn’t exist.
			</div>
			<div class="inner-container">
				<p class="lead-in">Use our search feature for help in finding what you’re looking for.</p>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>