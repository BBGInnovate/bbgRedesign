<?php
/**
 * Media Clips Reference
 * Drop down showing entities for posts that are either written about or cited
 * @var string
 */


function build_media_clips_entity_dropdown($reference = NULL) {
	$network_entities = get_page_by_path('networks');
	$qParams = array(
		'post_type' => array('page'),
		'posts_per_page' => -1,
		'post_parent' => $network_entities->ID,
		'orderby' => 'meta_value_num',
		'meta_key' => 'entity_year_established',
		'order' => 'ASC'
	);

	$menu_set = array();
	foreach ($reference as $ref) {
		$entity_dropdown  = '<div class="media-clips-entities-dropdown">';
		$entity_dropdown .= 	'<ul class="unstyled-list">';
		if ($ref == "about") {
			$entity_dropdown .= 		'<li><h6>About Networks</h6></li>';
		} else if ($ref == "citation") {
			$entity_dropdown .= 		'<li><h6>Citations</h6></li>';
		}

		$custom_query = new WP_Query($qParams);
		if ($custom_query -> have_posts()) {
			$entity_dropdown .= 		'<ul>';
			if ($ref == "about") {
				$entity_dropdown .= 	'<li>';
				$entity_dropdown .= 		'<a href="' . add_query_arg('entity', 'usagm', '/press-citing-listing/') . '">USAGM</a>';
				$entity_dropdown .= 	'</li>';
			}
			while ($custom_query -> have_posts())  {
				$custom_query -> the_post();
				$id = get_the_ID();
				$fullName=  get_post_meta($id, 'entity_full_name', true);
				if ($fullName != "") {
					$abbreviation = strtolower(get_post_meta($id, 'entity_abbreviation', true));
					$abbreviation = str_replace("/", "", $abbreviation);
					$description = get_post_meta($id, 'entity_description', true);
					$description = apply_filters('the_content', $description);
					$link = get_permalink(get_page_by_path("/networks/$abbreviation/"));

					$entity_dropdown .= 	'<li>';
					$entity_dropdown .= 		'<a href="' . add_query_arg('entity', $abbreviation, '/press-citing-listing/') . '">' . $fullName . '</a>';
					$entity_dropdown .= 	'</li>';
				}
			}
			$entity_dropdown .= 		'</ul>';
		}
		$entity_dropdown .= 		'</li>';
		$entity_dropdown .= 	'</ul>';
		$entity_dropdown .= '</div>';

		array_push($menu_set, $entity_dropdown);
	}

	wp_reset_postdata();
	return $menu_set;
}

?>

<script>
	$entity_dropdown = $('#sidebar-outlet-entities');
</script>