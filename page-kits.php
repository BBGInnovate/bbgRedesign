<?php
/**
 * Custom template for displaying informational kits — Press Room, Congressional.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 * @author Gigi Frias <gfrias@bbg.gov>
   template name: Info Kits
 */

include get_template_directory() . '/inc/bbg-functions-assemble.php';
include get_template_directory() . '/inc/constant-contact_sign-up.php';

function isOdd($pageTotal) {
	return ($pageTotal % 2) ? true : false;
}

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$pageName = get_the_title();
		$pageContent = get_the_content();
		$pageContent = apply_filters('the_content', $pageContent);
   		$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
	}
}
wp_reset_postdata();
wp_reset_query();

// CREATE AN ARRAY FOR AWARDS INFO
$awards = array();
$awardCategoryObj = get_category_by_slug('award');
if (is_object($awardCategoryObj)) {
	$awardCategoryID = $awardCategoryObj -> term_id;
	$award_params = array(
		'post_type' => array('award'),
		'posts_per_page' => 1,
		'orderby' => 'date',
		'order' => 'DESC'
	);
	$award_query = new WP_Query($award_params);
	if ($award_query -> have_posts()) {
		while ($award_query -> have_posts()) {
			$award_query -> the_post();
			$id = get_the_ID();

			$awardYears  = get_post_meta($id, 'standardpost_award_year');
			$awardTitle = get_post_meta($id, 'standardpost_award_title', true);
			$orgTerms = get_field('standardpost_award_organization', $id);
		    $organizations = array();
		    if (!empty($organizations)) {
		    	$organizations[] = $orgTerms -> name;
		    }
			$entity = get_post_meta($id, 'standardpost_award_recipient');
			$description = get_post_meta($id, 'standardpost_award_description');

			$awards[] = array(
				'id' => $id,
				'url' => get_permalink($id),
				'title' => get_the_title($id),
				'excerpt' => get_the_excerpt(),
				'awardYears' => $awardYears,
				'awardTitle' => $awardTitle,
				'organizations' => $organizations,
				'recipients' => $entity
			);
		}
	}
	wp_reset_postdata();
}

/******* GRAB THE MOST RECENT MEDIA ADVISORY THAT HAS AN EXPIRATION DATE IN THE FUTURE. WE CHECK LATEST 5 ******/
$advisory = false;
// Get all posts with advisory category
$mediaAdvisoryCategoryObj = get_category_by_slug('media-advisory');

if ($pageName != 'Office of Congressional Affairs' && is_object($mediaAdvisoryCategoryObj)) {
	// set up award category query parameters
	$mediaAdvisoryCategoryID = $mediaAdvisoryCategoryObj -> term_id;
	$mediaParams = array(
		'post_type' => array('post'),
		'posts_per_page' => 5,
		'category__and' => array($mediaAdvisoryCategoryID),
		'orderby' => 'date',
		'order' => 'DESC'
	);
	$todayDateObj = new DateTime("now");

	// execute advisory query
	$foundAdvisory = false;

	$media_query = new WP_Query( $mediaParams );

	if ($media_query -> have_posts()) {
		while ($media_query -> have_posts()) : $media_query -> the_post();
			if (!$foundAdvisory) {
				$id = get_the_ID();
				$expiryDate = get_field('media_advisory_expiration_date', $id);
				$expiryDateObj = DateTime::createFromFormat(
					"m/d/Y h:i",
					$expiryDate . " 00:00"
				);
				if ($expiryDateObj > $todayDateObj) {
					$foundAdvisory = true;
					$thumb =  get_the_post_thumbnail($id, 'medium-thumb');
					$advisory = array(
						'id' => $id,
						'url' => get_permalink(),
						'title' => get_the_title(),
						'excerpt' => get_the_excerpt(),	//leave this one last- it changes the post context
						'thumb' => $thumb,
						'expiryDate' => $expiryDate
					);
				}
			}
		endwhile;
	}
	wp_reset_postdata();
}

$numNews = 4;
if ($advisory) {
	$numNews = 1;
}

// PREPARE QUERY PARAMS PRESS RELEASES
$prCategoryObj = get_category_by_slug('press-release');
$prCategoryID = $prCategoryObj -> term_id;

$qParamsPressReleases = array(
	'post_type' => array('post'),
	'posts_per_page' => $numNews,
	'category__and' => array( $prCategoryID ),
	'orderby', 'date',
	'order', 'DESC',
	'tax_query' => array(
		array(
			'taxonomy' => 'post_format',
			'field' => 'slug',
			'terms' => 'post-format-quote',
			'operator' => 'NOT IN'
		)
	),
);

// set variable for PR category link to All network highlights page
$prCategoryLink = get_permalink(get_page_by_path('news/network-highlights'));

// PREPARE QUERY PARAMS CONGRESSIONAL NEWS
$qParamsCongressional = array(
	'post_type' => array('post'),
	'posts_per_page' => $numNews,
	'tag' => 'office-of-congressional-affairs',
	'orderby', 'date',
	'order', 'DESC',
	'tax_query' => array(
		array(
			'taxonomy' => 'post_format',
			'field' => 'slug',
			'terms' => 'post-format-quote',
			'operator' => 'NOT IN'
		)
	),
	'category__not_in' => get_cat_id('Award') // exclude Awards posts
);
// set variable for PR category link to main news page
$congCategoryLink = get_permalink( get_page_by_path('news'));

// KITS UMBRELLA
function get_kits_umbrella_main_data() {
	$header = get_sub_field('kits_subpage_heading');
	$header_link = get_sub_field('kits_subpage_heading_link');
	if (!empty($header_link)) {
		$header = '<a href="' . $header_link . '">' . $header . '</a>';
	}

	$kits_main_data = array(
		'header' => $header,
		'header_link' => $header_link
	);
	return $kits_main_data;
}
function get_kits_umbrella_content_data($layout_type) {
	$file = '';
	if ($layout_type == 'kits_internal_page') {
		$column_title = get_sub_field('kits_column_title');
		$thumbnail = get_sub_field('kits_thumbnail');
		$include_excerpt = get_sub_field('kits_include_excerpt');
		$link_arr = get_sub_field('kits_link');
		$post_id = $link_arr[0];
		$link = get_the_permalink($post_id);
		$post_title = get_the_title($post_id);
		$description = get_the_excerpt($post_id);

		if (!empty($link)) {
			$column_title = '<a href="' . $link . '">' . $column_title . '</a>';
		}
	}
	else if ($layout_type == 'kits_file') {
		$column_title = get_sub_field('kits_file_column_title');
		$thumbnail = get_sub_field('kits_file_thumbnail');
		$link_arr = get_sub_field('kits_file_link');
		$post_id = $link_arr[0];
		$link = get_the_permalink($post_id);
		$post_title = get_the_title($post_id);
		$description = get_the_excerpt($post_id);
		$file = get_sub_field('kits_file');

		if (!empty($link)) {
			$column_title = '<a href="' . $link . '">' . $column_title . '</a>';
		}
	}
	$kit_data = array(
		'column_title' => $column_title,
		'thumbnail' => $thumbnail,
		'link' => $link,
		'post_title' => $post_title,
		'file' => $file,
		'description' => $description
	);
	return $kit_data;
}

get_header();
?>

<style>
	@media screen and ( min-width: 600px ) {
		/*** JBF: this CSS is here because something is wrong with the grid at 1-2-2 ***/
		.bbg-grid--1-2-2:nth-child(2n+1) {
			clear: none;
			margin-right: 0;
		}
	}
</style>

<script type="text/javascript">
	/* show/hide the constant contact signup form */
	function toggleForm() {
		btnSignup = document.getElementById( 'btnSignup' );

		if (btnSignup.style.display == 'none') {
			btnSignup.style.display = '';
			ccForm = document.getElementsByName('embedded_signup');
			ccForm[0].style.display = 'none';
		} else {
			btnSignup.style.display = 'none';
			ccForm = document.getElementsByName('embedded_signup');
			ccForm[0].style.display = '';
		}
	}

	jQuery(document).ready(function() {
		/* click handler for the show/hide of constant contact form */
		jQuery('#btnClose').click(function() {
			toggleForm();
		});
	});
</script>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<div>

	<div id="primary" class="content-area">
		<main id="main" role="main">

			<div class="outer-container">
				<div class="grid-container">
					<header class="page-header">
						<?php echo '<h2 class="section-header">' . get_the_title() . '</h2>'; ?>
					</header>
				</div>
			</div>

			<!-- SET UNIVERSAL VARIABLES -->
			<?php
				// access site-wide variables
				global $post;
				// set locale for number filters
				setlocale( LC_ALL, 'en_US.UTF-8' ); // currency

				/* BBG settings variables */
				// LOAD URL FROM MISSION PAGE
				$missionURL = get_field( 'site_setting_mission_statement_link', 'options', 'false' );

				if ($missionURL) {
					$missionID = url_to_postid( $missionURL );
					$mission = my_excerpt( $missionID );
					$mission = apply_filters( 'the_content', $mission );
					$mission = str_replace( ']]>', ']]&gt;', $mission );
					$mission = substr($mission, 0, -5);
					$mission = $mission . ' <a href="' . $missionURL . '" class="bbg__kits__intro__more--link">Network missions »</a></p>';
				} else {
					$mission = get_field( 'site_setting_mission_statement', 'options', 'false' );
					$mission = apply_filters( 'the_content', $mission );
					$mission = str_replace( ']]>', ']]&gt;', $mission );
				}

			    // numbers
				$networks = get_field('site_setting_total_networks', 'options', 'false') . " networks";
				$languages = get_field('site_setting_total_languages', 'options', 'false') . " languages";
				$countries = get_field('site_setting_total_countries', 'options', 'false') . " countries";
				$audience = get_field('site_setting_unduplicated_audience', 'options', 'false') . " million";
				$affiliates = get_field('site_setting_total_affiliates', 'options', 'false');
				$affiliates = number_format($affiliates) . " affiliates"; // format number and append value desc
				$transmittingSites = get_field('site_setting_transmitting_sites', 'options', 'false');
				$transmittingSites = number_format($transmittingSites) . " transmitting sites"; // format number and append value desc
				$programming = get_field('site_setting_weekly_programming', 'options', 'false');
				$programming = number_format($programming) . " hours"; // format number and append value desc

				/* Contact information */
				$phone = get_field('agency_phone', 'options', 'false');
				$phone_link = str_replace(array('(',') ','-'), '' , $phone);
				$phoneMedia = get_field('agency_phone_press', 'options', 'false');
				$phoneMedia_link = str_replace(array('(',') ','-'), '' , $phoneMedia);
				$phoneCongress = get_field('agency_phone_congress', 'options', 'false');
				$phoneCongress_link = str_replace(array('(',') ','-'), '' , $phoneCongress);
				$email = get_field('agency_email', 'options', 'false');
				$emailPress = get_field('agency_email_press', 'options', 'false');
				$emailCongress = get_field('agency_email_congress', 'options', 'false');
				$street = get_field('agency_street', 'options', 'false');
				$city = get_field('agency_city', 'options', 'false');
				$state = get_field('agency_state', 'options', 'false');
				$zip = get_field('agency_zip', 'options', 'false');

				/* Format all contact information */
				// Pick which phone number to display based on page title
				if ($pageName == "Press room" && $phoneMedia != "")  {
					$phone = '<li itemprop="telephone" aria-label="telephone"><span class="bbg__list-label">Tel: </span><a href="tel:' . $phoneMedia_link . '">' . $phoneMedia . '</a></li>';
				} elseif ($pageName == "Office of Congressional Affairs" && $phoneCongress != "")  {
					$phone = '<li itemprop="telephone" aria-label="telephone"><span class="bbg__list-label">Tel: </span><a href="tel:' . $phoneCongress_link . '">' . $phoneCongress . '</a></li>';
				} else {
					$phone = '<li itemprop="telephone" aria-label="telephone"><span class="bbg__list-label">Tel: </span><a href="tel:' . $phone_link . '">' . $phone . '</a></li>';
				}

				// Pick which email address to display based on page title
				if ($pageName == "Press room" && $emailPress != "") {
					$email = '<li><span class="bbg__list-label">Email: </span><a itemprop="email" aria-label="email" href="mailto:' . $emailPress . '" title="Contact us">' . $emailPress . '</a></li>';
				} elseif ($pageName == "Office of Congressional Affairs" && $emailCongress != "") {
					$email = '<li><span class="bbg__list-label">Email: </span><a itemprop="email" aria-label="email" href="mailto:' . $emailCongress . '" title="Contact us">' . $emailCongress . '</a></li>';
				} else {
					$email = '<li><span class="bbg__list-label">Email: </span><a itemprop="email" aria-label="email" href="mailto:' . $email . '" title="Contact us">' . $email . '</a></li>';
				}

				if ($street != "" && $city != "" && $state != "" && $zip != "") {
					$address = $street . '<br/>' . $city . ', ' . $state . ' ' . $zip;
					//Strip spaces for url-encoding.
					$street = str_replace(" ", "+", $street);
					$city = str_replace(" ", "+", $city);
					$state = str_replace(" ", "+", $state);
					$mapLink = 'https://www.google.com/maps/place/' . $street . ',+' . $city . ',+' . $state . '+' . $zip . '/';
					$address = '<p itemprop="address" aria-label="address"><a href="'. $mapLink . '">' . $address . '</a></p>';
				}

				if ( $address != "" || $phone != "" || $email != "" || $phoneMedia != "" || $emailPress != "" ){
					$includeContactBox = TRUE;
				}

				// GET ALL CONTACT CARDS FOR DROPDOWN
				$allContacts = get_field('kits_network_contacts');
				$contactPostIDs = get_post_meta($post -> ID, 'contact_post_id', true);
			?>

			<!-- PART 1 **** News + media advisories + contact & inquiries **** -->
			<div class="outer-container">
				<div class="grid-container">
					<?php
						$includeInfoBox = get_post_meta($post -> ID, 'kits_include_info_box', true);
						if ($includeInfoBox) {
							$link = get_post_meta($post -> ID, 'kits_info_box_link', true);
							$title = get_post_meta($post -> ID, 'kits_info_box_title', true);
							$text = get_post_meta($post -> ID, 'kits_info_box_text', true);

							$s  = '';
							$s .= '<section class="usa-section">';
							$s .= 	'<div class="usa-alert usa-alert-info">';
							$s .= 		'<div class="usa-alert-body">';
							$s .= 			'<h3 class="usa-alert-heading">';
							if ($link == "") {
								$s .= 			$title;
							} else {
								$s .= 			'<a href="' . $link . '">' . $title . '</a>';
							}
							$s .= 			'</h3>';
							$s .= 			'<p class="usa-alert-text">' . $text . '</p>';
							$s .= 		'</div>';
							$s .= 	'</div>';
							$s .= '</section>';
							echo $s;
						}
					?>

					<?php
						echo '<!-- Recent news section -->';
						// echo '<section id="recent-posts" class="usa-section bbg__home__recent-posts">';
						if (!$advisory && $pageName == "Press room") {
							echo '<h3>Recent press releases</h3>';
						} elseif (!$advisory && $pageName == "Office of Congressional Affairs") {
							echo '<h3>Recent highlights</h3>';
						}
						// echo 	'<div class="bbg__kits__recent-posts">';
						echo 	'<div class="nest-container">';
						echo 		'<div class="inner-container">';
						echo 			'<div class="grid-third bbg__secondary-stories">';
						if ($advisory) {
							echo 			'<h2 class="section-header">Latest press release</h2>';
						}
						/**** START FETCH related news based on page title ****/
						if ($pageName == "Press Room") {
							query_posts($qParamsPressReleases);
						} elseif ($pageName == "Office of Congressional Affairs") {
							query_posts($qParamsCongressional);
						}

						if (have_posts()) {
							$counter = 0;
							$includeImage = TRUE;

							while (have_posts()) : the_post();
								$counter++;
								$postIDsUsed[] = get_the_ID();
								$includeMeta = false;
								$gridClass = "bbg-grid--full-width";
								$includeExcerpt = false;

								if ($counter > 1) {
									$includeImage = false;
									$includeMeta = false;
									if ($counter == 2) {
										echo '</div>';
										echo '<div class="grid-third">';
									}
								}
								if ($counter == 1) {
									$includePortfolioDescription = false;
									get_template_part('template-parts/content-portfolio', get_post_format());
								} else {
									get_template_part('template-parts/content-excerpt-list', get_post_format());
								}
							endwhile;

							echo '<br/><a href="' . $prCategoryLink . '" class="bbg__kits__intro__more--link">View more »</a>';
						}
						wp_reset_query();
						echo '</div>';

						if ($advisory && $pageName == "Press room") {
							echo '<div class="grid-third">';
							echo 	'<h3 class="entry-title bbg-blog__excerpt-title">';
							echo 		'<span class="usa-label bbg__label--advisory">Media Advisory</span><br/>';
							echo 		'<a href="' . $advisory['url'] . '">' . $advisory['title'] . '</a>';
							echo 	'</h3>';
							echo 	'<div class="entry-content bbg-blog__excerpt-content">';
							echo 		'<p>' . $advisory['excerpt'] . '</p>';
							echo 	'</div>';
							echo '</div>';
						}
					?>

					<!-- Contact card (tailored to audience based on page title) -->
					<div class="grid-third">
						<?php if ($includeContactBox) { ?>
								<div class="bbg__contact-card">
									<div class="bbg__contact-card__text">
										<?php
											if ($pageName == "Press room") {
												echo '<h3>Office of Public Affairs</h3>';
											} elseif ($pageName == "Office of Congressional Affairs") {
												echo '<h3>Office of Congressional Affairs</h3>';
											} else {
												echo '<h3>' . $pageName . ' Contact Information</h3>';
											}
											echo $address;
											echo '<ul class="unstyled-list">';
											echo 	$phone;
											echo 	$email;
											echo '</ul>';
											echo '<!-- Social media profiles -->';
											// check that budget repeater field exists
											$allSocial = get_field('agency_social_media_profiles', 'options', 'false');

											echo '<div class="bbg__kits__social">';
											if ($allSocial) {
												foreach ($allSocial as $socials) {
													$socialPlatform = $socials['social_media_platform'];
													$socialProfile = $socials['social_media_profile_name'];
													$socialURL = $socials['social_media_url'];
													echo '<a class="bbg__kits__social-link usa-link-' . strtolower( $socialPlatform ) . '" href="' . $socialURL . '" role="img" aria-label="' . $socialPlatform  . '"></a>';
												}
											}
											echo '</div>';
											echo '<div class="bbg__kits__contacts">';
											renderContactSelect($contactPostIDs);
											echo '</div>';
											echo "<button id='btnSignup' onclick=\" toggleForm();  \" class='usa-button-outline bbg__kits__inquiries__button--half' style='width:100%; margin-top:2rem;' data-enabled='enabled'>Sign up to receive updates</button>";
											echo $signupForm;
										?>
									</div>
								</div>
						<?php } ?>
							</div><!-- .inner-container -->
						</div><!-- .nest-container -->
					</div><!-- last third (contacts) -->
				</div><!-- END .grid-container -->
			</div><!-- END .outer-container -->
			<!-- END PART 1 **** News + media advisories + contact & inquiries **** -->

			<!-- PART 2 **** BBG by the numbers + CEO ribbon + latest award/featured page + Featured reports **** -->
			<div class="bbg__kits__section" id="page-sections">
				<!-- 2A **** 3-COL ROW: BBG by the numbers -->
				<div class="outer-container bbg__kits__section--row">
					<div class="grid-container bbg__kits__section--tiles">
						<h3 class="section-subheader">USAGM by the numbers</h3>
						<!-- DISTRIBUTION tile -->
						<div class="nest-container">
							<div class="inner-container">
								<article class="grid-third bbg__kits__section--tile">
									<h3 class="bbg__kits__section--tile__title-bar">International operations</h3>
									<p class="bbg__kits__section--tile__list"><span class="bbg__kits__section--tile__list--serif"><?php echo $networks; ?></span> and a system of <span class="bbg__kits__section--tile__list--sans"><?php echo $affiliates; ?></span> and over <span class="bbg__kits__section--tile__list--sans"><?php echo $transmittingSites; ?></span> distribute <span class="bbg__kits__section--tile__list--sans"><?php echo $programming; ?></span> of original content globally each week.</p>
								</article>

								<!-- AUDIENCE tile -->
								<article class="grid-third bbg__kits__section--tile">
									<h3 class="bbg__kits__section--tile__title-bar">Global audience</h3>
									<p class="bbg__kits__section--tile__list">A worldwide unduplicated audience of <span class="bbg__kits__section--tile__list--serif"><?php echo $audience; ?></span> from more than <span class="bbg__kits__section--tile__list--sans"><?php echo $countries; ?></span> tune in weekly in <span class="bbg__kits__section--tile__list--sans"><?php echo $languages; ?></span>.</p>
								</article>

								<!-- BUDGET tile -->
								<article class="grid-third bbg__kits__section--tile">
									<h3 class="bbg__kits__section--tile__title-bar">Annual budget</h3>
									<table class="bbg__kits__section--tile__table--borderless">
										<tbody>
											<?php
												// check that budget repeater field exists
												$allBudgets = get_field( 'site_setting_annual_budgets', 'options', 'false' );

												if( $allBudgets ) {
													//build a new array with the key and value
													foreach($allBudgets as $key => $value) {
														//still going to sort by firstname
														$budget[$key] = $value['fiscal_year'];
													}
													// sort multi-dimensional array by new array
													array_multisort( $budget, SORT_DESC, $allBudgets );

													// create a variable to limit number of budget years
													$maxYears = 0;

													// loop through repeater rows
													foreach( $allBudgets as $budget ) {
														// populate variables for each row
														$budgetFY = 'FY' . $budget['fiscal_year'];
														$budgetStatus = $budget['status'];
														$budgetAmount = $budget['dollar_amount'];

														echo '<!-- ' . $budgetFY . ' budget -->';
														echo '<tr>';
															// fiscal year column
															echo '<th scope="row">' . $budgetFY . ' <span class="bbg__file-size">(' . $budgetStatus  . ')</span></th>';
															// amount column
															echo '<td class="bbg__kits__section--tile__list--sans">' . money_format( '%.1n', $budgetAmount ) . 'M</td>';
														echo '</tr>';

														if ( $maxYears++ == 3 ) break; // change max number here to set new limit — current max is 4 (starting from 0)
													}
												}
											?>
										</tbody>
									</table>
								</article>
							</div>
						</div>
					</div>
				</div> <!-- END .outer-container .bbg__kits__section--row -->
				<!-- END 2A **** BBG by the numbers -->

				<!-- 2B **** Flexible content rows -->
		        <?php
				if (have_rows('kits_flexible_page_rows')):
					$counter = 0;
					$pageTotal = 1; // for setting grid based on odd/even count
					$containerClass = "bbg__kits__child ";

					/* @Check if number of pages is odd or even
					*  Return BOOL (true/false) */
					function checkNum($pageTotal) {
						return ($pageTotal % 2) ? TRUE : FALSE;
					}

					while (have_rows('kits_flexible_page_rows')) : the_row();
						$counter++;
						$sectionClasses = "usa-grid-full bbg__kits__section--row";

						// change section class for ribbon rows
						if (get_row_layout() == 'kits_ribbon_page') {
							$sectionClasses .= " bbg__ribbon";
						}

						echo '<!-- ROW ' . $counter . '-->'; // output counter to keep track of rows

						if (get_row_layout() == 'kits_ribbon_page') {
							$labelText = get_sub_field('kits_ribbon_label');
							$labelLink = get_sub_field('kits_ribbon_label_link');
							$headlineText = get_sub_field('kits_ribbon_headline');
							$headlineLink = get_sub_field('kits_ribbon_headline_link');
							$summary = get_sub_field('kits_ribbon_summary');
							$imageURL = get_sub_field('kits_ribbon_image');
							$fileDownload = get_sub_field('kits_ribbon_download_button');
							$fileDownloadURL = get_sub_field('kits_ribbon_download_url');
							$fileDownloadText = get_sub_field('kits_ribbon_download_prompt');

							$summary = apply_filters('the_content', $summary);
							$summary = str_replace(']]>', ']]&gt;', $summary);

							echo '<div class="bbg__ribbon">';
							echo 	'<div class="outer-container">';
							if (!empty($imageURL)) {
								echo 	'<div class="side-content-container">';
								echo 		'<div style="background-image: url(' . $imageURL . ');"></div>';
								echo 	'</div>';
							}
							echo 		'<div class="main-content-container">';
							if (!empty($labelLink)) {
								echo 		'<h2 class="section-header"><a href="' . get_permalink($labelLink) . '">' . $labelText . '</a></h2>';
							} else {
								echo 		'<h2 class="bbg__label">' . $labelText . '</h2>';
							}

							if (!empty($headlineLink)) {
								echo 		'<h4><a href="' . get_permalink($headlineLink) . '">' . $headlineText . '</a></h4>';
							} else {
								echo 		'<h4 class="bbg__announcement__headline">' . $headlineText . '</h4>';
							}
							echo $summary;

							if (!empty($fileDownload)) {
								echo 		'<button>';
								echo 			'<a href=' . $fileDownloadURL . ' target="_blank" download><span class="fa fa-download"></span>&emsp;' . $fileDownloadText . '</a>';
								echo 		'</button>';
							}
							echo 		'</div>';
							echo 	'</div><!-- .outer-container -->';
							echo '</div><!-- .bbg__ribbon -->';
						}
						elseif (get_row_layout() == 'kits_downloads_files') {
							$downloadsLabel = get_sub_field('kits_downloads_label');
							echo '<div class="outer-container">';
							echo 	'<div class="grid-container">';
							if ($downloadsLabel) {
								echo '<h3>' . $downloadsLabel . '</h3>';
							}
							echo 		'<div class="nest-container">';
							echo 			'<div class="inner-container">';

							$downloadFiles = get_sub_field('kits_downloads_file');
							$countFiles = count ($downloadFiles);
							if (checkNum($countFiles) === true) {
								$containerClass = 'grid-third';
							} else {
								$containerClass = 'grid-half';
							}
							if ($downloadFiles) {
								foreach ($downloadFiles as $file) {
									$fileImageObject = $file['downloads_file_image'];
									$thumbSrc = wp_get_attachment_image_src($fileImageObject['ID'] , 'large-thumb');
									$supportPageTitle = $file['kits_related_page_name'];
									$supportPage = $file['kits_related_page'];
									if ($supportPageTitle) {
										$pageHeadline = $supportPageTitle;
									} else {
										$pageHeadline = get_the_title($supportPage -> ID);
									}
									$pageURL = get_permalink($supportPage -> ID);
									$pageExcerpt = my_excerpt($supportPage -> ID);
									$pageExcerpt = apply_filters('the_content', $pageExcerpt);
									$pageExcerpt = str_replace(']]>', ']]&gt;', $pageExcerpt);


									$fileTitle = $file['downloads_link_name'];
									$fileObj = $file['downloads_file'];
									if ($fileTitle) {
										$fileName = $fileTitle;
									} else {
										$fileName = $fileObj['title'];
									}
									$fileID = $fileObj['ID'];
									$fileURL = $fileObj['url'];
									$file = get_attached_file($fileID);
									$fileExt = strtoupper(pathinfo( $file, PATHINFO_EXTENSION));
									$fileSize = formatBytes(filesize($file));

									echo '<article class="' . $containerClass . ' bbg__kits__section--tile">';
									echo 	'<header class="bbg__kits__section--tile__header">';
									if ( $supportPage ) {
										echo '<h4 class="bbg__kits__section--tile__title"><a href="' . $pageURL . '">' . $pageHeadline . '</a></h4>';
									} else {
										echo '<h4 class="bbg__kits__section--tile__title"><a href="' . $fileURL . '" target="_blank">' . $fileName . '</a></h4>';
									}
									echo 	'</header>';

									if ($thumbSrc) {
										echo '<a href="' . $fileURL . '" target="_blank">';
										echo '<img src="' . $thumbSrc[0] . '" alt="">';
										echo '</a>';
									}
									echo $pageExcerpt;
									echo 	'<p class="bbg__kits__section--tile__downloads"><a href="' . $fileURL . '" target="_blank">' . $fileName . '</a> <span class="bbg__file-size">(' . $fileExt . ', ' . $fileSize . ')</span></p>';
									echo '</article>';
								}
							}
							echo 			'</div>';
							echo 		'</div>';
							echo 	'</div>';
							echo '</div>'; // END .outer-container
						}
						elseif (get_row_layout() == 'kits_recent_awards') {
							$counter = 0;

							foreach ($awards as $current_award) {
								$counter++;

								$id = $current_award['id'];
								$url = $current_award['url'];
								$title = $current_award['title'];
								$awardYears = $current_award['awardYears'];
								$awardTitle = $current_award['awardTitle'];
								$awardCategoryLink = get_category_link($awardCategoryObj -> term_id);

								$award_block  = '<div class="outer-container">';
								$award_block .= 	'<div class="grid-half">';
								$award_block .= 		'<h3 class="section-subheader">Recent Awards</h3>';
								$award_block .= 		'<h4><a href="' . $url . '">' . $title . '</a></h4>';
								// $award_block .= 		'<h4>' . join($awardYears) . ' ' . join($organizations) . '</h4>';
								$award_block .= 		'<a href="' . $awardCategoryLink . '" class="read-more">View all awards »</a>';
								$award_block .= 	'</div>';
							}

							$focusPageObj = get_sub_field('kits_recent_awards_focus_page');
							$focusPageTitle = get_the_title($focusPageObj -> ID);
							$focusPageURL = get_the_permalink($focusPageObj -> ID);
							$focusPageExcerpt = my_excerpt($focusPageObj -> ID);
							$focusPageExcerpt = apply_filters('the_content', $focusPageExcerpt);
							$focusPageExcerpt = str_replace(']]>', ']]&gt;', $focusPageExcerpt);

							$award_block .= 	'<div class="grid-half bbg__post-excerpt bbg__award__excerpt">';
							$award_block .= 		'<h3 class="section-subheader">' . $focusPageTitle . '</h3>';
							$award_block .= 		'<p>' . $focusPageExcerpt . '</p>';
							$award_block .= 		'<a href="' . $focusPageURL . '" class="read-more">Read more</a>';
							$award_block .= 	'</div>';
							$award_block .= '</div>';
							echo $award_block;
						}
						elseif (get_row_layout() == 'kits_umbrella_page') {
							$kits_umbrella_main_data = get_kits_umbrella_main_data();
							$content_counter = count(get_sub_field('kits_section_content'));
							
							$grid_class = 'bbg-grid--1-2-2';
							if (isOdd($content_counter)) {
								$grid_class = 'bbg-grid--1-2-3';
							}
							$kit_umbrella  = '<div class="outer-container">';
							$kit_umbrella .= 	'<div class="grid-container">';
							$kit_umbrella .= 		'<h3 class="section-subheader">' . $kits_umbrella_main_data['header'] . '</h3>';
							$kit_umbrella .= 		'<div class="nest-container">';
							$kit_umbrella .= 			'<div class="inner-container">';
							$kit_umbrella .= 				'<div class="grid-container">';

							while (have_rows('kits_section_content')) {
								the_row();
								$kits_row = get_row_layout();
								$kit_umbrella_content_data = get_kits_umbrella_content_data($kits_row);
								if (!empty($kit_umbrella_content_data)) {
									$kit_umbrella .= 			'<div class="' . $grid_class . '">';
									$kit_umbrella .= 				'<h4>' . $kit_umbrella_content_data['column_title'] . '</h4>';
									$kit_umbrella .= 				'<div class="hd_scale umbrella-bg-image" ';
									$kit_umbrella .= 					'style="background-image: url(\'' . $kit_umbrella_content_data['thumbnail']['url'] . '\')">';
									$kit_umbrella .= 				'</div>';
									$kit_umbrella .= 				'<p>' . $kit_umbrella_content_data['description'] . '</p>';
									if (!empty($kit_umbrella_content_data['file'])) {
										$kit_umbrella .= 			'<p class="bbg__kits__section--tile__downloads"><a href="'. $kit_umbrella_content_data['file']['url'] . '">' . $kit_umbrella_content_data['file']['title'] . '</a> <span class="bbg__file">(' . $kit_umbrella_content_data['file']['subtype'] . ' ' . formatBytes($kit_umbrella_content_data['file']['filesize']) . ')</span></p>';
									} else {
										$kit_umbrella .= 				'<p><a href="'. $kit_umbrella_content_data['link'] . '">' . $kit_umbrella_content_data['post_title'] . '</a></p>';
									}
									$kit_umbrella .= 			'</div>';
								}
							}
							$kit_umbrella .= 				'</div>'; // END .grid-container
							$kit_umbrella .= 			'</div>';// END .inner-container
							$kit_umbrella .= 		'</div>'; // END .nest-container
							$kit_umbrella .= 	'</div>'; // END .grid-container
							$kit_umbrella .= '</div>'; // END .outer-container
							echo $kit_umbrella;
						}
					endwhile;
					echo '<!-- END ROWS -->';
				endif;
				?>
				<!-- END 2B **** Flexible content rows -->

			</div> <!-- End id="page-sections" -->

<?php get_footer(); ?>