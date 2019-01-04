<?php
/**
 * The template for displaying committee detail pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Committee Detail
 */

require 'inc/bbg-functions-assemble.php';

$committeeReportID = get_post_meta(get_the_ID(), "committee_report", true);
$committeeResolutionID = get_post_meta(get_the_ID(), "committee_establishment_resolution", true); 

$committeeReport = false;
if ($committeeReportID != "") {
	$committeeReportPost = get_post($committeeReportID);
	$committeeReport = array('title' => $committeeReportPost->post_title, 'url' => $committeeReportPost->guid);
}

$committeeResolution = false;
if ($committeeResolutionID != "") {
	$committeeResolutionPost = get_post($committeeResolutionID);
	$committeeResolution = array('title' => $committeeResolutionPost->post_title, 'url' => $committeeResolutionPost->guid);
}

$members = array();

/* chair comes first */
$committeeChairID = get_post_meta(get_the_ID(), "committee_chair", true);
$chair = get_post($committeeChairID);
if ($chair) {
	$profilePhotoID = get_post_meta($committeeChairID, 'profile_photo', true);
	$profilePhoto = "";
	if ($profilePhotoID) {
		$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
		$profilePhoto = $profilePhoto[0];
	}
	$members[] = array('name' => $chair->post_title, 'url' => get_permalink($chair->ID), 'chair'=>true, 'profilePhoto' => $profilePhoto);
}

/* add all non-chair members */
$committeeMemberIDs = get_post_meta(get_the_ID(), "committee_members", true);
foreach ($committeeMemberIDs as $memberID) {
	if ($memberID != $committeeChairID) {
		$member = get_post($memberID);
		$profilePhotoID = get_post_meta($memberID, 'profile_photo', true);
		$profilePhoto = "";
		if ($profilePhotoID) {
			$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
			$profilePhoto = $profilePhoto[0];
		}
		$members[] = array('name' => $member->post_title, 'url' => get_permalink($member->ID), 'chair' => false, 'profilePhoto' => $profilePhoto);
	}
}

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_ID();
		$page_title = get_the_title();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
		$ogDescription = get_the_excerpt();
	}
}
wp_reset_postdata();
wp_reset_query();

get_header();
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main id="main" role="main">

	<div class="outer-container">
		<div class="grid-container">
			<?php echo '<h2>' . $page_title . '</h2>'; ?>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<?php
				echo $page_content;

				$members_list  = '<h3>Committee Members</h3>';
				$members_list .= '<ul>';
				foreach ($members as $member) {
					$members_list .= '<li>';
					$members_list .= 	'<a href="' . $member['url'] . '">' . $member['name'];
					if ($member['chair']) {
						$members_list .= 	'<em>, Committee Chair</em>';
					}
					$members_list .= 	'</a>';
					$members_list .= '</li>';
				}
				$members_list .= '</ul>';
				echo $members_list;

				if ($committeeResolution || $committeeReport) {
					echo '<h3>Committee Docs</h3>';
					echo '<ul>';
					if ($committeeResolution) {
						$url = $committeeResolution['url'];
						$title = $committeeResolution['title'];
						if ($title != NULL) {
							echo '<li><a href="' . $url . '">' . $title . '</a></li>';
						}
					}
					if ($committeeReport) {
						$url = $committeeReport['url'];
						$title = $committeeReport['title'];
						if ($title != NULL) {
							echo '<li><a href="' . $url . '">' . $title . '</a></li>';
						}
					}
					echo '</ul>';
				}
			?>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<footer class="entry-footer bbg-post-footer 1234">
				<?php
					edit_post_link(
						sprintf(
							/* translators: %s: Name of current post */
							esc_html__( 'Edit %s', 'bbginnovate' ),
							the_title( '<span class="screen-reader-text">"', '"</span>', false )
						),
						'<span class="edit-link">',
						'</span>'
					);
				?>
			</footer>
		</div>
	</div>


	<div class="bbg-post-footer">
	<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	?>
	</div>

</main><!-- #main -->

<?php get_footer(); ?>