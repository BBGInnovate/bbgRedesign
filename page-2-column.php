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
include 'inc/shared_sidebar.php';

$addFeaturedGallery = get_post_meta( get_the_ID(), 'featured_gallery_add', true );
// 
$headline = get_field('headline', '', true);
$listsInclude = get_field('sidebar_dropdown_include', '', true);
// 
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
				$dl_link  = '<li>';
				$dl_link .= 	'<a href="' . $foia_url . $file . '">' . $file . '</a>';
				$dl_link .= '</li>';
				echo $dl_link;
			}
		}
		$counter++;
	}
	
	echo '</ul><p style="text-align: right;"><a href="https://www.usagm.gov/foia/">Visit the main FOIA page</a></p>';
}

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_ID();
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

<main id="main" class="bbg__2-column" role="main">

	<div class="outer-container">
		<div class="custom-grid-container">
			<div class="inner-container">
				<div class="main-content-container">
					<?php
						echo '<h2 class="section-header">' . get_the_title() . '</h2>';

						echo '<div class="page-content">';
						echo 	'<p>' . $page_content . '</p>';
						echo '</div>';

						if (is_page('foia-reports')) {
							display_foia_reports();
						}

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

							if ($custom_query->have_posts()) {
								echo '<h6 class="bbg__label"><a href="' . $categoryUrl . '">' . $relatedCategory->name . '</a></h6>';
								echo '<div class="usa-grid-full">';
									while ($custom_query->have_posts())  {
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
							echo '<div class="sidebar-section">';
							if ($secondaryColumnLabel != "") {
								echo '<h2 class="sidebar-section-header">' . $secondaryColumnLabel . '</h2>';
							}
							echo $secondaryColumnContent;
							echo '</div>';
						}
						if ($includeSidebar) {
							echo '<div class="sidebar-section">';
							echo 	$sidebar;
							echo '</div>';
						}
						if ($listsInclude) {
							echo '<div class="sidebar-section">';
							echo 	$sidebarDownloads;
							echo '</div>';
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
