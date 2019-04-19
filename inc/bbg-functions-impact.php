<?php
	function oneImpactStory($story) {
		$url = $story['url'];
		$title = $story['title'];
		$excerpt = $story['excerpt'];
		$thumb = $story['thumb'];

		$s  = '';
		$s .= '<article class="' . implode(" ", get_post_class("bbg__article")) . '">';
		$s .=	'<a tabindex="-1" href="' . $url . '">' . $thumb . '</a>';
		$s .=	'<p class="sans"><a href="' . $url . '">' . $title . '</a></p>';
		$s .= '</article><!-- .bbg-portfolio__excerpt -->';
		return $s;
	}

	function impact_shortcode($atts) {
		$label = 'Impact Stories';
		$permalink = '';

		$impacts = array(
			'inform' => array(),
			'engage' => array(),
			'be-influential' => array()
		);

		$informDesc = "";
		$engageDesc = "";
		$beInfluentialDesc = "";

		if (isset($atts['informdesc'])) {
			$informDesc = $atts['informdesc'];
		}
		if (isset($atts['engagedesc'])) {
			$engageDesc = $atts['engagedesc'];
		}
		if (isset($atts['beinfluentialdesc'])) {
			$beInfluentialDesc = $atts['beinfluentialdesc'];
		}

		$engageCategoryID = get_cat_ID('Engage');
		$influentialCategoryID = get_cat_ID('Be Influential');
		$informCategoryID = get_cat_ID('Inform');

		$qParams = array(
			'post_type' => array('post'),
			'posts_per_page' => 100,
			'category__in' => array(
								$engageCategoryID,
								$influentialCategoryID,
								$informCategoryID
							),
			'orderby', 'date',
			'order', 'DESC'
		);


		$custom_query = new WP_Query($qParams);
		if ($custom_query -> have_posts()) {
			while ( $custom_query -> have_posts() )  {
				$custom_query->the_post();
				$id = get_the_ID();
				if( has_category('inform')) {
					$target = &$impacts['inform'];
				} else if (has_category('be-influential')) {
					$target = &$impacts['be-influential'];
				} else if (has_category('engage')) {
					$target = &$impacts['engage'];
				}
				$target[] = array(
					'url' => get_permalink($id), 
					'title'=> get_the_title($id), 
					'excerpt' => get_the_excerpt(), 
					'thumb' => get_the_post_thumbnail($id, 'small-thumb')
				);
			}
		}
		wp_reset_postdata();

		$impactPortfolioPermalink = get_permalink( get_page_by_path( 'our-work/impact-and-results/impact-portfolio/' ) );

		$s  = ''; 
		$s .= '<h5><a href="' . $impactPortfolioPermalink .'">' . $label .'</a></h5>';
		

		if (count($impacts['inform'])) {
			//https://www.bbg.gov/category/impact/inform/
			$informLink = "/category/impact/inform/";
			$s .= '<h6><a href="' . $informLink . '">Inform</a></h6>';
			$s .= '<p class="sans">' . $informDesc . '</p>';
			$s .= oneImpactStory($impacts['inform'][0]);
		} 
		if (count($impacts['engage'])) {
			//https://www.bbg.gov/category/impact/engage,inform/
			$engageLink = "/category/impact/engage,inform/";
			$s .= '<h6><a href="'. $engageLink . '">Engage</a></h6>';
			$s .= '<p class="sans">' . $engageDesc . '</p>';
			$s .= oneImpactStory($impacts['engage'][0]);

		} 
		if (count($impacts['be-influential'])) {
			//https://www.bbg.gov/category/impact/be-influential,engage,inform/
			$beInfluentialLink = "/category/impact/be-influential,engage,inform/";
			$s .= '<h6><a href="' . $beInfluentialLink . '">Be Influential</a></h6>';
			$s .= '<p class="sans">' . $beInfluentialDesc . '</p>';
			$s .= oneImpactStory($impacts['be-influential'][0]);
		}
		return $s;
	}
	add_shortcode('impact', 'impact_shortcode');
?>