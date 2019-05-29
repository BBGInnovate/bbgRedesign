<?php
function getNetworkExcerptJS() {
	/* used on map container */
	$entityParentPage = get_page_by_path('networks');
	$qParams = array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $entityParentPage->ID
	);

	$entity_group = array();
	$custom_query = new WP_Query($qParams);

	if ($custom_query -> have_posts()) {
		while ($custom_query -> have_posts())  {
			$custom_query->the_post();
			$id = get_the_ID();
			$fullName = get_post_meta($id, 'entity_full_name', true);

			if ($fullName != "") {
				$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
				$abbreviation = str_replace("/", "", $abbreviation);
				$description = get_post_meta($id, 'entity_description', true);

				//process shortcodes in description
				$description = apply_filters('the_content', $description);
   				$description = str_replace(']]>', ']]&gt;', $description);

				$link = get_permalink(get_page_by_path("/broadcasters/$abbreviation/"));
				$url = get_post_meta($id, 'entity_site_url', true);

				$imgSrc = get_template_directory_uri().'/img/logo_' . $abbreviation . '--circle-200.png'; //need to fix this
				$entity_group[$abbreviation] = array(
					'description' => $description,
					'fullName' => $fullName,
					'url' => $url
				);
			}
		}
	}
	wp_reset_postdata();
	$entity_group['usagm'] = array(
		'description' => 'The five networks of the BBG are trusted news sources, providing high-quality journalism and programming to more than 278 million people each week. They provide international, U.S. and local news in more than 100 countries and in 59 languages.',
		'url' => 'https://www.usagm.gov',
		'fullName' => 'U.S. Agency for Global Media'
	);
	$entityJson = json_encode(new ArrayValue($entity_group), JSON_PRETTY_PRINT);
	$entityJson = str_replace("\/", "/", $entityJson);

	$entity_script = '<script type="text/javascript">';
	$entity_script .= 	'entities=' . $entityJson . ';';
	$entity_script .= '</script>';

	return $entity_script;
}

function outputBroadcasters($cols = '') {
	$entityParentPage = get_page_by_path('networks');
	$qParams = array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $entityParentPage->ID,
		'orderby' => 'meta_value_num',
		'meta_key' => 'entity_year_established',
		'order' => 'ASC'
	);
	$columnsClass = "";
	if ($cols == 2){
		$columnsClass = " bbg-grid--1-1-1-2";
	}

	$entity_markup  = '<div class="sidebar-entities">';
	$custom_query = new WP_Query($qParams);
	if ($custom_query -> have_posts()) {
		while ($custom_query -> have_posts())  {
			$custom_query->the_post();
			$id = get_the_ID();
			$fullName=  get_post_meta($id, 'entity_full_name', true);
			if ($fullName != "") {
				$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
				$abbreviation = str_replace("/", "", $abbreviation);
				$description = get_post_meta($id, 'entity_description', true);
				$description = apply_filters('the_content', $description);
				$link = get_permalink(get_page_by_path("/networks/$abbreviation/"));
				$imgSrc = get_template_directory_uri() . '/img/logo_' . $abbreviation . '--circle-200.png'; //need to fix this

				$entity_markup .= '<div class="inner-container">';
				$entity_markup .= 	'<div class="entity-image-side">';
				$entity_markup .= 		'<img src="' . $imgSrc . '" alt="Entity image">';
				$entity_markup .= 	'</div>';
				$entity_markup .= 	'<div class="entity-text-side">';
				$entity_markup .= 		'<h4 class="sidebar-section-subheader">';
				$entity_markup .= 			'<a href="' . $link . '">' . $fullName . '</a>';
				$entity_markup .= 		'</h4>';
				$entity_markup .= 	'</div>';
				$entity_markup .= '</div>';
			}
		}
	}
	$entity_markup .= '</div>';

	wp_reset_postdata();
	return $entity_markup;
}

function broadcasters_list_shortcode($atts) {
	if (!empty($atts['placement'])) {
		return outputBroadcasters($atts['placement']);
	} else {
		return outputBroadcasters();
	}
}
add_shortcode('broadcasters_list', 'broadcasters_list_shortcode');

?>