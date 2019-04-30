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

	$twitterText  = html_entity_decode(get_the_title());
	$twitterText .= ' by @bbggov ' . get_permalink();

	$twitterURL = '//twitter.com/intent/tweet?text=' . rawurlencode($twitterText);
	$fbUrl = '//www.facebook.com/sharer/sharer.php?u=' . urlencode(get_permalink());

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

//Add featured video
$video_url = get_post_meta($page_id, 'featured_video_url', true);

// Add support for sidebar dropdown
$lists_include = get_field('sidebar_dropdown_include', '', true);

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

							if (!empty($post_thumbnail_url)) {
								echo '<img src="' . $post_thumbnail_url . '" alt="' . $page_title . '">';
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
						<div class="share-social">
							<h3 class="sidebar-section-header">Share</h3>
							<a href="<?php echo $fbUrl; ?>">
								<span class="bbg__article-share__icon facebook"></span>
							</a>

							<a href="<?php echo $twitterURL; ?>">
								<span class="bbg__article-share__icon twitter"></span>
							</a>
						</div>
						<?php
							$event_info  = '<h3 class="sidebar-section-header"><a href="/news/events/">Event Information</a></h3>';
							$event_info .= '<p class="sans">' . $post_date . ', ' . $meeting_time . '<br><br>';
							$event_info .= $meeting_location . '</p>';
							$event_info .= $event_brite_button_string;
							$event_info .= '<p class="bbg-tagline bbg-tagline--main">';
							$event_info .= 	$meeting_contact_tagline;
							$event_info .= '</p>';
							echo $event_info;
						?>

						<!-- SPEAKERS -->
						<?php
							if (have_rows('board_meeting_speakers')) {
								$speakersLabel = get_field('board_meeting_speaker_label');
								
								echo '<h3 class="sidebar-section-header">' . $speakersLabel . '</h3>';
								while (have_rows('board_meeting_speakers')) : the_row();

									// SHOW INTERNAL SPEAKER LIST
									if (get_row_layout() == 'board_meeting_speakers_internal') {
										if (get_sub_field('bbg_speaker_name')) {
											$profiles = get_sub_field('bbg_speaker_name');

											echo '<ul class="usa-unstyled-list unstyled-list" style="width: 100%">';

											$i = 0;
											foreach ($profiles as $profile) {
												$i++;
												$profile_id = $profile->ID;
												$profile_name = get_the_title($profile_id);
												$isActing = get_post_meta($profile_id, 'acting', true);
												$profile_bio = get_sub_field('bbg_speaker_bio');
												$occupation = get_post_meta($profile_id, 'occupation', true);
												$profile_link = get_page_link($profile_id);

												if ($isActing) {
													$occupation = "Acting " . $occupation;
												}

												$name_of_internal_speaker = '<p class="sidebar-article-title">' . $profile_name . '</p>';
												$title_of_internal_speaker = '<p class="sans">' . $occupation . '</p>';
												$link_of_internal_speaker = '<p class="sans"><a href="' . $profile_link . '" target="_blank">View Profile</a></p>';

												$internal_speaker_list = '';
												if (!empty($profile_bio)) {
													$internal_speaker_list .= '<li class="usa-accordion speaker-accordion">';
													$internal_speaker_list .= 		'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $i . '">' . $name_of_internal_speaker . $title_of_internal_speaker . ' <i class="fas fa-plus"></i></button>';
													$internal_speaker_list .= 		'<div id="collapsible-faq-' . $i . '" aria-hidden="true" class="usa-accordion-content">';
													$internal_speaker_list .= 			'<p class="snas speaker-bio">' . $profile_bio . '</p>';
													$internal_speaker_list .= 	$link_of_internal_speaker;
													$internal_speaker_list .= 		'</div>';
													$internal_speaker_list .= '</li>';
												} else {
													$internal_speaker_list  = '<li>';
													$internal_speaker_list .= 	$name_of_internal_speaker;
													$internal_speaker_list .= 	$title_of_internal_speaker;
													$internal_speaker_list .= '</li>';
												}

												echo $internal_speaker_list;
											}
											echo '</ul>';
										}
									} else if (get_row_layout() == 'board_meeting_speakers_external') {
										if (get_sub_field('meeting_speaker')) {
											$profiles = get_sub_field('meeting_speaker');
											echo '<ul class="usa-unstyled-list unstyled-list">';

											$i = 0;
											foreach ($profiles as $profile) {
												$i++;
												$speaker_name = $profile["meeting_speaker_name"];
												$speaker_title = $profile["meeting_speaker_title"];
												$speaker_bio = $profile["meeting_speaker_bio"];
												$speaker_link = $profile["meeting_speaker_url"];

												$name_of_external_speaker = '<p class="sidebar-article-title">' . $speaker_name . '</p>';
												$title_of_external_speaker = '<p class="sans">' . $speaker_title . '</p>';
												if (!empty($speaker_link)) {
													$link_of_external_speaker = '<p class="sans speaker-link"><a href="' . $speaker_link . '" target="_blank">' . $speaker_link . '</a></p>';
												}

												$external_speaker_list = '';
												if (!empty($speaker_bio)) {
													$external_speaker_list .= '<li class="usa-accordion speaker-accordion">';
													$external_speaker_list .= 		'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $i . '">' . $name_of_external_speaker . $title_of_external_speaker . ' <i class="fas fa-plus"></i></button>';
													$external_speaker_list .= 		'<div id="collapsible-faq-' . $i . '" aria-hidden="true" class="usa-accordion-content">';
													$external_speaker_list .= 			'<p class="sans speaker-bio">' . $speaker_bio . '</p>';
													if (!empty($speaker_link)) {
														$external_speaker_list .= 	$link_of_external_speaker;
													}
													$external_speaker_list .= 		'</div>';
													$external_speaker_list .= '</li>';
												} else {
													$external_speaker_list .= '<li>';
													$external_speaker_list .= 	$name_of_external_speaker;
													$external_speaker_list .= 	$title_of_external_speaker;
													$external_speaker_list .= '</li>';
												}
												echo $external_speaker_list;
											}
											echo '</ul>';
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