<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */
include get_template_directory() . '/inc/shared_sidebar.php';
include get_template_directory() . '/inc/sidebar-dropdown.php';
$page_id = get_the_ID();

$videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );
$timelineUrl = get_post_meta( get_the_ID(), 'featured_timeline_url', true );

// INCLUDE SIDEBAR LIST OF PEOPLE WHO WORKED ON THE PROJECT
$teamRoster = "";
if(have_rows('project_team_members')):
	$s  = '<div class="bbg__project-team">';
	$s .= 	'<h5 class="bbg__project-team__header">Project team</h5>';
	while (have_rows('project_team_members')) {
		the_row();
		if (get_row_layout() == 'team_member') {
			$team_member_name = get_sub_field('team_member_name');
			$teamMemberRole = get_sub_field('team_member_role');
			$team_member_twitter_handle = get_sub_field('team_member_twitter_handle');

			if ($team_member_twitter_handle && $team_member_twitter_handle != "") {
				$team_member_name  = '<a href="https://twitter.com/' . $team_member_twitter_handle .'">';
				$team_member_name .= 	$team_member_name;
				$team_member_name .= '</a>';
			}

			$s .= '<p>';
			$s .= 	'<span class="bbg__project-team__name">' . $team_member_name . ',</span>';
			$s .= 	'<span class="bbg__project-team__role">$teamMemberRole</span>';
			$s .= '</p>';
		}
	}
	$s .= '</div>';
	$teamRoster .= $s;
endif;
?>

<div class="outer-container">
	<div class="main-content-container">
		<?php
			if($post -> post_parent) {
				// REFERENCE: https://wordpress.org/support/topic/link-to-parent-page
				$parent = $wpdb -> get_row("SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent");
				$parent_link_data = get_permalink($post -> post_parent) ;

				$parent_link_markup  = '<h2 class="section-header">';
				$parent_link_markup .= 	'<a href"' . $parent_link_data . '">' . $parent -> post_title . '</a>';
				$parent_link_markup .= '</h2>';

				if (!empty($page_header)) {
					$page_header .= $parent_link_markup;
				}
			}

			$page_header  = 	'<h2 class="section-header">' . get_the_title() . '</h2>';
			echo $page_header;
		?>
		<?php
			if (!empty($featuredImageClass)) {
				echo '<div class="entry-content bbg__article-content' . $featuredImageClass . '">';
			} else {
				echo '<div class="entry-content bbg__article-content">';
			}
		?>
			<?php
			$pageTagline = get_field('page_tagline');
			if ( $pageTagline ) {
				echo '<h3 class="article-title">' . $pageTagline . '</h3>';
			}
			the_content();
			?>
		</div><!-- .entry-content -->

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
		</footer><!-- .entry-footer -->
	</div>
	<div class="side-content-container">
		<?php
			// SHARE THIS PAGE
			$share_icons = social_media_share_page($page_id);
			if (!empty($share_icons)) {
				echo $share_icons;
			}

			echo '<!-- SIDEBAR CONTENT -->';
			echo '<article>';
			if ($includeSidebar && $sidebarTitle != "") {
				echo $sidebar;
			}
			if (!empty($secondaryColumnContent)) {
				echo $secondaryColumnContent;
			}
			echo 	$sidebarDownloads;
			echo 	$teamRoster;
			echo '</article>';
			?>
	</div>
</div><!-- .usa-grid -->

<?php 
if ( $timelineUrl != "" ) {
	$urlParts = parse_url($timelineUrl);
	$domain = $urlParts['host'];
	$path = $urlParts['path'];
	$urlQuery = $urlParts['query'];

	$timelineUrl = '//' . $domain . $path . '?' . $urlQuery;

	echo featured_timeline($timelineUrl);
	$hideFeaturedImage = TRUE;
}
?>