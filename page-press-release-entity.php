<?php
/**
 * The template for displaying highlights for a single entity.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Press Releases for Entity
 */

get_header();

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
    while (have_posts()) {
        the_post();
        $page_id = get_the_ID();
        $page_title = get_the_title();
    }
}
wp_reset_postdata();
wp_reset_query();

$entity_for_press_release_slug = get_post_meta($page_id, 'entity_for_press_release', true);
$entityCatObj = get_category_by_slug($entity_for_press_release_slug);
$entityCatID = $entityCatObj->term_id;

$pressReleaseObj = get_category_by_slug("press-release");
$pressReleaseID = $pressReleaseObj->term_id;

$qParams = array(
    'post_type' => 'post',
    'posts_per_page' => 1,
    'category__and' => array($entityCatID, $pressReleaseID),
    'category__not_in' => array(2280),
    'orderby', 'date',
    'order', 'DESC'
);
$feature_post_query = new WP_Query($qParams);
$feature_post = $feature_post_query->posts;
$do_not_duplicate[] = $feature_post[0]->ID;
wp_reset_query();
$post__not_in = ($do_not_duplicate) ? implode(',', $do_not_duplicate) : '';
?>

<main id="main" role="main">
    <?php
        echo '<div class="outer-container">';
        echo     '<div class="grid-container">';
        echo         '<h2 class="section-header">' . $page_title . '</h2>';

        echo         '<div class="grid-container">';
        if ($entity_for_press_release_slug == 'usagm') {
            echo         '<p class="read-more"><a href="' . get_the_permalink(
                                 get_page_by_path('news-and-information/press-releases/bbg')) . '">View Press Releases older than Aug 22, 2018</a></p>';
        }
        echo         '</div>';

        echo         '<div class="grid-container">';
        echo             build_main_head_article($feature_post[0]);
        echo         '</div>';

        echo         '<div class="grid-container">';
        echo             do_shortcode('[ajax_load_more post__not_in="' . $post__not_in .
                                 '" category__and="' . $entityCatID . ', ' . $pressReleaseID .'" category__not_in="2280" scroll="false"]');
        echo         '</div>';
        echo     '</div>';
        echo '</div><!-- .outer-container -->';
    ?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>