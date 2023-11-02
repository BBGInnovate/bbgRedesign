<?php
/**
 * The template for displaying the Threats to Press page.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Threats to Press
 */

require 'inc/bbg-functions-assemble.php';
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

// PAGE INFORMATION
$page_content = "";
$page_title = "";
$page_excerpt = "";
$page_id = 0;
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_content = get_the_content();
		$page_title = get_the_title();
		$page_excerpt = get_the_excerpt();
		// $ogDescription = $page_excerpt;
		// $page_content = apply_filters('the_content', $page_content);
		// $page_content = str_replace(']]>', ']]&gt;', $page_content);
		$page_id = get_the_ID();
	}
}
wp_reset_postdata();
wp_reset_query();

$threats_category = get_category_by_slug('threats-to-press');
$threatsPermalink = get_category_link( $threats_category->term_id );

// GET POSTS OF THREATS TO PRESS FOR PAGE
$threats_posts_args = array(
	'post_type' => array('post'),
	'cat' => get_cat_id('Threats to Press'),
	'posts_per_page' => 6,
	'post_status' => array('publish')
);
$threats_posts_query = new WP_Query($threats_posts_args);
$threats_posts = $threats_posts_query->posts;


// GET FALLEN JOURNALIST INFORMATION
$wall = "";
$journalist = "";
$journalist_name = "";
$mugshot = "";
$altText = "";

$fallen_journalists = get_field('fallen_journalists_section');

if ($fallen_journalists) {
	foreach ($fallen_journalists as $cur_fallen_journalist) {
		$cur_fallen_journalist_id = $cur_fallen_journalist->ID;
		$mugshot = '/wp-content/media/2016/07/blankMugshot.png';
		$date = get_field('profile_date_of_passing', $cur_fallen_journalist_id, true); 
		$date_precision = get_field('profile_date_of_passing_precision', $cur_fallen_journalist_id, true); 
		if ($date_precision == 'month') {
			$dateObj = explode('/', $date);
			$date = $dateObj[0] . '/' . $dateObj[2];
		}
		$link = get_the_permalink($cur_fallen_journalist_id);
		$name = get_the_title($cur_fallen_journalist_id);
		/*** Get the profile photo mugshot ***/
		$profilePhotoID = get_post_meta( $cur_fallen_journalist_id, 'profile_photo', true );
		$profilePhoto = "";
		if ($profilePhotoID) {
			$profilePhoto = wp_get_attachment_image_src( $profilePhotoID , 'mugshot');
			$mugshot = $profilePhoto[0];
		}
		$altText = "";
		$imgSrc = '<img src="' . $mugshot . '" alt="' . $altText . '" class="bbg__profile-grid__profile__mugshot"/>';

		//JBF 2/8/2017: not using link until we fill out profiles.
		if ($link != "") {
			$journalist_name = '<a href="' . $link . '">' . $name . "</a>";
			$imgSrc = '<a href="' . $link . '">' . $imgSrc . "</a>";
		} else {
			$journalist_name = $name;
		}

		$journalist  = "";
		$journalist .= '<div class="bbg__profile-grid__profile">';
		$journalist .= 	$imgSrc;
		$journalist .= 	'<h4 class="article-title">' . $journalist_name . '</h4>';
		$journalist .= 	'<p class="sans">Killed ' . $date . '</p>';
		$journalist .= '</div>';
		$wall .= $journalist;
	}
}
wp_reset_postdata();
wp_reset_query();


// CUSTOM QUOTATION SPECIFIC TO THIS PAGE (IN WORDPRESS BACKEND)
$include_custom_quotation = get_field('quotation_include', '', true);
$quotation = "";
if ($include_custom_quotation) {
	$quotation_text = get_field('quotation_text', '', false);
	$quotation_speaker = get_field('quotation_speaker', '', false);
	$quotation_tagline = get_field('quotation_tagline', '', false);

	$quote_mugshot_id = get_field('quotation_mugshot', '', false);
	$quote_mugshot = "";

	if ($quote_mugshot_id) {
		$quote_mugshot = wp_get_attachment_image_src($quote_mugshot_id , 'mugshot');
		$quote_mugshot = $quote_mugshot[0];
	}

	$quotation  = '<img class="quote-image"  src="' . $quote_mugshot .'" alt="Mugshot">';
	$quotation .= '<h4 class="quote-text">'. $quotation_text .'</h4>';
	$quotation .= '<p  class="quote-byline">';
	$quotation .= 	$quotation_speaker .'<br>';
	$quotation .= 	'<span class="occupation">'. $quotation_tagline .'</span>';
	$quotation .= '</p>';
}


get_header();

?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>
<main id="main" role="main">


	<?php
		// NEWS AND UPDATES
		$threat_structure  = '<section class="outer-container threats-box">';
		$threat_structure .= 	'<div class="grid-container">';
		$threat_structure .= 		'<h2 class="section-header">' . $page_title . '</h2>';
		$threat_structure .= 		'<p class="lead-in">' . $page_content . '</p>';
		$threat_structure .= 	'</div>';
		$threat_structure .= 	'<div class="grid-half" id="threats-main-column">';
		$threat_structure .= 			'<article>';
		$threat_structure .= 				'<div class="article-image">';
		$threat_structure .= 					'<a href="' . get_the_permalink($threats_posts[0]) . '">';
		$threat_structure .= 						'<img src="' . get_the_post_thumbnail_url($threats_posts[0], 'large') . '" alt="Image link to ' . get_the_title($threats_posts[0]) . ' post">';
		$threat_structure .= 					'</a>';
		$threat_structure .= 				'</div>';
		$threat_structure .= 				'<div class="article-info">';
		$threat_structure .= 					'<h2 class="article-title"><a href="' . get_the_permalink($threats_posts[0]) . '">' . get_the_title($threats_posts[0]) . '</a></h2>';
		$threat_structure .= 				'</div>';
		$threat_structure .= 			'</article>';
		$threat_structure .= 	'</div>';
		$threat_structure .= 	'<div class="grid-half" id="threats-side-column">';
		$secondary_threats = array_shift($threats_posts);
		foreach ($threats_posts as $recent_threat) {
			$threat_structure .= 		'<article>';
			$threat_structure .= 			'<h2 class="article-title"><a href="' . get_the_permalink($recent_threat) . '">' . get_the_title($recent_threat) . '</a></h2>';
			$threat_structure .= 		'</article>';
		}
		$threat_structure .= 	'</div>';
		$threat_structure .= '</section>';
		echo $threat_structure;
	?>

	<?php
		$includeRibbon = get_field('include_ribbon');
		if (!empty($includeRibbon) && $includeRibbon == true) {
			if (have_rows('ribbon_group')) {
				while (have_rows('ribbon_group')) {
					the_row();

					$ribbonData = get_ribbon_data();
					$ribbonParts = build_ribbon_parts($ribbonData);
					$ribbonModule = assemble_ribbon_module($ribbonParts);

					echo $ribbonModule;
				}
			}
		}
	?>

	<?php
		$featuredJournalists = "";
		$profilePhoto = "";

		$choices = array();

		if (have_rows('watching_journalists_section')) {
			while (have_rows('watching_journalists_section')) {
				the_row();
				if (empty($choices)) {
					$choices = get_sub_field_object('status')['choices'];
				}
			}
			reset_rows();

			$featuredJournalists .= '<section class="outer-container">';
			$featuredJournalists .= 	'<div class="grid-container">';
			$featuredJournalists .= 		'<h2 class="section-subheader">Cases We\'re Watching</h2>';

			$featuredJournalists .= '        <div class="nest-container">';
			$featuredJournalists .= '            <div class="inner-container featured-journalist__dropdown">';
			$featuredJournalists .= '                <label class="grid-four">Status';
			$featuredJournalists .= '                    <select name="featured-journalist__dropdown">';
			$featuredJournalists .= '                        <option value="">ALL</option>';

			foreach ($choices as $value => $label) {
				$featuredJournalists .= '                    <option value="' . $value . '">' . $label . '</option>';
			}
			$featuredJournalists .= '                    </select>';
			$featuredJournalists .= '                </label>';
			$featuredJournalists .= '            </div>';

			$featuredJournalists .= '            <div class="inner-container">';

			while (have_rows('watching_journalists_section')) {
				the_row();

				$status = get_sub_field('status');
				$featuredJournalist = get_sub_field('journalist');

				$profileTitle = $featuredJournalist->post_title;
				$profileName = $featuredJournalist->first_name . ' ' . $featuredJournalist->last_name;
				$profileOccupation = $featuredJournalist->occupation;
				$profilePhoto = $featuredJournalist->profile_photo;
				$profileUrl = get_permalink($featuredJournalist->ID);
				$profileExcerpt = my_excerpt($featuredJournalist->ID);

				if ($profilePhoto) {
					$profilePhoto = wp_get_attachment_image_src( $profilePhoto , 'Full');
					$profilePhoto = $profilePhoto[0];
					$profilePhoto = '<a href="' . $profileUrl . '"><img src="' . $profilePhoto . '" class="bbg__profile-excerpt__photo" alt="Profile photo"></a>';
				}

				$featuredJournalists .= 			'<div class="grid-half profile-clears featured-journalist__grid-item status-' . $status['value'] . '">';
				$featuredJournalists .= 				$profilePhoto;
				$featuredJournalists .= 				'<h4 class="article-title"><a href="' . $profileUrl . '">'. $profileName .'</a></h4>';
				$featuredJournalists .= 				'<p class="sans" style="margin-bottom: 1rem;">' . $profileOccupation . '</p>';
				$featuredJournalists .= 				'<p class="featured-journalist__status">' . $status['label'] . '</p>';
				$featuredJournalists .= 				'<p>' . $profileExcerpt . '</p>';
				$featuredJournalists .= 			'</div>';
			}

			$featuredJournalists .= 			    '<div class="grid-half featured-journalist__no-status">';
			$featuredJournalists .= 			        'There are no journalists with this status.';
			$featuredJournalists .= 			    '</div>';
			$featuredJournalists .= 			'</div>';
			$featuredJournalists .= 		'</div>';
			$featuredJournalists .= 	'</div>';
			$featuredJournalists .= '</section>';
		}

		echo $featuredJournalists;
	?>

	<div class="outer-container bbg__memorial">
		<div class="grid-container">
			<h3 class="section-subheader">Fallen journalists</h3>
			<div class="usa-grid">
				<?php echo $wall; ?>
			</div>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<div class="usagm-quotation ">
				<?php echo $quotation; ?>
			</div>
		</div>
	</div>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>