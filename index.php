<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 */

$feature_post_arg = array(
	'post_type' => 'post',
	'posts_per_page' => 1,
	'category__not_in' => array(2276),
);
$feature_post_query = new WP_Query($feature_post_arg);   
$feature_post = $feature_post_query->posts;
$do_not_duplicate[] = $feature_post[0]->ID;
wp_reset_query();   

// LOAD USED IDs ARRAY INTO AJAX LOAD MORE PLUGIN
$post__not_in = ($do_not_duplicate) ? implode(',', $do_not_duplicate) : '';     

get_header();
?>

<main id="main" role="main">

	<div class="outer-container">
		<div class="grid-container">
			<h2 class="section-header"><?php echo single_post_title(); ?></h2>
		<?php
			echo '<div class="grid-container">';
			echo 	build_main_head_article($feature_post[0]);
			echo '</div>';

			echo '<div class="grid-container">';
			// AJAX LOAD MORE PLUGIN
			// EXCLUDE USAGM EXPERTS FROM APPEARING IN THIS LIST
			echo 	do_shortcode('[ajax_load_more post__not_in="'. $post__not_in .'" category__not_in="2280" scroll="false"]');
			echo '</div>';
		?>
		</div>
	</div>
</main>

<?php get_footer(); ?>