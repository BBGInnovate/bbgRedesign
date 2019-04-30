<?php

/**
 * Custom Page for the Burke Awards.
 * It includes:
 * 	- Featured Image
 * 	- Page Description
 *	- Introduction to Latest Ceremony 
 * 		- Video and description, list of speakers, link to latest ceremony page and archive
 * 	- Five Winners
 * 	- David Burke Ribbon
 * 	- John Lansing Quore
 *
 * @package bbgRedesign
   template name: Burke Awards 2
 */

require 'inc/bbg-functions-assemble.php';

// GET CURRENT PAGE CONTENT
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$burke_page_id = get_the_id();
		$page_content = do_shortcode(get_the_content());
	}
}

// GET ONE WINNER FROM EACH NETWORK
$all_entity_winners = get_post_meta($burke_page_id, 'grid_box');
if (have_rows('grid_box')) {
	$all_entity_winners = array();
	while (have_rows('grid_box')) {
		the_row();
		$icon = get_sub_field('grid_box_icon');
		$icon_url = $icon['url'];
		$grid_text = get_sub_field('grid_box_text');
		$bg_image = get_sub_field('grid_box_background_image');
		$bg_image_url = $bg_image['url'];
		$candidate_profile_link = get_sub_field('grid_box_profile_link');

		$winner_set = array(
			'icon_url' => $icon_url,
			'grid_text' => $grid_text,
			'grid_image' => $bg_image_url,
			'candidate_profile' => $candidate_profile_link
		);
		array_push($all_entity_winners, $winner_set);
	}
}

get_header();
echo '<style>body {background-color: rgba(228, 235, 236, 0.95);}</style>';
echo '<style>.bbg__main-navigation .menu-usagm-container {background-color: rgba(228, 235, 236, 0.95);}</style>';
echo '<style>.bbg__main-navigation ul li ul li:hover {background-color: #d7e1e2;}</style>';
?>

<?php
	// SET FEAUTRE DATA FOR THIS BURKE AWARDS PAGE
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>


<?php
// GET LATEST BURKE CEREMONY
// Category Ids Events: 3, Press Release: 18
$burke_params = array(
	'category_name' => 'Burke Awards',
	'category_and' => array(3, 18),
	'order' => 'DESC',
	'posts_per_page' => 1
);
$ceremony_post = new WP_Query($burke_params);
if ($ceremony_post -> have_posts()) {
	while ($ceremony_post -> have_posts()) {
		$ceremony_post -> the_post();
		$ceremony_id = get_the_ID();
		$latest_ceremony_title = get_the_title();
		$thumbnail_src = get_the_post_thumbnail_url($ceremony_id, array(700, 450));
		$event_time = get_post_meta($ceremony_id, 'board_meeting_time');
		$event_location = get_post_meta($ceremony_id, 'board_meeting_location');
		$more_info = get_post_meta($ceremony_id, 'board_meeting_contact_tagline');
	}
}
?>
<main id="new-burke-test" role="main">

	<div class="outer-container">
		<div class="grid-container">
			<?php echo '<p class="lead-in">' . $page_content . '</p>'; ?>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<?php echo '<h2 class="section-header"><a href="' . get_the_permalink($ceremony_id) . '">' . get_the_title($ceremony_id) . '</a></h2>'; ?>
		</div>
		<!-- TWO COLUMNS FOR LATEST CEREMONY INTRO -->
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php
						// SET FEAUTRE DATA FOR LATEST CEREMONY PAGE
						$featured_media_result = get_feature_media_data();
						if ($featured_media_result != "") {
							echo $featured_media_result;
						}
						echo '<p style="margin-top: -4rem;">' . get_the_excerpt($ceremony_id) . '</p>';
					?>
				</div>
				<div class="side-content-container">
					<article>
						<h2 class="sidebar-section-header">Event</h2>
						<h6>Burke Award Ceremony</h6>
						<?php
							echo '<p class="sans">' . $event_location[0] . '<br>' . $event_time[0] . '</p>';
							echo '<p class="sans">' . $more_info[0] . '</p>';
						?>
					</article>

					<article>
						<h2 class="sidebar-section-header">Past Winners</h2>
						<?php
							$burke_archive_page = new WP_Query(array('page_id' => 39519));
							if ($burke_archive_page -> have_posts()) {
								while ($burke_archive_page -> have_posts()) {
									$burke_archive_page -> the_post();
									echo '<h6><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h6>';
								}
							}
						?>
					</article>
				</div>
			</div>
		</div>
	</div>

	<section class="grid-box-container">
		<div class="outer-container">
			<?php
				foreach($all_entity_winners as $entity_winner) {
					$grid_box_set  = '<a href="' . $entity_winner['candidate_profile'] . '">';
					$grid_box_set .= 	'<div class="grid-box-chunk" style="background-image: url(' . $entity_winner['grid_image'] . ');">';
					$grid_box_set .= 		'<div class="grid-box-text">';
					$grid_box_set .= 			'<img src=' . $entity_winner['icon_url'] . ' alt="Entity winner image">';
					$grid_box_set .= 			'<p>' . $entity_winner['grid_text'] . '</p>';
					$grid_box_set .= 		'</div>';
					$grid_box_set .= 	'</div>';
					$grid_box_set .= '</a>';
					echo $grid_box_set;
				}
			?>
		</div>
	</section>

	<div class="outer-container">
		<div class="grid-container">
			<?php echo '<h2 class="section-header">BBG History</h2>'; ?>
		</div>
		<div class="bbg__ribbon inner-ribbon">
			<div class="outer-container">
				<div class="side-content-container">
					<div style="background-image: url('http://dev.usagm.com/wp-content/uploads/2017/08/David-Burke-profile.png');"></div>
				</div>
				<div class="main-content-container">
					<h4>David W. Burke</h4>
					<p class="sans">(Former Chairman of the Board)</p>
					<p>David W. Burke was named to the first Broadcasting Board of Governors (BBG) by President Clinton in 1995 and served as its first chairman, leaving the board in 1998. His BBG legacy includes the David Burke Distinguished Journalism Award, which recognizes the courage, integrity, and professionalism of individuals in reporting the news within the BBG broadcast entities. Burke arrived at the BBG as director and a trustee of various Dreyfus Funds. Previously, he served as President of CBS News and served as both Vice President and Executive Vice President of ABC News.</p>
				</div>
			</div>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<div class="usagm-quotation">
				<img class="quote-image" src="http://dev.usagm.com/wp-content/uploads/2016/07/john_lansing_ceo-sq.jpg" class="bbg__quotation-attribution__mugshot" alt="John Lansing image">
				<h4 class="quote-text">These journalists have exemplified the definition of bravery and courage by risking their lives to report from some of the most dangerous places in the world.</h4>
				<p class="quote-byline">
					John Lansing<br>
					<span class="occupation">BBG CEO and Director</span>
				</p>
			</div>
		</div>
	</div>

</main>

<?php get_footer(); ?>