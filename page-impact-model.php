<?php
/**
 * A custom page for the impact model infographic.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Impact model
 */

$bannerPosition = get_field( 'adjust_the_banner_image', '', true);
$videoUrl = get_field( 'featured_video_url', '', true );
$secondaryColumnContent = get_field( 'secondary_column_content', '', true );

get_header();
?>

<main id="main" role="main" class="bbg__impact-model">

	<?php while (have_posts()) : the_post(); ?>
		<?php
			$hideFeaturedImage = get_post_meta( $id, "hide_featured_image", true );
			if ( has_post_thumbnail() && ( $hideFeaturedImage != 1 ) ) {
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
		<div class="outer-container">
			<div class="grid-container">
				<header class="entry-header">
					<?php if($post->post_parent) {
						//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
						$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
						$parent_link = get_permalink($post->post_parent);
						echo '<h2 class="section-header"><a href="' . $parent_link . '">' . $parent->post_title . '</a></h2>';
					?>
					<?php } ?>
				</header><!-- .entry-header -->
			</div>
		</div>



		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container">
				<p class="lead-in">USAGM networks operate in a competitive, diverse, fragmented global media environment undergoing revolutionary change. There is more information, more channels of distribution and limited freedom of the press.</p>
			</div>
			<div class="grid-container bbg__impact-model__grid-6-3-container">
				<div class="bbg__impact-model__grid-6">
					<img src="<?php echo get_template_directory_uri() ?>/img/impact/Impact-Model_Our-Challenges_01.jpg" alt="" class="bbg__impact-model__graphic large" >
				</div>
				<div class="bbg__impact-model__grid-3">
					<img src="<?php echo get_template_directory_uri() ?>/img/impact/Impact-Model_Our-Challenges_02.jpg" alt="" class="bbg__impact-model__graphic large" >
				</div>
			</div>
			<div class="grid-container">
			<hr style="background-color: #eee; width: 90%; opacity: 0.8; height: 2px; border: none;" />
			</div>
			<div class="grid-container">
				<div class="grid-four">
					<img src="<?php echo get_template_directory_uri() ?>/img/impact/Impact-Model_Our-Challenges_03.jpg" alt="" class="bbg__impact-model__graphic large" >
				</div>
				<div class="grid-four">
					<img src="<?php echo get_template_directory_uri() ?>/img/impact/Impact-Model_Our-Challenges_04.jpg" alt="" class="bbg__impact-model__graphic large" >
				</div>
				<div class="grid-four">
					<img src="<?php echo get_template_directory_uri() ?>/img/impact/Impact-Model_Our-Challenges_05.jpg" alt="" class="bbg__impact-model__graphic large" >
				</div>
				<div class="grid-four">
					<img src="<?php echo get_template_directory_uri() ?>/img/impact/Impact-Model_Our-Challenges_06.jpg" alt="" class="bbg__impact-model__graphic large" >
				</div>
			</div>
		</section>

		<?php
		$impactModelFactSheetId = get_field('impact_model_fact_sheet');
		$impactModelFactSheetUrl = wp_get_attachment_url($impactModelFactSheetId);

		$impactModelFactSheetAnchorTag = '<a href="' . esc_url($impactModelFactSheetUrl) . '" target="_blank">USAGM Impact Model</a>';
		?>

		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container">
				<h3 class="section-subheader">How do we measure impact?</h3>
				<p>For any media organization, understanding the audience is critical to developing programming that is appealing and impactful. USAGM has a unique challenge in that our content appears in <?php echo do_shortcode("[languages]"); ?> and our audiences are located in more than <?php echo do_shortcode("[countries]"); ?>.</p>
				<p>The <?php echo $impactModelFactSheetAnchorTag ?> serves as a framework for collecting data on our audiences and measuring the effectiveness of our programming. It comprises over 35 indicators organized around the agency’s mission: to inform, engage, and connect people around the world in support of freedom and democracy. The model looks beyond sheer audience size to assess the change that USAGM’s network and entity programming has made in the lives of audience members, in the local media sector, and among governments.</p>
				<p>To assess programming performance, the Impact Model draws from a diverse set of evidence complementing quantitative measures such as survey data and digital analytics with structured anecdotal data.</p>
			</div>
		</section>

		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container">
				<h3 class="section-subheader">Sources of evidence that inform the Impact Model</h3>
			</div>
			<div class="grid-container">
				<div class="grid-half bbg__impact-model__sources">
					<div class="bbg__impact-model__sources-img-container">
						<img src="<?php echo get_template_directory_uri() ?>/img/impact/impact-model-sources-1.png" alt="" class="bbg__impact-model__graphic large" >
					</div>
					<div class="bbg__impact-model__sources-text-container">
						<h4 class="bbg__impact-model__sources-header">Survey Data</h4>
						<ul>
							<li>USAGM Surveys</li>
							<li>Omnibus Surveys</li>
						</ul>
					</div>
				</div>
				<div class="grid-half bbg__impact-model__sources">
					<div class="bbg__impact-model__sources-img-container">
						<img src="<?php echo get_template_directory_uri() ?>/img/impact/impact-model-sources-2.png" alt="" class="bbg__impact-model__graphic large" >
					</div>
					<div class="bbg__impact-model__sources-text-container">
						<h4 class="bbg__impact-model__sources-header">Survey Data</h4>
						<ul>
							<li>Website and App Analytics</li>
							<li>Social Media Analytics (aggregated and analyzed in the USAGM Data Portal)</li>
						</ul>
					</div>
				</div>
				<div class="grid-half bbg__impact-model__sources">
					<div class="bbg__impact-model__sources-img-container">
						<img src="<?php echo get_template_directory_uri() ?>/img/impact/impact-model-sources-3.png" alt="" class="bbg__impact-model__graphic large" >
					</div>
					<div class="bbg__impact-model__sources-text-container">
						<h4 class="bbg__impact-model__sources-header">Survey Data</h4>
						<ul>
							<li>Evidence on guest appearances, impact stories, news pick-ups, co-productions with media partners, government reactions, etc.</li>
						</ul>
					</div>
				</div>
				<div class="grid-half bbg__impact-model__sources">
					<div class="bbg__impact-model__sources-img-container">
						<img src="<?php echo get_template_directory_uri() ?>/img/impact/impact-model-sources-4.png" alt="" class="bbg__impact-model__graphic large" >
					</div>
					<div class="bbg__impact-model__sources-text-container">
						<h4 class="bbg__impact-model__sources-header">Survey Data</h4>
						<ul>
							<li>Databases (CRM, etc.)</li>
							<li>Internal Records</li>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container" style="margin-bottom: 6rem;">
				<h3 class="section-subheader">Impact Pillars and Select Indicators</h3>
				<p>Below is an illustrative sample of indicators in the Impact Model. The full model offers USAGM networks 36 indicators that they can use to fit with market conditions for each region.</p>

				<img src="<?php echo get_template_directory_uri() ?>/img/impact/Impact-Pillars-and-Sector-Indicators.png" alt="" class="bbg__impact-model__graphic" style="width: 100%;" >
			</div>
		</section>

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
				</footer><!-- .entry-footer -->
			</div>
		</div><!-- .usa-grid -->

	<?php endwhile; ?>

</main><!-- #main -->

<?php get_footer(); ?>