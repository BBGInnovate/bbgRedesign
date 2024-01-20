<?php
function events_shortcode($atts) {
    $attributes = shortcode_atts(array(
        'categories' => ''
    ), $atts);

    $cat_slugs = array('event');

    if (!empty($attributes['categories'])) {
        $additional_slugs = array_map('trim', explode(',', $attributes['categories']));
        $cat_slugs = array_merge($cat_slugs, $additional_slugs);
    }

    $tax_query = array('relation' => 'AND');

    foreach ($cat_slugs as $slug) {
        if (!empty($slug)) {
            $tax_query[] = array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $slug
            );
        }
    }

    $past_event_parameters = array(
        'post_type' => array('post'),
        'posts_per_page' => -1,
        'post_status' => array('publish'),
        'tax_query' => $tax_query
    );

    $past_events_query_args = $past_event_parameters;
    $past_events_query = new WP_Query($past_events_query_args);

    $pastEvents = '';
    if ($past_events_query->have_posts()) {
        $pastEvents .= '<div class="cards--container">';
        $pastEvents .= '    <div class="inner-container css--grid-3">';
        while ($past_events_query->have_posts()) {
            $past_events_query->the_post();
            $pastEvents .= getEventsCard(get_permalink(), has_post_thumbnail(), get_the_ID(), get_the_title(), get_the_date(), get_the_excerpt());
        }
        $pastEvents .= '    </div>';
        $pastEvents .= '</div>';
    }

	return $pastEvents;
}

function getEventsCard($permalink, $hasPostThumbnail, $id, $title, $date, $excerpt) {
	$event = '';
	$event .= '    <div class="cards cards--layout-general cards--size-1-3-small-small cards--events css--grid margin-top-small">';
	$event .= '        <div class="cards__fixed">';
	$event .= '            <div class="cards__wrapper">';
	$event .= '                <div class="cards__backdrop">';
	$event .= '                    <a href="' . $permalink . '">';
	if ($hasPostThumbnail) {
		$event .=                      get_the_post_thumbnail($id, 'medium-thumb');
	} else {
		$event .= '                    <img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="White USAGM logo on medium gray background" />';
	}
	$event .= '                    </a>';
	$event .= '                    <div class="cards__backdrop-shadow"></div>';
	$event .= '                </div>';
	$event .= '                <div class="cards__footer">';
	$event .= '                    <h3><a href="' . $permalink . '">' . $title . '</a></h3>';
	$event .= '                </div>';
	$event .= '            </div>';
	$event .= '        </div>';
	$event .= '        <div class="cards__flexible">';
	$event .= '            <div class="cards__excerpt">';
	$event .= '                <div class="cards__date">' . $date . '</div>';
	$event .= '                <p>' . $excerpt . '</p>';
	$event .= '            </div>';
	$event .= '        </div>';
	$event .= '    </div>';

	return $event;
}

add_shortcode('events', 'events_shortcode');
?>
