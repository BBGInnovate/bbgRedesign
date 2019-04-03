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
if (have_posts()) {
	the_post();

	$id = get_the_ID();
	$page_content = do_shortcode(get_the_content());
	$page_content = apply_filters('the_content', $page_content);

	$metaAuthor = get_the_author();
	$metaKeywords = strip_tags( get_the_tag_list('',', ',''));

	$ogTitle = get_the_title();
	$ogDescription = get_the_excerpt();

	/**** CREATE OG:IMAGE *****/
	$thumb = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'Full');
	$ogImage = $thumb['0'];

	$socialImageID = get_post_meta($id, 'social_image', true);
	if ($socialImageID) {
		$socialImage = wp_get_attachment_image_src($socialImageID , 'Full');
		$ogImage = $socialImage[0];
	}

	/**** Get profile fields *****/
	$isActing = get_post_meta($id, 'acting', true);
	if ($isActing) {
		$occupation = "Acting ";
	}
	$occupation = get_post_meta($id, 'occupation', true);
	$email = get_post_meta($id, 'email', true);
	$phone = get_post_meta($id, 'phone', true);
	$twitterProfileHandle = get_post_meta($id, 'twitter_handle', true);
	$relatedLinksTag = get_post_meta($id, 'related_links_tag', true);

	/**** CREATE $formerCSS - applies black and white to retired board members ***/
	$active = get_post_meta( $id, 'active', true );
	$formerCSS = "";
	if (!$active) {
		$occupation = "(Former) " . $occupation;
		$formerCSS = " bbg__former-member";
	}

	/*** Get the profile photo mugshot ***/
	$profilePhotoID = get_post_meta($id, 'profile_photo', true);
	$profilePhoto = "";
	if ( $profilePhotoID ) {
		$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
		$profilePhoto = $profilePhoto[0];
	}

	/*** Generate the code for the latest tweets that we use in the sidebar ***/
	$latestTweetsStr = "";
	if ($twitterProfileHandle != "") {
		$showLatestTweets = get_post_meta($id, 'show_latest_tweets', true);
		if ($showLatestTweets) {
			$latestTweetsStr = '<a data-chrome="noheader nofooter noborders transparent noscrollbar" data-tweet-limit="2" class="twitter-timeline" href="https://twitter.com/' . $twitterProfileHandle . '" data-screen-name="' . $twitterProfileHandle . '" >Tweets by @' . $twitterProfileHandle . '</a><script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		}
	}

	$resolution = get_field('board_resolution_of_honor');
	rewind_posts();
}

get_header();
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main id="main" role="main">
	<?php
		//Default adds a space above header if there's no image set
		$featuredImageClass = " bbg__article--no-featured-image";

		//the title/headline field, followed by the URL and the author's twitter handle
		$twitterText = "";
		$twitterText .= "Profile of " . html_entity_decode(get_the_title());
		$twitterText .= " by @bbggov " . get_permalink();
		$twitterURL = "//twitter.com/intent/tweet?text=" . rawurlencode($twitterText);
		$fbUrl = "//www.facebook.com/sharer/sharer.php?u=" . urlencode(get_permalink());

		include get_template_directory() . "/inc/shared_sidebar.php";
	?>

	<div class="outer-container">
		<div class="main-content-container">
			<div class="nest-container">
				<div class="inner-container">
					<div class="icon-side-content-container">
						<img src="<?php echo $profilePhoto; ?>" alt="Profile photo">
					</div>
					<div class="icon-main-content-container">
						<?php
							$profile_head  = '<h2 class="profile-name">';
							$profile_head .= 	get_the_title();
							$profile_head .= '</h2>';
							echo $profile_head;

							$profile_occupation  = '<h6 class="profile-occupation">';
							$profile_occupation .= 	$occupation;
							$profile_occupation .= '</h6>';
							echo $profile_occupation;

							$main_content  = '<div class="page-content">';
							$main_content .= 	$page_content;
							$main_content .= '</div>';
							echo $main_content;

							$modification_date  = '<p class="bbg-tagline" style="text-align: right;">';
							$modification_date .= 	'Last modified: ' . get_the_modified_date('F d, Y');
							$modification_date .= '</p>';

							echo $modification_date;
						?>


						<!-- section for content below biography -->
						<?php
							$content_below_bio = get_field('profile_content_below_biography', $id);
							if ($content_below_bio != "") {
								echo '<div class="bbg__profile__content__section">';
								echo 	$content_below_bio;
								echo '</div>';
							}
						?>

						<!-- section for CEO -->
						<?php
							$ceo = get_post_meta($id, 'ceo', true);
							if  ($ceo) {
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
										'terms' => array('appearance','bbg-in-the-news'),
										'operator' => 'IN'
									)
								);
								$qParams2 = array(
									'post_type' => array('post'),
									'posts_per_page' => 2,
									'tax_query' => $tax_query,
									'orderby' => 'date',
									'order' => 'DESC'
								);

								$categoryUrl = "https://www.bbg.gov/tag/john-lansing/?category_name=appearance,bbg-in-the-news";
								$categoryLabel = "News & Appearances";
								$custom_query = new WP_Query($qParams2);
								if ($custom_query -> have_posts()) {
									echo '<div class="">';
									echo 	'<h3><a href="' . $categoryUrl . '">' . $categoryLabel . '</a></h3>';
									while ($custom_query -> have_posts())  {
										$custom_query -> the_post();
										get_template_part('template-parts/content-portfolio', get_post_format());
									}
									echo '</div>';
								}
								wp_reset_postdata();
							}
						?>

						<!-- section for related blog posts. Previously was used for "from the ceo". Currently not used on any profiles. -->
						<?php
							//Add blog posts below the main content
							$relatedCategory = get_field('profile_related_category', $id);

							if ($relatedCategory != "") {
								$qParams2 = array(
									'post_type' => array('post'),
									'posts_per_page' => 2,
									'cat' => $relatedCategory -> term_id,
									'orderby' => 'date',
									'order' => 'DESC'
								);
								$categoryUrl = get_category_link($relatedCategory -> term_id);
								$custom_query = new WP_Query($qParams2);
								if ($custom_query -> have_posts()) {
									echo '<div>';
									echo 	'<h3><a href="' . $categoryUrl . '">' . $relatedCategory -> name . '</a></h3>';
									echo 	'<div class="inner-container">';
									while ($custom_query -> have_posts())  {
										$custom_query -> the_post();
										get_template_part('template-parts/content-portfolio', get_post_format());
									}
									echo 	'</div>';
									echo '</div>';
								}
								wp_reset_postdata();
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<!-- BEGIN SIDEBAR -->
		<aside class="side-content-container">
			<article class="social-share">
				<h5>Share</h5>
				<a href="<?php echo $fbUrl; ?>" target="_blank">
					<i class="fab fa-facebook-square"></i>
				</a>
				<a href="<?php echo $twitterURL; ?>" target="_blank">
					<i class="fab fa-twitter-square"></i>
				</a>
			</article>

			<?php
				// CONTACT INFORMATION
				// EMAIL EXAMPLE Nasserie Carew, CURRENTLY NO PHONE NUMBERS USED ON PROFILES
				if ($email != "" || $phone != "") {
					echo '<article>';
					echo 	'<h5>Contact</h5>';
					echo 	'<ul class="bbg__article-share">';
					if ($email != "") {
						$email_address .= '<li>';
						$email_address .= 	'<a href="mailto:' . $email . '" title="Email ' . get_the_title() . '">';
						$email_address .= 		'<span class="bbg__article-share__icon email"></span>';
						$email_address .= 		$email;
						$email_address .= 	'</a>';
						$email_address .= '</li>';
						echo $email_address;
					}
					if ($phone != "") {
						$phone_number  = '<li>';
						$phone_number .= 	'<span class="bbg__article-share__icon phone"></span>';
						$phone_number .= 	$phone;
						$phone_number .= '</li>';
						echo $phone_number;

					}
					echo 	'</ul>';
					echo '</article>';
				}

				// IF TWITTER HANDLE, SHOW IT, SHOW RECENT TWEETS IF ENABLED ON PAGE
				// LATEST TWEETS ex. CEO John Lansing, Amanda Bennet
				if ($twitterProfileHandle != "") {
					$twitter_markup  = '<article>';
					$twitter_markup .= 	'<h5>Follow on Twitter</h5>';
					$twitter_markup .= 	'<a href="https://twitter.com/' . $twitterProfileHandle . '" target="_blank" title="Follow ' . get_the_title() . ' on Twitter">';
					$twitter_markup .= 		'<i class="fab fa-twitter"></i> @' . $twitterProfileHandle;
					$twitter_markup .= 	'</a>';
					$twitter_markup .= '</article>';
					$twitter_markup .= '<article style="background-color: #e1f3f8;">';
					$twitter_markup .= 	$latestTweetsStr;
					$twitter_markup .= '</article>';
					echo $twitter_markup;
				}

				// STANDARD SIDEBAR CONTENT ex. Rex Tillerson
				if ($includeSidebar) {
					echo $sidebar;
				}

				// RESOLUTION OF HONOR ex. Victor Ashe
				if ($resolution) {
					echo '<h3 class="bbg__sidebar-label">Resolution of Honor</h3>';
					echo '<p>';
					echo 	'<a href="' . $resolution['url'] . '">' . $resolution['title'] .'</a>';
					echo '</p>';
				}

				if ($relatedLinksTag != "") {
					$qParams2 = array(
						'post_type' => array('post'),
						'posts_per_page' => 4,
						'tag' => $relatedLinksTag,
						'orderby' => 'date',
						'order' => 'DESC'
					);
					$custom_query = new WP_Query($qParams2);

					if ($custom_query -> have_posts()) {
						echo '<h5>Related posts  <!--(tag "$relatedLinksTag")--></h5>';
						echo '<ul>';
						while ($custom_query -> have_posts())  {
							$custom_query -> the_post();
							$link = get_the_permalink();
							$title = get_the_title();

							echo '<li>';
							echo 	'<a href="' . $link . '">' . $title . '</a>';
							echo '</li>';
						}
						echo '</ul>';
						$viewAllLink = get_term_link($relatedLinksTag, 'post_tag');
						echo "<a class='bbg__read-more' href='" . $viewAllLink . "'>VIEW ALL »</a>";
					}
					wp_reset_postdata();
				}
			?>
		</aside>
	</div><!-- END GRID -->
</main>

<section class="outer-container">
	<?php get_sidebar(); ?>
</section>

<?php get_footer(); ?>