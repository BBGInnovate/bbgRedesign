<?php

/*
    Script to convert Experts posts to custom post type.
*/

die();

require ('../../../../wp-load.php');

$args = array(
    'post_type' => 'post',
    'post_status' => array('publish'),
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
    'tax_query' => array(
        array (
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => array('usagm-experts')
        )
    ),
);

$profile_fields = array();
foreach (acf_get_field_groups() as $acf_group) {
    if ($acf_group['title'] == 'Profile Fields (All Profiles)') {
        $profile_fields = acf_get_fields($acf_group['ID']);
        break;
    }
}

$expert_posts = new WP_Query($args);

if ($expert_posts->have_posts()) {
    while ($expert_posts->have_posts()) {
        $expert_posts->the_post();

        $entity_cat_id_array = array();
        $categories = get_the_category();
        foreach( $categories as $category) {
            if (in_array(strtoupper($category->slug), array('VOA', 'RFERL', 'OCB', 'RFA', 'MBN', 'OTF'))) {
                $entity_cat = get_category_by_slug($category->slug);
                $entity_cat_id_array[] = $entity_cat->term_id;
            }
        }

        $post_data = array(
            'post_title' => get_the_title(),
            'post_content' => get_the_content(),
            'post_type' => 'experts',
            'post_status' => 'publish',
            'post_author' => get_the_author_meta('ID'),
            'post_category' => $entity_cat_id_array,
            'post_date' => get_the_date("Y-m-d H:i:s"),
        );

        $post_id = wp_insert_post($post_data);
        if ($post_id == 0) {
            echo 'ERROR inserting post with title: ' . $post_data['post_title'] . "\n";
        } else {
            $redirect = get_the_permalink() . ',' . get_the_permalink($post_id) . "\n";
            echo $redirect;

            foreach ($profile_fields as $profile_field) {
                $field_value = get_field($profile_field['name']);

                if (isset($field_value)) {
                    update_field($profile_field['name'], $field_value, $post_id);
                }
            }
        }
    }
}

?>