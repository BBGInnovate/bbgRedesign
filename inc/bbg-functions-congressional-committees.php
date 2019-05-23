<?php 
	// ProPublica Congress API
	// https://projects.propublica.org/api-docs/congress-api/
	function get_congressional_committee($congress, $chamber, $committee_id, $subcommittee_id, $title) {
		$committee_title = $title;
		if (!empty($subcommittee_id)) {
			$api_url = 'https://api.propublica.org/congress/v1/' . $congress .'/'. $chamber . '/committees/' . $committee_id .'/subcommittees/'. $subcommittee_id .'.json';
		} else {
			$api_url = 'https://api.propublica.org/congress/v1/' . $congress .'/'. $chamber . '/committees/' . $committee_id .'.json';
		}

		// REQUEST DATA FROM API, RETURN STRING INSTEAD OF PRINTING ARRAY DIRECTLY TO SCREEN
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HTTPHEADER => array('X-API-Key: ' . PROPUBLICA_CONGRESS_API_KEY),
			CURLOPT_URL => $api_url
		]);
		$response = curl_exec($curl);
		curl_close($curl);

		// CONVERT JSON STRING TO ARRAY
		$response_str_to_arr = json_decode($response, true);
		$members = $response_str_to_arr['results'][0]['current_members'];

		$majority_side = [];
		$minority_side = [];
		for ($i = 0; $i < count($members); $i++) {			
			if ($members[$i]['side'] == 'majority') {
				$majority_side[] = $members[$i];
			} else {
				$minority_side[] = $members[$i];
			}
		}

		$committee_markup  = '<div class="usa-accordion bbg__committee-list">';
		$committee_markup .= 	'<ul class="usa-unstyled-list">';
		$committee_markup .= 		'<li>';
		$committee_markup .= 			'<h4 class="article-title">';
		$committee_markup .= 				'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-' . $committee_id . '">';
		$committee_markup .= 					$committee_title;
		$committee_markup .= 				'</button>';
		$committee_markup .= 			'</h4>';
		$committee_markup .= 			'<div id="collapsible-' . $committee_id . '" aria-hidden="true" class="usa-accordion-content">';
		$committee_markup .= 				'<div class="grid-container">';
		$committee_markup .= 					'<div class="grid-half">';
		$committee_markup .= 						'<span class="paragraph-header">MAJORITY (' . strtoupper($majority_side[0]['party']) . ')</span>';
		$committee_markup .= 						'<ul class="unstyled-list">';
		for ($i = 0; $i < count($majority_side); $i++) {
			$committee_markup .= 						'<li>';
			$committee_markup .= 							$majority_side[$i]['name'] . ', ' . $majority_side[$i]['state'];
			if ($majority_side[$i]['rank_in_party'] == 1) {
				$committee_markup .= 							'&mdash;' . $majority_side[$i]['note'];
			}
			$committee_markup .= 						'</li>';
		}
		$committee_markup .= 						'</ul>';
		$committee_markup .= 					'</div>';
		$committee_markup .= 					'<div class="grid-half">';
		$committee_markup .= 						'<span class="paragraph-header">MINORITY (' . strtoupper($minority_side[0]['party']) . ')</span>';
		$committee_markup .= 						'<ul class="unstyled-list">';
		for ($i = 0; $i < count($minority_side); $i++) {
			$committee_markup .= 						'<li>';
			$committee_markup .= 							$minority_side[$i]['name'] . ', ' . $minority_side[$i]['state'];
			if ($minority_side[$i]['rank_in_party'] == 1) {
				$committee_markup .= 							'&mdash;' . $minority_side[$i]['note'];
			}
			$committee_markup .= 						'</li>';
		}
		$committee_markup .= 						'</ul>';
		$committee_markup .= 					'</div>';
		$committee_markup .= 				'</div>';
		$committee_markup .= 			'</div>';
		$committee_markup .= 		'</li>';
		$committee_markup .= 	'</ul>';
		$committee_markup .= '</div>';

		return $committee_markup;
	}


	function congressional_committee_shortcode($atts) {
		$congress = $atts['congress'];
		$chamber = $atts['chamber'];
		$committee_id = $atts['committee-id'];
		if (!empty($atts['subcommittee-id'])) {
			$subcommittee_id = $atts['subcommittee-id'];
		} else {
			$subcommittee_id = NULL;
		}
		$title = $atts['title'];
		return get_congressional_committee($congress, $chamber, $committee_id, $subcommittee_id, $title);
	}
	add_shortcode('congressional_committee', 'congressional_committee_shortcode');

?>