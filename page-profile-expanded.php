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

	$profile_id = get_the_ID();
	$page_content = do_shortcode(get_the_content());
	$page_content = apply_filters('the_content', $page_content);

	if (get_post_meta($profile_id, 'append_press_release_excerpts') == true) {
		$pressReleaseExcerpts = getSidebarPressReleaseExcerpts($profile_id);
		$page_content .= $pressReleaseExcerpts;
	}

	$metaAuthor = get_the_author();
	$metaKeywords = strip_tags( get_the_tag_list('',', ',''));

	$ogTitle = get_the_title();
	$ogDescription = get_the_excerpt();

	/**** CREATE OG:IMAGE *****/
	$thumb = wp_get_attachment_image_src(get_post_thumbnail_id($profile_id), 'Full');
	if (!empty($thumb)) {
		$ogImage = $thumb['0'];
	}

	$socialImageID = get_post_meta($profile_id, 'social_image', true);
	if ($socialImageID) {
		$socialImage = wp_get_attachment_image_src($socialImageID , 'Full');
		$ogImage = $socialImage[0];
	}

	/**** Get profile fields *****/
	$occupation = get_post_meta($profile_id, 'occupation', true);
	$isActing = get_post_meta($profile_id, 'acting', true);
	$email = get_post_meta($profile_id, 'email', true);
	$phone = get_post_meta($profile_id, 'phone', true);
	$twitterProfileHandle = get_post_meta($profile_id, 'twitter_handle', true);
	$instagramProfileHandle = get_post_meta($profile_id, 'instagram_handle', true);
	$relatedLinksTag = get_post_meta($profile_id, 'related_links_tag', true);

	/**** CREATE $formerCSS - applies black and white to retired board members ***/
	if ($isActing) {
		$occupation = "Acting " . $occupation;
	}
	$active = get_post_meta( $profile_id, 'active', true );
	$formerCSS = "";
	if (!$active) {
		$occupation = "(Former) " . $occupation;
		$formerCSS = " bbg__former-member";
	}

	/*** Get the profile photo mugshot ***/
	$profilePhotoID = get_post_meta($profile_id, 'profile_photo', true);
	$profilePhoto = "";
	if ( $profilePhotoID ) {
		$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
		$profilePhoto = $profilePhoto[0];
	}

	/*** Generate the code for the latest tweets that we use in the sidebar ***/
	$latestTweetsStr = "";
	if ($twitterProfileHandle != "") {
		$showLatestTweets = get_post_meta($profile_id, 'show_latest_tweets', true);
		if ($showLatestTweets) {
			$latestTweetsStr = '<a data-chrome="noheader nofooter noborders transparent noscrollbar" data-tweet-limit="2" class="twitter-timeline" href="https://twitter.com/' . $twitterProfileHandle . '" data-screen-name="' . $twitterProfileHandle . '" >Tweets by @' . $twitterProfileHandle . '</a><script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		}
	}

	$resolution = get_field('board_resolution_of_honor');
	rewind_posts();
}

// SECTION FOR CEO
$ceo = get_post_meta($profile_id, 'ceo', true);

if (isset($ceo)) {
	function get_ceo_article_arguments($slug) {
		if ($slug == 'john-lansing-chief-executive-officer-and-director') {
			$ceo_params = array(
				'post_type' => array('post'),
				'posts_per_page' => 2,
				'orderby' => 'date',
				'order' => 'DESC',
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'john-lansing' ),
						'operator' => 'IN'
					),
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'bbg-ceo' ),
						'operator' => 'IN'
					)
				)
			);
		} else if ($slug == 'grant-turner') {
			$ceo_params = array(
				'post_type' => array('post'),
				'posts_per_page' => 2,
				'orderby' => 'date',
				'order' => 'DESC',
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'grant-turner' ),
						'operator' => 'IN'
					),
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'bbg-ceo' ),
						'operator' => 'IN'
					)
				)
			);
		} else if ($slug == 'michael-pack') {
			$ceo_params = array(
				'post_type' => array('post'),
				'posts_per_page' => 2,
				'orderby' => 'date',
				'order' => 'DESC',
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'michael-pack' ),
						'operator' => 'IN'
					),
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'bbg-ceo' ),
						'operator' => 'IN'
					)
				)
			);
		} else if ($slug == 'amanda-bennett') {
			$ceo_params = array(
				'post_type' => array('post'),
				'posts_per_page' => 2,
				'orderby' => 'date',
				'order' => 'DESC',
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'amanda-bennett' ),
						'operator' => 'IN'
					),
					array(
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => array( 'usagm-ceo' ),
						'operator' => 'IN'
					)
				)
			);
		}
		return $ceo_params;
	}
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
		$twitterText  = "";
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
							$profile_information  = '<h2 class="section-header">' . get_the_title() . '</h2>';
							$profile_information .= '<p class="lead-in">' . $occupation . '</p>';
							$profile_information .= '<div class="page-content">';
							$profile_information .= 	$page_content;
							// $profile_information .= 	 '<p class="bbg-tagline" style="text-align: right;">';
							// $profile_information .= 		'Last modified: ' . get_the_modified_date('F d, Y');
							// $profile_information .= 	'</p>';
							$profile_information .= '</div>';
							echo $profile_information;
						?>

						<?php
							// CONTENT BELOW BIOGRAPHY
							$content_below_bio = get_field('profile_content_below_biography', $profile_id);
							if ($content_below_bio != "") {
								echo '<div class="bbg__profile__content__section">';
								echo 	$content_below_bio;
								echo '</div>';
							}
						?>

						<?php
							// SECTION FOR CEO
							if ($ceo ) {
								global $post;
								$post_slug = $post->post_name;
								$ceo_articles = get_ceo_article_arguments($post_slug);
								$ceo_post_query = new WP_Query($ceo_articles);
								$ceo_article_array = $ceo_post_query->posts;
								if (!empty($ceo_article_array)) {
									$categoryUrl = '/tag/amanda-bennett/?category_name=appearance,bbg-in-the-news';
									$categoryLabel = 'News & Appearances';
									// $ceo_post_query = new WP_Query($ceo_articles);
									// $ceo_article_array = $ceo_post_query->posts;

									echo '<div class="ceo-news-posts">';
									// echo 	'<h3 class="section-subheader"><a href="' . $categoryUrl . '">' . $categoryLabel . '</a></h3>';
									echo 	'<h3 class="section-subheader">' . $categoryLabel . '</h3>';
									foreach($ceo_article_array as $ceo_article_data) {
										$ceo_news_post = build_article_standard_vertical($ceo_article_data);
										echo $ceo_news_post;
									}
									echo '</div>';
								}
							}
						?>

						<!-- section for related blog posts. Previously was used for "from the ceo". Currently not used on any profiles. -->
						<?php
							//Add blog posts below the main content
							$relatedCategory = get_field('profile_related_category', $profile_id);

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
		<div class="side-content-container">
			<?php
				// SHARE THIS PAGE
				$share_icons = social_media_share_page($profile_id);
				if (!empty($share_icons)) {
					echo $share_icons;
				}

				// CONTACT INFORMATION
				// EMAIL EXAMPLE Nasserie Carew, CURRENTLY NO PHONE NUMBERS USED ON PROFILES
				if ($email != "" || $phone != "") {
					echo '<aside>';
					echo 	'<h2 class="sidebar-section-header">Contact</h2>';
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
					echo '</aside>';
				}

				if ($instagramProfileHandle != "") {
					$instagram_markup  = '<aside>';
					$instagram_markup .= 	'<h2 class="sidebar-section-header">Follow on Instagram</h2>';
					$instagram_markup .= 	'<a href="https://www.instagram.com/' . $instagramProfileHandle . '" target="_blank" title="Follow ' . get_the_title() . ' on Instagram">';
					$instagram_markup .= 		'<i class="fa-brands fa-square-instagram"></i> @' . $instagramProfileHandle;
					$instagram_markup .= 	'</a>';
					$instagram_markup .= '</aside>';
					echo $instagram_markup;
				}

				// IF TWITTER HANDLE, SHOW IT, SHOW RECENT TWEETS IF ENABLED ON PAGE
				// LATEST TWEETS ex. CEO John Lansing, Amanda Bennet
				if ($twitterProfileHandle != "") {
					$twitter_markup  = '<aside>';
					$twitter_markup .= 	'<h2 class="sidebar-section-header">Follow on Twitter</h2>';
					$twitter_markup .= 	'<a href="https://twitter.com/' . $twitterProfileHandle . '" target="_blank" title="Follow ' . get_the_title() . ' on Twitter">';
					$twitter_markup .= 		'<i class="fa-brands fa-square-x-twitter"></i> @' . $twitterProfileHandle;
					$twitter_markup .= 	'</a>';
					$twitter_markup .= '</aside>';
					$twitter_markup .= '<aside style="background-color: #e1f3f8;">';
					$twitter_markup .= 	$latestTweetsStr;
					$twitter_markup .= '</aside>';
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
						echo '<h2 class="sidebar-section-header">Related posts  <!--(tag "$relatedLinksTag")--></h2>';
						while ($custom_query -> have_posts())  {
							$custom_query -> the_post();

							echo '<p class="sidebar-article-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></p>';
						}
						$viewAllLink = get_term_link($relatedLinksTag, 'post_tag');
						echo "<a class='bbg__read-more' href='" . $viewAllLink . "'>VIEW ALL »</a>";
					}
					wp_reset_postdata();
				}
			?>
		</div>
	</div><!-- END GRID -->
</main>

<?php get_footer(); ?>