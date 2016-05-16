<?php
/**
 * The custom home page for the Broadcasting Board of Governors.
 * It includes the mission, a portfolio of recent projects, recent blog posts and staff.
 *
 * template name: About
 *
 * @author Gigi Frias <gfrias@bbg.gov>
 * @package bbgRedesign
 */

$templateName = "about";

$bannerPosition = get_field( 'adjust_the_banner_image', '', true);
$videoUrl = get_field( 'featured_video_url', '', true );

get_header();

?>

<div id="main" class="site-main">

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
				$hideFeaturedImage = FALSE;
				if ($videoUrl != "") {
					echo featured_video($videoUrl);
					$hideFeaturedImage = TRUE;
				} elseif ( has_post_thumbnail() && ( $hideFeaturedImage != 1 ) ) {
					echo '<div class="usa-grid-full">';
					$featuredImageClass = "";
					$featuredImageCutline = "";
					$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
					if ($thumbnail_image && isset($thumbnail_image[0])) {
						$featuredImageCutline = $thumbnail_image[0]->post_excerpt;
					}

					$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 700,450 ), false, '' );

					echo '<div class="single-post-thumbnail clear bbg__article-header__thumbnail--large bbg__article-header__banner" style="background-image: url('.$src[0].'); background-position: '.$bannerPosition.'">';
					echo '</div>';
					echo '</div> <!-- usa-grid-full -->';
				}
			?><!-- .bbg__article-header__thumbnail -->

			<!-- Page header -->
			<div class="usa-grid">
				<!-- Parent title -->
				<?php if ($post->post_parent) {
					$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
					$parent_link = get_permalink($post->post_parent);
				?>
					<h5 class="entry-category bbg-label"><a href="<?php echo $parent_link; ?>"><?php echo $parent->post_title; ?></a></h5>
				<?php } ?>

				<!-- Page title -->
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>
			</div>

			<!-- Child pages content -->
			<section id="page-children" class="usa-section usa-grid ">
				<div class="usa-grid-full">
					<?php
						$childrenParams = array(
							'meta_key' => 'introduction',
							'meta_value' => '0',
							'parent' => $currentPageID,
							'post_type' => 'page',
							'post_status' => 'publish',
							'sort_column' => 'menu_order'
						);

						$children = get_pages($childrenParams);

						// Loop through the child pages
						foreach( $children as $child ) {
							$showInParent = $child->show_in_parent_page;
							$umbrella = $child->umbrella_category;
							$childPageID = $child->ID;

							// If the section is an umbrella category with subcategories beneath it
							if ($showInParent && $umbrella) {

								$excerpt = $child->post_excerpt;
								$excerpt = apply_filters( 'the_content', $excerpt );
								$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
							?>
							<article class="bbg__entity">
								<div>
									<!-- Child page title -->
									<h6 class="bbg-label">
										<a href="<?php echo get_page_link( $child->ID ); ?>">
											<?php echo $child->post_title; ?>
										</a>
									</h6>
									<!-- Child page excerpt -->
									<div class="usa-intro bbg__broadcasters__intro">
										<h3 class="usa-font-lead">
											<?php echo $excerpt; ?>
										</h3>
									</div>
								</div>
							</article>

							<!-- Grandchild pages -->
							<?php
								$grandchildrenParams = array(
									'sort_column' => 'menu_order',
									'child_of' => $childPageID,
									'parent' => $childPageID,
									'post_type' => 'page',
									'post_status' => 'publish'
								);

								$grandchildren = get_pages($grandchildrenParams);

								foreach( $grandchildren as $grandchild ) {
									$excerpt = $grandchild->post_excerpt;
									$excerpt = apply_filters( 'the_content', $excerpt );
									$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
									?>

									<article class="bbg-grid--1-2-2">
										<div class="">
											<!-- Child page title -->
											<h3>
												<a href="<?php echo get_page_link( $grandchild->ID ); ?>">
													<?php echo $grandchild->post_title; ?>
												</a>
											</h3>
											<!-- Child page excerpt -->
											<p class="">
												<?php
													echo $excerpt;
												?>
											</p>
										</div>
									</article>
								<?php
								}
							?>
						<?php
							// If the section is stand-alone without subcategories beneath it
							} else if ($showInParent) {
								$excerpt = $child->post_excerpt;
								$excerpt = apply_filters( 'the_content', $excerpt );
								$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
							?>
								<article class="bbg-grid--1-3-3">
									<div class="">
										<!-- Child page title -->
										<h6 class="bbg-label">
											<a href="<?php echo get_page_link( $child->ID ); ?>">
												<?php echo $child->post_title; ?>
											</a>
										</h6>
										<!-- Child page excerpt -->
										<p class="">
											<?php echo $excerpt; ?>
										</p>
									</div>
								</article>
						<?php
							}
						}
					?>
				</div>
			</section>

			<?php wp_reset_postdata(); ?>

		</main>
	</div><!-- #primary .content-area -->
	<div id="secondary" class="widget-area" role="complementary">
	</div><!-- #secondary .widget-area -->
</div><!-- #main .site-main -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
