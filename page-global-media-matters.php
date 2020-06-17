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
    $current_page_id = get_the_id();
    $current_page_title = get_the_title();

    wp_reset_query();
    wp_reset_postdata();
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
    </div>

    <div class="outer-container">
        <div class="grid-container">
            <div class="gmm__grid">
                <div class="gutter-sizer"></div>
            </div>
            <button type="button" class="button__load-more" name="more-matters">Load More</button>
        </div>
    </div>
</main>

<?php get_footer(); ?>