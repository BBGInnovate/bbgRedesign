<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: 2-column
 */

require 'inc/custom_field_data_retriever.php';
require 'inc/bbg-functions-assemble.php';

$secondaryColumnLabel = get_field( 'secondary_column_label', '', true );
$secondaryColumnContent = get_field('secondary_column_content', '', true);

$headline = get_field( 'headline', '', true );
$headlineStr = "";

$listsInclude = get_field( 'sidebar_dropdown_include', '', true);

include get_template_directory() . "/inc/shared_sidebar.php";

function display_foia_reports() {
	// $foia_url = WP_CONTENT_URL . '/uploads/foia-reports/'; // LOCAL
	$foia_url = WP_CONTENT_URL . '/media/foia-reports/'; // LIVE
	// $foia_path = 'wp-content/uploads/foia-reports'; // LOCAL
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

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$ogDescription = get_the_excerpt();
	endwhile;
endif;
wp_reset_postdata();
wp_reset_query();

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main bbg__2-column" role="main">
			<?php display_feature_media_type(); ?>
			<div class="usa-grid-full">
				<?php while ( have_posts() ) : the_post();
					//$videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );
				?>
						<div class="usa-grid">

							<header class="entry-header">
								<!-- .bbg__label -->
								<?php 
									if ( $post->post_parent ) {
										// GET RID OF THIS SQL CALL
										//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
										$parent = $wpdb->get_row( "SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent" );
										$parent_link = get_permalink($post->post_parent);
										the_title( '<h1 class="entry-title">', '</h1>' );
										echo "KR - Fix SQL Call";
										// THOSE VARS ARENT EVEN USED ANYWHERE ELSE
									}
									else {
										$headlineStr = "<h2>" . $headline . "</h2>";
									}
								?>
							</header><!-- .entry-header -->

							<div class="entry-content bbg__article-content large <?php echo $featuredImageClass; ?>">
								<div class="bbg__profile__content">
									<?php
										echo $headlineStr;
										the_content();
										if (is_page('foia-reports')) {
											display_foia_reports();
										}
									?>
								</div>

								<?php
									//Add blog posts below the main content
									$relatedCategory = get_field('related_category_posts', $id);

									if ( $relatedCategory != "" ) {
										$qParams2 = array(
											'post_type' => array('post'),
											'posts_per_page' => 2,
											'cat' => $relatedCategory->term_id,
											'orderby' => 'date',
											'order' => 'DESC'
										);
										$categoryUrl = get_category_link($relatedCategory->term_id);
										$custom_query = new WP_Query( $qParams2 );

										if ( $custom_query -> have_posts() ) {
											echo '<h6 class="bbg__label"><a href="' . $categoryUrl . '">' . $relatedCategory->name . '</a></h6>';
											echo '<div class="usa-grid-full">';
												while ( $custom_query -> have_posts() )  {
													$custom_query->the_post();
													get_template_part( 'template-parts/content-portfolio', get_post_format() );
												}
											echo '</div>';
										}
										wp_reset_postdata();
									}
								?>
							</div><!-- .entry-content -->

							<div class="bbg__article-sidebar large">
								<?php
									if ($secondaryColumnContent != "") {
										if ($secondaryColumnLabel != "") {
											echo '<h6>' . $secondaryColumnLabel . '</h6>';
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
							</div><!-- .bbg__article-sidebar -->
						</div>

						<div class="usa-grid">
							<footer class="entry-footer bbg-post-footer 1234">
								<?php
									edit_post_link(
										sprintf(
											/* translators: %s: Name of current post */
											esc_html__( 'Edit %s', 'bbginnovate' ),
											the_title( '<span class="screen-reader-text">"', '"</span>', false )
										),
										'<span class="edit-link">',
										'</span>'
									);
								?>
							</footer><!-- .entry-footer -->
						</div><!-- .usa-grid -->

					</article><!-- #post-## -->

					<div class="bbg-post-footer">
					<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>
					</div>

				<?php endwhile; // End of the loop. ?>
			</div><!-- .usa-grid-full -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>
