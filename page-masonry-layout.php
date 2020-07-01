<?php
/**
 * The template for displaying cards in a masonry layout.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Masonry Layout
 */

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
    the_post();

    $current_page_id = get_the_id();
    $current_page_title = get_the_title();

    $page_content = do_shortcode(get_the_content());
    $page_content = apply_filters('the_content', $page_content);
}

$cards = array();
if (have_rows('masonry_posts', $current_page_id)) {
    while (have_rows('masonry_posts', $current_page_id)) {
        the_row();

        $thePost = get_sub_field('masonry_post');

        $card = array();

        $card['title'] = get_the_title($thePost->ID);
        $card['url'] = get_the_permalink($thePost->ID);
        $card['image'] = get_the_post_thumbnail($thePost->ID, 'medium-thumb');
        $card['excerpt'] = $thePost->post_excerpt;

        $cards[] = $card;
    }
}

$markup = '';
foreach ($cards as $card) {
    $markup .= '<div class="grid-item item-blog">';
    $markup .= '    <div class="grid-item__top">';
    if (!empty($card['image'])) {
        $markup .= '    <a href="' . $card['url'] . '">';
        $markup .=          $card['image'];
        $markup .= '    </a>';
    }
    $markup .= '    </div>';
    $markup .= '    <div class="grid-item__bottom">';
    $markup .= '        <h3>';
    $markup .= '            <a href="' . $card['url'] . '">';
    $markup .=                  $card['title'];
    $markup .= '            </a>';
    $markup .= '        </h3>';
    if (!empty($card['excerpt'])) {
        $markup .= '    <p>';
        $markup .=          $card['excerpt'];
        $markup .= '    </p>';
    }
    $markup .= '    </div>';
    $markup .= '</div>';
}

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
            <div class="masonry__grid">
                <div class="gutter-sizer"></div>
                <?php
                    echo $markup;
                ?>
            </div>
        </div>
        <script></script>
    </div>
</main>

<?php get_footer(); ?>