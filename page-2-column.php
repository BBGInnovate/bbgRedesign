<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: 2-column
 */

// FUNCTION THAT BUILD SECTIONS
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

require 'inc/bbg-functions-assemble.php';
include "inc/shared_sidebar.php";

$addFeaturedGallery = get_post_meta( get_the_ID(), 'featured_gallery_add', true );

$headline = get_field('headline', '', true);
$listsInclude = get_field( 'sidebar_dropdown_include', '', true);

// THIS COULD BELONG IN A BUILD INCLUDE FILE
function display_foia_reports() {
	// $foia_url = WP_CONTENT_URL . '/uploads/foia-reports/'; // LOCAL
	// $foia_path = 'wp-content/uploads/foia-reports'; // LOCAL
	$foia_url = WP_CONTENT_URL . '/media/foia-reports/'; // LIVE
	$foia_path = 'wp-content/media/foia-reports'; // LIVE
	$filenames = preg_grep('/^([^.])/', scandir($foia_path));

	function get_nums($x) {
		if (preg_match("/[0-9]+/", $x, $date_nums)) {
			if (strlen($date_nums[0]) == 2) {
				$date_nums[0] = '20' . $date_nums[0];
			}
			return $date_nums[0];
		}
	}
	$num_group = [];
	foreach($filenames as $file) {
		$nums = get_nums($file);
		array_push($num_group, $nums);
	}
	$num_group = array_unique($num_group);
	rsort($num_group);
	$counter = 0;
	foreach($num_group as $year) {
		if ($counter != 0) {
			echo '</ul>';
		}
		echo '<h4>'.$year.'</h4>';
		echo '<ul>';
		foreach($filenames as $file) {
			$file_year = get_nums($file);
			if ($file_year == $year) {
				$dl_link  = '<li><a href="';
				$dl_link .= 	$foia_url . $file;
				$dl_link .= 	'">';
				$dl_link .= 		$file;
				$dl_link .= '</a></li>';
				echo $dl_link;
			}
		}
		$counter++;
	}
	
	echo '</ul><p style="text-align: right;"><a href="https://www.bbg.gov/foia/">Visit the main FOIA page</a></p>';
}

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_ID();
		$ogDescription = get_the_excerpt();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
	}
}
wp_reset_postdata();
wp_reset_query();

get_header(); ?>

<main id="main" class="site-main bbg__2-column" role="main">

	<?php
		$featured_media_result = get_feature_media_data();
		if ($featured_media_result != "") {
			echo $featured_media_result;
		}
	?>

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php
						if($post->post_parent) {
							$parent_link = get_permalink($post->post_parent);
							echo '<h2><a href="' . $parent_link . '">' . get_the_title($post->post_parent) . '</a></h2>';
						}
						else {
							$headline_string  = '<h3>' . $headline . '</h3>';
							echo $headline_string;
						}

						echo '<div class="page-content">';
						echo $page_content;
						echo '</div>';

						if (is_page('foia-reports')) {
							display_foia_reports();
						}

						//Add blog posts below the main content
						$relatedCategory = get_field('related_category_posts', $id);

						if ($relatedCategory != "") {
							$qParams2 = array(
								'post_type' => array('post'),
								'posts_per_page' => 2,
								'cat' => $relatedCategory->term_id,
								'orderby' => 'date',
								'order' => 'DESC'
							);
							$categoryUrl = get_category_link($relatedCategory->term_id);
							$custom_query = new WP_Query($qParams2);

							if ($custom_query -> have_posts()) {
								echo '<h6 class="bbg__label"><a href="' . $categoryUrl . '">' . $relatedCategory->name . '</a></h6>';
								echo '<div class="usa-grid-full">';
									while ($custom_query -> have_posts())  {
										$custom_query->the_post();
										get_template_part('template-parts/content-portfolio', get_post_format());
									}
								echo '</div>';
							}
							wp_reset_postdata();
						}
					?>
				</div>
				<div class="side-content-container">
					<?php
						$secondaryColumnLabel = get_field('secondary_column_label');
						$secondaryColumnContent = get_field('secondary_column_content');

						if ($secondaryColumnContent != "") {
							if ($secondaryColumnLabel != "") {
								echo '<h5>' . $secondaryColumnLabel . '</h5>';
							}
							echo $secondaryColumnContent;
						}
						if ($includeSidebar) {
							echo $sidebar;
						}
						if ($listsInclude) {
							echo $sidebarDownloads;
						}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="outer-container">
		<footer class="entry-footer bbg-post-footer 1234">
			<?php
				edit_post_link(
					sprintf(
						/* translators: %s: Name of current post */
						esc_html__('Edit %s', 'bbginnovate'),
						the_title('<span class="screen-reader-text">"', '"</span>', false)
					),
					'<span class="edit-link">',
					'</span>'
				);
			?>
		</footer><!-- .entry-footer -->
	</div><!-- .usa-grid -->

	<div class="bbg-post-footer">
	<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	?>
	</div>
</main><!-- #main -->

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>
