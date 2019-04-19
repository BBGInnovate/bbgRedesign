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

<main id="main" role="main">

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
				<h3 class="section-subheader">How do we measure impact?</h3>
				<p>We measure impact across networks, across media, in <?php echo do_shortcode("[languages]"); ?> languages and in more than <?php echo do_shortcode("[countries]"); ?> countries. Our shared mission provides the framework for a common standard to define and measure impact.</p>
			</div>
			<div class="side-content-container">
				<h4 class="article-title">5 Networks. </h4>
				<h4 class="article-title">1 Mission. </h4>
			</div>
			<div class="main-content-container">
				<h4 class="article-title">To inform, engage and connect people around the world in support of freedom and democracy.</h4>
			</div>
		</section>

		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container">
				<h3 class="section-subheader">Impact</h3>
				<p>The guiding principle we use to drive our strategy, implementation and review cycle.</p>
				<img src="<?php echo get_template_directory_uri() ?>/img/impact/03_cycle_impact.png" alt="" class="usagm__impact-model__graphic full" >
			</div>
		</section>

		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container" style="margin-bottom: 6rem;">
				<h3 class="section-subheader">Impact Pillars + Indicators</h3>
				<p>Below are illustrative samples of core and optional indicators. The full impact model offers USAGM networks 12 core and 28 optional indicators that they can use to fit with market conditions for each region. The indicators do not attempt to assess causality; they examine correlations.</p>

				<div class="nest-container">
					<div class="inner-container">
						<div class="grid-half">
							<h4 class="article-title">Inform</h4>
							<h5 class="paragraph-header">Reach Audiences</h5>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/04a_inform_reach.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Weekly reach</p>
							</div>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/04b_inform_visits.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Weekly digital visits</p>
							</div>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/04c_inform_targeted.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Weekly reach of target segment*</p>
							</div>
						</div>
						<div class="grid-half">
							<h4 class="article-title">Provide Value</h4>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/05a_inform_unique.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Provide exceptional or unique information</p>
							</div>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/05b_inform_credibility.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Audience finds information or service trustworthy / credible.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container">
				<h4 class="article-title">Engage/Connect</h4>

				<div class="nest-container">
					<div class="inner-container">
						<div class="grid-third">
							<h5 class="paragraph-header">Engage Audiences</h5>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/06a_engage_digital.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Digital engagement</p>
							</div>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/06b_engage_share.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Shared something or talked with someone as a result of reporting*</p>
							</div>
						</div>
						<div class="grid-third">
							<h5 class="paragraph-header">Engage Media</h5>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/07a_engage_cocreate.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Content co-creation with affiliates*</p>
							</div>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/07b_engage_downloaded.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Content downloaded by affiliates*</p>
							</div>
						</div>
						<div class="grid-third">
							<h5 class="paragraph-header">Create Loyalty</h5>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/08a_engage_loyalty.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Audience is likely to continue to use</p>
							</div>
							<div class="bbg__impact-model__subsection">
								<img src="<?php echo get_template_directory_uri() ?>/img/impact/08b_engage_appointment.png" alt="" class="bbg__impact-model__graphic" >
								<p class="sans">Appointment listening or viewing*</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="outer-container bbg__impact-model__section">
			<div class="grid-container">
				<h4 class="article-title">BE INFLUENTIAL</h4>
				<p>in support of freedom and democracy</p>

				<div class="nest-container">
					<div class="inner-container">
						<div class="grid-third bbg__impact-model__subsection">
							<img src="<?php echo get_template_directory_uri() ?>/img/impact/09a_influence_people.png" alt="" class="bbg__impact-model__graphic" >
							<h5 class="paragraph-header">People</h5>
							<p class="sans">Increased audience understanding of current events</p>
						</div>
						<div class="grid-third bbg__impact-model__subsection">
							<img src="<?php echo get_template_directory_uri() ?>/img/impact/09b_influence_media.png" alt="" class="bbg__impact-model__graphic" >
							<h5 class="paragraph-header">Media</h5>
							<p class="sans">Drive the news agenda/high profile news pickups*</p>
						</div>
						<div class="grid-third bbg__impact-model__subsection">
							<img src="<?php echo get_template_directory_uri() ?>/img/impact/09c_influence_government.png" alt="" class="bbg__impact-model__graphic" >
							<h5 class="paragraph-header">Government</h5>
							<p class="sans">Attention from government officials*</p>
						</div>
					</div>
				</div>
			</div>
			<div class="grid-container">
				<p class="sans"  style="text-align: right;"><span style="font-size:3rem; vertical-align: -40%;">*</span> Optional indicator</p>
			</div>
		</section>

		<div class="outer-container">
			<div class="grid-container">
				<p class="article-title">“Everyone has the right to freedom of opinion and expression; this right includes freedom to hold opinions without interference, and impart information and ideas through any media regardless of frontiers.”</p>
				<p quote-name>— The Universal Declaration of Human Rights</p>
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

	<?php endwhile; ?>

</main><!-- #main -->

<?php get_footer(); ?>