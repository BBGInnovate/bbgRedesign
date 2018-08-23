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

$includeDescription = true;
if (isset($includePortfolioDescription) && $includePortfolioDescription == false ) {
	$includeDescription = false;
}

if (!isset($gridClass)) {
	$gridClass = "grid-half";
}

$postPermalink = esc_url(get_permalink());
if (isset($_GET['category_id'])) {
	$postPermalink = add_query_arg('category_id', $_GET['category_id'], $postPermalink);
}

echo '<article id="'. get_the_ID() . '">';
	$post_image  = '<a href="' . $postPermalink . '" rel="bookmark" tabindex="-1">';
	if (has_post_thumbnail()) {
		$post_image .= the_post_thumbnail('medium-thumb');
	} else {
		$post_image .= '<img src="' . get_template_directory_uri() . '/img/BBG-portfolio-project-default.png" alt="Missing image" />';
	}
	$post_image .= '</a>';
	echo $post_image;

	echo buildLabel(implode(get_post_class($classNames))); //check bbg-functions-utilities

	$link_header  = '<h4>';
	$link_header .= 	'<a href="' . $postPermalink . ' rel="bookmark">';
	$link_header .= 		get_the_title();
	$link_header .= 	'</a>';
	$link_header .= '</h4>';
	echo $link_header;

	if ($includeDescription) {
		echo '<div class="entry-content bbg-portfolio__excerpt-content bbg-blog__excerpt-content">';
			if (get_post_type() == 'burke_candidate') {
				$burkeNetwork = get_post_meta(get_the_ID(), 'burke_award_info_0_burke_network');
				$burkeNetwork = strtoupper($burkeNetwork[0]);
				if ($burkeNetwork == "RFERL") {
					$burkeNetwork = "RFE/RL";
				}
				$burkeReason = get_post_meta(get_the_ID(), 'burke_award_info_0_burke_reason');

				echo '<p><strong>Network:</strong> ' . $burkeNetwork . '</p>';
				echo '<p>' . $burkeReason[0] . '</p>';
			} else {
				the_excerpt();
			}

			wp_link_pages(array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bbginnovate' ),
				'after'  => '</div>',
			));
		echo '</div>';
	}
echo '</article>';

?>