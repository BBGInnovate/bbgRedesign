<?php
/**
 * The template for displaying the CEO's posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: CEO Posts
 */

get_header();
require 'inc/bbg-functions-assemble.php';
?>

<?php
remove_filter('term_description','wpautop');

$feature_post_arg = array(
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

$feature_post_query = new WP_Query($feature_post_arg);

$feature_posts = $feature_post_query->posts;
$do_not_duplicate[] = $feature_posts[0]->ID;
wp_reset_query();
$post__not_in = ($do_not_duplicate) ? implode(',', $do_not_duplicate) : '';
?>

<main id="main" role="main">
<?php
    echo '<div class="outer-container">';
    echo     '<div class="grid-container">';
    echo         '<h2 class="section-header">' . get_the_title() . '</h2>';
    echo         '<div class="grid-container">';
    echo             build_main_head_article($feature_posts[0]);
    echo         '</div>';
    echo          '<div class="grid-container">';
    echo             do_shortcode('[ajax_load_more post__not_in="' . $post__not_in . '" scroll="false"]');
    echo          '</div>';
    echo     '</div>';
    echo '</div><!-- .outer-container -->';
?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>