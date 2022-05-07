<?php
/**
 * The template for displaying all single Burke profile posts. *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post *
 * @package bbgRedesign
 */
/* we go through the loop once and reset it in order to get some vars for our og tags */

require 'inc/bbg-functions-assemble.php';
include get_template_directory() . '/inc/shared_sidebar.php';

if (have_posts()) {
	the_post();
	// $metaAuthor = get_the_author();
	$metaKeywords = strip_tags(get_the_tag_list('', ', ', ''));
	/**** CREATE OG TAGS ***/
	$ogDescription = get_the_excerpt();
	$ogTitle = get_the_title();
	$thumb = wp_get_attachment_image_src(get_post_thumbnail_id( $post -> ID ) , 'Full');
	if (!empty($thumb)) {
		$ogImage = $thumb['0'];
	}
	$socialImageID = get_post_meta($post -> ID, 'social_image', true);
	if ( $socialImageID ) {
		$socialImage = wp_get_attachment_image_src($socialImageID, 'Full');
		$ogImage = $socialImage[0];
	}


	rewind_posts();
}

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$burke_candidate_id = get_the_ID();
		$page_title = get_the_title();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
		$post_thumbnail_url = get_the_post_thumbnail_url();
		$ogDescription = get_the_excerpt();

		$twitterText = '';
		$twitterText.= 'Profile of ' . html_entity_decode(get_the_title());
		$twitterText.= ' by @bbggov ' . get_permalink();
		$twitterURL = '//twitter.com/intent/tweet?text=' . rawurlencode($twitterText);
		$fbUrl = '//www.facebook.com/sharer/sharer.php?u=' . urlencode(get_permalink());
	}
}
wp_reset_postdata();
wp_reset_query();

get_header();
echo '<style>.bbg__main-navigation .menu-usagm-container {background-color: rgba(228, 235, 236, 0.95);}</style>';
echo '<style>.bbg__main-navigation ul li ul li:hover {background-color: #d7e1e2;}</style>';
?>


<main id="main" role="main">
	<section class="outer-container">
		<div class="grid-container">
			<h2 class="section-header"><a href="<?php echo network_home_url(); ?>/burke-awards-archive">Burke Awards honorees</a></h2>
		</div>
		<div class="grid-container sidebar-grid--large-gutter">
			<div class="nest-container">
				<div class="inner-container">
					<div class="main-column">
						<?php
							$burkeProfileObj = get_field('burke_award_info');
							$numRows = count ($burkeProfileObj);
							// Create variable to sort the award array by year
							// Populate and sort awards in reverse chronological order
							$orderByYear = array();
							foreach($burkeProfileObj as $i => $row) {
								$orderByYear[$i] = $row['burke_ceremony_year'];
							}
							array_multisort($orderByYear, SORT_DESC, $burkeProfileObj);

							$candidata_and_win  = '<h3 class="article-title">' . get_the_title() . ', ';

							// Check if repeater field has data
							// Check if profile won this year
							foreach (array_values($burkeProfileObj) as $i => $profile) {
								$burkeYear = $profile['burke_ceremony_year'];
								$burkeWin = $profile['burke_is_winner'];
								if (!$burkeWin) {
									$burkeStatus = ' nominee';
								} else {
									$burkeStatus = ' winner';
								}

								$candidata_and_win .= $burkeYear . $burkeStatus;
								if ($numRows > 1 && $i + 1 < $numRows) {
									$candidata_and_win .= ' | ';
								}
							}
							$candidata_and_win .= '</h3>';
							echo $candidata_and_win;
							
							if (!empty($post_thumbnail_url)) {
								echo '<img src="' . $post_thumbnail_url . '" alt="' . $page_title . '">';
							}

							echo $page_content;

							$acceptance_vidoes = '';
							foreach (array_values($burkeProfileObj) as $i => $profileVid) {
								$burke_video_url = $profileVid['burke_acceptance_video_url'];
								if ($burke_video_url) {
									if ($numRows > 1) {
										$acceptance_vidoes .= '<h4>Watch acceptance videos</h4>';
									} else {
										$acceptance_vidoes .=  '<h4>Watch acceptance video</h4>';
									}
									$acceptance_vidoes .= apply_filters('the_content', $burke_video_url);
								}
							}
							echo $acceptance_vidoes;
						?>
					</div>
					<div class="side-column divider-left">
					<?php
						// SHARE THIS PAGE
						$share_icons = social_media_share_page($burke_candidate_id);
						if (!empty($share_icons)) {
							echo $share_icons;
						}

						foreach (array_values($burkeProfileObj) as $i => $profile) {
							$i = $i + 1; // add a number to the item key to start at 1
							$award_list = '';

							$burkeTitle = $profile['burke_occupation'];
							$burkeNetwork = strtoupper($profile['burke_network']);
							if ($burkeNetwork == "RFERL") {
								$burkeNetwork = "RFE/RL";
							}
							$burkeService = '';
							if ($profile['burke_service']) {
								$burkeService = $profile['burke_service'] . ' Service';
							}

							echo '<div id="award-' . $i . '" class="bbg__sidebar__primary">';
							$award_list .= '<h3 class="sidebar-section-header">' . $profile['burke_ceremony_year'];
							if ($profile['burke_is_winner']) {
								$award_list .= ' Winner';
							} else {
								$award_list .= ' Nominee';
							}
							$award_list .= '</h3>';

							$award_list .= '<p class="sidebar-section-subheader">';
							if ($burkeTitle) {
								$award_list .= $burkeTitle . '<br/>';
								if ($burkeService) {
									$award_list .= $burkeNetwork . ', ' . $burkeService;
								} else {
									$award_list .= $burkeNetwork;
								}
							} else {
								if ($burkeService) {
									$award_list .= $burkeNetwork . ', ' . $burkeService;
								} else {
									$award_list .= $burkeNetwork;
								}
							}
							$award_list .= '</p>';

							$award_list .= '<p class="sans">' . $profile['burke_reason'] . '</p>';
							$burkeRelated = $profile ['burke_associated_profiles'];
							if ($burkeRelated) {
								$award_list .= '<h3 class="sidebar-section-header">Recognized with</h3>';
									foreach($burkeRelated as $burkeRelProfile) {
										$award_list .= '<p class="sidebar-article-title">';
										$award_list .= 	'<a target="_blank" href="' . get_post_permalink($burkeRelProfile->ID) . '">';
										$award_list .= 		$burkeRelProfile->post_title;
										$award_list .= 	'</a>';
										$award_list .= '</p>';
									}
							}

							// set variable for sample work URL repeater
							$burkeWorkObj = $profile['burke_sample_works'];
							$workLinks = array();
							$otherLinks = array();
							if ($burkeWorkObj) {
								$links = count( $burkeWorkObj );

								for ($l = 0; $l + 1 <= $links; $l++) {
									$linkType = $burkeWorkObj[$l]['burke_sample_works_type'];
									if ($linkType == 'work') {
									 	$workLinks[] = array (
											'type' => $linkType,
											'title' => $burkeWorkObj[$l]['burke_sample_works_title'],
											'url' => $burkeWorkObj[$l]['burke_sample_works_link']
										);
									} elseif ($linkType == 'related') {
										$otherLinks[] = array (
												'type' => $linkType,
												'title' => $burkeWorkObj[$l]['burke_sample_works_title'],
												'url' => $burkeWorkObj[$l]['burke_sample_works_link']
										);
									}
								}

								if (count($workLinks) == 1 && $workLinks[0]['url']) {
									$award_list .= '<h6>Award-winning work</h6>';
									$award_list .= '<p class="sans"><a target="_blank" href="' . $workLinks[0]['url'] . '">' . $workLinks[0]['title'] . ' »</a></p>';
								} elseif (count($workLinks) > 1) {
									$award_list .= '<h6>Award-winning works</h6>';
									$award_list .= '<ul class="unstyled-list" style="margin-bottom: 1rem;">';
										foreach($workLinks as $workURL) {
											$award_list .= '<li class="sans"><a target="_blank" href="' . $workURL['url'] . '">' . $workURL['title'] . ' »</a></li>';
										}
									$award_list .= '</ul>';
								}
								// output other links header
								if (count($otherLinks) == 1 && $otherLinks[0]['url']) {
									$award_list .= '<h3 class="sidebar-section-header">Related link</h3>';
									$award_list .= '<p class="sidebar-article-title"><a target="_blank" href="' . $otherLinks[0]['url'] . '">' . $otherLinks[0]['title'] . ' »</a></p>';
								} elseif (count($otherLinks) > 1) {
									$award_list .= '<h3 class="sidebar-section-header">Related links</h3>';
									$award_list .= '<ul class="unstyled-list" style="margin-bottom: 1rem;">';
										foreach($otherLinks as $otherURL) {
											$award_list .= '<li class="sidebar-article-title"><a target="_blank" href="' . $otherURL['url'] . '">' . $otherURL['title'] . '</a></li>';
										}
									$award_list .= '</ul>';
								}
							}
							echo 	$award_list;
							echo '</div>';
						}
					?>
					<article id="added-sidebar">
						<?php
							echo "<!-- Additional sidebar content -->";
							if ($includeSidebar) {
								echo $sidebar;
							}
							echo $sidebarDownloads;
						?>
					</article>
					<?php wp_reset_postdata();?>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>