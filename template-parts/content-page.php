<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */


require get_template_directory() . '/inc/bbg-functions-assemble.php';

$videoUrl = get_post_meta( get_the_ID(), 'featured_video_url', true );
$timelineUrl = get_post_meta( get_the_ID(), 'featured_timeline_url', true );

// EXPERIMENTING WITH ADDING THE SOCIAL SHARE CODE TO PAGES
// TITLE/HEADLINE, URL, AUTHOR'S TWITTER HANDLE
$twitterText  = "";
$twitterText .= html_entity_decode(get_the_title());
$twitterText .= " by @bbggov " . get_permalink();

$twitterURL = "//twitter.com/intent/tweet?text=" . rawurlencode( $twitterText );
$fbUrl = "//www.facebook.com/sharer/sharer.php?u=" . urlencode( get_permalink() );

// INCLUDE SIDEBAR LIST OF PEOPLE WHO WHORED ON THE PROJECT
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


include get_template_directory() . "/inc/shared_sidebar.php";

?>

<article id="post-<?php the_ID(); ?>" <?php post_class("bbg__article"); ?>>

	<?php
		check_featured_media_type();
	?><!-- .bbg__article-header__thumbnail -->

	<div class="usa-grid">
	<?php
		$page_header = '<header class="entry-header">';
		if($post -> post_parent) {
			// REFERENCE: https://wordpress.org/support/topic/link-to-parent-page
			$parent = $wpdb -> get_row( "SELECT post_title FROM $wpdb->posts WHERE ID = $post->post_parent" );
			$parent_link_data = get_permalink($post -> post_parent) ;

			$parent_link_markup  = '<h2>';
			$parent_link_markup .= 	'<a href"' . $parent_link_data . '">';
			$parent_link_markup .= 		$parent -> post_title;
			$parent_link_markup .= 	'</a>';
			$parent_link_markup .= '</h2>';

			$page_header .= $parent_link_markup;
		}

		$page_header  = 	'<h1 class="entry-title">';
		$page_header .= 		get_the_title();
		$page_header .= 	'</h1>';
		$page_header .= '</header>';
		echo $page_header;
	?>

		<!-- <div class="bbg__article-sidebar--left"> -->
		<div class="">
			<h6>Share </h6>
					<a href="<?php echo $fbUrl; ?>">
						<span class="bbg__article-share__icon facebook"></span>
					</a>
					<a href="<?php echo $twitterURL; ?>">
						<span class="bbg__article-share__icon twitter"></span>
					</a>
		</div>

		<div class="entry-content bbg__article-content <?php echo $featuredImageClass; ?>">

			<?php

			$pageTagline = get_field('page_tagline');

			if ( $pageTagline ) {
				echo "<h2>" . $pageTagline . "</h2>";
			}

			the_content();

			?>
		</div><!-- .entry-content -->


		<div class="">
			<?php
				// Right sidebar
				echo "<!-- Sidebar content -->";
				if ( $includeSidebar && $sidebarTitle != "" ) {
					echo $sidebar;
				}

				if ( $secondaryColumnContent != "" ) {
					echo $secondaryColumnContent;
				}

				echo $sidebarDownloads;

				echo $teamRoster;
				echo "Roster";
			?>
		</div><!-- .bbg__article-sidebar -->



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
	</div><!-- .usa-grid -->

	<?php if ( $timelineUrl != "" ) {
		$urlParts = parse_url($timelineUrl);
		$domain = $urlParts['host'];
		$path = $urlParts['path'];
		$urlQuery = $urlParts['query'];

		$timelineUrl = "//" . $domain . $path . "?" . $urlQuery;

		echo featured_timeline($timelineUrl);
		$hideFeaturedImage = TRUE;
	} ?>


</article><!-- #post-## -->
