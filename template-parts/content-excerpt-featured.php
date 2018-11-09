<?php
/**
 * Template part for displaying a featured excerpt.
 * Large full width photo and large excerpt text.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */

//The byline meta info is displayed by default
global $includeMetaFeatured;
if (!isset($includeMetaFeatured)) {
	$includeMetaFeatured = true;
}

$postPermalink = esc_url(get_permalink());


/*** the only way you should ever have a future post status here is if a future event is featured on the homepage */
if (get_post_status() == 'future') {
	global $post;
	$my_post = clone $post;
	$my_post->post_status = 'published';
	$my_post->post_name = sanitize_title($my_post->post_name ? $my_post->post_name : $my_post->post_title, $my_post->ID);
	$postPermalink = get_permalink($my_post);
}

if (isset($_GET['category_id'])) {
	$postPermalink=add_query_arg('category_id', $_GET['category_id'], $postPermalink);
}

//Add featured video
// $videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );
$videoUrl = get_field('featured_video_url');
require get_template_directory() . '/inc/bbg-functions-assemble.php';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class("bbg-blog__excerpt--featured usa-grid-full"); ?>>
	<header class="entry-header bbg-blog__excerpt-header--featured usa-grid-full">
		<?php
			$featured_media_result = get_feature_media_data();
			if ($featured_media_result != "") {
				echo $featured_media_result;
			}
		?>

		<div class="outer-container">
			<div class="grid-container">
			<?php echo '<h3>' . get_the_title() . '</h3>'; ?>
			<?php if ($includeMetaFeatured){ ?>
				<div class="entry-meta bbg__excerpt-meta bbg__excerpt-meta--featured">
					<?php bbginnovate_posted_on(); ?>
				</div>
			<?php }?>
			<p class="lead-in">
				<?php echo get_the_excerpt(); ?>
			</p>
			</div>
		</div>
	</header>

	<div class="entry-content bbg-blog__excerpt-content--featured usa-grid">
<!-- 		<p class="lead-in">
			<?php echo get_the_excerpt(); ?>
		</p> -->
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bbginnovate' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->