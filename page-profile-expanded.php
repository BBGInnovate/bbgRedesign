<?php
/**
 * The template for displaying expaneded profile posts (e.g the CEO).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bbgRedesign
  template name: Profile

 */

require 'inc/bbg-functions-assemble.php';

/* we go through the loop once and reset it in order to get some vars for our og tags */
if ( have_posts() ) {
	the_post();

	$metaAuthor = get_the_author();
	$metaKeywords = strip_tags( get_the_tag_list( '',', ','' ) );

	$ogTitle = get_the_title();
	$ogDescription = get_the_excerpt();
	$id = get_the_ID();

	/**** CREATE OG:IMAGE *****/
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'Full' );
	$ogImage = $thumb['0'];

	$socialImageID = get_post_meta( $id, 'social_image',true );
	if ( $socialImageID ) {
		$socialImage = wp_get_attachment_image_src( $socialImageID , 'Full' );
		$ogImage = $socialImage[0];
	}

	/**** Get profile fields *****/
	$isActing = get_post_meta( $id, 'acting', true );
	if ( $isActing ) {
		$occupation = "Acting ";
	}
	$occupation .= get_post_meta( $id, 'occupation', true );
	$email = get_post_meta( $id, 'email', true );
	$phone = get_post_meta( $id, 'phone', true );
	$twitterProfileHandle = get_post_meta( $id, 'twitter_handle', true );
	$relatedLinksTag = get_post_meta( $id, 'related_links_tag', true );

	/**** CREATE $formerCSS - applies black and white to retired board members ***/
	$active = get_post_meta( $id, 'active', true );
	$formerCSS = "";
	if ( !$active ){
		$occupation = "(Former) " . $occupation;
		$formerCSS = " bbg__former-member";
	}

	/*** Get the profile photo mugshot ***/
	$profilePhotoID = get_post_meta( $id, 'profile_photo', true );
	$profilePhoto = "";
	if ( $profilePhotoID ) {
		$profilePhoto = wp_get_attachment_image_src( $profilePhotoID , 'mugshot' );
		$profilePhoto = $profilePhoto[0];
	}

	/*** Generate the code for the latest tweets that we use in the sidebar ***/
	$latestTweetsStr = "";
	if ( $twitterProfileHandle != "" ) {
		$showLatestTweets = get_post_meta( $id, 'show_latest_tweets', true );
		if ( $showLatestTweets ) {
			$latestTweetsStr = '<a data-chrome="noheader nofooter noborders transparent noscrollbar" data-tweet-limit="2" class="twitter-timeline" href="https://twitter.com/' . $twitterProfileHandle . '" data-screen-name="' . $twitterProfileHandle . '" >Tweets by @' . $twitterProfileHandle . '</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		}
	}

	$resolution = get_field( 'board_resolution_of_honor' );
	rewind_posts();
}

get_header(); ?>
	<div id="primary" class="content-area bbg__profile">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post();

			//Default adds a space above header if there's no image set
			$featuredImageClass = " bbg__article--no-featured-image";

			//the title/headline field, followed by the URL and the author's twitter handle
			$twitterText = "";
			$twitterText .= "Profile of " . html_entity_decode( get_the_title() );
			$twitterText .= " by @bbggov " . get_permalink();
			$twitterURL = "//twitter.com/intent/tweet?text=" . rawurlencode( $twitterText );
			$fbUrl = "//www.facebook.com/sharer/sharer.php?u=" . urlencode( get_permalink() );

			include get_template_directory() . "/inc/shared_sidebar.php";

			?>

<!-- 				<div class="outer-container">
					<div class="grid-container">
					<?php if( $post -> post_parent ) {
						//borrowed from: https://wordpress.org/support/topic/link-to-parent-page
						$parent = $wpdb -> get_row( "SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent" );
						$parent_link = get_permalink( $post -> post_parent );
						?>
						<h5 class="bbg__label--mobile large"><a href="<?php echo $parent_link; ?>"><?php echo $parent -> post_title; ?></a></h5>
					<?php } ?>
					</div>
				</div> -->

				<?php
					$featured_media_result = get_feature_media_data();
					if ($featured_media_result != "") {
						echo $featured_media_result;
					}
				?>

		<div class="outer-container profile-header">
			<div class="inner-container">
				<div class="image-container">
					<img src="<?php echo $profilePhoto; ?>">
				</div>

				<div class="content-column">
					<div class="content">
						<?php the_title( '<h1 class="entry-title bbg__article-header__title">', '</h1>' ); ?>
						<h5 class="entry-category bbg__profile-tagline">
							<?php echo $occupation; ?>
						</h5>
					</div>
				</div>
			</div>
		</div>

<div class="outer-container">
	<!-- BEGIN MAIN CONTENT -->
	<div class="main-content-container">
		<div class="bbg__profile__content__section">
			<?php the_content(); ?>
			<p class="bbg-tagline" style="text-align: right;">Last modified: <?php the_modified_date('F d, Y'); ?></p>
		</div>
		<!-- section for content below biography -->
		<?php
			$contentBelowBio = get_field( 'profile_content_below_biography', $id );
			if ( $contentBelowBio != "" ) {
				echo "<div class='bbg__profile__content__section' >$contentBelowBio</div>";
			}
		?>

		<!-- section for CEO -->
		<?php
			$ceo = get_post_meta( $id, 'ceo', true );
			if ( $ceo ) {

				$tax_query = array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'john-lansing' ),
						'operator' => 'IN'
					),
					array(
						'taxonomy' => 'category',
						'field' => 'slug',
						'terms' => array( 'appearance','bbg-in-the-news' ),
						'operator' => 'IN'
					)
				);

				$qParams2 = array(
					'post_type' => array( 'post' ),
					'posts_per_page' => 2,
					'tax_query' => $tax_query,
					'orderby' => 'date',
					'order' => 'DESC'
				);

				$categoryUrl = "https://www.bbg.gov/tag/john-lansing/?category_name=appearance,bbg-in-the-news";
				$categoryLabel = "News & Appearances";
				$custom_query = new WP_Query( $qParams2 );
				if ( $custom_query -> have_posts() ) {
					echo '<div class="bbg__profile__content__section" >';
						echo '<h6 class="bbg__label"><a href="' . $categoryUrl . '">' . $categoryLabel . '</a></h6>';
						echo '<div class="usa-grid-full">';
						while ( $custom_query -> have_posts() )  {
							$custom_query -> the_post();
							get_template_part( 'template-parts/content-portfolio', get_post_format() );
						}
						echo '</div>';
					echo '</div>';
				}
				wp_reset_postdata();
			}
		?>

		<!-- section for related blog posts. Previously was used for "from the ceo". Currently not used on any profiles. -->
		<?php
			//Add blog posts below the main content
			$relatedCategory = get_field( 'profile_related_category', $id );

			if ( $relatedCategory != "" ) {
				$qParams2 = array(
					'post_type' => array( 'post' ),
					'posts_per_page' => 2,
					'cat' => $relatedCategory -> term_id,
					'orderby' => 'date',
					'order' => 'DESC'
				);
				$categoryUrl = get_category_link( $relatedCategory -> term_id );
				$custom_query = new WP_Query( $qParams2 );
				if ( $custom_query -> have_posts() ) {
					echo '<div class="bbg__profile__content__section" >';
						echo '<h6 class="bbg__label"><a href="' . $categoryUrl . '">' . $relatedCategory -> name . '</a></h6>';
						echo '<div class="usa-grid-full">';
						while ( $custom_query -> have_posts() )  {
							$custom_query -> the_post();
							get_template_part( 'template-parts/content-portfolio', get_post_format() );
						}
						echo '</div>';
					echo '</div>';
				}
				wp_reset_postdata();
			}
		?>
	</div>
	<!-- BEGIN SIDEBAR -->
	<div class="sidebar-content-container">
		<div class="social-share">
			<h6 class="bbg__sidebar-label bbg__contact-label">Share </h6>
			<a href="<?php echo $fbUrl; ?>">
				<span class="bbg__article-share__icon facebook"></span>
			</a>
			<a href="<?php echo $twitterURL; ?>">
				<span class="bbg__article-share__icon twitter"></span>
			</a>
		</div>

		<?php
			// EMAIL ex. Nasserie Carew
			// CURRENTLY NO PHONE NUMBERS USED ON PROFILES
			if ( $email != "" || $phone != "" ) {
				echo '<h6 class="bbg__sidebar-label bbg__contact-label">Contact </h6>';
				echo '<ul class="bbg__article-share">';
				if ( $email != "" ) {
					echo '<li class="bbg__article-share__link email"><a href="mailto:' . $email . '" title="Email ' . get_the_title() . '"><span class="bbg__article-share__icon email"></span><span class="bbg__article-share__text">' . $email . '</span></a></li>';
				}
				if ( $phone != "" ){
					echo '<li class="bbg__article-share__link phone"><span class="bbg__article-share__icon phone"></span><span class="bbg__article-share__text">' . $phone . '</span></li>';
				}
				echo '</ul>';
			}

			// IF TWITTER HANDLE, SHOW IT, SHOW RECENT TWEETS IF ENABLED ON PAGE
			// LATEST TWEETS ex. CEO John Lansing, Amanda Bennet
			if ($twitterProfileHandle != "") {
				echo '<h6 class="bbg__sidebar-label bbg__contact-label">Follow on Twitter</h6>';
				echo '<li class="bbg__article-share__link twitter"><a href="https://twitter.com/' . $twitterProfileHandle . '" title="Follow ' . get_the_title() . ' on Twitter"><i class="fa fa-twitter"></i> <span class="bbg__article-share__text">@' . $twitterProfileHandle . '</span></a></li>';
				echo $latestTweetsStr;	//see top of this page template for definition of this string
			}

			// STANDARD SIDEBAR CONTENT ex. Rex Tillerson
			echo "<!-- Sidebar content -->";
			if ( $includeSidebar ) {
				echo $sidebar;
			}

			// RESOLUTION OF HONOR ex. Victor Ashe
			if ( $resolution ) {
				echo '<h3 class="bbg__sidebar-label">Resolution of Honor</h3>';
				echo "<p><a href='" . $resolution['url'] ."'>" . $resolution['title'] .'</a></p>';
			}

			if ( $relatedLinksTag != "" ) {
				$qParams2 = array(
					'post_type' => array( 'post' ),
					'posts_per_page' => 4,
					'tag' => $relatedLinksTag,
					'orderby' => 'date',
					'order' => 'DESC'
				);
				$custom_query = new WP_Query( $qParams2 );
				if ( $custom_query -> have_posts() ) {
					echo '<h3 class="bbg__sidebar-label">Related posts  <!--(tag "$relatedLinksTag")--></h3>';
					echo '<ul class="bbg__profile__related-link__list">';
					while ( $custom_query -> have_posts() )  {
						$custom_query -> the_post();
						$link = get_the_permalink();
						$title = get_the_title();
						echo '<li class="bbg__profile__related-link"><a href="' . $link . '">' . $title . '</a></li>';
					}
					echo "</ul>";
					$viewAllLink = get_term_link( $relatedLinksTag, 'post_tag' );
					echo "<a class='bbg__read-more' href='" . $viewAllLink . "'>VIEW ALL »</a>";
				}
				wp_reset_postdata();
			}
		?>
	</div>
</div>

		<?php endwhile; // End of the loop. ?>
	</main><!-- #main -->
</div><!-- #primary -->

	<section class="usa-grid">
		<?php get_sidebar(); ?>
	</section>
<?php get_footer(); ?>