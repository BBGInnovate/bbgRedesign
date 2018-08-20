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

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

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
							echo '<h2><a href="' . $parent_link . '">' . $parent->post_title . '</a></h2>';
						?>
						<?php } ?>
					</header><!-- .entry-header -->
				</div>
			</div>



			<section class="outer-container bbg__impact-model__section">
				<div class="grid-container">
					<p class="lead-in">USAGM networks operate in a competitive, diverse, fragmented global media environment undergoing revolutionary change. There is more information, more channels of distribution and Limited Freedom of the Press.</p>

					<div class="grid-half">
						<img src="<?php echo get_template_directory_uri() ?>/img/impact/01_pie_free-press.png" alt="" class="bbg__impact-model__graphic large" >
						<h4 class="bbg__big-type">6,233,903,487</h4>
						<p class="bbg__infobox__tagline">people live in countries that have a press that is partly free or not free</p>
					</div>
					<div class="grid-half">
						<img src="<?php echo get_template_directory_uri() ?>/img/impact/02_pictograph_free-press.png" alt="" class="bbg__impact-model__graphic large" >
						<h4 class="bbg__big-type">6 out of 7 people</h4>
						<p class="bbg__infobox__tagline">live in countries without a free press</p>
						<br/>
						<h4>They face more: </h4>
						<ul>
							<li>CENSORSHIP</li>
							<li>PROPAGANDA</li>
							<li>DISINFORMATION</li>
							<li>THREATS TO JOURNALISTS</li>
							<li>RESTRICTIVE LAWS</li>
						</ul>
					</div>
				</div>
			</section>

			<section class="outer-container bbg__impact-model__section">
				<div class="grid-container">
					<h3>How do we measure impact?</h3>
					<p class="lead-in">We measure impact across networks, across media, in 61 languages and in more than 100 countries. Our shared mission provides the framework for a common standard to define and measure impact.</p>
				</div>
				<div class="side-content-container">
					<h4>5 Networks. </h4>
					<h4>1 Mission. </h4>
				</div>
				<div class="main-content-container">
					<h4>To inform, engage and connect people around the world in support of freedom and democracy.</h4>
				</div>
			</section>

			<section class="outer-container bbg__impact-model__section">
				<div class="grid-container">
					<h3>Impact</h3>
					<p class="lead-in">The guiding principle we use to drive our strategy, implementation and review cycle.</p>
					<img src="<?php echo get_template_directory_uri() ?>/img/impact/03_cycle_impact.png" alt="" class="usagm__impact-model__graphic full" >
				</div>
			</section>

			<section class="outer-container bbg__impact-model__section">
				<div class="grid-container" style="margin-bottom: 6rem;">
					<h3>Impact Pillars + Indicators</h3>
					<p class="lead-in">Below are illustrative samples of core and optional indicators. The full impact model offers USAGM networks 12 core and 28 optional indicators that they can use to fit with market conditions for each region. The indicators do not attempt to assess causality; they examine correlations.</p>

					<div class="nest-container">
						<div class="inner-container">
							<div class="grid-half">
								<h3>INFORM</h3>
								<h4>Reach Audiences</h4>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/04a_inform_reach.png" alt="" class="bbg__impact-model__graphic" >
									<h5>Weekly reach</h5>
								</div>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/04b_inform_visits.png" alt="" class="bbg__impact-model__graphic" >
									<h5>Weekly digital visits</h5>
								</div>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/04c_inform_targeted.png" alt="" class="bbg__impact-model__graphic" >
									<h5>Weekly reach of target segment*</h5>
								</div>
							</div>
							<div class="grid-half">
								<h4>Provide Value</h4>
								<div class="bbg__impact-model__subsection">
								
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/05a_inform_unique.png" alt="" class="bbg__impact-model__graphic" >
									<h5>Provide exceptional or unique information</h5>
								</div>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/05b_inform_credibility.png" alt="" class="bbg__impact-model__graphic" >
									<h5>Audience finds information or service trustworthy / credible.</h5>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section class="outer-container bbg__impact-model__section">
				<div class="grid-container">
					<h3>ENGAGE / CONNECT</h3>

					<div class="nest-container">
						<div class="inner-container">
							<div class="grid-third">
								<h4>Engage Audiences</h4>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/06a_engage_digital.png" alt="" class="bbg__impact-model__graphic" >
									<h5>Digital engagement</h5>
								</div>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/06b_engage_share.png" alt="" class="bbg__impact-model__graphic" >
									<h5 class="bbg__impact-model__optional">Shared something or talked with someone as a result of reporting*</h5>
								</div>
							</div>
							<div class="grid-third">
								<h4>Engage Media</h4>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/07a_engage_cocreate.png" alt="" class="bbg__impact-model__graphic" >
									<h5 class="bbg__impact-model__optional">Content co-creation with affiliates*</h5>
								</div>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/07b_engage_downloaded.png" alt="" class="bbg__impact-model__graphic" >
									<h5 class="bbg__impact-model__optional">Content downloaded by affiliates*</h5>
								</div>
							</div>
							<div class="grid-third">
								<h4>Create Loyalty</h4>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/08a_engage_loyalty.png" alt="" class="bbg__impact-model__graphic" >
									<h5>Audience is likely to continue to use</h5>
								</div>
								<div class="bbg__impact-model__subsection">
									<img src="<?php echo get_template_directory_uri() ?>/img/impact/08b_engage_appointment.png" alt="" class="bbg__impact-model__graphic" >
									<h5 class="bbg__impact-model__optional">Appointment listening or viewing*</h5>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<section class="outer-container bbg__impact-model__section">
				<div class="grid-container">
					<h3>BE INFLUENTIAL</h3>
					<h6>in support of freedom and democracy</h6>

					<div class="nest-container">
						<div class="inner-container">
							<div class="grid-third bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/09a_influence_people.png" alt="" class="bbg__impact-model__graphic" >
								<h4>People</h4>
								<p>Increased audience understanding of current events</p>
							</div>
							<div class="grid-third bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/09b_influence_media.png" alt="" class="bbg__impact-model__graphic" >
								<h4>Media</h4>
								<p class="bbg__impact-model__optional">Drive the news agenda/high profile news pickups*</p>
							</div>
							<div class="grid-third bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/09c_influence_government.png" alt="" class="bbg__impact-model__graphic" >
								<h4>Government</h4>
								<p class="bbg__impact-model__optional">Attention from government officials*</p>
							</div>
						</div>
					</div>
				</div>
				<div class="grid-container">
					<h6 style="text-align: right;"><span style="font-size:3rem; vertical-align: -40%;">*</span> Optional indicator</h6>
				</div>
			</section>

			<div class="outer-container">
				<div class="grid-container">
					<h4>“Everyone has the right to freedom of opinion and expression; this right includes freedom to hold opinions without interference, and impart information and ideas through any media regardless of frontiers.”</h4>
					<h5>— The Universal Declaration of Human Rights</h5>
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
					</footer><!-- .entry-footer -->
				</div>
			</div><!-- .usa-grid -->

			<div class="bbg-post-footer">
			<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?>
			</div>

		<?php endwhile; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>