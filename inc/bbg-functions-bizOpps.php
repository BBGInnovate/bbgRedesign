<?php

function fetchSamOppsRemote($postedFromDate, $postedToDate, $resultsLimit) {

	if (!defined('SAM_GOV_API_KEY')) {
		throw new Exception('Missing SAM_GOV_API_KEY constant.');
	}

	$samGovApiKey = SAM_GOV_API_KEY;
	$samGovBase = 'https://api.sam.gov/prod/opportunities/v1/search';
	$deptName = 'UNITED STATES AGENCY FOR GLOBAL MEDIA, BBG';

	$queryParams = array(
		'api_key' => $samGovApiKey,
		'postedFrom' => $postedFromDate,
		'postedTo' => $postedToDate,
		'deptname' => $deptName,
		'limit' => $resultsLimit
	);

	$samGovUrl = $samGovBase . '?' . http_build_query($queryParams);

	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $samGovUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		if ($response === false) {
			throw new Exception('Error making request to SAM.gov: ' . curl_error($ch));
		}

		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($responseCode >= 500) {
			throw new Exception('Error making request to SAM.gov. Received response code: ' . $responseCode);
		}

		$results = json_decode($response, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception('Error decoding the JSON response from SAM.gov.');
		}

		$totalRecords = $results['totalRecords'] ?? 0;
		$opportunities = $results['opportunitiesData'] ?? array();
		if ($totalRecords === 0 || empty($opportunities)) {
			$errorCode = $results['errorCode'] ?? '';
			$errorMessage = $results['errorMessage'] ?? '';
			$errorMessageAlt = $results['error']['message'] ?? '';
			if ($errorCode && $errorMessage) {
				throw new Exception('Received error response from SAM.gov: (Error code: '. $errorCode . ') ' . $errorMessage);
			} else if ($errorMessageAlt) {
				throw new Exception('Received error response from SAM.gov: ' . $errorMessageAlt);
			} else {
				return array();
			}
		}

		return $opportunities;
	} finally {
		curl_close($ch);
	}
}

function fetchSamOpps() {
	$resultsLimit = 5;
	$relativeYear = 0;
	$yearsToSearch = 1;
	$daysInYear = 365;

	$opportunities = array();

	while ($relativeYear < $yearsToSearch && $resultsLimit > 0) {
		$relativePostedTo = 0 - ($relativeYear * $daysInYear);
		$relativePostedFrom = $relativePostedTo - ($daysInYear - 1);

		$postedFromDate = date('m/d/Y', strtotime($relativePostedFrom . ' days', time()));
		$postedToDate = date('m/d/Y', strtotime($relativePostedTo . ' days', time()));

		try {
			$moreOpportunities = fetchSamOppsRemote($postedFromDate, $postedToDate, $resultsLimit);
			for ($i = 0; $i < count($moreOpportunities) && $resultsLimit > 0; $i++) {
				array_push($opportunities, $moreOpportunities[$i]);
				$resultsLimit--;
			}
		} catch(Exception $e) {
			error_log($e->getMessage());
			return $opportunities;
		}

		$relativeYear++;
	}

	return $opportunities;
}

function getSamOpps() {
	$samOppsCacheFilepath = get_template_directory() . '/external-feed-cache/biz-opps-cache.json';
	$opportunities = array();

	if (fileExpired($samOppsCacheFilepath, 60)) {
		$opportunities = fetchSamOpps();

		if (!empty($opportunities)) {
			file_put_contents($samOppsCacheFilepath, json_encode($opportunities));
		} else {
			$opportunities = json_decode(file_get_contents($samOppsCacheFilepath), true);
		}
	} else {
		$opportunities = json_decode(file_get_contents($samOppsCacheFilepath), true);
	}

	return $opportunities;
}

function bizopps_shortcode() {
	$markup = '';
	$markup .= '<div id="biz-opps">';
	$markup .= '<img id="loading-biz-opps" src="' . get_template_directory_uri() . '/img/loading.gif" alt="loader"/>';
	$markup .= '</div>';

	return $markup;
}

function getBizOpps() {
	$samMarkup = '';
	$samMarkup .= '<table class="usa-table-borderless bbg__jobs__table">';
	$samMarkup .= '<thead><tr><th scope="col">Title</th><th scope="col" width="95">Posted On</th></tr></thead>';
	$samMarkup .= '<tbody>';

	$opportunities = getSamOpps();

	foreach ($opportunities as $opportunity) {
		$oppTitle = $opportunity['title'];
		$oppTitle = wp_trim_words($oppTitle, 10, ' ...');

		$oppLink = $opportunity['uiLink'];

		$postedDate = $opportunity['postedDate'];

		$samMarkup .= '<tr><td><a target="_blank" href="' . $oppLink . '" class="bbg__jobs-list__title">' . $oppTitle . '</a></td><td>' . $postedDate . '</td></tr>';
	}

	$samMarkup .= '</tbody>';
	$samMarkup .= '</table>';

	return $samMarkup;
}
add_shortcode('bizopps', 'bizopps_shortcode');

?>