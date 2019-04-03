<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Hot Spot
 */

require 'inc/bbg-functions-assemble.php';

// $challenges = get_field( 'hot_spot_challenges', '', true ); // THIS IS THE ONLY INSTANCE)
$tag = get_field('hot_spot_tag', '', true);
// $priorities = get_field( 'hot_spot_strategic_priorities', '', true ); // THIS IS THE ONLY INSTANCE)
// $programming = get_field( 'hot_spot_special_programming', '', true ); // THIS IS THE ONLY INSTANCE)
$mapImage = get_field( 'hot_spot_map_image', '', true );
$mapImageSrc = $mapImage['sizes']['medium'];

$featuredImagesData = get_field( 'hot_spot_rotating_featured_images', '', true );
$randomFeaturedImage = $featuredImagesData[array_rand($featuredImagesData)];

$pressFreedomIntro = get_field('site_setting_press_freedom_intro', 'options', 'false');

// CREATE THREATS TO PRESS ARRAY
$threat_query_args = array(
	'post_type' => array('post'),
	'cat' => get_cat_id('Threats to Press'),
	'posts_per_page' => 2,
	'post_status' => array('publish'),
	'orderby' => 'date',
	'order' => 'DESC',
	'tag' => $tag->slug
);

$used_post_id_group = array();
$threatsToPress = array();
$threat_query = new WP_Query($threat_query_args);

if ($threat_query -> have_posts()) {
	while ($threat_query -> have_posts())  {
		$threat_query -> the_post();
		$id = $threat_query -> post -> ID;
		$used_post_id_group[] = $id;
		
		$threatsToPress[] = array(
			'url' => get_the_permalink(),
			'title' => get_the_title(),
			'id' => $id,
			'thumb' => get_the_post_thumbnail($id, 'medium-thumb'),
			'excerpt' => my_excerpt($id)
		);
	}
}

if (count($threatsToPress) > 0) {
	$threat_article  = '<div class="nest-container"">';
	$threat_article .= 	'<div class="inner-container">';
	foreach ($threatsToPress as $threat_post) {
		$threat_article .= '<article class="grid-half">';
		$threat_article .=	'<a tabindex="-1" href="' . $threat_post['url'] . '">' . $threat_post['thumb'] . '</a>';
		$threat_article .= 	'<h4><a href="' . $threat_post['url'] . '">' . $threat_post['title'] . '</a></h4>';
		$threat_article .= '</article>';
	}
	$threat_article .= 	'</div>';
	$threat_article .= '</div>';
}
$threats_shortcode_result = $threat_article;


function network_news_posts() {
	// INITIATED EARLIER IN THIS FILE
	global $used_post_id_group;

	$network_news_args = array(
		'post_type' => array('post'),
		'posts_per_page' => 3,
		'tag' => $tag -> slug,
		'orderby' => 'date',
		'order' => 'DESC',
		'post__not_in' => $used_post_id_group
	);

	$news_from_networks = array();
	$i = 0;
	$custom_query = new WP_Query($network_news_args);
	if ($custom_query -> have_posts()) {
		while ($custom_query -> have_posts())  {
			$custom_query -> the_post();

			$network_news_item = array(
				'url' => get_the_permalink(),
				'title' => get_the_title(),
				'id' => get_the_ID(),
				'thumb' => get_the_post_thumbnail($id)
			);
			${"network_news_block" . $i} = $network_news_item;
			array_push($news_from_networks, ${"network_news_block" . $i});
			$i++;
		}
		return $news_from_networks;
	}
}


if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_id();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}

get_header();
?>


<main id="main" role="main">

<?php
	$featuredImageSrc = $randomFeaturedImage['hot_spot_rotating_featured_image']['sizes']['large-thumb'];
	$featuredImageBackgroundPosition = $randomFeaturedImage['hot_spot_rotating_featured_image_background_position'];

	$hotspot_featured_media  = '<div class="page-post-featured-graphic">';
	$hotspot_featured_media .= 	'<div class="bbg__article-header__banner--profile" style="';
	$hotspot_featured_media .= 		'background-image: url(' . $featuredImageSrc . ');';
	$hotspot_featured_media .= 		'background-position: ' . $featuredImageBackgroundPosition . '">';
	$hotspot_featured_media .= 	'</div>';
	$hotspot_featured_media .= 	'</div>';
	echo $hotspot_featured_media;

	$hotspot_page_title  = '<div class="outer-container">';
	$hotspot_page_title .= 	'<div class="bbg__profile-photo">';
	$hotspot_page_title .= 		'<img class="bbg__profile-photo__image" src="' . $mapImageSrc . '" alt="">';
	$hotspot_page_title .= 	'</div>';
	$hotspot_page_title .= 	'<div class="bbg__profile-title">';
	$hotspot_page_title .= 		'<h2>' . str_replace("Private:", "Draft:", get_the_title()) . '</h2>';
	$hotspot_page_title .= 		'<h5 class="entry-category bbg__profile-tagline">';
	$hotspot_page_title .= 			'<a href="' . get_permalink(get_page_by_path('hot-spots')) . '">Hot Spots</a>';
	$hotspot_page_title .= 		'</h5>';
	$hotspot_page_title .= 	'</div>';
	$hotspot_page_title .= '</div>';
	// echo $hotspot_page_title;
?>
	<div class="outer-container">
		<div class="main-content-container">

			<div class="nest-container">
				<div class="inner-container">
					<div class="icon-side-content-container">
						<?php echo '<img src="' . $mapImageSrc . ' alt=''">'; ?>
					</div>

					<div class="icon-main-content-container">
					<?php
						$hotspot_page_title  = '<h2>' . str_replace("Private:", "Draft:", get_the_title()) . '</h2>';
						$hotspot_page_title .= 	'<h5 class="entry-category bbg__profile-tagline">';
						$hotspot_page_title .= 		'<a href="' . get_permalink(get_page_by_path('hot-spots')) . '">Hot Spots</a>';
						$hotspot_page_title .= 	'</h5>';
						$hotspot_page_title .= '</h2>';
						echo $hotspot_page_title;

						// PAGE CONTENT
						$body_copy  = '<div class="page-content">';
						$body_copy .= 	$page_content;
						$body_copy .= '</div>';
						echo $body_copy;

						// FREEFORM TEXT AREA
						if (have_rows('hot_spot_freeform_textareas')) {
							while(have_rows('hot_spot_freeform_textareas')) {
								the_row();
								$label = get_sub_field('hot_spot_freeform_textarea_label');
								$content = get_sub_field('hot_spot_freeform_textarea_text');
								$content = str_replace("[threatstopress]", $threats_shortcode_result, $content);
								echo '<h3>' . $label . '</h3>';
								echo $content;
							}
						}
					?>
					</div>
				</div>
			</div>

		</div>
		<div class="side-content-container">

			<article>
				<div class="nest-container">
					<div class="inner-container">
						<div class="grid-container">
							<h5>Languages Served</h5>
						</div>

						<?php while(have_rows('hot_spot_languages')): the_row(); ?>
							<div class="grid-container">
								<h6><?php the_sub_field('hot_spot_language_name'); ?></h6>
							</div>
							<?php 
								if( have_rows('hot_spot_language_sites') ): 
							?>
								<?php 
									while(have_rows('hot_spot_language_sites')): 
										the_row();
										$link = get_sub_field('hot_spot_site_url');
										$serviceInLanguage = get_sub_field('hot_spot_language_site_name_in_language');
										$serviceInEnglish = get_sub_field('hot_spot_site_name_in_english');
										$hotSpotNetwork = get_sub_field('hot_spot_site_network');
										$serviceName = $serviceInLanguage;
										$entityLogo = getTinyEntityLogo($hotSpotNetwork);
								?>
								<div class="inner-container">
									<div class="small-side-content-container">
										<img width="20" height="20" style="height:20px !important; width:20px !important; max-width:none; margin-bottom:0;" src="<?php if ($entityLogo) { echo $entityLogo; } ?>" alt="Entity logo">
										<a title="<?php echo $serviceInEnglish; ?>"  target="_blank" href="<?php echo $link; ?>" class="bbg__jobs-list__title"><?php echo $serviceName; ?></a>
									</div>
									<div class="small-main-content-container">
										<?php echo str_replace("http://", "", $link); ?>
									</div>
								</div>

								<?php endwhile; ?>
							<?php endif; ?>
						<?php endwhile; ?>
					</div>
				</div>
			</article>

			<?php 
				if (have_rows('hot_spot_press_freedom_numbers')):
					echo '<article>';
					echo '<h5>Press Freedom</h5>';
					echo $pressFreedomIntro;
					echo '<ul>';
					while ( have_rows('hot_spot_press_freedom_numbers') ) : the_row();
						$countryName = get_sub_field('hot_spot_press_freedom_country_name');
						$freedomIndex = get_sub_field('hot_spot_press_freedom_index');
						echo "<li>$countryName ($freedomIndex)</li>";
					endwhile;
					echo '</ul>';
					echo '</article>';
				endif;
			?>

			<?php
				$xxx = network_news_posts();
				if (count($xxx) > 0) {
					echo '<article class="inner-container">';
					echo 	'<h5>News from our Networks</h5>';
					foreach ($xxx as $news_post) {
						$network_article  = '<div class="nest-container">';
						$network_article .= 	'<div class="inner-container">';
						$network_article .= 		'<div class="side-content-container">';
						$network_article .=				'<a tabindex="-1" href="' . $news_post['url'] . '">' . $news_post['thumb'] . '</a>';
						$network_article .=			'</div>';
						$network_article .=			'<div class="main-content-container">';
						$network_article .=				'<h6><a href="' . $news_post['url'] . '">' . $news_post['title'] . '</a></h6>';
						$network_article .= 		'</div>';
						$network_article .= 	'</div>';
						$network_article .= '</div>';
						echo $network_article;
					}
					echo '</article>';
				}
			?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
