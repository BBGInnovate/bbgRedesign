<?php
/**
 * The template for displaying Instagram links
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Instagram Links
 */

require 'inc/bbg-functions-assemble.php';

if (have_posts()) {
    the_post();

    $current_page_id = get_the_id();
    $current_page_title = get_the_title();

    $page_content = do_shortcode(get_the_content());
    $page_content = apply_filters('the_content', $page_content);
}

$qParams = array(
	'post_type'=> 'instagram_links',
	'post_status' => 'publish',
	'orderby' => 'post_date',
	'order' => 'desc',
	'posts_per_page' => -1
);

$custom_query = new WP_Query($qParams);

$instagramLinks = array();
if ($custom_query->have_posts()) {
	while ($custom_query->have_posts()) {
        $custom_query->the_post();

        $instagramLink['title'] = get_the_title();
        $thumbnailUrl = get_the_post_thumbnail_url( get_the_ID(), 'large-mugshot' );
        $thumbnailFiletype = wp_check_filetype($thumbnailUrl);
        if ($thumbnailFiletype['ext'] == 'gif') {
            $thumbnailUrl = get_the_post_thumbnail_url( get_the_ID(), 'full' );
        }
        $instagramLink['image_src'] = $thumbnailUrl;
        $instagramLink['url'] = get_post_meta( get_the_ID(), 'instagram_link_url', true );

        if ($instagramLink['image_src']) {
            $instagramLinks[] = $instagramLink;
        }
    }
}

wp_reset_postdata();
wp_reset_query();

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
            <div class="inner-container instagram-links">
            <?php
                foreach ($instagramLinks as $instagramLink) {
                    echo '<div class="grid-third instagram-link instagram-link-clears">';
                    echo     '<img src="' . $instagramLink['image_src'] . '"/>';
                    echo     '<a href="' . $instagramLink['url'] . '"/>';
                    echo         '<h3>' . $instagramLink['title'] . '</h3>';
                    echo     '</a>';
                    echo '</div>';
                }
            ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>