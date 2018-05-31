<?php
/**
 * The custom home page for the Broadcasting Board of Governors.
 * It includes:
 *      - the mission
 *      - a portfolio of recent impact stories,
 *      - recent stories,
 *      - an optional soapbox for senior leadership commentary,
 *      - updates on threats to press around the world
 *      - and a list of the entities.
 *
 * @package bbgRedesign
  template name: Custom BBG Home
 */

// FUNCTION THAT BUILD SECTIONS
require get_template_directory() . '/inc/bbg-functions-home.php';
require get_template_directory() . '/inc/bbg-functions-assemble.php';

$templateName = 'customBBGHome';

// GET CUSTOM FIELDS

/*** store a handful of page links that we'll use in a few places ***/
$impactPermalink = get_permalink( get_page_by_path('our-work/impact-and-results'));
$impactPortfolioPermalink = get_permalink( get_page_by_path('our-work/impact-and-results/impact-portfolio'));


/*** add any posts from custom fields to our array that tracks post IDs that have already been used on the page ***/
$postIDsUsed = array();

$threatsToPressPost = get_field('homepage_threats_to_press_post', 'option');
$threatsPermalink = get_permalink(get_page_by_path('threats-to-press'));
$randomFeaturedThreatsID = false;
if ($threatsToPressPost) {
	$randKey = array_rand($threatsToPressPost);
	$randomFeaturedThreatsID = $threatsToPressPost[$randKey];
}

get_header();
?>
<!-- test rsub -->
<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<main id="bbg-home" class="site-content bbg-home-main" role="main">
			<?php get_homepage_banner_data(); ?>

			<!-- MISSION -->
			<section id="mission" class="usa-section usa-grid">
				<h1 class="header-outliner">About the BBG</h1>
				<?php
					$settings_result = get_site_settings_data();
					$site_introduction  = '<p id="site-intro" class="usa-font-lead">';
					$site_introduction .= 	$settings_result['intro_content'];
					$site_introduction .= 	'<a href="';
					$site_introduction .= 		$settings_result['intro_link'];
					$site_introduction .= 		'" class="bbg__read-more">LEARN MORE »</a>';
					$site_introduction .= '</p>';
					echo $site_introduction;
				?>
			</section>

			<!-- BBG NEWS -->
			<section id="recent-posts" class="usa-section usa-grid bbg__home__recent-posts">
				<h1 class="header-outliner">BBG News</h1>
				<h2><a href="<?php echo get_permalink(get_page_by_path('news')); ?>">BBG News</a></h2>
				<div class="usa-width-two-thirds">
				<?php
					// FEATURED POST FROM HOMEPAGE SETTINGS OR MOST RECENT POST
					$featured_post = display_featured_post();
				?>
				</div>

				<div class="usa-width-one-third">
					<?php
						$post_qty = 2;

						display_additional_recent_posts($post_qty );
					?>
					<nav class="navigation posts-navigation bbg__navigation__pagination" role="navigation">
						<h2 class="screen-reader-text">Recent Posts Navigation</h2>
						<div class="nav-links">
							<div class="nav-previous">
								<a href="<?php echo get_permalink( get_page_by_path('news') ); ?>" >Previous posts</a>
							</div>
						</div>
					</nav>
				</div>
			</section>
			
			<!-- SOAPBOX, CORNER HERO  -->
			<section class="usa-grid">
				<?php
					// TEST FOR SHOWING BOTH, ONE OR NEITHER
					$test = 1;

					$soap_result = get_soap_box_data();
					$corner_hero_result = get_corner_hero_data();

					if ($test == 1) {
					// if (!empty($soap_result) && !empty($corner_hero_result)) {
						echo '<div class="kr-five-twelfths">';
					} else {
						echo '<div class="usa-width-one-whole">';
					}
					if ($soap_result) {
						echo $soap_result;
					}
					if ($corner_hero_result) {
						echo $corner_hero_result;
					}
					echo '</div>';

					$qty = '';
					if ($test == 1) {
					// if (!empty($soap_result) && !empty($corner_hero_result)) {
						$qty = 1;
						echo '<div class="kr-seven-twelfths">';
					} else {
						$qty = 2;
						echo '<div class="usa-width-one-whole">';
					}
				?>
					<article id="impact-stories">
						<h2>
							<a href="<?php echo $impactPortfolioPermalink; ?>">Impact stories</a>
						</h2>
						<div class="usa-grid-full" style="margin-bottom: 1.5rem;">
							<?php get_impact_stories_data($qty); ?>

							<div class="usa-width-one-whole u--space-below-mobile--large">
								<a href="<?php echo $impactPermalink; ?>">Find out how the BBG defines and measures impact »</a>
							</div>
						</div>
					</article>
				</div>
			</section>

			<!-- THREATS TO PRESS  -->
			<section id="threats-to-journalism" class="usa-section bbg__ribbon">
				<div class="usa-grid">
					<h2 class="bbg__label small"><a href="<?php echo $threatsPermalink; ?>">Threats to Press</a></h2>
				</div>

				<div class="usa-grid">
					<div class="threats-box">
					<?php
						$threats_post_qty = 2;
						$threatsUsedPosts = array();
							if ($randomFeaturedThreatsID) {
								$qParams = array(
									'post__in' => array($randomFeaturedThreatsID),
								);
							} else {
								$qParams = create_threats_post_query_params($threats_post_qty, $threatsUsedPosts);
							}
							query_posts($qParams);

							$counter = 0;
							if (have_posts()) {
								while (have_posts()) : the_post();
									$counter++;
									$id = get_the_ID();
									$threatsUsedPosts[] = $id;
									$postIDsUsed[] = $id;
									$permalink = get_the_permalink();

									$threat_markup  = '<article id="post-' . $id . '">';
									// $threat_markup  = '<article id="post-' . $id . '" ' . get_post_class() . '>';
									$threat_markup .= 	'<div class="image-block">';
									$threat_markup .= 		'<a href="' . $permalink . '" rel="bookmark" tabindex="-1">';
									$threat_markup .= 			get_the_post_thumbnail();
									$threat_markup .= 		'</a>';
									$threat_markup .= 	'</div>';
									$threat_markup .= 	'<div class="threat-copy">';
									$threat_markup .= 		'<header><h5 class=""><a href="'.get_the_permalink().'">' . get_the_title() . '</a></h5></header>';
									$threat_markup .= 		'<p>' . get_the_excerpt() . '</p>';
									$threat_markup .= 	'</div>';
									$threat_markup .= '</article>';
									echo $threat_markup;
								endwhile;
							}	
							wp_reset_query();
					?>
					</div>
				</div>
			</section>

			<!-- ENTITY LIST -->
			<?php echo get_entity_data(); ?>

			<!-- Quotation -->
			<section class="usa-section ">
				<div class="usa-grid">
					<?php
						$q = getRandomQuote( 'allEntities', $postIDsUsed );
						if ($q) {
							$postIDsUsed[] = $q["ID"];
							outputQuote($q);
						}
					?>
				</div>
			</section><!-- Quotation -->

		</main>
	</div><!-- #primary .content-area -->
	<div id="secondary" class="widget-area" role="complementary">
	</div><!-- #secondary .widget-area -->
</div><!-- #main .site-main -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>


<script type="text/javascript">
function navSlide(){
	var currentScroll = jQuery( "html" );
	//console.log("Currently scrolled to: " + currentScroll.scrollTop());

	var p = jQuery( "#threats-to-journalism" );
	var offset = p.offset();
	//console.log("#threats-to-journalism position: " + offset.top);

	if (currentScroll.scrollTop() > offset.top){
		//console.log("the Threats-to-press section should be at the top of the page");
		jQuery(".bbg__social__container").hide();
	} else {
		//console.log("the Threats-to-press section is below the top of the page");
		jQuery(".bbg__social__container").show();
	}
}

jQuery(window).scroll(navSlide);
</script>
