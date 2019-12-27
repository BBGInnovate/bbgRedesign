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

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$qParams = array(
    'post_type' => array('post'),
    'paged' => $paged,
    'posts_per_page' => 10,
    'category__and' => array($entityCatID, $pressReleaseID),
    'category__not_in' => array(2280),
    'orderby', 'date',
    'order', 'DESC'
);
$custom_query = new WP_Query($qParams);
?>

<main id="main" role="main">
    <?php
        if ($custom_query->have_posts()) {
            echo '<div class="outer-container">';
            echo     '<div class="grid-container">';
            echo         '<h2 class="section-header">' . $page_title . '</h2>';
            echo     '</div>';
            echo '</div>';

            echo '<div class="outer-container">';
            echo     '<div class="grid-container">';
            if ($entity_for_press_release_slug == 'usagm') {
                echo    '<p class="read-more"><a href="' . get_the_permalink(get_page_by_path('news-and-information/press-releases/bbg')) . '">View Press Releases older than Aug 22, 2018</a></p>';
            }
            while ($custom_query->have_posts()) {
                $custom_query->the_post();
                $article_markup  = '<article id="'. get_the_ID() . '" style="margin-bottom: 1.5rem">';
                $article_markup .= '<h3 class="article-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
                $article_markup .= '<p class="date-meta">' . get_the_date() . '</p>';
                $article_markup .= '<p>' . get_the_excerpt() . '</p>';
                $article_markup .= '</article>';
                echo $article_markup;
            }

            echo         '<nav class="navigation posts-navigation" role="navigation">';
            echo             '<h2 class="screen-reader-text">Event navigation</h2>';
            echo             '<div class="nav-links">';
            $nextLink = get_next_posts_link('Older Press Releases', $custom_query->max_num_pages);
            $prevLink = get_previous_posts_link('Newer Press Releases');
            if ($nextLink != '') {
                echo             '<div class="nav-previous">';
                echo                 $nextLink;
                echo             '</div>';
            }
            if ($prevLink != '') {
                echo             '<div class="nav-next">';
                echo                 $prevLink;
                echo             '</div>';
            }
            echo             '</div>';
            echo         '</nav>';
            echo '</div><!-- .grid-container -->';

            wp_reset_postdata();
        } else {
            get_template_part('template-parts/content', 'none');
            echo '</div><!-- .outer-container -->';
        }
    ?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>