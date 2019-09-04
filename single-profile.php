<?php
/**
 * The template for displaying all single profile posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bbgRedesign
 */

include get_template_directory() . '/inc/shared_sidebar.php';

if (have_posts()) {
	the_post();
	$page_id = get_the_ID();
	$page_title = get_the_title();
	$page_content = get_the_content();
	$page_content = wpautop($page_content);

	rewind_posts();
}

// PROFILE FIELDS
$occupation = get_post_meta($page_id, 'occupation', true);
$email = get_post_meta($page_id, 'email', true);
$phone = get_post_meta($page_id, 'phone', true);
$twitterProfileHandle = get_post_meta($page_id, 'twitter_handle', true);
$relatedLinksTag = get_post_meta($page_id, 'related_links_tag', true);

// ADJUSTMENTS FOR RETIRED EMPLOYEETS
$active = get_post_meta($page_id, 'active', true);
if (!$active){
	$occupation = '(Former) ' . $occupation;
}


// GET PROFILE PHOTO
$profile_photo = get_field('profile_photo', $page_id);
$profile_photo_url = $profile_photo['url'];


// GET INTERN INFORMATION
$intern_tagline = '';
$intern_date = get_post_meta($page_id, 'intern_date', true);
if (!empty($intern_date)) {
	$intern_name = $page_title;
	$intern_tagline = '<p class="bbg__post__author-tagline">— ' . $intern_name . ', ' . $intern_date . '</p>';
}


// GET CATEGORY
$projectCategoryID = get_cat_id('Project');
$isProject = has_category($projectCategoryID);

$hideFeaturedImage = get_post_meta(get_the_ID(), 'hide_featured_image', true);
$thumbnail_image = '';
if (has_post_thumbnail() && ($hideFeaturedImage != 1)) {
	$featuredImageCutline="";
	$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id(get_the_ID()), 'post_type' => 'attachment'));
	$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');

	if ($thumbnail_image && isset($thumbnail_image[0])) {
		$featuredImageCutline=$thumbnail_image[0]->post_excerpt;
	}
	$thumbnail_image = true;
}

get_header(); ?>

<main id="main" role="main" style="padding-top: 3rem;">
	<div class="outer-container">
		<div class="main-content-container">
			<div class="nest-container">
				<div class="inner-container">
					<div class="icon-side-content-container">
						<img src="<?php echo $profile_photo_url; ?>" alt="Profile photo">
					</div>
					<div class="icon-main-content-container">
						<?php
							$profile_head  = '<h2 class="section-header">';
							$profile_head .= 	$page_title;
							$profile_head .= '</h2>';
							echo $profile_head;

							$profile_occupation  = '<p class="lead-in">';
							$profile_occupation .= 	$occupation;
							$profile_occupation .= '</p>';
							echo $profile_occupation;

							echo $page_content;
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="side-column divider-left">
			<?php
				echo '<aside>';
				echo '<h3 class="sidebar-section-header">Contact</h3>';
				if ($email != "") {
					$email_link  = 	'<a href="mailto:' . $email . '" title="Email ' . get_the_title() . '">';
					$email_link .= 		'<span class="bbg__article-share__text">' . $email . '</span>';
					$email_link .= 	'</a>';
					echo $email_link;
				}
				if ($twitterProfileHandle != "") {
					$twitter_link  = 	'<br><a href="https://twitter.com/' . $twitterProfileHandle . '" title="Follow ' . $page_title . ' on Twitter">';
					$twitter_link .= 		'<span class="bbg__article-share__text">@' . $twitterProfileHandle . '</span>';
					$twitter_link .= 	'</a>';
					echo $twitter_link;
				}
				if ($phone != "") {
					$phone_string = '<br><span class="bbg__article-share__text">' . $phone . '</span>';
					echo $phone_string;
				}
				echo '</aside>';

				// SHARE THIS PAGE
				$share_icons = social_media_share_page($page_id);
				if (!empty($share_icons)) {
					echo $share_icons;
				}
				
				if ($includeSidebar) {
					echo $sidebar;
				}
			?>
		</div>
	</div>
</main>

<?php get_footer(); ?>