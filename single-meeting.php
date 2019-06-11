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
include get_template_directory() . '/inc/shared_sidebar.php';


if (have_posts()) {
	the_post();
	$page_id = get_the_ID();
	$page_title = get_the_title();
	$post_date = get_the_date('F j, Y');
	$post_thumbnail_url = get_the_post_thumbnail_url();
	$page_content = apply_filters('the_content', get_the_content());

	$project_category_id = get_cat_id('Project');
	$isProject = has_category($project_category_id);

	// SET PAGE TYPE VARIABLES
	$event_page_header = 'Event';
	$is_board_meeting = false;
	$is_press_release = false;
	if (in_category('Board Meetings')) {
		$event_page_header = 'Board Meeting';
		$is_board_meeting = true;
	}
	if (in_category('Press Release')) {
		$is_press_release = true;
	}

	// EVENT FIELDS - MEETING REGISTRATION AND COMMENTS
	$today = new DateTime('now', new DateTimeZone('America/New_York'));
	$today_string = $today->format('Y-m-d H:i:s');

	$meeting_registration_close_time = get_post_meta($page_id, 'board_meeting_registration_close_time', true);
	$comment_form_close_time = get_post_meta($page_id, 'board_meeting_comment_form_close_time', true);
	$registration_is_closed = false;
	if ($meeting_registration_close_time) {
		$registration_is_closed = ($meeting_registration_close_time <  $today_string);
		//get a display friendly version of this date for later
		$meeting_registration_close_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $meeting_registration_close_time);
		$meeting_registration_close_date_string = $meeting_registration_close_date_object->format("F j, Y");
	}

	$comment_form_is_closed = false;
	if ($comment_form_close_time) {
		$comment_form_is_closed = ($comment_form_close_time <  $today_string);
		$comment_form_close_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $comment_form_close_time);
		//get a display friendly version of this date for later
		$comment_form_close_string = $comment_form_close_date_object->format('F j, Y');
	}

	//EVENT FIELDS - MEETING DATA
	$meeting_location = get_post_meta($page_id, 'board_meeting_location', true);
	$meeting_time = get_post_meta($page_id, 'board_meeting_time', true);
	$meeting_summary = get_post_meta($page_id, 'board_meeting_summary', true);
	$meeting_contact_tagline = get_post_meta($page_id, 'board_meeting_contact_tagline', true);
	if (!$meeting_contact_tagline || $meeting_contact_tagline == "") {
		$meeting_contact_tagline = 'For more information, please contact BBG Public Affairs at (202) 203-4400 or by e-mail at pubaff@bbg.gov.';
	}
	if ($meeting_time != '') {
		$meeting_time = $meeting_time;
	}
	$meeting_speakers = get_post_meta($page_id, 'board_meeting_speakers', true);

	//EVENT FIELDS - EVENTBRITE IFRAME
	$event_brite_button_string = '';
	$eventbrite_url = get_post_meta($page_id, 'board_meeting_eventbrite_url', true);
	if ($eventbrite_url && !empty($eventbrite_url) && !$is_press_release) {
		if (!$registration_is_closed) {
			$event_brite_button_string  = '<a class="usa-button" href="' . $eventbrite_url . '" target="_blank">Register for this Event</a>';
		} else {
			$event_brite_button_string = '<p class="registrationClosed" style="font-style:italic;">Registration for this event has closed.</p>';
		}
	}
	rewind_posts();
}

// FEATURED VIDEO
$video_url = get_post_meta($page_id, 'featured_video_url', true);

// ADD SUPPORT FOR SIDEBAR DROPDOWN
$lists_include = get_field('sidebar_dropdown_include', '', true);

// GET ALL SPEAKERS
if (have_rows('board_meeting_speakers')) {
	$speaker_data_set = array();
	$speaker_data = '';
	$speaker_list = '';

	$speakers_label = get_field('board_meeting_speaker_label');

	while (have_rows('board_meeting_speakers')) {
		the_row();
		// SHOW INTERNAL SPEAKER LIST
		if (get_row_layout() == 'board_meeting_speakers_internal') {
			if (get_sub_field('bbg_speaker_name')) {
				$profile_speaker = get_sub_field('bbg_speaker_name');
				$i = 0;
				foreach ($profile_speaker as $profile) {
					$i++;
					$profile_id = $profile->ID;
					$profile_name = get_the_title($profile_id);
					$isActing = get_post_meta($profile_id, 'acting', true);
					$profile_bio = get_sub_field('bbg_speaker_bio');
					$occupation = get_post_meta($profile_id, 'occupation', true);
					$profile_link = get_page_link($profile_id);

					if ($isActing) {
						$occupation = 'Acting ' . $occupation;
					}
					$speaker_data = array(
						'entry' => $i,
						'name' => $profile_name,
						'title' => $occupation,
						'bio' => $profile_bio,
						'link' => $profile_link
					);
					$speaker_list[] = $speaker_data;
				}
			}
		} else if (get_row_layout() == 'board_meeting_speakers_external') {
			if (get_sub_field('meeting_speaker')) {
				$profile_speaker = get_sub_field('meeting_speaker');
				$i = 0;
				foreach ($profile_speaker as $profile) {
					$i++;
					$speaker_name = $profile["meeting_speaker_name"];
					$speaker_title = $profile["meeting_speaker_title"];
					$speaker_bio = $profile["meeting_speaker_bio"];
					$speaker_link = $profile["meeting_speaker_url"];

					$speaker_data = array(
						'entry' => $i,
						'name' => $speaker_name,
						'title' => $speaker_title,
						'bio' => $speaker_bio,
						'link' => $speaker_link
					);
					$speaker_list[] = $speaker_data;
				}
			}
		}
	}
	$speaker_data_set = array(
		'field_label' => $speakers_label,
		'speaker_list' => $speaker_list
	);
}


get_header();
?>

<main id="main" role="main" style="padding-top: 3rem;">
	<section class="outer-container">
		<div class="grid-container">
			<?php
				if(in_category( 'Board Meetings' )) {
					echo '<h2 class="section-header">Board Meetings</h2>';
				} else if(in_category( 'Event' )) {
					echo '<h2 class="section-header">Event</h2>';
				}
			?>
		</div>

		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
				<div class="inner-container">
					<div class="main-column">
						<?php
							$post_header  = '<header>';
							$post_header .= 	'<h3 class="article-title">' . $page_title . '</h3>';
							$post_header .= 	'<p class="date-meta">' . $post_date . '</p>';
							$post_header .= '</header>';
							echo $post_header;

							$featured_media_result = get_feature_media_data();
							if ($featured_media_result != "") {
								echo $featured_media_result;
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

							echo $page_content;

							if (!$is_press_release && $is_board_meeting && !isset($_GET['success'])) {
								if ($comment_form_is_closed) {
									echo '<p>The deadline for public comments for this meeting has passed.</p>';
								}
								else {
									echo '<h3>Public Comments Form</h3>';
									echo '<p>Public comments related to U.S. international media are now being accepted for review by the board. Comments intended for the ' . $post_date . ' meeting of the board must be submitted by <b>' . $comment_form_close_string . '</b>.</p>';
									echo '<p>Comments received after that date will be forwarded to the board for the following meeting.</p>';

									echo '<p>The public comments you provide to the Broadcasting Board of Governors are collected by the agency voluntarily and may be publicly disclosed on the Internet and/or via requests submitted to the BBG under the Freedom of Information Act.</p>';

									echo '<p>By providing public comments, you are consenting to their use and consideration by the Board and to their possible public dissemination. Personal contact information will not be made available to the public and will only be used by agency staff to engage with submitters regarding their own comments.</p>';

									$redirectLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

									if (strpos($redirectLink, '?')) {
										$redirectLink .= '&';
									} else {
										$redirectLink .= '?';
									}
									$redirectLink .= 'success = true';
									echo do_shortcode('[si-contact-form form ="2" redirect = "$redirectLink"]');
									echo '<script type="text/javascript" src="' . get_template_directory_uri() . '/js/meeting-comment-form.js"></script>';
								}
							}
						?>
					</div>
					<div class="side-column divider-left">
						<?php
							// SHARE THIS PAGE
							$share_icons = social_media_share_page($page_id);
							if (!empty($share_icons)) {
								echo $share_icons;
							}

							$event_info  = '<h3 class="sidebar-section-header">Event Information</h3>';
							$event_info .= '<p class="sans">' . $post_date . ', ' . $meeting_time . '<br><br>';
							$event_info .= $meeting_location . '</p>';
							$event_info .= $event_brite_button_string;
							$event_info .= '<p class="bbg-tagline bbg-tagline--main">';
							$event_info .= 	$meeting_contact_tagline;
							$event_info .= '</p>';
							echo $event_info;
						?>

						<!-- SPEAKERS LIST -->
						<?php
							if (!empty($speaker_data_set)) {
								$speaker_list = $speaker_data_set['speaker_list'];
								$speaker_markup  = '<aside class="speaker-list">';
								$speaker_markup .= 	'<h3 class="sidebar-section-header">' . $speaker_data_set['field_label'] . '</h3>';
								$speaker_markup .= 	'<ul class="unstyled-list meeting-speakers">';
								foreach($speaker_list as $speaker_data) {
									if (!empty($speaker_data['bio'])) {
										$speaker_markup .= '<li class="usa-accordion speaker-accordion">';
										$speaker_markup .= 	'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $speaker_data['entry'] . '">';
										$speaker_markup .= 		'<p class="speaker-name sidebar-paragraph-header">' . $speaker_data['name'] . '</p>';
										if (!empty($speaker_data['title'])) {
											$speaker_markup .= 	'<p class="speaker-title sans">' . $speaker_data['title'] . '</p>';
										}
										$speaker_markup .= 		' <i class="fas fa-plus"></i>';
										$speaker_markup .= 	'</button>';
										$speaker_markup .= 	'<div id="collapsible-faq-' . $speaker_data['entry'] . '" aria-hidden="true" class="usa-accordion-content">';
										$speaker_markup .= 		'<p class="sans speaker-bio">';
										$speaker_markup .= 			$speaker_data['bio'];
										$speaker_markup .= 		'</p>';
										if (!empty($speaker_data['link'])) {
											$speaker_markup .= 		'<p class="sans">';
											$speaker_markup .= 			'<a href="' . $speaker_data['link'] . '" target="_blank">View Profile</a>';
											$speaker_markup .= 		'</p>';
										}
										$speaker_markup .= 	'</div>';
										$speaker_markup .= '</li>';
									} else {
										$speaker_markup .= '<li>';
										$speaker_markup .= 	'<p class="speaker-name sidebar-paragraph-header">' . $speaker_data['name'] . '</p>';
										if (!empty($speaker_data['title'])) {
											$speaker_markup .= 	'<p class="speaker-title sans">' . $speaker_data['title'] . '</p>';
										}
										$speaker_markup .= '</li>';
									}
								}
								$speaker_markup .= 	'</ul>';
								$speaker_markup .= '</aside>';
								echo $speaker_markup;
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
				</div>
			</div>
		</div>
	</section>
</main><!-- #main -->

<script type="text/javascript">
(function($) {
$('document').ready(function() {
	// MAKE SMALL POST FEATURE IMAGES THE WIDTH OF THE MAIN COLUMN
	var mainColumn = $('.main-column');
	var thumbnailImage = $('.main-column').children('img');
	if (thumbnailImage.width() < mainColumn.width()) {
		thumbnailImage.css({
			'width' : mainColumn.width(),
			'height' : 'auto'
		});
	}

	// SWAP OUT PLUS SIGN FOR MINUS FOR SPEAKER ACCORDIONS
	var accordionButton = $('.speaker-accordion').children('button');
	accordionButton.on('click', function() {
		if ($(this).attr('aria-expanded') == 'false') {
			$(this).children('.fas').removeClass('fa-plus');
			$(this).children('.fas').addClass('fa-minus');
		} else {
			$(this).children('.fas').removeClass('fa-minus');
			$(this).children('.fas').addClass('fa-plus');
		}
	});
}); // END READY
})(jQuery);
</script>

<?php get_footer(); ?>