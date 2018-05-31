<?php
/**
 * Template part for displaying a portfolio excerpt
 * 3 columns without byline or date
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
 */



global $includePortfolioDescription;
global $gridClass;

$includeDescription = TRUE;
if ( isset ($includePortfolioDescription) && $includePortfolioDescription == FALSE ) {
	$includeDescription = FALSE;
}

if ( !isset ($gridClass) ) {
	$gridClass = "bbg-grid--1-2-2";
}
$classNames = "bbg-portfolio__excerpt " . $gridClass;

$postPermalink = esc_url( get_permalink() );
if ( isset($_GET['category_id']) ) {
	$postPermalink = add_query_arg('category_id', $_GET['category_id'], $postPermalink);
}

?>

<article id="post-<?php the_ID(); ?>" <?php get_post_class($classNames); ?>>
	<header class="entry-header bbg-portfolio__excerpt-header">
		<div class="single-post-thumbnail clear bbg__excerpt-header__thumbnail--medium">
			<?php
				$image_tag_opener  = '<a href="' . $postPermalink . '" rel="bookmark" tabindex="-1">';
				if (has_post_thumbnail()) {
					$image_tag_opener .= the_post_thumbnail('medium-thumb');
				} else {
					$image_tag_opener .= 	'<img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="White BBG logo on medium gray background" />';
				}
				$image_tag_opener .= '</a>';
				echo $image_tag_opener;
			?>
		</div>
		<?php echo buildLabel( implode( get_post_class( $classNames ) ) );	//check bbg-functions-utilities ?>
		<?php
			$link_header  = '<h3>';
			$link_header .= 	'<a href="' . $postPermalink . ' rel="bookmark">';
			$link_header .= 		get_the_title();
			$link_header .= 	'</a>';
			$link_header .= '</h3>';
			echo $link_header;
		?>
	</header><!-- .entry-header -->

	<?php if ( $includeDescription ) { ?>
		<div class="entry-content bbg-portfolio__excerpt-content bbg-blog__excerpt-content">
			<?php

				if ( get_post_type() == 'burke_candidate' ) {
						$burkeNetwork = get_post_meta( get_the_ID(), 'burke_award_info_0_burke_network' );
						$burkeNetwork = strtoupper( $burkeNetwork[0] );
						if ( $burkeNetwork == "RFERL" ) {
							$burkeNetwork = "RFE/RL";
						}
						$burkeReason = get_post_meta( get_the_ID(), 'burke_award_info_0_burke_reason' );

						echo '<p><strong>Network:</strong> ' . $burkeNetwork . '</p>';
						echo '<p>' . $burkeReason[0] . '</p>';
				} else {
					the_excerpt();
				}
			?>

			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bbginnovate' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .bbg-portfolio__excerpt-title -->
	<?php } ?>

</article><!-- .bbg-portfolio__excerpt -->
