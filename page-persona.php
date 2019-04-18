<?php
/**
 * The template for displaying 2 column pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Persona (Press Freedom Day)
 */

$bannerPosition = get_field( 'adjust_the_banner_image', '', true);
$bannerPositionCSS = get_field( 'adjust_the_banner_image_css', '', true);
$bannerAdjustStr="";
if ($bannerPositionCSS) {
	$bannerAdjustStr = $bannerPositionCSS;
} elseif ($bannerPosition) {
	$bannerAdjustStr = $bannerPosition;
}

$videoUrl = get_field( 'featured_video_url', '', true );
$addFeaturedGallery = get_post_meta( get_the_ID(), 'featured_gallery_add', true );
$secondaryColumnLabel = get_field( 'secondary_column_label', '', true );
$secondaryColumnContent = get_field( 'secondary_column_content', '', true );

$headline = get_field( 'headline', '', true );
$headlineStr = "";

$journalistName = get_post_meta( get_the_ID(), 'persona_journalist_name', true );
$avatar = get_field('persona_avatar');
$lead = get_post_meta( get_the_ID(), 'persona_lead', true );
$investigation = get_post_meta( get_the_ID(), 'persona_investigation', true );
$confrontation = get_post_meta( get_the_ID(), 'persona_confrontation', true );
$decision = get_post_meta( get_the_ID(), 'persona_decision', true );
$twitterCode = get_post_meta( get_the_ID(), 'persona_twitter', true ); 
$decidedToPublish = get_post_meta( get_the_ID(), 'persona_decided_to_publish', true );
$decidedNotToPublish = get_post_meta( get_the_ID(), 'persona_decided_not_to_publish', true );
$listsInclude = get_field( 'sidebar_dropdown_include', '', true);

include get_template_directory() . "/inc/shared_sidebar.php";

get_header(); ?>

<main id="main" class="bbg__2-column" role="main">

	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class("bbg__article"); ?>>
			<div class="outer-container">
				<div class="grid-container">
					<header class="page-header">
						<?php if( $post->post_parent ) {
							//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
							$parent = $wpdb->get_row( "SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent" );
							$parent_link = get_permalink( $post->post_parent );
							?>
							<h2 class="section-header"><a href="<?php echo $parent_link; ?>"><?php echo $parent->post_title; ?></a></h2>
						<?php } else { ?>
							<h2 class="section-header"><?php the_title(); ?></h2>
						<?php } ?>
					</header><!-- .page-header -->
				</div>
			</div>

			<?php 
				if ($addFeaturedGallery) {
					echo '<div class="usa-grid-full">';
					echo 	'<div class="usa-grid-full bbg__article-featured__gallery">';
					$featuredGalleryID = get_post_meta( get_the_ID(), 'featured_gallery_id', true );
					putUniteGallery($featuredGalleryID);
					echo 	'</div>';
					echo '</div>';
				}

				$hideFeaturedImage = FALSE;
				if ( $videoUrl != "" ) {
					echo featured_video($videoUrl);
					$hideFeaturedImage = TRUE;
				} elseif ( has_post_thumbnail() && ( $hideFeaturedImage != 1 ) ) {
					echo '<div class="usa-grid-full">';
						$featuredImageClass = "";
						$featuredImageCutline = "";
						$thumbnail_image = get_posts( array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment') );
						if ( $thumbnail_image && isset($thumbnail_image[0]) ) {
							$featuredImageCutline = $thumbnail_image[0]->post_excerpt;
						}

						$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 700,450 ), false, '' );

						echo '<div class="single-post-thumbnail clear bbg__article-header__thumbnail--large bbg__article-header__banner" style="background-image: url(' . $src[0] . '); background-position: ' . $bannerAdjustStr . '">';
						echo '</div>';
					echo '</div> <!-- usa-grid-full -->';
				}
			?><!-- .bbg__article-header__thumbnail -->

			<div class="outer-container">
				<div class="custom-grid-container">
					<div class="inner-container">
						<div class="main-content-container">
							<?php
								if ( $post->post_parent ) {
									//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
									$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
									$parent_link = get_permalink($post->post_parent);
									the_title( '<h3 class="section-subheader">', '</h3>' );
								} else {
									$headlineStr = "<h3 class='bbg__entry__secondary-title'>" . $headline . "</h3>";
								}

								echo $headlineStr;

								$lead = apply_filters('the_content',$lead);
								$investigation = apply_filters('the_content',$investigation);
								$confrontation = apply_filters('the_content',$confrontation);
								$decision = apply_filters('the_content',$decision);

								echo '<h4>The Lead</h4>';
								echo '<img width="200" style="float:right;" src="' . $avatar["url"] . '" alt="Avatar image"> ';
								echo $lead;
								echo "<h4>The Investigation</h4>";
								echo $investigation;
								echo "<h4>The Confrontation</h4>";
								echo $confrontation;
								echo "<h4>The Decision</h4>";
								echo $decision;

								the_content();

								//Add blog posts below the main content
								$relatedCategory=get_field('related_category_posts', $id);

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
						</div>
						<div class="side-content-container">
							<?php 
								if ($twitterCode) {
									echo $twitterCode;
								}
							?>

							<h5 class="bbg__label small bbg__sidebar__download__label">What happened</h5>
							<div class="usa-accordion bbg__committee-list">
								<ul class="usa-unstyled-list">
									<li>
										<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-1">Decided to publish</button>
										<div id="collapsible-faq-1" aria-hidden="true" class="usa-accordion-content">
											<p><?php echo $decidedToPublish; ?></p>
										</div>
									</li>
									<li>
										<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-2">Decided not to publish</button>
										<div id="collapsible-faq-2" aria-hidden="true" class="usa-accordion-content">
											<p><?php echo $decidedNotToPublish; ?></p>
										</div>
									</li>
								</ul>
							</div>

							<?php
								if ( $secondaryColumnContent != "" ) {
									if ( $secondaryColumnLabel != "" ) {
										echo '<h5 class="bbg__label small">' . $secondaryColumnLabel . '</h5>';
									}
									echo $secondaryColumnContent;
								}
								if ( $includeSidebar ) {
									echo $sidebar;
								}
								if ( $listsInclude ) {
									echo $sidebarDownloads;
								}
							?>
						</div>
					</div>
				</div>
			</div>

			<div class="outer-container">
				<div class="grid-container">
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
					</footer>
				</div>
			</div>

			<div class="bbg-post-footer">
			<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?>
			</div>

		<?php endwhile; // End of the loop. ?>
</main><!-- #main -->

<?php get_footer(); ?>