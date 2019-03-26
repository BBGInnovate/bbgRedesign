<?php
/**
 * The template for displaying board meetings and events.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bbgRedesign
 */
/* we go through the loop once and reset it in order to get some vars for our og tags */

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
	the_post();

	// SET PAGE TYPE VARS
	$eventPageHeader = "Event";
	$isBoardMeeting = false;
	$isPressRelease = false;
	if (in_category("Board Meetings")) {
		$eventPageHeader = "Board Meeting";
		$isBoardMeeting = true;
	}
	if (in_category("Press Release")) {
		$isPressRelease = true;
	}

	// GET POST THUMBNAIL
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'Full' );
	$ogImage = $thumb['0'];
	$socialImageID = get_post_meta( $post->ID, 'social_image',true );
	if ($socialImageID) {
		$socialImage = wp_get_attachment_image_src( $socialImageID , 'Full');
		$ogImage = $socialImage[0];
	}

	// ADJUST BANNER IMAGE STYLE
	$bannerPosition = get_post_meta( get_the_ID(), 'adjust_the_banner_image', true);
	$bannerPositionCSS = get_field( 'adjust_the_banner_image_css', '', true);
	$bannerAdjustStr = "";
	if ($bannerPositionCSS) {
		$bannerAdjustStr = $bannerPositionCSS;
	} else if ($bannerPosition) {
		$bannerAdjustStr = $bannerPosition;
	}

	$today = new DateTime("now", new DateTimeZone('America/New_York'));
	$todayStr = $today->format('Y-m-d H:i:s');

	$meetingRegistrationCloseTime = get_post_meta( get_the_ID(), 'board_meeting_registration_close_time', true );
	$commentFormCloseTime = get_post_meta( get_the_ID(), 'board_meeting_comment_form_close_time', true );
	$registrationIsClosed = false;
	if ($meetingRegistrationCloseTime) {
		$registrationIsClosed = ($meetingRegistrationCloseTime <  $todayStr);
		//get a display friendly version of this date for later
		$meetingRegistrationCloseDateObj = DateTime::createFromFormat('Y-m-d H:i:s', $meetingRegistrationCloseTime);
		$meetingRegistrationCloseDateStr = $meetingRegistrationCloseDateObj->format("F j, Y");
	}

	$commentFormIsClosed = false;
	if ($commentFormCloseTime) {
		$commentFormIsClosed = ($commentFormCloseTime <  $todayStr);
		$commentFormCloseDateObj = DateTime::createFromFormat('Y-m-d H:i:s', $commentFormCloseTime);
		//get a display friendly version of this date for later
		$commentFormCloseStr = $commentFormCloseDateObj->format("F j, Y");
	}

	// GET MEETING DATA
	$meetingLocation = get_post_meta(get_the_ID(), 'board_meeting_location', true);
	$meetingTime = get_post_meta(get_the_ID(), 'board_meeting_time', true);
	$meetingSummary = get_post_meta(get_the_ID(), 'board_meeting_summary', true);
	$meetingContactTagline = get_post_meta(get_the_ID(), 'board_meeting_contact_tagline', true);
	if (!$meetingContactTagline || $meetingContactTagline == "") {
		$meetingContactTagline = "For more information, please contact BBG Public Affairs at (202) 203-4400 or by e-mail at pubaff@bbg.gov.";
	}
	if ($meetingTime != '') {
		$meetingTime = $meetingTime;
	}
	$meetingSpeakers = get_post_meta(get_the_ID(), 'board_meeting_speakers', true);

	// CREATE EVENTBRITE IFRAME
	$eventBriteButtonStr = "";
	$eventbriteUrl = get_post_meta( get_the_ID(), 'board_meeting_eventbrite_url', true );
	if ($eventbriteUrl && $eventbriteUrl != "" && !$isPressRelease) {
		if (!$registrationIsClosed) {
			$eventBriteButtonStr = "<a target='_blank' class='usa-button style='color:white;text-decoration:none;' href='" . $eventbriteUrl . "'>Register for this Event</a>";
		} else {
			$eventBriteButtonStr = "<p style='font-style:italic;' class='registrationClosed'>Registration for this event has closed.</p>";
		}
	}
	rewind_posts();
}

//Add shared sidebar
include get_template_directory() . "/inc/shared_sidebar.php";

//Add featured video
$videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );

// Add support for sidebar dropdown
$listsInclude = get_field( 'sidebar_dropdown_include', '', true);

include get_template_directory() . "/inc/shared_sidebar.php";

get_header();
?>

<main id="main" role="main" style="padding-top: 3rem;">
	<?php
		while (have_posts()) : the_post(); 
			$projectCategoryID = get_cat_id('Project');
			$isProject = has_category($projectCategoryID);

			//Default adds a space above header if there's no image set
			$featuredImageClass = " bbg__article--no-featured-image";

			//the title/headline field, followed by the URL and the author's twitter handle
			$twitterText  = html_entity_decode(get_the_title());
			$twitterText .= " by @bbggov " . get_permalink();

			$twitterURL = "//twitter.com/intent/tweet?text=" . rawurlencode($twitterText);
			$fbUrl = "//www.facebook.com/sharer/sharer.php?u=" . urlencode(get_permalink());
			$postDate = get_the_date();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( "bbg__article" ); ?>>
			<div class="outer-container">
				<div class="grid-container">
					<?php
						$category_title = '';
						if(in_category( 'Board Meetings' )) {
							$category_title = "Board Meetings";
						} else if(in_category( 'Event' )) {
							$category_title = "Event";
						}
						$post_header  = '<header>';
						$post_header .= 	'<h2>' . $category_title . '</h2>';
						$post_header .= 	'<h3>' . get_the_title() . '</h3>';
						$post_header .= '</header>';
						echo $post_header;
					?>
				</div>
			</div>

			<div class="outer-container">
				<div class="custom-grid-container">
					<div class="inner-container">
						<div class="main-content-container page-content">
						<?php
							$post_thumbnail_url = get_the_post_thumbnail_url();
							if (!empty($post_thumbnail_url)) {
								echo '<img src="' . $post_thumbnail_url . '" alt="' . get_the_title() . '">';
							}
						?>
						<?php
							if (isset($_GET['success'])) {
								$success_alert  = '<div class="usa-alert usa-alert-success">';
								$success_alert .= 	'<div class="usa-alert-body">';
								$success_alert .= 		'<h3 class="usa-alert-heading">Submission Successful</h3>';
								$success_alert .= 		'<p class="usa-alert-text">Your comment was successfully submitted.</p>';
								$success_alert .= 	'</div>';
								$success_alert .= '</div>';
								echo $success_alert;
							}
							the_content();

							if (!$isPressRelease && $isBoardMeeting && !isset($_GET['success'])) {
								if ($commentFormIsClosed) {
									echo '<p>The deadline for public comments for this meeting has passed.</p>';
								}
								else {
									echo '<h3>Public Comments Form</h3>';
									echo '<p>Public comments related to U.S. international media are now being accepted for review by the board. Comments intended for the ' . $postDate . ' meeting of the board must be submitted by <b><?php echo $commentFormCloseStr; ?></b>.</p>';
									echo '<p>Comments received after that date will be forwarded to the board for the following meeting.</p>';

									echo '<p>The public comments you provide to the Broadcasting Board of Governors are collected by the agency voluntarily and may be publicly disclosed on the Internet and/or via requests submitted to the BBG under the Freedom of Information Act.</p>';

									echo '<p>By providing public comments, you are consenting to their use and consideration by the Board and to their possible public dissemination. Personal contact information will not be made available to the public and will only be used by agency staff to engage with submitters regarding their own comments.</p>';

									$redirectLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

									if (strpos($redirectLink, "?")) {
										$redirectLink .= "&";
									} else {
										$redirectLink .= "?";
									}
									$redirectLink .= "success = true";
									echo do_shortcode("[si-contact-form form ='2' redirect = '$redirectLink']");
									echo '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/meeting-comment-form.js"></script>';
								}
							}
						?>
						</div>
				
				<!-- SIDEBAR -->
				<div class="side-content-container">
						<?php
							$event_info  = '<h5><a href="/news/events/">Event</a></h5>';
							$event_info .= '<p class="aside">' . $postDate . ', ' . $meetingTime . '<br><br>';
							$event_info .= $meetingLocation . '</p>';
							$event_info .= $eventBriteButtonStr;
							$event_info .= '<p class="bbg-tagline bbg-tagline--main">';
							$event_info .= 	$meetingContactTagline;
							$event_info .= '</p>';
							echo $event_info;
						?>
						<div class="share-social">
							<h5>Share</h5>
							<a href="<?php echo $fbUrl; ?>">
								<span class="bbg__article-share__icon facebook"></span>
							</a>

							<a href="<?php echo $twitterURL; ?>">
								<span class="bbg__article-share__icon twitter"></span>
							</a>
						</div>
						<!-- SPEAKERS -->
						<?php
							if (have_rows('board_meeting_speakers')) {
								$speakersLabel = get_field('board_meeting_speaker_label');
								
								echo '<h5>' . $speakersLabel . '</h5>';
								while (have_rows('board_meeting_speakers')) : the_row();

									// SHOW INTERNAL SPEAKER LIST
									if (get_row_layout() == 'board_meeting_speakers_internal') {
										if (get_sub_field('bbg_speaker_name')) {
											$profiles = get_sub_field('bbg_speaker_name');

											echo "<ul class='usa-unstyled-list unstyled-list'>";

											foreach ($profiles as $profile) {
												$pID = $profile->ID;
												$profile_id = get_post_meta($pID);
												$includeProfile = false;

												if ($profile_id) {
													$includeProfile = true;

													$twitterProfileHandle = get_post_meta($pID, 'twitter_handle', true);
													$profileName = get_the_title( $pID );
													$occupation = get_post_meta($pID, 'occupation', true);
													$profileLink = get_page_link($pID);

													$profile_list  = '<li>';
													$profile_list .= 	'<h6><a href="' . $profileLink . '">' . $profileName . '</a></h6>';
													$profile_list .= 	'<span class="bbg__profile-excerpt__occupation">' . $occupation . '</span>';
													$profile_list .= '</li>';
												}

												if ($includeProfile) {
													echo $profile_list;
												}
											}
											echo "</ul>";
										}
									} else if (get_row_layout() == 'board_meeting_speakers_external') {
										if (get_sub_field('meeting_speaker')) {
											$profiles = get_sub_field('meeting_speaker');

											echo "<ul class='usa-unstyled-list usa-unstyled-list unstyled-list'>";

											foreach ($profiles as $profile) {
												$speakerName = $profile["meeting_speaker_name"];
												$speakerTitle = $profile["meeting_speaker_title"];
												$speakerLink = $profile["meeting_speaker_url"];

												$external_speaker_list  = '<li>';
												$external_speaker_list .= 	'<h6>';
												if ($speakerName && $speakerLink != "") {
													$external_speaker_list .= 		'<a href="' . $speakerLink . '">' . $speakerName . '</a>';
												} else {
													$external_speaker_list .= $speakerName;
												}
												$external_speaker_list .= 	'</h6>';
												$external_speaker_list .= 	'<span class="bbg__profile-excerpt__occupation">' . $speakerTitle . '</span>';
												$external_speaker_list .= '</li>';

												echo $external_speaker_list;
											}
											echo "</ul>";
										}
									}
								endwhile;
							}
						?>

						<!-- RELATED DOCUMENTS -->
						<?php
							if (have_rows('board_meeting_related_documents')) {
								echo '<h5>Meeting documents</h5>';
								echo '<ul>';
								while (have_rows('board_meeting_related_documents')) { 
									the_row();
									echo '<li>';
									$dl = get_sub_field('board_meeting_related_document');
									echo '<a href="' . $dl['url'] . '">' . $dl['title'] . '</a>';
									echo '</li>';
								}
								echo '</ul>';
							}

							echo "<!-- Additional Sidebar Content -->";
							if ( $includeSidebar) {
								echo $sidebar;
							}
							echo $sidebarDownloads;
						?>

				</div>
		</article><!-- #post-## -->
	<?php endwhile; // END POST LOOP ?>
</main><!-- #main -->

	<section class="usa-grid">
		<?php get_sidebar(); ?>
	</section>
<?php get_footer(); ?>