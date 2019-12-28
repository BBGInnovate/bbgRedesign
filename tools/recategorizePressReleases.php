<?php

die();

require ('../../../../wp-load.php');

function recategorizeBbgToUsagm() {
    echo "Recategorizing BBG to USAGM\n";

    $bbgObj = get_category_by_slug("bbg");
    $usagmObj = get_category_by_slug("usagm");

    $args = array(
        'post_type' => 'post',
        'post_status' => array('publish'),
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => array(
            'operator' => 'AND',
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => array('press-release'),
            ),
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => array($bbgObj->slug),
            )
        ),
        'date_query' => array(
            'after' => array(
                'year'  => 2018,
                'month' => 8,
                'day'   => 22,
            ),
            'inclusive' => true,
        )
    );

    recategorize($args, $bbgObj->term_id, $usagmObj->term_id);
}

function recategorizeUsagmToBbg() {
    echo "Recategorizing USAGM to BBG\n";

    $bbgObj = get_category_by_slug("bbg");
    $usagmObj = get_category_by_slug("usagm");

    $args = array(
        'post_type' => 'post',
        'post_status' => array('publish'),
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'tax_query' => array(
            'operator' => 'AND',
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => array('press-release'),
            ),
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => array($usagmObj->slug),
            )
        ),
        'date_query' => array(
            'before' => array(
                'year'  => 2018,
                'month' => 8,
                'day'   => 22,
            ),
            'inclusive' => false,
        )
    );

    recategorize($args, $usagmObj->term_id, $bbgObj->term_id);
}

function recategorize($query_args, $removeIds = array(), $addIds = array()) {
    $posts = new WP_Query($query_args);

    $cat_counts = array();
    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            echo 'Modified: (' . get_the_date() . ') ' . get_the_title() . "\n";
            wp_remove_object_terms(get_the_ID(), $removeIds, 'category');
            wp_set_post_categories(get_the_ID(), $addIds, true);
        }
    }
}

recategorizeBbgToUsagm();
recategorizeUsagmToBbg();

?>