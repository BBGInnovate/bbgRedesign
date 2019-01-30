<?php
/**
 * Media Clips Reference
 * Drop down showing entities for posts that are either written about or cited
 * @var string
 */


function build_media_clips_entity_dropdown($reference = NULL) {
	// KEEP ENTIRE DIV BLOCK IN THIS ARRAY
	$menu_set = array();

	// GET ALL THE NETWORK ENTITIES
	$network_entities = get_page_by_path('networks');
	$qParams = array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $network_entities->ID,
		'orderby' => 'meta_value_num',
		'meta_key' => 'entity_year_established',
		'order' => 'ASC'
	);
	$custom_query = new WP_Query($qParams);
	if ($custom_query -> have_posts()) {
		$entity_set = array();
		while ($custom_query -> have_posts())  {
			$custom_query -> the_post();
			$id = get_the_ID();
			$full_name = get_post_meta($id, 'entity_full_name', true);
			if ($full_name != "") {
				$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
				$abbreviation = str_replace("/", "", $abbreviation);
				$link = get_permalink(get_page_by_path("/networks/$abbreviation/"));

				$entity_data = array(
					'abbr' => $abbreviation,
					'link' => $link
				);
				array_push($entity_set, $entity_data);
			}
		}
	}

	// CONTSTRUCT DIV BLOCK
	$entity_dropdown  = '<div class="media-clips-entities-dropdown">';
	$entity_dropdown .= 	'<ul class="unstyled-list">';
	// LOOP THROUGH PARAMETERS
	foreach ($reference as $ref) {
		if ($ref == 'About Networks') {
			$cat_param = 'about-';
		} else if ($ref == 'Citations') {
			$cat_param = 'citation-';
		}
		$entity_dropdown .= 	'<li>';
		$entity_dropdown .= 		'<h6>';
		if ($ref != 'Of Interest') {
			$entity_dropdown .= 		'<a href="javascript:void(0)">' . strtoupper($ref) . ' <i class="fas fa-angle-down"></i></a>';
		} else {
			$entity_dropdown .= 		'<a href="' . add_query_arg('clip-type', 'interest', '/press-clipping-collections/') . '">' . strtoupper($ref) . '</a>';
		}
		$entity_dropdown .= 		'</h6>';
		if ($ref != 'Of Interest') {
			$entity_dropdown .= 	'<ul class="clipping_nest_list">';
			if ($ref == 'About Networks') {
				$entity_dropdown .= 	'<li><a href="' . add_query_arg('clip-type', 'about-usagm', '/press-clipping-collections/') . '">USAGM / BBG</a></li>';
			}
			// LOOP ENTITIES FROM THE QUERY ABOVE
			foreach ($entity_set as $entity_item) {
				$entity_dropdown .= 	'<li><a href="' . add_query_arg('clip-type', $cat_param . $entity_item['abbr'], '/press-clipping-collections/') . '">';
				if ($entity_item['abbr'] == 'rferl') {
					$entity_dropdown .= 	'RFE/RL';
				} else {
					$entity_dropdown .= 	strtoupper($entity_item['abbr']);
				}
				$entity_dropdown .= 	'</a></li>';
			}
			$entity_dropdown .= 	'</ul>';
		}
		$entity_dropdown .= 	'</li>';

		array_push($menu_set, $entity_dropdown);
	}
	$entity_dropdown .= 	'</ul>';
	$entity_dropdown .= '</div>';

	wp_reset_postdata();
	return $entity_dropdown;
}

?>