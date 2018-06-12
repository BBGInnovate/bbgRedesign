<?php
/**
 * Custom landing page for the "Who we are" and "Our Work" sections
 *
 * template name: About
 *
 * @author Gigi Frias <gfrias@bbg.gov>
 * @package bbgRedesign
 */

/* @Check if number of pages is odd or even
*  Return BOOL (true/false) */

require 'inc/custom_field_data_retriever.php';
require 'inc/bbg-functions-assemble.php';

function isOdd($pageTotal) {
	return ($pageTotal % 2) ? true : false;
}

$templateName = "about";

if (have_posts()) :
	while (have_posts()) : the_post();
		$pageContent = get_the_content();
		// KEEP THIS OFF, KEEP AN EYE OUT FOR FUTURE ERRORS
		// $pageContent = apply_filters('the_content', $pageContent);
		// WHAT IS THIS BELOW?
		$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
	endwhile;
endif;

wp_reset_postdata();
wp_reset_query();

get_header();
?>

<main id="main" class="site-main" role="main">
<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
	$page_content  = '<div class="outer-container">';
	$page_content .= 	'<p class="lead-in">' . $pageContent . '</p>';
	$page_content .= '</div>';
	echo $page_content;
?>
	<!-- Child pages -->
	<div id="page-children">
	<?php
		if (have_rows('about_flexible_page_rows')):
			$counter = 0;
			$pageTotal = 1;
			$containerClass = 'bbg__about__child ';

			while (have_rows('about_flexible_page_rows')) : the_row();
				$counter++;

				if (get_row_layout() != 'about_ribbon_page') {
					echo '<section class="outer-container">';
				} else {
					echo '<section class="usa-grid-full bbg__about__children--row bbg__ribbon--thin">';
				}

				// REFACTORED
				if (get_row_layout() == 'marquee') {
					$marquee_result = get_marquee_data();

					$marquee_markup  = '<div class="outer-container">';
					$marquee_markup .= 		'<p class="red-special">' . $marquee_result['content'] . '</p>';
					$marquee_markup .= '</div>';
					echo $marquee_markup;
				} elseif (get_row_layout() == 'umbrella') {
					$umbrella_result = get_umbrella_main_data();
					if ($umbrella_result['header'] != "") {
						// IS THIS CORRECT?
						if ($umbrella_result['header_link']) {
							$sectionHeading  = '<a href="' . $umbrella_result['header_link'] . '">';
							$sectionHeading .= 		$umbrella_result['header_link'];
							$sectionHeading .= '</a> ';
							$sectionHeading .= '<span class="bbg__links--right-angle-quote" aria-hidden="true">&raquo;</span>';
						} 
						echo '<h2>' . $umbrella_result['header'] . '</h2>';	
					} 
					
					if ($umbrella_result['intro_text'] != "") {
						if ($umbrella_result['intro_text']) { 
							echo '<div class="outer-container">';
							echo 	'<p class="lead-in">' . $umbrella_result['intro_text'] . '</p>';
							echo '</div>';
						}
					}

					// DETERMINE GRID FOR UMBRELLA
					$num_rows = count(get_sub_field('umbrella_content'));
					$containerClass = 'bbg-grid--1-2-2';
					if (isOdd($num_rows)) {
						$containerClass = 'bbg-grid--1-3-3';
					}

					echo '<div class="outer-container">';
					
					while (have_rows('umbrella_content')) {
						the_row();
						$content_result = get_umbrella_content_data($umbrella_result['content'], $containerClass);

						if ($content_result['layout'] == "full") {
							echo '<article class="' . $content_result['grid'] . '">';
							if ($content_result['header'] != "") {
								echo $content_result['header'];
							}
							if ($content_result['image_source']) {
								echo 	'<div>';
								echo 		$content_result['image'];
								echo 	'</div>';
							}

							echo '<h4>';
							echo 	'<a href="' . $content_result['link'] . '" ' . $content_result['link_target'] . '>';
							echo 		$content_result['title'];
							echo 	'</a>';
							echo 	$content_result['link_suffix'];
							echo '</h4>';

							if ($subTitle != "") {
								echo '<h5>' . $content_result['subtitle'] . '</h5>';
							}
							echo 	$content_result['description'];
							echo '</article>';
						} else {
							echo '<article class="' . $content_result['grid'] . ' bbg__about__grandchild">';
							echo 	'<h4 class="bbg__about__grandchild__title">' . $content_result['title'] . '</h4>';	
							echo 	'<a href="' . $content_result['link'] . '" ' . $content_result['link_target'] . '>';
							echo 		'<div class="bbg__about__grandchild__thumb" ';
							echo 			'style="background-image: url(' . $content_result['image_source'] . '); ';
							echo 			'background-position: center center;"></div>';
							echo 	'</a>';
							echo 	$content_result['description'];
							echo '</article>';
						}
					}
					echo '</div>';
					echo '</section>'; 
					// END UMBRELLA SECTION
				}

				// REFACTORED
				elseif (get_row_layout() == 'about_ribbon_page') {
					$ribbon_result = get_ribbon_data();

					$ribbon_markup  = '<div class="usa-grid">';
					$ribbon_markup .= 	'<div class="bbg__announcement__flexbox" name="' . $ribbon_result['label'] . '">';
					if ($imageURL) {
						$ribbon_markup .= 	'<div class="bbg__announcement__photo" style="background-image: url(' . $ribbon_result['image_url'] . ');"></div>';
					}
					$ribbon_markup .= 		'<div>';
					if ($labelLink) {
						$ribbon_markup .= 		'<h6 class="bbg__label"><a href="' . get_permalink($ribbon_result['label_link']) . '">' . $ribbon_result['label'] . '</a></h6>';
					} else {
						$ribbon_markup .= 		'<h6 class="bbg__label">' . $ribbon_result['label'] . '</h6>';
					}
					if ($headlineLink) {
						$ribbon_markup .= 		'<h2 class="bbg__announcement__headline"><a href="' . get_permalink($ribbon_result['headline_link']) . '">' . $ribbon_result['headline'] . '</a></h2>';
					} else {
						$ribbon_markup .= 		'<h2 class="bbg__announcement__headline">' . $ribbon_result['headline'] . '</h2>';
					}
					$ribbon_markup .= 			$ribbon_result['summary'];
					$ribbon_markup .= 		'</div>';
					$ribbon_markup .= 	'</div>';
					$ribbon_markup .= '</div>';
					echo '</section><!-- END Ribbon -->';
				/*** END DISPLAY OF ENTIRE RIBBON ROW ***/
				}
				elseif (get_row_layout() == 'about_office') {
					/*** BEGIN DISPLAY OF OFFICE ROW ***/
					$officeTag = get_sub_field( 'office_tag' );
					$officeTagBoolean = get_sub_field('office_tags_boolean_operator');
					$officeTitle = get_sub_field( 'office_title' );
					$officeEmail = get_sub_field( 'office_email' );
					$officePhone = get_sub_field( 'office_phone' );
					$officeFacebook = get_sub_field( 'office_facebook' );
					$officeTwitter = get_sub_field( 'office_twitter' );
					$officeYoutube = get_sub_field( 'office_youtube' );
					$officeEvent = false;
					$postIDsUsed = [];

					// set upcoming events query parameters
					$qParamsUpcoming = array(
						'post_type' => array('post'),
						'cat' => get_cat_id('Event'),
						'post_status' => array('future'),
						'order' => 'ASC',
						'posts_per_page' => 1
					);

					$tag_id_group = array();
					// foreach ($office_result['tag'] as $term) {
					foreach ($officeTag as $term) {
						$tag_id_group [] = $term->term_id;
					}
					if (count($officeTag)) {
						if ($officeTagBoolean == "AND") {
							$qParamsUpcoming['tag__and'] = $tag_id_group;
						} else {
							$qParamsUpcoming['tag__in'] = $tag_id_group;
						}
					}

					// execute upcoming events query
					$future_events_query = new WP_Query($qParamsUpcoming);
					$eventDetail = [];
					if ($future_events_query -> have_posts()) {
						$officeEvent = true; // set events variable to true

						// Loop through all event posts
						while ( $future_events_query -> have_posts() ) {
							$future_events_query -> the_post();
							// set variables from post array
							$id = get_the_ID();
							$eventDetail['url'] = get_the_permalink();
							$eventDetail['title'] = get_the_title();
							$eventDetail['thumb'] = get_the_post_thumbnail( $id, 'medium-thumb' );
							$eventDetail['excerpt'] = my_excerpt( $id );
							$eventDetail['id'] = $id;
							$postIDsUsed[] = $id;
						}
					}
					$maxPosts = 4; // set max number of events

					$qParamsOffice = array(
						'post_type' => array('post'),
						'posts_per_page' => $maxPosts,
						'orderby' => 'date',
						'order' => 'DESC',
						'post__not_in' => $postIDsUsed
					);
					if (count($officeTag)) {
						if ($officeTagBoolean == "AND") {
							$qParamsOffice['tag__and'] = $tag_id_group;
						} else {
							$qParamsOffice['tag__in'] = $tag_id_group;
						}
					}

					// set address variables
					$street = get_field( 'agency_street', 'options', 'false' );
					$city = get_field( 'agency_city', 'options', 'false' );
					$state = get_field( 'agency_state', 'options', 'false' );
					$zip = get_field( 'agency_zip', 'options', 'false' );
					$address = ""; // create full address variable

					// If all the address variables exists
					if ( $street != "" && $city != "" && $state != "" && $zip != "" ) {
						// concatenate all address variables
						$address = $street . '<br/>' . $city . ', ' . $state . ' ' . $zip;

						// Strip spaces for url-encoding.
						$street = str_replace( " ", "+", $street );
						$city = str_replace( " ", "+", $city );
						$state = str_replace( " ", "+", $state );
						$mapLink = 'https://www.google.com/maps/place/' . $street . ',+' . $city . ',+' . $state . '+' . $zip . '/';

						// set full address variable
						$address = '<p itemprop="address" aria-label="address"><a href="'. $mapLink . '">' . $address . '</a></p>';
					}
					$tagLink = get_tag_link( $officeTag[0] -> term_id );

					$office_event_label_style  = '<style>';
					$office_event_label_style .= 	'.bbg-blog__officeEvent-label {margin-top: 15px !important;}';
					$office_event_label_style .= '</style>';
					echo $office_event_label_style;

				// OFFICE ALERTS
					echo '<article class="bbg__article bbg__kits__section">';
					echo 	'<div class="usa-grid-full">';
					if ($officeEvent) {
						$office_alert  = '<section class="usa-section">';
						$office_alert .= 	'<div class="usa-alert usa-alert-info">';
						$office_alert .= 		'<div class="usa-alert-body">';
						$office_alert .= 			'<h3 class="usa-alert-heading">';
						$office_alert .= 				'<a href="' . $eventDetail['url'] . '">';
						$office_alert .= 					$eventDetail['title'];
						$office_alert .= 				'</a>';
						$office_alert .= 			'</h3>';
						$office_alert .= 			'<p class="usa-alert-text">';
						$office_alert .= 				$eventDetail['excerpt'];
						$office_alert .= 			'</p>';
						$office_alert .= 		'</div>';
						$office_alert .= 	'</div>';
						$office_alert .= '</section>';
					}

				// BBG KITS SECTION
				// START .entry-content
					echo '<div class="entry-content bbg__article-content large">';
					// HIGHLIGHTS SECTION
					echo 	'<section id="recent-posts" class="usa-section bbg__home__recent-posts">';
					echo 		'<h2>Recent Highlights</h2>';
					echo 		'<div class="bbg__kits__recent-posts">';
					echo 			'<div class="usa-width-one-half bbg__secondary-stories">';
					/* BEWARE: STICKY POSTS ADD RECORDS */
					// START FETCHING RELATED HIGHLIGHTS
					// RUN PRESS RELEASE QUERY
					query_posts($qParamsOffice);
					if (have_posts()) {
						$counter = 0;
						$includeImage = true;

						while (have_posts()) : the_post();
							$counter++;
							$includeMeta = false;
							$gridClass = 'bbg-grid--full-width';
							$includeExcerpt = false;

							if ($counter > 1) {
								$includeImage = false;
								$includeMeta = false;
								if ($counter == 2) {
									echo '</div>';
									echo '<div class="usa-width-one-half tertiary-stories">';
								}
							}
							if ($counter == 1) {
								$includePortfolioDescription = false;
								get_template_part( 'template-parts/content-portfolio', get_post_format() );
							} else {
								get_template_part( 'template-parts/content-excerpt-list', get_post_format() );
							}
						endwhile;

						echo '<a href="' . $tagLink . '" class="bbg__kits__intro__more--link">';
						echo 	'View all highlights »';
						echo '</a>';
					}
					wp_reset_query();

					echo 			'</div>';
					echo 		'</div>';
					echo 	'</section><!-- .BBG News -->';
					echo '</div>';
				// END .entry-content

				// REFACTORED CARD
				// CONTACT CARD (TAILORED TO AUDIENCE)
					$contact_card  = '<div class="bbg__article-sidebar large">';
					$contact_card .= 	'<aside>';
					$contact_card .= 		'<div class="bbg__contact-card">';
					$contact_card .= 			'<div class="bbg__contact-card__text">';
					$contact_card .= 				'<h3>' . $officeTitle . '</h3>';
					$contact_card .= 				$address;
					$contact_card .= 				'<ul class="usa-unstyled-list">';
					$contact_card .= 					'<li itemprop="telephone" aria-label="telephone">';
					$contact_card .= 						'<span class="bbg__list-label">Tel: </span>';
					$contact_card .= 						'<a href="tel:' . $officePhone . '">';
					$contact_card .= 							$officePhone;
					$contact_card .= 						'</a>';
					$contact_card .= 					'</li>';
					$contact_card .= 					'<li>';
					$contact_card .= 						'<span class="bbg__list-label">Email: </span>';
					$contact_card .= 						'<a itemprop="email" aria-label="email" href="mailto:' . $officeEmail . '" title="Contact us">';
					$contact_card .= 							$officeEmail;
					$contact_card .= 						'</a>';
					$contact_card .= 					'</li>';
					$contact_card .= 				'</ul>';
					$contact_card .= 				'<div class="bbg__kits__social">';
					if ($officeFacebook) {
						$contact_card .= '<a class="bbg__kits__social-link usa-link-facebook" href="' . $officeFacebook . '" role="img" aria-label="facebook"></a>';
					}
					if ($officeTwitter) {
						$contact_card .= '<a class="bbg__kits__social-link usa-link-twitter" href="' . $officeTwitter . '" role="img" aria-label="twitter"></a>';
					}
					if ($officeYoutube) {
						$contact_card .= '<a class="bbg__kits__social-link usa-link-youtube" href="' . $officeYoutube . '" role="img" aria-label="youtube"></a>';
					}
					$contact_card .= 				'</div>';
					$contact_card .= 			'</div>';
					$contact_card .= 		'</div>';
					$contact_card .= 	'</aside>';
					$contact_card .= '</div>';
					echo $contact_card;
				// END CONTACT CARD

					echo 	'</div>';
					echo '</article>';
				// END bbg__article bbg__kits__section

					echo '</section>';

				// END OFFICE ROW IF STATEMENT
				}
			endwhile;
			echo '<!-- END ROWS -->';
		endif;
	?>
	</div> <!-- End id="page-children" -->

	<!-- NETWORKS -->
	<?php
		$showNetworks = get_field('about_networks_row');
		if ($showNetworks) {
			echo get_entity_data();
		}
		wp_reset_postdata();
	?>
</main>

<?php get_footer(); ?>