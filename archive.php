<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 */

get_header();
require 'inc/bbg-functions-assemble.php';
?>

<?php
remove_filter('term_description','wpautop');
$term = get_queried_object();

$feature_post_arg = array(
    'post_type' => 'post',
    'posts_per_page' => 1,
    'tag__in' => $term->term_id,
);

$feature_post_query = new WP_Query($feature_post_arg);
$feature_post = $feature_post_query->posts;
$post__not_in = '';
if (!empty($feature_post)) {
    $do_not_duplicate[] = $feature_post[0]->ID;
    $post__not_in = ($do_not_duplicate) ? implode(',', $do_not_duplicate) : '';
}
wp_reset_query();
?>

<main id="main" role="main">

<?php
    if(is_tag()){
        echo '<div class="outer-container">';
        echo     '<div class="grid-container">';
        echo         '<h2 class="section-header">' . get_the_archive_title() . '</h2>';
        echo         '<div class="grid-container">';
        echo             build_main_head_article($feature_post[0]);
        echo         '</div>';
        echo          '<div class="grid-container">';
        echo             do_shortcode('[ajax_load_more post__not_in="' . $post__not_in . '" tag="' . $term->slug . '" scroll="false"]');
        echo          '</div>';
        echo     '</div>';
        echo '</div><!-- .outer-container -->';
    }
?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>