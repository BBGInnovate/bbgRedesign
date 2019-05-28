<?php
/**
 * Template part for displaying laws in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */

$hashtags = '';

$listsInclude = get_field('sidebar_dropdown_include', '', true);

include get_template_directory() . '/inc/shared_sidebar.php';
?>

<div class="outer-container">
	<div class="custom-grid-container">
		<div class="inner-container">
			<div class="main-content-container">
				<?php
					echo bbginnovate_post_categories();
					if($post->post_parent) {
						// Borrowed from: https://wordpress.org/support/topic/link-to-parent-page
						$parent = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
						$parent_link = get_permalink($post->post_parent);
						echo'<h2 class="section-header"><a href="' . $parent_link . '">' . $parent->post_title . '</a></h2>';
					}
					echo '<h3 class="article-title">' . get_the_title() . '</h3>';

					echo '<div>';
					$lawName = get_field('law_name');
					if ($lawName) {
						echo '<h4>' . $lawName . '</h4>';
					}
					echo 	'<p>' . get_the_content() . '</p>';
					echo '</div>';
				?>
			</div>
			<div class="side-content-container">
				<?php
					// SHARE THIS PAGE
					$share_icons = social_media_share_page(get_the_ID());
					if (!empty($share_icons)) {
						echo $share_icons;
					}

					echo '<article class="bbg__article-sidebar">';
					if ($includeSidebar && $sidebarTitle != "") {
						echo $sidebar;
					}

					if ($listsInclude) {
						echo $sidebarDownloads;
					}
					echo '</article>';
				?>
			</div>
		</div>
	</div>
</div>

<div class="outer-container">
	<footer class="entry-footer bbg-post-footer 1234">
	<?php
		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', 'bbginnovate' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<span class="edit-link">',
			'</span>'
		);
	?>
	</footer>
</div>