<?php
/**
 * The template for displaying Global Media Matters items
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Global Media Matters
 */

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
    the_post();

    $current_page_id = get_the_id();
    $current_page_title = get_the_title();

    $page_content = do_shortcode(get_the_content());
    $page_content = apply_filters('the_content', $page_content);
}

$customCardsArray = array();

get_header();
?>
<?php
    $featured_media_result = get_feature_media_data();
    if ($featured_media_result != '') {
        echo $featured_media_result;
    }
?>

<main id="main" role="main">
    <div class="outer-container">
        <div class="grid-container">
            <?php
                echo '<h2 class="section-header">' . $current_page_title . '</h2>';
            ?>
        </div>
        <?php
            if ($page_content != '') {
                echo '<div class="grid-container page-content">';
                echo $page_content;
                echo '</div>';
            }
        ?>
        <div class="grid-container">
            <div class="gmm__grid">
                <div class="gutter-sizer"></div>
            </div>
            <button type="button" class="button__load-more" name="more-matters">Load More</button>
        </div>
    </div>
</main>

<?php get_footer(); ?>