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
				<h1>Sorry, the page you are looking for doesn’t exist. Just like press freedom in <span id="impact-country" style="color: #900">________</span>.</h1>
			</div>
			<div class="inner-container">
				<p class="lead-in">Use our search feature for help in finding what you’re looking for.</p>
				<p>As for press freedom around the world, we’re working on it. Learn how, here: <a href="/news-and-information/threats-to-press/">Threats to Press</a></p>
			</div>
		</div>
	</section>
</main>

<script type="text/javascript">
	var countries = <?php echo json_encode(getThreatsToPressCountries()); ?>;
	var countriesCount = countries.length;
	setInterval(
		function() {
			var rand = Math.floor(Math.random() * countriesCount);
			document.getElementById('impact-country').innerHTML = countries[rand];
		},
	2000);
</script>

<?php get_footer(); ?>