<?php
	function getAllQuotes( $entity, $idsToExclude ) {
		//	allEntities or rfa, rferl, voa, mbn, ocb
		if ( $entity == 'allEntities' ) {
			$qParams = array(
				'post_type' => 'quotation',
				'post_status' => 'publish',
				'order' => 'DESC',
				'post__not_in' => $idsToExclude
			);
		} else {
			$qParams = array(
				'post_type' => 'quotation',
				'post_status' => 'publish',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'category',
						'field' => 'slug',
						'terms' => array( $entity )
					),
				),
				'post__not_in' => $idsToExclude
			);
		}

		$quotes = array();
		$custom_query = new WP_Query($qParams);
		while ( $custom_query -> have_posts() )  {
			$custom_query -> the_post();
			$id = get_the_ID();
			$speaker = get_post_meta( $id, 'quotation_speaker', true );
			$quoteTagline = get_post_meta( $id, 'quotation_tagline', true );
			//$quoteDate = get_post_meta( $id, 'quotation_date', true );
			$quoteDate = get_field( 'quotation_date', $id, true );

			$quoteMugshotID = get_post_meta( get_the_ID(), 'quotation_mugshot', true );
			$quoteMugshot = '';

			if ( $quoteMugshotID ) {
				$quoteMugshot = wp_get_attachment_image_src( $quoteMugshotID , 'mugshot' );
				$quoteMugshot = $quoteMugshot[0];
			}

			// populate array with quotation posts
			$quotes[] = array(
				'ID' => $id,
				'url' => get_permalink( $id ),
				'quoteNetwork' => get_the_category( $id ),
				'quoteDate' => $quoteDate,
				'speaker' => $speaker,
				'quoteText' => get_the_content(),
				'quoteTagline' => $quoteTagline,
				'quoteMugshot' => $quoteMugshot
			);
		}
		wp_reset_postdata();
		return $quotes;
	}
	function getRandomQuote($entity, $idsToExclude, $slider = NULL) {
		//	allEntities or rfa, rferl, voa, mbn, ocb
		$allQuotes = getAllQuotes($entity, $idsToExclude);
		$returnVal = false;
		/**
		 * IF SLIDER IS NULL, ONLY SEND BACK ONE VALUE
		 * IF NOT, SEND BACK ALL VALUES TO DISPLAY ALL AT ONCE
		 */
		if (count($allQuotes) && $slider == NULL) {
			$randKey = array_rand($allQuotes);
			$returnVal = $allQuotes[$randKey];
		} elseif ($slider != NULL) {
			$returnVal = $allQuotes;
		}
		return $returnVal;
	}

	function output_quote($q, $placement = '') {
		$quoteDate = $q['quoteDate'];
		$ID = $q['ID'];
		$url = $q['url'];
		$speaker = $q['speaker'];
		$quoteText = $q['quoteText'];
		$tagline = $q['quoteTagline'];
		if ($tagline != '') {
			$tagline = '' . $tagline;
		}
		$mugshot = $q['quoteMugshot'];

		$catArray = $q['quoteNetwork'];
		$networks = array('VOA','OCB','RFE/RL','RFA','MBN');
		$networkColors = array('#1330bf','#003a8d','#EA6903','#478406','#E64C66');
		$quoteNetwork = '';

		foreach ($catArray as $cat) {
			$networkName = $cat -> cat_name;

			for ($i = 0; $i <= count($networks) - 1; $i++) {
				if ($networks[$i] == $networkName) {
					$quoteNetwork = $networkName;
					$networkColor = $networkColors[$i];
					break;
				}
			}
		}
		if ($placement == 'mention') {
			$quote_data = array(
				'color' => $networkColor,
				'network' => $quoteNetwork,
				'quote' => $quoteText,
				'image' => $mugshot,
				'speaker' => $speaker,
				'tagline' => $tagline
			);
			return $quote_data;
		}
		else {
			$quote  = '<div class="homepage-quote">';
			if ($mugshot != '') {
				$quote .= 		'<img src="' . $mugshot . '" class="quote-image" alt="' . $speaker . ' image">';
			}
			$quote .= 	'<p class="quote-line">&ldquo;' . $quoteText . '&rdquo;</p>';
			$quote .= 	'<p class="quote-name">' . $speaker . '</p>';
			$quote .= 	'<p class="quote-credit">' . $tagline . '</p>';
			$quote .= '</div>';
			return $quote;
		}
	}

	function outputCallout($q, $header) {
		$id = $q -> ID;
		$body = $q -> post_content;
		$title = $q -> post_title;
		$calloutMugshot = get_field('callout_mugshot', $id, true);
		$calloutNetwork = get_post_meta($id, 'callout_network', true);
		$callToAction = get_post_meta($id, 'callout_call_to_action', true);
		$callToActionLabel = get_post_meta($id, 'callout_action_label', true);
		$callToActionLink = get_post_meta($id, 'callout_action_link', true);

		$mugshot = $calloutMugshot['url'];
		if (isset ( $calloutMugshot['sizes']) && isset($calloutMugshot['sizes']['mugshot'])) {
			$mugshot = $calloutMugshot['sizes']['mugshot'];
		}

		if ($calloutNetwork == "") {
			$calloutNetwork = "BBG";
		}
		$colors = array(
			'VOA' =>  '#1330bf',
			'OCB' => '#003a8d',
			'RFE/RL' => '#EA6903',
			'RFA' => '#478406',
			'MBN' => '#E64C66',
			'BBG' => '#981B1E'
		);
		$networkBackgroundColor = $colors[$calloutNetwork];

		$quote = ''; 
		$quote .= '<div class="outer-container">';
		$quote .= 	'<div class="grid-container bbg__quotation">';
		if (!empty($header)) {
			$quote .= '<h2 class="bbg__quotation-text--large">' . $header . '</h2>';
		}
			$quote .= '<div class="bbg__quotation-label" style="background-color:' . $networkBackgroundColor . '">' . $calloutNetwork . '</div>';
			$quote .= '<h3 class="article-title">' . $title . '</h3>';
			$quote .= '<p>' . $body . '</p>';

			if ($mugshot != '' ||  $callToAction != '' || $callToActionLabel != '') {
				$quote .= '<hr style="width:50%; text-align:center;">';
				$quote .= '<div class="bbg__quotation-attribution__container">';
					$quote .= '<p class="bbg__quotation-attribution">';
					if ( $mugshot != '' ) {
						$quote .= '<img src="' . $mugshot . '" class="bbg__quotation-attribution__mugshot" alt="Mugshot">';
					}
					$quote .= '<span class="bbg__quotation-attribution__text">';
					if ( $callToAction != '' ) {
						$quote .= '<span class="bbg__quotation-attribution__name">' . $callToAction . '</span>';
					}
					if ( $callToActionLabel != '' ) {
						if ( $callToActionLink != '') {
							$quote .= '<span class="bbg__quotation-attribution__credit"><a href="' . $callToActionLink . '">' . $callToActionLabel . '</a></span>';
						} else {
							$quote .= '<span class="bbg__quotation-attribution__credit">' . $callToActionLink . '</span>';
						}
					} 
					$quote .= '</span></p>';
				$quote .= '</div>';
			}
			
		$quote .= 	'</div>';
		$quote .= '</div>';
		echo $quote;
	}
?>