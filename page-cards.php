<?php
/**
 * The template for displaying a card-based layout
 *
 * @package bbgRedesign
 * template name: Cards
 */

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
    the_post();

    $current_page_title = get_the_title();

    $page_content = do_shortcode(get_the_content());
    $page_content = apply_filters('the_content', $page_content);
}

get_header();

?>

<?php
    $featured_media_result = get_feature_media_data();
    if ($featured_media_result != '') {
        echo $featured_media_result;
    }
?>

<main id="main" class="site-content" role="main">
    <section class="outer-container">
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
            <div class="nest-container">
                <?php
                    echo getCardsRows();
                ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>