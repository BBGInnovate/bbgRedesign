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
require 'inc/bbg-functions-assemble.php';

get_header();
?>

<main id="main" role="main">

	<div class="outer-container">
		<div class="grid-container">
			<h2><?php echo single_post_title(); ?></h2>
		</div>
	</div>

	<?php 
		if (have_posts()) :
			$counter = 0;
			while (have_posts()) : the_post();
				$counter++;
				if ($counter < 4) {
					$in_sidebar = false;
				} else {
					$in_sidebar = true;
				}
				// ONLY SHOW FEATURED IF IT'S NOT PAGINATED
				if ((!is_paged() && $counter == 1)) {
					$thumbnail_image = get_posts(array('p' => get_post_thumbnail_id($id), 'post_type' => 'attachment'));
					$banner_position = get_field('adjust_the_banner_image', '', true);
					$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(700, 450), false, '');
					echo '<div class="outer-container">';
					echo 	'<div class="grid-container">';
					echo 		'<article id="post-' . get_the_ID() . '">';
					echo 			'<div class="bbg__article-header__banner" ';
					echo 				'style="background-image: url(' . $src[0] . '); background-position: ' . $banner_position . '">';
					echo 			'</div>';
					echo 		'<h4><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h4>';
					echo 		'<p class="aside date-meta">' . get_the_date() . '</p>';
					echo 		'<p>' . get_the_excerpt() . '</p>';
					echo '</article>';
					echo 	'</div>';
					echo '</div>';
				}
				if ($counter == 2) {
					echo '<div class="outer-container">';
					echo 	'<div class="custom-grid-container">';
					echo 		'<div class="inner-container">';
				}
				if ((!is_paged() && $counter > 1)) {
					if ((!is_paged() && $counter == 2) || (is_paged() && $counter == 1)) {
						echo '</div>';
						echo 	'<div class="main-content-container">';
					} elseif( (!is_paged() && $counter == 4) || (is_paged() && $counter == 3)){
						echo '</div>';
						echo '<div class="side-content-container">';
						echo 	'<h5>More news</h5>';
					}
					$article_markup  = '<article id="'. get_the_ID() . '" style="margin-bottom: 1.5rem">';
					if ($in_sidebar == false) {
						$article_markup .= '<h4><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h4>';
						$article_markup .= '<p class="aside date-meta">' . get_the_date() . '</p>';
						$article_markup .= '<p>' . get_the_excerpt() . '</p>';
					} else {
						$article_markup .= '<h6><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h6>';
					}
					$article_markup .= '</article>';
					echo $article_markup;
				}
			endwhile;
			the_posts_navigation();
			echo 		'</div>';
			echo 	'</div>';
			echo '</div>';
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
	?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>