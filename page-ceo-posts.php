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

$tag1Slug = 'amanda-bennett';
$tag2Slug = 'usagm-ceo';

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
            'terms' => array( $tag1Slug ),
            'operator' => 'IN'
        ),
        array(
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => array( $tag2Slug ),
            'operator' => 'IN'
        )
    )
);

$feature_post_query = new WP_Query($feature_post_arg);

$feature_posts = $feature_post_query->posts;
$do_not_duplicate = [];
if (!empty($feature_posts[0]->ID)) {
    $do_not_duplicate[] = $feature_posts[0]->ID;
}
$post__not_in = ($do_not_duplicate) ? implode(',', $do_not_duplicate) : '';
wp_reset_query();

$tag1 = get_term_by('slug', $tag1Slug, 'post_tag');
$tag2 = get_term_by('slug', $tag2Slug, 'post_tag');

$tag_ids = [];

if ($tag1) {
    $tag_ids[] = $tag1->term_id;
}

if ($tag2) {
    $tag_ids[] = $tag2->term_id;
}

$tag_ids_string = implode(',', $tag_ids);

?>

<main id="main" role="main">
<?php
    echo '<div class="outer-container">';
    echo     '<div class="grid-container">';
    echo         '<h2 class="section-header">' . get_the_title() . '</h2>';
    echo         '<div class="grid-container">';
    echo             !empty($feature_posts[0]->ID) ? build_main_head_article($feature_posts[0]) : '';
    echo         '</div>';
    echo          '<div class="grid-container">';
    echo             do_shortcode('[ajax_load_more post__not_in="' . $post__not_in . '" tag__and="' . $tag_ids_string . '" scroll="false"]');
    echo          '</div>';
    echo     '</div>';
    echo '</div><!-- .outer-container -->';
?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>