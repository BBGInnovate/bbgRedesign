<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// TEMPORARILY DISABLE CACHE
?>
<?php
/**
 * template name: Who We Are
 *
 * @author Kirk Radish <kradish@bbg.gov>
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
						echo 'United';
						echo "<div class='usa-grid-full bbg__article-featured__gallery'>";
							$featuredGalleryID = get_post_meta(get_the_ID(), 'featured_gallery_id', true);
							putUniteGallery($featuredGalleryID);
						echo "</div>";
					}
				?>
			</div>

			<?php
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

			<!-- Child pages XXX -->
			<div id="page-children" class="usa-section usa-grid bbg__about__children">
			<?php
			if (is_page('who-we-are')) {
				echo 'Who we are page';
			} else {
				echo 'Other page';
			}
				if (have_rows('about_flexible_page_rows')) {
					$counter = 0;
					$pageTotal = 1;
					$containerClass = 'bbg__about__child ';
					while (have_rows('about_flexible_page_rows')) {
						$counter++;
						the_row();
						echo '<!-- ROW. ' . $counter . '-->';
						if (get_row_layout() == 'ribbon') {
							echo 'ribbon<br>';
						}
						elseif (get_row_layout() == 'office') {
							echo 'office<br>';
						}
						elseif (get_row_layout() == 'umbrella') {
							echo 'umbrella<br>';
						}
						elseif (get_row_layout() == 'marquee') {
							$marquee_result = get_marquee_data();
							$marquee_markup  = '<h2>' . $marquee_result['heading'] . '</h2>';
							$marquee_markup .= '<p>' . $marquee_result['content'] . '</p>';
							echo $marquee_markup;
						}
					}
				}
			?>
			</div><!-- END #page-children -->

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
