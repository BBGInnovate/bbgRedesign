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

		// NO 'else' IN CASE USER CHECKED 'specific_image' WITHOUT SELECTING ONE
		if ($useRandomImage) {
			$randomImg = getRandomEntityImage();
			$attachment_id = $randomImg['imageID'];
			$bannerCutline = $randomImg['imageCutline'];
			$bannerAdjustStr = $randomImg['bannerAdjustStr'];
		}

		$tempSources = bbgredesign_get_image_size_links( $attachment_id );

		// SORT SOURCES IN NUMERIC ORDER
		ksort( $tempSources );
		$prevWidth=0;

		// NO IMAGE LARGER THAN 1200px WIDE??
		foreach( $tempSources as $key => $tempSource ) {
			if ( $key > 1900 ) {
				unset( $tempSources[$key] );
			}
		}

		foreach( $tempSources as $key => $tempSourceObj ) {
			$tempSource = $tempSourceObj['src'];
		}

		$banner_data = array(
			'image_source' => $tempSource,
			'position' => $bannerAdjustStr,
			'caption' => $bannerCutline
		);
		return $banner_data;
	}
}

function get_impact_stories_data($numPosts) {
	global $includePortfolioDescription;
	global $postIDsUsed;

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

function get_featured_post_data() {
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

	$counter = 0;
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			$counter++;
			$postIDsUsed[] = get_the_ID();

			$feature_post_data = array(
				'title' => get_the_title(),
				'media' => get_feature_media_data()
			);
			return $feature_post_data;
		}
	}
	wp_reset_query();
}

function get_recent_post_data($maxPostsToShow) {
	$qParams = array(
		'post_type' => array('post'),
		'posts_per_page' => $maxPostsToShow,
		'orderby' => 'post_date',
		'order' => 'desc',
		'category__not_in' => $catExclude,
		'post__not_in' => $used,
		'tax_query' => array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => 'post-format-quote',
				'operator' => 'NOT IN'
			)
		)
	);
	return query_posts($qParams);
}

function get_soapbox_data() {
	$soapbox_toggle = get_field('soapbox_toggle', 'option');
	$soapbox_post = get_field('homepage_soapbox_post', 'option');

	if ($soapbox_post) {
		$postIDsUsed[] = $soapbox_post[0] -> ID;
	}
	$soap_index = $soapbox_post[0];
	$id = $soap_index -> ID;
	$soap_category = wp_get_post_categories( $id );
	$is_ceo_post = false;
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
	$homepage_soapbox_label = get_field( 'homepage_soapbox_label', 'options' );

	// BLUE BACKGROUND DEFAULT
	$soap_class = "bbg__voice--featured";
	if ( $homepage_soapbox_label != "" ) {
		$soapHeaderText = $homepage_soapbox_label;
		$soapHeaderPermalink = get_field('homepage_soapbox_link', 'options');
	} else {
		/** if the user did not manually enter a soapbox label, use the categories on this post
			to decide the class (background color) as well as the header link, and in some cases the profile photo/name
		**/

		foreach ($soap_category as $c) {
			$cat = get_category($c);
			$soapHeaderPermalink = get_category_link($cat -> term_id);
			$soapHeaderText = $cat -> name;

			if ($cat -> slug == "from-the-ceo") {
				$is_ceo_post = true;
				$soap_class = "bbg__voice--ceo";
				$soapHeaderText = "From the CEO";
				$profilePhoto = get_field('homepage_soapbox_image', 'options');
				$profileName = "John Lansing";
				break;
			}
			else if ($cat -> slug == "usim-matters") {
				$isSpeech = true;
				$soap_class = "bbg__voice--guest";
			} else if ($cat -> slug == "speech" ||  $cat -> slug == "statement" || $cat -> slug == "media-advisory") {
				$isMediaAdvisory = true;
				$soap_class = "bbg__voice--featured";
			}
		}
	}

	// MANUALLY OVERRIDE SOAPBOX IMAGE
	if ($soapbox_post) {
		if (wp_get_attachment_image_src($soapbox_post , 'profile_photo')) {
			$profilePhoto = wp_get_attachment_image_src($soapbox_post , 'profile_photo');
			$profilePhoto = $profilePhoto[0];
		}
	}

	// OVERRIDE SOAPBOX CAPTION
	$soap_contents_caption = get_field('homepage_soapbox_image_caption', 'option');
	if ($soap_contents_caption != "") {
		$profileName = $soap_contents_caption;
	}

	$soapbox_package = array('toggle' => $soapbox_toggle, 'post_id' => $id, 'article_class' => $soap_class, 'title' => get_the_title($id), 'header_link' => $soapHeaderPermalink, 'header_text' => $soapHeaderText, 'post_link' => $soapPostPermalink, 'profile_image' => $profilePhoto, 'profile_name' => $profileName, 'read_more' => $readMoreLabel);
	return build_soapbox_pieces($soapbox_package);
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

	$postIDsUsed = array();
	if ($homepage_hero_corner == 'event' && $featuredEvent) {
		$postIDsUsed = $featuredEvent -> ID;
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

// BUILDIND BLOCKS
function build_featured_post($feat_post_data) {
	$featured_post = '<article id="' . get_the_ID() . '">';
	$featuredImageClass = "";
	$featuredImageCutline = "";
	$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id(get_the_ID()), 'post_type' => 'attachment'));

	if ($thumbnail_image && isset($thumbnail_image[0])) {
		$featuredImageCutline = $thumbnail_image[0] -> post_excerpt;
	}
	$featured_post .= '<a href="' . $postPermalink . '" rel="bookmark">';
	$featured_post .= 		get_the_post_thumbnail();
	$featured_post .= '</a>';
	$featured_post .= '<h4><a href="' . $postPermalink . '" rel="bookmark">';
	$featured_post .= 	get_the_title();
	$featured_post .= '</a></h4>';
	if ($includeMetaFeatured) {
		$featured_post .= bbginnovate_posted_on();
	}
	$featured_post .= '<p>' . get_the_excerpt() . '</p>';
						wp_link_pages(array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bbginnovate' ),
							'after'  => '</div>',
						));
	$featured_post .= '</article>';
	echo $featured_post;
}

function build_soapbox_pieces($soap_data) {
	$toggle = $soap_data['toggle'];
	$article_class = $soap_data['article_class'];
	$soapbox_content .= '<header class="entry-header bbg__article-icons-container">';
	if (!empty($soap_data['post_link'])) {
		$soapbox_content .= '<h2><a href="' . $soap_data['header_link'] . '">' . $soap_data['header_text'] . '</a></h2>';
	} else if (!empty($soap_data['header_text'])) {
		$soapbox_content .= '<h2>' . $soap_data['header_text'] . '</h2>';
	}
	$soapbox_content .= '</header>';
	$soapbox_content .= '<h5>';
	$soapbox_content .= 	'<a href="' . $soap_data['header_link'] . '">';
	$soapbox_content .= 		$soap_data['title'];
	$soapbox_content .= 	'</a>';
	$soapbox_content .= '</h5>';
	$soapbox_content .= '<p>';
	$soapbox_content .= 	my_excerpt($soap_data['post_id']);
	$soapbox_content .= 	' <a href="' . $soap_data['post_link'] . '" class="bbg__read-more">' . $soap_data['read_more'] . ' »</a>';
	$soapbox_content .= '</p>';

	if (!empty($soap_data['profile_image'])) {
		$soapbox_image = '<img src="' . $soap_data['profile_image'] . '">';
		if ($soap_data['profile_name'] != "") {
			$soapbox_image .= '<p class="profile-name">' . $soap_data['profile_name'] . '</p>';
		}
	}
	$soapbox_pieces = array('toggle' => $toggle, 'class' => $article_class, 'content' => $soapbox_content, 'image' => $soapbox_image);
	return $soapbox_pieces;
}

function build_corner_hero($corner_hero_data) {
	$corner_hero_image = 		'<img src="' . content_url( $path = '/uploads/2018/06/usagm-touch-image.png' ) . '">';

	$corner_hero_content  = 		'<div class="bbg__article-icons-container">';
	$corner_hero_content .= 			'<h2 class="bbg__label bbg__label--outside">' . $corner_hero_data['label'] . '</h2>';
	$corner_hero_content .= 			'<div class="bbg__article-icon"></div>';
	$corner_hero_content .= 		'</div>';
	$corner_hero_content .= 		'<h5>';
	$corner_hero_content .= 			'<a href="' . $corner_hero_data['p_link'] . '" rel="bookmark">"' . $corner_hero_data['title'] . '"</a>';
	$corner_hero_content .= 		'</h5>';
	$corner_hero_content .= 		'<p>' . $corner_hero_data['excerpt'] . '</p>';

	$corner_hero_pieces = array('image' => $corner_hero_image, 'content' => $corner_hero_content);
	return $corner_hero_pieces;
}

function build_impact_markup($divide_blocks) {
	$impact_markup  = '<div class="' . $divide_blocks . '">';
	$impact_markup .=  	'<a href="' . get_the_permalink() . '">';
	if (get_the_post_thumbnail()) {
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

?>