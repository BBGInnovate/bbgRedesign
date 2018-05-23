<?php
function select_impact_story_id_at_random($used) {
	$qParams = array(
		'post_type'=> 'post',
		'post_status' => 'publish',
		'cat' => get_cat_id('impact'),
		'post__not_in' => $used,
		'posts_per_page' => 12,
		'orderby' => 'post_date',
		'order' => 'desc',
	);

	$custom_query = new WP_Query( $qParams );
	$allIDs = [];
	if ( $custom_query -> have_posts() ) :
		while ( $custom_query -> have_posts() ) : $custom_query -> the_post();
			$allIDs[] = get_the_ID();
		endwhile;
	endif;

	if ( count( $allIDs ) > 2 ) {
		shuffle( $allIDs );
		$ids = [];
		$ids[] = array_pop( $allIDs );
		$ids[] = array_pop( $allIDs );
	} else {
		$ids = $allIDs;
	}
	return $ids;
}

function select_recent_post_query_params($numPosts, $used, $catExclude) {
	$qParams = array(
		'post_type' => array( 'post' ),
		'posts_per_page' => $numPosts,
		'orderby' => 'post_date',
		'order' => 'desc',
		'category__not_in' => $catExclude,
		'post__not_in' => $used,
		/*** NOTE - we could have also done this by requiring quotation category, but if we're using post formats, this is another way */
		'tax_query' => array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => 'post-format-quote',
				'operator' => 'NOT IN'
			)
		)
	);
	return $qParams;
}

function create_threats_post_query_params($numPosts, $used) {
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $numPosts,
		'orderby' => 'post_date',
		'order' => 'desc',
		'cat' => get_cat_id('Threats to Press'),
		'post__not_in' => $used
	);
	$threats_package = array('params' => $qParams);
	// return $threats_package;
}

// function get_entity_data() {
	// $entityParentPage = get_page_by_path('networks');
	// $qParams = array(
	// 	'post_type' => array('page'),
	// 	'posts_per_page' => -1,
	// 	'post_parent' => $entityParentPage->ID,
	// 	'orderby' => 'meta_value_num',
	// 	'meta_key' => 'entity_year_established',
	// 	'order' => 'ASC'
	// );

	// $custom_query = new WP_Query($qParams);
	// return build_entity_list($custom_query);

	// if ($custom_query -> have_posts()) {
	// 	$entity_markup = '<div class="usa-grid-full">';

	// 	while ($custom_query -> have_posts())  {
	// 		$custom_query -> the_post();
	// 		$id = get_the_ID();
	// 		$fullName = get_post_meta($id, 'entity_full_name', true);

	// 		if ($fullName != "") {
	// 			$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
	// 			$abbreviation = str_replace("/", "",$abbreviation);
	// 			$description = get_post_meta($id, 'entity_description', true);
	// 			$description = apply_filters('the_content', $description);
	// 			$link = get_permalink( get_page_by_path("/networks/$abbreviation/" ));
	// 			$imgSrc = get_template_directory_uri() . '/img/logo_' . $abbreviation . '--circle-200.png'; //need to fix this

	// 			// $entity_data_package = array('column_rule' => $columnsClass, 'link' => $link, 'img_src' => $imgSrc, 'desc' => $description);
	// 			// return build_entity_list($entity_data_package);

	// 			$entity_markup .= '<article class="bbg__entity'. $columnsClass .'">';
	// 			$entity_markup .= 	'<div class="bbg__avatar__container bbg__entity__icon">';
	// 			$entity_markup .= 		'<a href="'.$link.'" tabindex="-1">';
	// 			$entity_markup .= 			'<div class="bbg__avatar bbg__entity__icon__image" style="background-image: url('.$imgSrc.');"></div>';
	// 			$entity_markup .= 		'</a>';
	// 			$entity_markup .= 	'</div>';
	// 			$entity_markup .= 	'<div class="bbg__entity__text">';
	// 			$entity_markup .= 		'<h2 class="bbg__entity__name"><a href="'.$link.'">'.$fullName.'</a></h2>';
	// 			$entity_markup .= 		'<p class="bbg__entity__text-description">'.$description.'</p>';
	// 			$entity_markup .= 	'</div>';
	// 			$entity_markup .= '</article>';
	// 		}
	// 	}
	// 	$entity_markup .= '</div>';
	// 	// echo $entity_markup;
	// }
	// wp_reset_postdata();
	// return $entity_markup;
// }

// CUSTOM FIELD DATA
function get_site_settings_data() {
	$intro_content = get_field('site_setting_mission_statement','options','false');
	$intro_link = get_field('site_setting_mission_statement_link', 'options', 'false');
	$site_setting_package = array('intro_content' => $intro_content, 'intro_link' => $intro_link);
	return $site_setting_package;
}

function get_homepage_banner_data() {
	$homepageBannerType = get_field('homepage_banner_type', 'option');

	if ($homepageBannerType == 'revolution_slider') {
		$bannerBackgroundPosition = get_field('homepage_banner_background_position', 'option');
		$sliderAlias = get_field('homepage_banner_revolution_slider_alias', 'option');

		echo '<section class="usa-section bbg-banner__section" style="position: relative; z-index:9990;">';
		echo do_shortcode('[rev_slider alias="' . $sliderAlias . '"]');
		echo '</section>';
	} else {
		$useRandomImage = true;
		$bannerCutline = '';
		$bannerAdjustStr = '';

		if ($homepageBannerType == 'specific_image') {
			$img = get_field( 'homepage_banner_image', 'option' );
			if ($img) {
				$attachment_id = $img['ID'];
				$useRandomImage = false;
				$featuredImageCutline='';
				$thumbnail_image = get_posts(
					array(
						'p' => $attachment_id,
						'post_type' => 'attachment'
					)
				);
				if ($thumbnail_image && isset($thumbnail_image[0])) {
					$bannerCutline=$thumbnail_image[0]->post_excerpt;
				}

				$bannerAdjustStr = '';
				$bannerBackgroundPosition = get_field( 'homepage_banner_background_position', 'option' );
				if ( $bannerBackgroundPosition ) {
					$bannerAdjustStr = $bannerBackgroundPosition;
				}
			}
		}

		// deilibarately didn't do an 'else' here in case they checked 'specific_image' without actually selecting one
		if ($useRandomImage) {
			$randomImg= getRandomEntityImage();
			$attachment_id = $randomImg['imageID'];
			$bannerCutline = $randomImg['imageCutline'];
			$bannerAdjustStr = $randomImg['bannerAdjustStr'];
		}

		$tempSources= bbgredesign_get_image_size_links( $attachment_id );
		//sources aren't automatically in numeric order.  ksort does the trick.
		ksort( $tempSources );
		$prevWidth=0;

		// Let's prevent any images with width > 1200px from being an output as part of responsive banner
		foreach( $tempSources as $key => $tempSource ) {
			if ( $key > 1900 ) {
				unset( $tempSources[$key] );
			}
		}

		echo "<style>";
		if ( $bannerAdjustStr != "" ) {
			echo "\t.bbg-banner { background-position: $bannerAdjustStr; }";
		}
		$counter=0;
		foreach( $tempSources as $key => $tempSourceObj ) {
			$counter++;
			$tempSource=$tempSourceObj['src'];
			if ( $counter == 1 ) {
				echo "\t.bbg-banner { background-image: url($tempSource) !important; }\n";
			} elseif ( $counter < count($tempSources) ) {
				echo "\t@media (min-width: " . ($prevWidth+1) . "px) and (max-width: " . $key . "px) {\n";
				echo "\t\t.bbg-banner { background-image: url($tempSource) !important; }\n";
				echo "\t}\n";
			} else {
				echo "\t@media (min-width: " . ($prevWidth+1) . "px) {\n";
				echo "\t\t.bbg-banner { background-image: url($tempSource) !important; }\n";
				echo "\t}\n";
			}
			$prevWidth = $key;
		}
		echo "</style>";

		$banner_markup  = '<div class="usa-section bbg-banner__section" style="position: relative; z-index:9990;">';
		$banner_markup .=		'<div class="bbg-banner">';
		$banner_markup .=			'<div class="bbg-banner__gradient"></div>';
		$banner_markup .=			'<div class="usa-grid bbg-banner__container--home">';
		$banner_markup .=				'<div class="bbg-social__container">';
		$banner_markup .=					'<div class="bbg-social">';
		$banner_markup .=		'</div></div></div></div>';
		$banner_markup .=		'<div class="bbg-banner__cutline usa-grid">';
		$banner_markup .=			$bannerCutline;
		$banner_markup .=	'</div></div>';
		echo $banner_markup;
	}
}

function get_impact_stories_data($numPosts) {
	global $includePortfolioDescription;

	$impactPostIDs = select_impact_story_id_at_random($postIDsUsed);
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $numPosts,
		'orderby' => 'post_date',
		'order' => 'desc',
		'post__in' => $impactPostIDs
	);
	query_posts($qParams);

	if (have_posts()) {
		while (have_posts()) { 
			the_post();
			if ($numPosts == 1) {
				$gridClass = "usa-width-one-whole";
			}
			else {
				$gridClass = "usa-width-one-half";
			}
			$includePortfolioDescription = false;
			$postIDsUsed[] = get_the_ID();

			build_impact_markup($gridClass);
		}
	}
	wp_reset_query();
}

function get_corner_hero_data() {
	//What will go in the corner hero? off (gives random quote), event, callout, advisory
	$homepage_hero_corner = get_field( 'homepage_hero_corner', 'option' );

	if ( $homepage_hero_corner  == 'event' ) {
		$featuredEvent = get_field( 'homepage_featured_event', 'option' );
	} else if ( $homepage_hero_corner == 'advisory' ) {
		$featuredAdvisory = get_field( 'homepage_featured_advisory', 'option' );
	} else if ( $homepage_hero_corner == "callout" ) {
		$featuredCallout = get_field('homepage_featured_callout', 'option');
	}

	$cornerHeroLabel = get_field( 'corner_hero_label', 'option' );
	if ( $cornerHeroLabel == '' ) {
		$cornerHeroLabel = 'This week';
	}

	if ($homepage_hero_corner == 'event' && $featuredEvent) {
		$postIDsUsed[] = $featuredEvent -> ID;
	}

	if (($homepage_hero_corner == 'event' && $featuredEvent) || ($homepage_hero_corner == 'advisory' && $featuredAdvisory)) {
		if ($homepage_hero_corner == 'event') {
			$cornerHeroPost = $featuredEvent;
		} else {
			$cornerHeroPost = $featuredAdvisory;
		}
		$cornerHeroClass = 'bbg__event-announcement';
		if (has_category('Media Advisory', $featuredEvent)) {
			$cornerHeroClass = 'bbg__advisory-announcement';
		}
		$id = $cornerHeroPost -> ID;
		$cornerHeroPermalink = get_the_permalink( $id );

		/* permalinks for future posts by default don't return properly. fix that. */
		if ($cornerHeroPost -> post_status == 'future') {
			$my_post = clone $cornerHeroPost;
			$my_post -> post_status = 'published';
			$my_post -> post_name = sanitize_title($my_post -> post_name ? $my_post -> post_name : $my_post -> post_title, $my_post -> ID);
			$cornerHeroPermalink = get_permalink($my_post);
		}

		$cornerHeroTitle = $cornerHeroPost -> post_title;
		$excerpt = my_excerpt($id);
		$corner_hero_package = array('class' => $cornerHeroClass, 'p_link' => $cornerHeroPermalink, 'label' => $cornerHeroLabel, 'title' => $cornerHeroTitle, 'excerpt' => $excerpt);

		return build_corner_hero($corner_hero_package);
	}
	// CONTINUE WORKING ON STATEMENTS BELOW
	else if ( $homepage_hero_corner == 'callout' && $featuredCallout ) {
		outputCallout($featuredCallout);
	} else {
		//in this case, either the user selected 'off' for the hero corner, or there was no valid callout/event/advisory selected
		$q = getRandomQuote( 'allEntities', $postIDsUsed );
		if ( $q ) {
			$postIDsUsed[] = $q['ID'];
			outputQuote( $q, '' );
		}
	}
}

function display_featured_post() {
	$featuredPost = get_field('homepage_featured_post', 'option');
	if ($featuredPost) {
		$featuredPost = $featuredPost[0];
		$postIDsUsed[] = $featuredPost -> ID;
	}

	if ($featuredPost) {
		$qParams = array(
			'post__in' => array($featuredPost -> ID),
			'post_status' => array('publish', 'future')
		);
	} else {
		$qParams = select_recent_post_query_params(1, $postIDsUsed, $STANDARD_POST_CATEGORY_EXCLUDES);
	}
	query_posts($qParams);

	if (have_posts()) {
		while (have_posts()) {
			the_post();
			$counter++;
			$postIDsUsed[] = get_the_ID();
			// return get_post_format();
			get_template_part('template-parts/content-excerpt-featured', get_post_format());
		}
	}
	wp_reset_query();
}

function display_additional_recent_posts() {
	$maxPostsToShow = 2;
	$qParams = select_recent_post_query_params($maxPostsToShow, $postIDsUsed, $STANDARD_POST_CATEGORY_EXCLUDES);
	query_posts($qParams);

	if (have_posts()) {
		$counter = 0;
		while (have_posts()) {
			the_post();
			$counter++;
			$postIDsUsed[] = get_the_ID();
			$gridClass = "bbg-grid--full-width";
			if ($counter > 2) {
				$includeImage = false;
				$includeMeta = false;
				$includeExcerpt = false;
				if ($counter == 3) {
					$read_more  = '</div>';
					$read_more .= '<div class="usa-width-one-half tertiary-stories">';
					$read_more .= 		'<header class="page-header">';
					$read_more .= 			'<h6 class="page-title bbg__label small">More news';
					$read_more .=  '</h6></header>';
				}
			}
			get_template_part('template-parts/content-excerpt-list', get_post_format());
		}
	}
	wp_reset_query();
}

function get_soap_box_data() {
	$soap_contents = get_field('homepage_soapbox_post', 'option');

	if ($soap) {
		$postIDsUsed[] = $soap[0] -> ID;
	}
	$soap_index = $soap_contents[0];
	$id = $soap_index -> ID;
	$soapCategory = wp_get_post_categories( $id );
	$isCEOPost = false;
	$isSpeech = false;
	$relatedProfile = false;
	$soap_class = "";
	$soapHeaderPermalink = "";
	$soapHeaderText = "";
	$soapPostPermalink = get_the_permalink( $id );
	$profilePhoto = "";
	$profileName = "";

	// ATLERNATE BYLINE OVERRIDE?
	$includeByline = get_post_meta( $id, 'include_byline', true );
	if ( $includeByline ) {
		$bylineOverride = get_post_meta( $id, 'byline_override', true );
	} else {
		$bylineOverride = "";
	}

	// CHECK FOR POSTS RELATED TO MAIN POST
	$relatedProfileID = get_post_meta( $id, 'statement_related_profile', true );
	if ( $relatedProfileID ) {
		$includeRelatedProfile = TRUE;

		$alternatePhotoID = get_post_meta( $id, 'statement_alternate_profile_image', true );
		if ( $alternatePhotoID ) {
			$profilePhotoID = $alternatePhotoID;
		} else {
			$profilePhotoID = get_post_meta( $relatedProfileID, 'profile_photo', true );
		}

		if ( $profilePhotoID ) {
			$profilePhoto = wp_get_attachment_image_src( $profilePhotoID , 'medium-thumb' );
			$profilePhoto = $profilePhoto[0];
		}

		if ( $bylineOverride !== "" ) {
			$profileName =  $bylineOverride;
		} else {
			$profileName = get_the_title( $relatedProfileID );
		}
	}

	// CHANGE "READ MORE" TEXT?
	$homepageSoapboxReadMore = get_field( 'homepage_soapbox_read_more', 'options' );
	$readMoreLabel = "READ MORE";
	if ( $homepageSoapboxReadMore != "" ) {
		$readMoreLabel = strtoupper($homepageSoapboxReadMore);
	}

	// CHANGE SOAPBOX LABEL
	$homepageSoapboxLabel = get_field( 'homepage_soapbox_label', 'options' );

	// BLUE BACKGROUND DEFAULT
	$soap_class = "bbg__voice--featured";
	if ( $homepageSoapboxLabel != "" ) {
		$soapHeaderText = $homepageSoapboxLabel;
		$soapHeaderPermalink = get_field( 'homepage_soapbox_link', 'options' );
	} else {
		/** if the user did not manually enter a soapbox label, use the categories on this post
			to decide the class (background color) as well as the header link, and in some cases the profile photo/name
		**/

		foreach ($soapCategory as $c) {
			$cat = get_category($c);
			$soapHeaderPermalink = get_category_link($cat -> term_id);
			$soapHeaderText = $cat -> name;

			if ($cat -> slug == "from-the-ceo") {
				$isCEOPost = true;
				$soap_class = "bbg__voice--ceo";
				$soapHeaderText = "From the CEO";
				// $profilePhoto = "/wp-content/media/2016/07/john_lansing_ceo-sq-200x200.jpg";
				$profilePhoto = "/wp-content/themes/bbgRedesign/img/john_lansing_ceo-sq-200x200.jpg"; //for local testing
				$profileName = "John Lansing";
			} else if ($cat -> slug == "usim-matters") {
				$isSpeech = true;
				$soap_class = "bbg__voice--guest";
			} else if ($cat -> slug == "speech" ||  $cat -> slug == "statement" || $cat -> slug == "media-advisory") {
				$isMediaAdvisory = true;
				$soap_class = "bbg__voice--featured";
			}
		}
	}

	// MANUALLY OVERRIDE SOAPBOX IMAGE
	if ($soap_contents) {
		if (wp_get_attachment_image_src($soap_contents , 'profile_photo')) {
			$profilePhoto = wp_get_attachment_image_src($soap_contents , 'profile_photo');
			$profilePhoto = $profilePhoto[0];
		}
	}

	// OVERRIDE SOAPBOX CAPTION
	$soap_contents_caption = get_field('homepage_soapbox_image_caption', 'option');
	if ($soap_contents_caption != "") {
		$profileName = $soap_contents_caption;
	}

	$mugshotImageFloat = "right";
	$soap_contents_float = get_field('homepage_soapbox_image_float', 'options');
	if ($soap_contents_float) {
		$mugshotImageFloat = $soap_contents_float;
	}

	$soapbox_data = array('post_id' => $id, 'article_class' => $soap_class, 'title' => get_the_title($id), 'header_link' => $soapHeaderPermalink, 'header_text' => $soapHeaderText, 'post_link' => $soapPostPermalink, 'profile_image' => $profilePhoto, 'profile_name' => $profileName, 'read_more' => $readMoreLabel);
	return build_soap_markup($soapbox_data);
}

// BUILDIND BLOCKS
function build_corner_hero($data) {
	$corner_hero_markup  = '<article class="bbg-portfolio__excerpt ' . $data['class'] . '">';
	$corner_hero_markup .= 	'<header class="entry-header bbg__article-icons-container">';
	$corner_hero_markup .= 		'<div class="bbg__article-icon"></div>';
	$corner_hero_markup .= 		'<h2 class="bbg__label bbg__label--outside">' . $data['label'] . '</h2>';
	$corner_hero_markup .= 	'</header>';
	$corner_hero_markup .= 	'<div class="bbg__event-announcement__content">';
	$corner_hero_markup .= 		'<header class="entry-header bbg-portfolio__excerpt-header">';
	$corner_hero_markup .= 			'<h3 class="entry-title bbg-portfolio__excerpt-title bbg__event-announcement__title">';
	$corner_hero_markup .= 				'<a href="' . $data['p_link'] . '" rel="bookmark">"' . $data['title'] . '"</a>';
	$corner_hero_markup .= 			'</h3>';
	$corner_hero_markup .= 		'</header>';
	$corner_hero_markup .= 		'<div class="entry-content bbg-portfolio__excerpt-content bbg-blog__excerpt-content bbg__event-announcement__excerpt">';
	$corner_hero_markup .= 			'<p>' . $data['excerpt'] . '</p>';
	$corner_hero_markup .= 		'</div>';
	$corner_hero_markup .= 	'</div>';
	$corner_hero_markup .= '</article>';

	return $corner_hero_markup;
}

function build_soap_markup($data) {
	// var_dump($data['profile_image']);
	// BUILD MARKUP
	$soapbox_markup  = '<article class="' . $data['article_class'] . '">';
	if (!empty($data['profile_image'])) {
		$soapbox_markup .= '<div class="usa-grid-full">';
		$soapbox_markup .= 	'<div class="usa-width-two-thirds">';
	}
	$soapbox_markup .= 	'<header class="entry-header bbg__article-icons-container">';
	if (!empty($data['post_link'])) {
		$soapbox_markup .= '<h2><a href="' . $data['header_link'] . '">' . $data['header_text'] . '</a></h2>';
	} else if (!empty($data['header_text'])) {
		$soapbox_markup .= '<h2>' . $data['header_text'] . '</h2>';
	}
	$soapbox_markup .= 	'</header>';
	$soapbox_markup .= 	'<h5 class="bbg-blog__excerpt-title"><a href="' . $data['header_link'] . '">';
	$soapbox_markup .= 		$data['title'];
	$soapbox_markup .= 	'</a></h5>';
	$soapbox_markup .= 		'<p>';
	$soapbox_markup .= 		my_excerpt($id);
	$soapbox_markup .= 		' <a href="' . $data['post_link'] . '" class="bbg__read-more">' . $data['read_more'] . ' »</a></p>';
	if (!empty($data['profile_image'])) {
		$soapbox_markup .= '</div>';
		$soapbox_markup .= '<div class="usa-width-one-third">';
		$soapbox_markup .= 	'<span class="bbg__mugshot"><img src="' . $data['profile_image'] . '" class="bbg__ceo-post__mugshot" />';
		if ($data['profile_name'] != "") {
			$soapbox_markup .= '<span class="bbg__mugshot__caption">' . $data['profile_name'] . '</span>';
		}
		$soapbox_markup .= 		'</span>';
		$soapbox_markup .= 	'</div>';
	}
	$soapbox_markup .= '</article>';

	return $soapbox_markup;
}

function build_impact_markup($divide_blocks) {
	$impact_markup  = '<div class="' . $divide_blocks . '">';
	$impact_markup .=  	'<a href="' . get_the_permalink() . '">';
	if (has_post_thumbnail()) {
		$impact_markup .= 		get_the_post_thumbnail();
	} else {
		$impact_markup .= 		'<img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="White BBG logo on medium gray background" />';
	}
	$impact_markup .= 	'</a>';
	$impact_markup .= 	'<h5><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h5>';
	$impact_markup .= 	'<p>';
	$impact_markup .= 		get_the_excerpt();
	$impact_markup .= 	'</p>';
	$impact_markup .= '</div>';
	echo $impact_markup;
}

// function build_entity_list($data) {
// 	if ($custom_query -> have_posts()) {
// 		$entity_markup = '<div class="usa-grid-full">';

// 		while ($custom_query -> have_posts())  {
// 			$custom_query -> the_post();
// 			$id = get_the_ID();
// 			$fullName = get_post_meta($id, 'entity_full_name', true);

// 			if ($fullName != "") {
// 				$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
// 				$abbreviation = str_replace("/", "",$abbreviation);
// 				$description = get_post_meta($id, 'entity_description', true);
// 				$description = apply_filters('the_content', $description);
// 				$link = get_permalink( get_page_by_path("/networks/$abbreviation/" ));
// 				$imgSrc = get_template_directory_uri() . '/img/logo_' . $abbreviation . '--circle-200.png'; //need to fix this

// 				$entity_markup .= '<article class="bbg__entity'. $columnsClass .'">';
// 				$entity_markup .= 	'<div class="bbg__avatar__container bbg__entity__icon">';
// 				$entity_markup .= 		'<a href="'.$link.'" tabindex="-1">';
// 				$entity_markup .= 			'<div class="bbg__avatar bbg__entity__icon__image" style="background-image: url('.$imgSrc.');"></div>';
// 				$entity_markup .= 		'</a>';
// 				$entity_markup .= 	'</div>';
// 				$entity_markup .= 	'<div class="bbg__entity__text">';
// 				$entity_markup .= 		'<h2 class="bbg__entity__name"><a href="'.$link.'">'.$fullName.'</a></h2>';
// 				$entity_markup .= 		'<p class="bbg__entity__text-description">'.$description.'</p>';
// 				$entity_markup .= 	'</div>';
// 				$entity_markup .= '</article>';
// 			}
// 		}
// 		$entity_markup .= '</div>';
// 		// echo $entity_markup;
// 	}
// 	wp_reset_postdata();
// 	return $entity_markup;
// }
?>