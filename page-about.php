<?php
/**
 * Custom landing page for the "Who we are" and "Our Work" sections
 *
 * template name: About
 *
 * @author Gigi Frias <gfrias@bbg.gov>
 * @package bbgRedesign
 */

// REMOVE P TAGS FROM CONTENT TO PUT IN YOUR OWN PARAGRAPHY STYLES
remove_filter ('the_content', 'wpautop');

require get_template_directory() . '/inc/bbg-functions-assemble.php';

/* @Check if number of pages is odd or even
*  Return BOOL (true/false) */
function isOdd( $pageTotal ) {
	return ( $pageTotal % 2 ) ? TRUE : FALSE;
}


// FLEXIBLE ROW DATA
// MARQUEE
function get_marquee_data() {
	$marquee_heading = get_sub_field('marquee_heading');
	$marquee_link = get_sub_field('marquee_link');
	$marquee_content = get_sub_field('marquee_content');
	$marquee_content = apply_filters( 'the_content', $marquee_content );
	$marquee_content = str_replace( ']]>', ']]&gt;', $marquee_content );

	$marquee_package = array('heading' => $marquee_heading, 'link' => $marquee_link, 'content' => $marquee_content);
	return $marquee_package;
}

// UMBRELLA
function get_umbrella_content_data($type, $grid) {
	$umbrella_content_package = '';

	if ($type == 'internal') {
		$id = $page_object[0] -> ID;
		$link = get_the_permalink($id);

		$column_title = 			get_sub_field('umbrella_content_internal_column_title');
		$titleOverride = 			get_sub_field('umbrella_content_internal_title');
		$page_object = 				get_sub_field('umbrella_content_internal_link');
		$includeTitle = 			get_sub_field('umbrella_content_internal_include_item_title');
		$secondaryHeadline = 		get_post_meta($id, 'headline', true);
		$lawName = 					get_post_meta($id, 'law_name', true);
		$umbrella_featured_image = 	get_sub_field('umbrella_content_internal_include_featured_image');
		$showExcerpt = 				get_sub_field('umbrella_content_internal_include_excerpt');

		$item_title = "";
		if ($includeTitle) {
			$titleOverride = get_sub_field('umbrella_content_internal_item_title');
			if ($titleOverride != "" ) {
				$item_title = $titleOverride;
			}
			else {
				if ($secondaryHeadline) {
					$item_title = $secondaryHeadline;	
				}
				else {
					$item_title = $page_object[0] -> post_title;	
				}
			}
		}

		$thumbSrc = "";
		if ($umbrella_featured_image) {
			$thumbSrc = wp_get_attachment_image_src( get_post_thumbnail_id($id) , 'medium-thumb' );
			if ($thumbSrc) {
				$thumbSrc = $thumbSrc[0];
			}
		}

		$description = "";
		if ($showExcerpt) {
			$description = my_excerpt( $id );
			$description = apply_filters( 'the_content', $description );
			$description = str_replace( ']]>', ']]&gt;', $description );
		}

		$umbrella_content_package = array(
			'columnTitle' => $column_title,
			'itemTitle' => $item_title,
			'description' => $description,
			'link' => $link, 
			'thumbSrc' => $thumbSrc,
			'gridClass' => $grid,
			'forceContentLabels' => $force_content_labels,
			'columnType' => 'internal',
			'layout' => get_sub_field('umbrella_content_internal_layout'),
			'subTitle' => $lawName
		);
	}
	
	if ($type == 'external') {
		$thumbnail = get_sub_field('umbrella_content_external_thumbnail');
		$thumbnailID = $thumbnail['ID'];
		$thumbSrc = wp_get_attachment_image_src($thumbnailID , 'medium-thumb');

		if ($thumbSrc) {
			$thumbSrc = $thumbSrc[0];
		}

		$umbrella_content_package = array(
			'columnTitle' => get_sub_field('umbrella_content_external_column_title'),
			'itemTitle' => get_sub_field('umbrella_content_external_item_title'),
			'description' => get_sub_field('umbrella_content_external_description'),
			'link' => get_sub_field('umbrella_content_external_link'),
			'thumbSrc' => $thumbSrc,
			'gridClass' => $grid,
			'forceContentLabels' => $force_content_labels,
			'columnType' => 'external',
			'layout' => get_sub_field('umbrella_content_external_layout'),
			'subTitle' => ''
		);
	}

	showUmbrellaArea($umbrella_content_package);
}

function showUmbrellaArea($atts) {
	$itemTitle = $atts['itemTitle'];
	$columnTitle = $atts['columnTitle'];
	$link = $atts['link'];
	$gridClass = $atts['gridClass'];
	$description = $atts['description'];
	$force_content_labels = $atts['forceContentLabels'];
	$thumbPosition = "center center";
	$subTitle = $atts['subTitle'];
	$thumbSrc = $atts['thumbSrc'];
	$columnType = $atts['columnType'];
	$anchorTarget = "";
	$layout = $atts['layout'];
	$linkSuffix = "";

	if ($columnType == "file") {
		$fileSize = $atts['fileSize'];
		$fileExt = $atts['fileExt'];
		$linkSuffix = ' <span class="bbg__file-size">(' . $fileExt . ', ' . $fileSize . ')</span>';
	}
	if ($columnType == "external" || $columnType == "file") {
		$anchorTarget = " target='_blank' ";
	}

	$layout_package = array('layout' => $layout, 'grid' => $gridClass, 'column_title' => $columnTitle, 'force_content_label' => $force_content_labels, 'anchor' => $anchorTarget, 'link' => $link, 'link_suffix' => $linkSuffix, 'thumb' => $thumbSrc, 'item_title' => $itemTitle, 'sub_title' => $subTitle, 'description' => $description);
	return build_layout_module($layout_package);
}

function build_layout_module($build_data) {
	if ($build_data['layout'] == 'full') {

		if ($build_data['column_title'] == "") {
			if ($build_data['force_content_label']) {
				$blank_title = '&nbsp;';
				$section_header_build = $blank_title;
			}
		} else {
			if ($build_data['link'] != "") {
				$linked_title  = '<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '">';
				$linked_title .= 	$build_data['column_title'];
				$linked_title .= '</a>';
				$section_header_build = $linked_title;
			}
		}

		$full_layout_markup  = '<article class="' . $build_data['grid'] . '">';
		$full_layout_markup .= 		'<h2>' . $section_header_build . '</h2>';

		if ($build_data['thumb']) {
			$thumbnail  = 	'<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '" rel="bookmark" tabindex="-1">';
			$thumbnail .= 		'<img src="' . $build_data['thumb'] .  '">';
			$thumbnail .= 	'</a>';
			$full_layout_markup .= $thumbnail;
		}
		$title  = '<h4 class="bbg__about__grandchild__title">';
		$title .= 	'<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '">';
		$title .= 		$build_data['item_title'];
		$title .= 	'</a>';
		$title .= 	$build_data['link_suffix'];
		$title .= '</h4>';
		$full_layout_markup .= $title;
		
		if ($build_data['sub_title'] != "") {
			$subtitle = '<h5 class="bbg__about__grandchild__subtitle">' . $build_data['sub_title'] . '</h5>';
			$full_layout_markup .= $subtitle;
		}
		
		$full_layout_markup .= 		$build_data['description'];
		$full_layout_markup .= '</article>';

		echo $full_layout_markup;
	}
	else {
		$layout_markup  = '<article class="' . $build_data['grid'] . ' bbg__about__grandchild">';
		$columnTitle = $itemTitle;
		if ($build_data['link'] != "") {
			$columnTitle = '<a ' . $build_data['anchor'] . ' href="' . $build_data['link'] . '">' . $build_data['column_title'] . '</a>';
		}
		$columnTitle = $columnTitle . $build_data['link_suffix'];
		$layout_markup .= '<h3 class="bbg__about__grandchild__title">' . $columnTitle . '</h3>';	
		$layout_markup .= '<a '  . $build_data['anchor'] . ' href="' . $build_data['link'] . '">';
		$layout_markup .= '<div class="bbg__about__grandchild__thumb" style="background-image: url(' . $build_data['thumb'] . '); background-position:center center;"></div></a>' . $build_data['description'];
		$layout_markup .= '</article>';
		$layout_markup .= '<!--xxxxxxxx-->';
		echo $layout_markup;
	}
}

$templateName = "about";

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$pageContent = get_the_content();
		$pageContent = apply_filters('the_content', $pageContent);
   		$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
	endwhile;
endif;
wp_reset_postdata();
wp_reset_query();

get_header();

?>

<div id="main" class="site-main">

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<div class="usa-grid-full">
				<?php
					if ($addFeaturedGallery) {
						echo "<div class='usa-grid-full bbg__article-featured__gallery'>";
							$featuredGalleryID = get_post_meta(get_the_ID(), 'featured_gallery_id', true);
							putUniteGallery($featuredGalleryID);
						echo "</div>";
					}
				?>
			</div>

			<?php
				// GET RID OF THIS FOR NOW, WE'RE NOT EVEN USING IT ANYWHERE
				// $addFeaturedGallery = get_post_meta(get_the_ID(), 'featured_gallery_add', true);
				// if ($addFeaturedGallery) {
				// 	$hideFeaturedImage = true;
				// }

				$bannerPosition = get_field('adjust_the_banner_image', '', true);
				$videoUrl = get_field('featured_video_url', '', true);

				if ($videoUrl != "") {
					$hideFeaturedImage = true;
					$video_data = featured_video($videoUrl);

					$video_markup  = '<iframe scrolling="no" src="';
					$video_markup .= 	$video_data['url'];
					$video_markup .= '" frameborder="0" allowfullscreen="" data-ratio="NaN" data-width="" data-height="" style="display: block; margin: 0px;"></iframe>';

					$featured_data = $video_markup;

				} elseif ( has_post_thumbnail()) {
					$featuredImageClass = "";
					$featuredImageCutline = "";
					$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
					$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');

					if ( $thumbnail_image && isset($thumbnail_image[0]) ) {
						$featuredImageCutline = $thumbnail_image[0] -> post_excerpt;
					}

					$post_featured_image  = '<div class="feature-image">';
					$post_featured_image .= 		'<img src="' . $src[0] . '">';
					$post_featured_image .= '</div>';

					$featured_data =  $post_featured_image;
				}

				// ACTUAL BANNER MARKUP
				if ($videoURL != "" || has_post_thumbnail()) {
					$featured_markup  = '<div class="usa-grid">';
					$featured_markup .= 	'<div class="feature-element">';
					$featured_markup .= 		$featured_data;
					$featured_markup .= 	'</div>';
					$featured_markup .= '</div>';
					echo $featured_markup;
				}
			?>

			<!-- PAGE CONTENT -->
			<section class="usa-section usa-grid">
				<p class="lead-in">
					<?php echo $pageContent; ?>
				</p>
			</section>

			<!-- Child pages -->
			<div id="page-children" class="usa-section usa-grid bbg__about__children">
			<?php
				// check if the flexible content field has rows of data
				if (have_rows('about_flexible_page_rows')):
					$counter = 0;
					$pageTotal = 1;
					$containerClass = 'bbg__about__child ';

					while (have_rows('about_flexible_page_rows')) : the_row();
						$counter++;
// END WEIRD SECTION
						if (get_row_layout() != 'about_ribbon_page') {
							echo '<!-- FLEX ROW ' . $counter . '-->';
							echo '<section class="usa-grid-full bbg__about__children--row">';
						} else {
							echo '<!-- FLEX ROW ' . $counter . '-->';
							echo '<section class="usa-grid-full bbg__about__children--row bbg__ribbon--thin">';
						}

						// MARQUEE REFACTORED SECTION
						if (get_row_layout() == 'marquee'):
							$marquee_data = get_marquee_data();

							$marquee_markup  = '<section class="usa-grid-full bbg__about__children--row bbg__about--marquee">';
							$marquee_markup .= 	'<article id="post-25948" class="bbg__about__excerpt bbg__about__child bbg__about__child--mission bbg-grid--1-1-1 post-25948 page type-page status-publish has-post-thumbnail hentry">';
							$marquee_markup .= 		'<header>';
							$marquee_markup .= 			'<h2><a href="' . $marquee_data['link'] . '">' . $marquee_data['heading'] . '</a></h2>';
							$marquee_markup .= 		'</header>';
							$marquee_markup .= 		'<div class="entry-content bbg__about__excerpt-content">';
							$marquee_markup .= 			$marquee_data['content'];
							$marquee_markup .= 		'</div>';
							$marquee_markup .= 	'</article>';
							$marquee_markup .= '</section>';
							echo $marquee_markup;
						
						elseif (get_row_layout() == 'umbrella'):
							// BEGIN UMBRELLA ROW
							$section_header = get_sub_field('umbrella_section_heading');
							$section_heading_link = get_sub_field('umbrella_section_heading_link');
							$force_content_labels = get_sub_field('umbrella_force_content_labels');
							$section_intro_text = get_sub_field('umbrella_section_intro_text');

							$section_intro_text = apply_filters('the_content', $section_intro_text);
							$section_intro_text = str_replace(']]>', ']]&gt;', $section_intro_text);

							$section_head = '';
							if ($section_header != "") {
								$section_head  = '<h2>';
								if ($section_heading_link) {
									$section_head .= '<a href="' . $umbrella_data['header_link'] . '">';
									$section_head .= 	'testing ' . $umbrella_data['header'];
									$section_head .= 	'<span class="bbg__links--right-angle-quote" aria-hidden="true">&raquo;</span>';
									$section_head .= '</a>';
								} else {
									$section_head .= 	'testing ' . $umbrella_data['header'];
								}
								$section_head .= '</h2>';
								echo $section_head_markup;
							} 
							
							if ($section_intro_text != "") {
								if ($section_intro_text) { 
									$section_intro_markup  = '<div class="bbg__about__child__intro">';
									$section_intro_markup .= 	$umbrella_data['intro_text'];
									$section_intro_markup .= '</div>';
									echo $section_intro_markup;
								}
							}

							$numRows = count(get_sub_field('umbrella_content'));
							$containerClass = 'bbg-grid--1-2-2';
							if (isOdd($numRows)) {
								$containerClass = 'bbg-grid--1-3-3'; // add 3-col grid class
							}

							echo '<div class="usa-grid-full bbg__about__grandchildren">'; // open grandchildren container
							while (have_rows('umbrella_content')) : the_row();
								if (get_row_layout() == 'umbrella_content_internal') {
									get_umbrella_content_data('internal', $containerClass);
								}
								elseif (get_row_layout() == 'umbrella_content_external') {
									get_umbrella_content_data('external', $containerClass);
								}
								elseif (get_row_layout() == 'umbrella_content_file') {

									$fileObj = get_sub_field('umbrella_content_file_file');
									$description = get_sub_field('umbrella_content_file_description');
									$layout = get_sub_field('umbrella_content_file_layout');

									$thumbnail = get_sub_field('umbrella_content_file_thumbnail');
									$thumbnailID = $thumbnail['ID'];
									$thumbSrc = wp_get_attachment_image_src( $thumbnailID , 'medium-thumb' );
									if ($thumbSrc) {
										//$thumbSrc = 'src="' . $thumbSrc[0] . '"';	
										$thumbSrc = $thumbSrc[0];
									}
									
									$description = get_sub_field('umbrella_content_file_description');
									$description = apply_filters( 'the_content', $description );
									$description = str_replace( ']]>', ']]&gt;', $description );
									
									$fileTitle = get_sub_field('umbrella_content_file_item_title');
									//parse information about the file so we can append file sizeto append to our file title
									$fileID = $fileObj['ID'];
									$fileURL = $fileObj['url'];
									$file = get_attached_file( $fileID );
									$fileExt = strtoupper( pathinfo( $file, PATHINFO_EXTENSION ) );
									$fileSize = formatBytes( filesize( $file ) );
									
									showUmbrellaArea(array(
										'columnTitle' => get_sub_field('umbrella_content_file_column_title'),
										'itemTitle' => $fileTitle,
										'description' => $description,
										'link' => $fileURL, 
										'thumbSrc' => $thumbSrc,
										'gridClass' => $containerClass,
										'forceContentLabels' => $force_content_labels,
										'columnType' => 'file',
										'layout' => $layout,
										'fileExt' => $fileExt,
										'fileSize' => $fileSize,
										'subTitle' => ''
									));
								}
							endwhile;
							echo '</div>';
							echo '</section><!--wtf?-->'; 
// END WEIRD SECTION
							// END UMBRELLA ROW

						elseif(get_row_layout() == 'about_ribbon_page'):
							// BEGIN RIBBON ROW
							$labelText = get_sub_field( 'about_ribbon_label' );
							$labelLink = get_sub_field( 'about_ribbon_label_link' );
							$headlineText = get_sub_field( 'about_ribbon_headline' );
							$headlineLink = get_sub_field( 'about_ribbon_headline_link' );
							$summary = get_sub_field( 'about_ribbon_summary' );
							$imageURL = get_sub_field( 'about_ribbon_image' );

							// ALLOW SHORTCODES
							$summary = apply_filters( 'the_content', $summary );
							$summary = str_replace( ']]>', ']]&gt;', $summary );

							echo '<div class="usa-grid">';
							echo 	'<div class="bbg__announcement__flexbox" name="' . $labelText . '">';
							if ($imageURL) {
								echo 	'<div class="bbg__announcement__photo" style="background-image: url(' . $imageURL . ');"></div>';
							}
							echo 		'<div>';
							if ($labelLink) {
								echo 		'<h6 class="bbg__label"><a href="' . get_permalink($labelLink) . '">' . $labelText . '</a></h6>';
							} else {
								echo 		'<h6 class="bbg__label">' . $labelText . '</h6>';
							}
							if ( $headlineLink ) {
								echo 		'<h2 class="bbg__announcement__headline"><a href="' . get_permalink($headlineLink) . '">' . $headlineText . '</a></h2>';
							} else {
								echo 		'<h2 class="bbg__announcement__headline">' . $headlineText . '</h2>';
							}

							echo $summary;
							echo 		'</div>'; // close ribbon text container
							echo 	'</div><!-- .bbg__announcement__flexbox -->'; // close ribbon container
							echo '</div><!-- .usa-grid -->';
							echo '</section><!-- huh? -->';
							/*** END DISPLAY OF ENTIRE RIBBON ROW ***/

						elseif ( get_row_layout() == 'about_office' ):
							// OFFICE ROW
							$officeTag = get_sub_field( 'office_tag' );
							$officeTagBoolean = get_sub_field('office_tags_boolean_operator');
							$officeTitle = get_sub_field( 'office_title' );
							$officeEmail = get_sub_field( 'office_email' );
							$officeEmail = '<li><span class="bbg__list-label">Email: </span><a itemprop="email" aria-label="email" href="mailto:' . $officeEmail . '" title="Contact us">' . $officeEmail . '</a></li>';
							$officePhone = get_sub_field( 'office_phone' );
							$officePhone = '<li itemprop="telephone" aria-label="telephone"><span class="bbg__list-label">Tel: </span><a href="tel:' . $officePhone . '">' . $officePhone . '</a></li>';
							$officeFacebook = get_sub_field( 'office_facebook' );
							$officeTwitter = get_sub_field( 'office_twitter' );
							$officeYoutube = get_sub_field( 'office_youtube' );
							$officeEvent = false;
							$postIDsUsed = [];

							// set upcoming events query parameters
							$qParamsUpcoming = array(
								'post_type' => array( 'post' )
								,'cat' => get_cat_id( 'Event' )
								,'post_status' => array( 'future' )
								,'order' => 'ASC'
								,'posts_per_page' => 1
							);
							$tagIDs = array();
							foreach($officeTag as $term) {
								$tagIDs []= $term->term_id;
							}
							if (count($officeTag)) {
								if ($officeTagBoolean == "AND") {
									$qParamsUpcoming['tag__and'] = $tagIDs;
								} else {
									$qParamsUpcoming['tag__in'] = $tagIDs;
								}
							}

							// execute upcoming events query
							$future_events_query = new WP_Query( $qParamsUpcoming );
							$eventDetail = [];

							// if upcoming events query has posts
							if ( $future_events_query -> have_posts() ) {
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

							// define office query parameters
							$qParamsOffice = array(
								'post_type' => array( 'post' ),
								'posts_per_page' => $maxPosts,
								'orderby' => 'date',
								'order' => 'DESC',
								'post__not_in' => $postIDsUsed
							);
							if (count($officeTag)) {
								if ($officeTagBoolean == "AND") {
									$qParamsOffice['tag__and'] = $tagIDs;
								} else {
									$qParamsOffice['tag__in'] = $tagIDs;
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
			// TEMPORARILY CLOSE PHP
			?>
			<style>.bbg-blog__officeEvent-label { margin-top:15px !important; }</style>
			<article class="bbg__article bbg__kits__section">
				<div class="usa-grid-full">
					<?php if ( $officeEvent ): ; // if there are events ?>
						<section class="usa-section">
							<div class="usa-alert usa-alert-info">
							    <div class="usa-alert-body">
							      <h3 class="usa-alert-heading"><?php echo '<a href="' . $eventDetail['url'] . '">' . $eventDetail['title'] . '</a>'; ?></h3>
							      <p class="usa-alert-text"><?php echo $eventDetail['excerpt']; ?></p>
							    </div>
							</div>
						</section>
					<?php endif; ?>

					<div class="entry-content bbg__article-content large">
						<!-- Highlights section -->
						<section id="recent-posts" class="usa-section bbg__home__recent-posts">
							<h2>Recent Highlights</h2>

							<div class="bbg__kits__recent-posts">
								<div class="usa-width-one-half bbg__secondary-stories">
									<?php
										/* BEWARE: sticky posts add a record */
										/**** START FETCH related highlights ****/

										// Run press releases query
										query_posts( $qParamsOffice );

										if ( have_posts() ) {
											$counter = 0;
											$includeImage = TRUE;

											while ( have_posts() ) : the_post();
												$counter++;
												$includeMeta = false;
												$gridClass = 'bbg-grid--full-width';
												$includeExcerpt = false;

												if ( $counter > 1 ) {
													$includeImage = false;
													$includeMeta = false;
													if ( $counter == 2 ) {
														echo '</div><div class="usa-width-one-half tertiary-stories">';
													}
												}
												if ( $counter == 1 ) {
													$includePortfolioDescription = false;
													get_template_part( 'template-parts/content-portfolio', get_post_format() );
												} else {
													get_template_part( 'template-parts/content-excerpt-list', get_post_format() );
												}
											endwhile;

											echo '<br/><a href="' . $tagLink . '" class="bbg__kits__intro__more--link">View all highlights »</a>';
										}
										wp_reset_query();
									?>
								</div>
							</div>
						</section><!-- .BBG News -->
					</div>
					<!-- Contact card (tailored to audience) -->
					<div class="bbg__article-sidebar large">
						<aside>
							<div class="bbg__contact-card">
								<div class="bbg__contact-card__text">
									<?php
										echo '<h3>' . $officeTitle . '</h3>';
										echo $address;
										echo '<ul class="usa-unstyled-list">';
											echo $officePhone;
											echo $officeEmail;
										echo '</ul>';

										echo '<!-- Social media profiles -->';
										echo '<div class="bbg__kits__social">';
											$officeFacebook = get_sub_field( 'office_facebook' );
											$officeTwitter = get_sub_field( 'office_twitter' );
											$officeYoutube = get_sub_field( 'office_youtube' );

											if ( $officeFacebook ) {
												echo '<a class="bbg__kits__social-link usa-link-facebook" href="' . $officeFacebook . '" role="img" aria-label="facebook"></a>';
											}
											if ( $officeTwitter ) {
												echo '<a class="bbg__kits__social-link usa-link-twitter" href="' . $officeTwitter . '" role="img" aria-label="twitter"></a>';
											}
											if ( $officeYoutube ) {
												echo '<a class="bbg__kits__social-link usa-link-youtube" href="' . $officeYoutube . '" role="img" aria-label="youtube"></a>';
											}
										echo '</div>';

									?>
								</div>
							</div>
						</aside>
					</div>
				</div>
			</article>
			<?php
			// REOPEN PHP
						echo '</section><!-- row? -->';
			// CLOSE ROW SECTION
						/*** END DISPLAY OF OFFICE ROW ***/
						endif;
					endwhile;
					echo '<!-- END ROWS -->';
				endif;
			?>
			</div> <!-- End id="page-children" -->

			<?php
				$showNetworks = get_field( 'about_networks_row' );
				if ( $showNetworks ) { ?>

				<!-- Entity list -->
				<section id="entities" class="usa-section bbg__staff">
					<div class="usa-grid">
						<h6 class="bbg__label"><a href="<?php echo get_permalink( get_page_by_path( 'networks' ) ); ?>" title="List of all BBG broadcasters">Our networks</a></h6>
						<div class="usa-intro bbg__broadcasters__intro">
							<h3 class="usa-font-lead">Every week, more than <?php echo do_shortcode('[audience]'); ?> listeners, viewers and Internet users around the world turn on, tune in and log onto U.S. international broadcasting programs. The day-to-day broadcasting activities are carried out by the individual BBG international broadcasters.</h3>
						</div>
						<?php echo outputBroadcasters('2'); ?>
					</div>
				</section><!-- entity list -->
			<?php
				}
			wp_reset_postdata();
			?>

		</main>
	</div><!-- #primary .content-area -->
</div><!-- #main .site-main -->

<?php get_footer(); ?>
