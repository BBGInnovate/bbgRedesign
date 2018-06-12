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

<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<main id="bbg-home" class="site-content bbg-home-main" role="main">
			
			<?php
				$banner_result = get_homepage_banner_data();

				$banner_markup  = '<div class="page-post-featured-graphic">';
				$banner_markup .= 	'<div class="bbg-banner" ';
				$banner_markup .= 		'style="background-image: url(' . $banner_result['image_source'] . ') !important; background-position: ' . $banner_result['position'] . '">';
				$banner_markup .= 	'</div>';
				$banner_markup .=	'<div class="bbg-banner__cutline usa-grid">';
				$banner_markup .=		$banner_result['caption'];
				$banner_markup .=	'</div>';
				$banner_markup .= '</div>';
				echo $banner_markup;
			?>

			<section id="mission" class="outer-container">
				<h1 class="header-outliner">About the BBG</h1>
				<div class="container-grid">
				<?php
					$settings_result = get_site_settings_data();

					$mission  = '<p class="lead-in">';
					$mission .= 	$settings_result['intro_content'];
					$mission .= 	'<a href="';
					$mission .= 		$settings_result['intro_link'];
					$mission .= 		'" class="bbg__read-more">LEARN MORE »</a>';
					$mission .= '</p>';
					echo $mission;
				?>
				</div>
			</section>

			<!-- BBG NEWS -->
			<section id="recent-posts" class="outer-container">
				<h1 class="header-outliner">BBG News</h1>
				<div class="container-grid">
					<h2><a href="<?php echo get_permalink(get_page_by_path('news')); ?>">BBG News</a></h2>
				</div>
				<?php
					$featured_post = get_featured_post_data();

					$main_featured_post  = '<div class="home-feature-primary-post">';
					$main_featured_post .= 		'<a href="' . get_the_permalink() . '">';
					$main_featured_post .= 			$featured_post['media'];
					$main_featured_post .= 		'</a>';
					$main_featured_post .= 		'<h3>';
					$main_featured_post .= 			'<a href="' . get_the_permalink() . '">' . $featured_post['title'] . '</a>';
					$main_featured_post .= 		'</h3>';
					$main_featured_post .= 		'<p class="post-date">' . get_the_date() . '</p>';
					$main_featured_post .= 		'<p>' . get_the_excerpt() . '</p>';
					$main_featured_post .= '</div>';
					echo $main_featured_post;
				?>
				<?php
					$post_qty = 2;
					$recent_result = get_recent_post_data($post_qty);
					if (have_posts()) {
						while (have_posts()) {
							the_post();
							$recent_post  = '<div class="home-recent-posts">';
							$recent_post .= 	'<h4>';
							$recent_post .= 		'<a href="' . get_the_permalink() . '">';
							$recent_post .= 			get_the_title();
							$recent_post .= 		'</a>';
							$recent_post .= 	'</h4>';
							$recent_post .= 	'<p class="post-date">' . get_the_date() . '</p>';
							$recent_post .= 	'<p>' . wp_trim_words(get_the_content(), 50) . '</p>';
							$recent_post .= '</div>';
							echo $recent_post;
						}
					}
				?>
					<!-- <nav class="navigation posts-navigation bbg__navigation__pagination" role="navigation">
						<h2 class="screen-reader-text">Recent Posts Navigation</h2>
						<div class="nav-links">
							<div class="nav-previous">
								<a href="<?php //echo get_permalink( get_page_by_path('news') ); ?>" >Previous posts</a>
							</div>
						</div>
					</nav> -->
			</section>
			
			<!-- SOAPBOX, CORNER HERO  -->
			<section class="usa-grid">
				<?php
					$soap_result = get_soapbox_data();
					$corner_hero_result = get_corner_hero_data();

					// OPEN DIV FOR SOAPBOX AND CORNER HERO ON SIDE
					// OR JUST ONE OVER TOP
					if (($soap_result['toggle'] == 'on') && !empty($corner_hero_result)) {
					// if (!empty($soap_result) && !empty($corner_hero_result)) {
						$layout = 'side';
						echo '<div class="kr-five-twelfths">';
					} else {
						$layout = 'full';
						echo '<div class="soapbox container">';
					}
					if ($soap_result['toggle'] == 'on') {
						if ($layout == 'side') {
							$soapbox_side  = '<article class="' . $soap_result['class'] . ' soapbox-side">';
							$soapbox_side .= 	'<div class="usa-grid-full">';
							$soapbox_side .= 		'<div class="usa-width-two-thirds">';
							$soapbox_side .= 			$soap_result['content'];
							$soapbox_side .= 		'</div>';
							$soapbox_side .= 		'<div class="usa-width-one-third">';
							$soapbox_side .= 			$soap_result['image'];
							$soapbox_side .= 		'</div>';
							$soapbox_side .= 	'</div>';
							$soapbox_side .= '</article>';
							echo $soapbox_side;
						} else if ($layout == 'full') {
							$soapbox_full  = '<article class="' . $soap_result['class'] . ' soapbox-full">';
							$soapbox_full .= 	'<div class="full-width">';
							$soapbox_full .= 		'<div class="soapbox-image">';
							$soapbox_full .= 			$soap_result['image'];
							$soapbox_full .= 		'</div>';
							$soapbox_full .= 		'<div class="soapbox-content">';
							$soapbox_full .= 			$soap_result['content'];
							$soapbox_full .= 		'</div>';
							$soapbox_full .= 	'</div>';
							$soapbox_full .= '</article>';
							echo $soapbox_full;
						}
					}

					if ($corner_hero_result != "") {
						if ($layout == 'side') {
							$corner_hero_side  = '<article class="' . $corner_hero_result['class'] . ' corner-hero-side">';
							$corner_hero_side .= 	'<div class="usa-grid-full">';
							$corner_hero_side .= 		'<div class="usa-width-one-third">';
							$corner_hero_side .= 			$corner_hero_result['image'];
							$corner_hero_side .= 		'</div>';
							$corner_hero_side .= 		'<div class="usa-width-two-thirds">';
							$corner_hero_side .= 			$corner_hero_result['content'];
							$corner_hero_side .= 		'</div>';
							$corner_hero_side .= 	'</div>';
							$corner_hero_side .= '</article>';
							echo $corner_hero_side;
						} 
						else if ($layout == 'full') {
							$corner_hero_full  = '<article class="' . $corner_hero_result['class'] . ' corner-hero-full">';
							$corner_hero_full .= 	'<div class="full-width">';
							$corner_hero_full .= 		'<div class="corner-hero-image">';
							$corner_hero_full .= 			$corner_hero_result['image'];
							$corner_hero_full .= 		'</div>';
							$corner_hero_full .= 		'<div class="corner-hero-content">';
							$corner_hero_full .= 			$corner_hero_result['content'];
							$corner_hero_full .= 		'</div>';
							$corner_hero_full .= 	'</div>';
							$corner_hero_full .= '</article>';
							echo $corner_hero_full;
						}
						// echo $corner_hero_result;
					}
					echo '</div>';

					$impact_post_qty = '';
					if (($soap_result['toggle'] == 'on') && !empty($corner_hero_result)) {
						$impact_post_qty = 1;
						echo '<div class="kr-seven-twelfths">';
					}
					else {
						$impact_post_qty = 2;
						echo '<div class="usa-width-one-whole">';
					}
				?>
					<article id="impact-stories">
						<h2>
							<a href="<?php echo $impactPortfolioPermalink; ?>">Impact stories</a>
						</h2>
						<div class="usa-grid-full" style="margin-bottom: 1.5rem;">
							<?php get_impact_stories_data($impact_post_qty); ?>

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
$randomFeaturedThreatsID = false;
function get_threats_post_data() {
	$threatsToPressPost = get_field('homepage_threats_to_press_post', 'option');
	$threatsPermalink = get_permalink(get_page_by_path('threats-to-press'));
	$randomFeaturedThreatsID = false;
	
	if ($threatsToPressPost) {
		$randKey = array_rand($threatsToPressPost);
		$randomFeaturedThreatsID = $threatsToPressPost[$randKey];
	}
	build_threat_params();
}
get_threats_post_data();
						$threatsUsedPosts = array();
						$counter = 0;
						$threat_post_qty = 2;

						function build_threat_params() {
							if ($randomFeaturedThreatsID) {
								$qParams = array(
									'post__in' => array($randomFeaturedThreatsID),
								);
							} else {
								$qParams = array(
									'post_type' => array('post'),
									'posts_per_page' => 2,
									'orderby' => 'post_date',
									'order' => 'desc',
									'cat' => get_cat_id('Threats to Press'),
									'post__not_in' => $threatsUsedPosts
								);
							}
							query_posts($qParams);
							while (have_posts()) : the_post();
								$counter++;
								$id = get_the_ID();
								$threatsUsedPosts[] = $id;
								$postIDsUsed[] = $id;
								$permalink = get_the_permalink();

								$threat_markup  = '<article id="post-' . $id . '">';
								$threat_markup .= 	'<div class="image-block">';
								$threat_markup .= 		'<a href="' . $permalink . '" rel="bookmark" tabindex="-1">';
								$threat_markup .= 			get_the_post_thumbnail();
								$threat_markup .= 		'</a>';
								$threat_markup .= 	'</div>';
								$threat_markup .= 	'<div class="threat-copy">';
								$threat_markup .= 		'<header>';
								$threat_markup .= 			'<h5><a href="'.get_the_permalink().'">' . get_the_title() . '</a></h5>';
								$threat_markup .= 		'</header>';
								$threat_markup .= 		'<p>' . get_the_excerpt() . '</p>';
								$threat_markup .= 	'</div>';
								$threat_markup .= '</article>';
								echo $threat_markup;
							endwhile;
						}
						if ($counter > $threat_post_qty) {
							get_threats_post_data();
						}
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
