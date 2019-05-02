<?php
	function get_jobs() {
		$jobsFilepath = get_template_directory() . '/external-feed-cache/jobcache.json';
		if ( fileExpired( $jobsFilepath, 90 )  ) {  //1440 min = 1 day
			$jobs_url = 'https://data.usajobs.gov/api/search?Organization=IB00';
			$api_key = USAJOBS_API_KEY;	// USAJOBS_API_KEY is set in wp-config.php
			$host = 'data.usajobs.gov';
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $jobs_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Authorization-Key: ' . $api_key,
				'Host: ' . $host
			));

			$result = curl_exec($ch);
			curl_close($ch);
			if ($result !== false && $result != "") {
				file_put_contents($jobsFilepath, $result);
			} else {
				$result = file_get_contents($jobsFilepath);
			}
		} else {
			$result = file_get_contents($jobsFilepath);
		}
		$jobs = json_decode($result, true);
		return $jobs;
	}

	function dCompare($a, $b) {
	    $t1 = ($a['endDateTimestamp']);
	    $t2 = ($b['endDateTimestamp']);
	    return $t1 - $t2;
	}

	function outputJoblist() {
		$jobs_object = get_jobs();
		if ($jobs_object['SearchResult']['SearchResultCount'] == 0) {
			$jobs_table = 'No federal job opportunities are currently available on <a href="https://www.usajobs.gov/">USAjobs.gov</a>.<br>';
		} else {
			$job_search_link = 'https://www.usajobs.gov/Search?keyword=Broadcasting+Board+of+Governors&amp;Location=&amp;AutoCompleteSelected=&amp;search=Search';
			$jobs_table = '<p class="bbg__article-sidebar__tagline">';
			$jobs_table = 	'Includes job postings from Voice of America and Office of Cuba Broadcasting. All federal job opportunities are available on <a target="_blank" href="' . $job_search_link . '">USAjobs.gov</a>';
			$jobs_table = '</p>';
			$jobs = $jobs_object['SearchResult']['SearchResultItems'];
			//sort by end date, and add formatted end date
			$fixed_jobs = array();
			for ($i = 0; $i < count($jobs); $i++) {
				$j = &$jobs[$i]['MatchedObjectDescriptor'];
				$date_object = date_parse($j['PositionEndDate']);
				$endDateTimestamp = mktime( 0, 0, 0, $date_object['month'], $date_object['day'], $date_object['year'] );
				$j['formatted_end_date'] = date('m/d/Y', $endDateTimestamp);
				$j['endDateTimestamp'] = $endDateTimestamp;
				$fixed_jobs[] = $j;
			}
			$jobs = $fixed_jobs;
			usort($jobs, 'dCompare');
			$jobs_table  = '<table class="usa-table-borderless bbg__jobs__table">';
			$jobs_table .= 	'<thead>';
			$jobs_table .= 		'<tr>';
			$jobs_table .= 			'<th scope="col"><span class="sidebar-paragraph-header">Job</span>';
			$jobs_table .= 		'</th>';
			$jobs_table .= 			'<th scope="col"><span class="sidebar-paragraph-header">Closing date</span></th>';
			$jobs_table .= 		'</tr>';
			$jobs_table .= 	'</thead>';
			$jobs_table .= 	'<tbody>';

			for ($i = 0; $i < count($jobs); $i++) {
				$j = $jobs[$i];
				$url = $j['PositionURI'];
				$title = $j['PositionTitle'];
				$endDate = $j['formatted_end_date'];
				$jobs_table .= '<tr>';
				$jobs_table .= 	'<td><span class="sidebar-article-title"><a target="_blank" href="' . $url . '">' . $title . '</a></span></td>';
				$jobs_table .= 	'<td>' . $endDate . '</td>';
				$jobs_table .= '</tr>';
			}
			$jobs_table .= 	'</tbody>';
			$jobs_table .= '</table>';
		}
		return $jobs_table;
	}

	// Add shortcode to output the jobs list
	function jobs_shortcode() {
		return outputJoblist();
	}
	add_shortcode( 'jobslist', 'jobs_shortcode' );

	function outputEmployeeProfiles() {

		$qParams = array(
			'post_type' => array( 'post' ),
			'post_status' => array( 'publish' ),
			'posts_per_page' => 6,
			'cat' => get_cat_id( 'Employee' ),
		);
		$custom_query = new WP_Query( $qParams );

		$employee_block  = '<div style="margin-top: 3rem;">';
		$employee_block .= 	'<h3 class="section-subheader">Employee spotlight</h3>';
		$employee_block .= '</div>';
		$employee_block .= '<div class="nest-container">';
		$employee_block .= 	'<div class="inner-container">';
		remove_filter('the_content', 'wpautop');
		while ($custom_query -> have_posts())  {
			$custom_query -> the_post();
			$id = get_the_ID();
			$active = get_post_meta($id, 'active', true);

			if ($active) {
				$firstName = get_post_meta($id, 'first_name', true);
				$lastName = get_post_meta($id, 'last_name', true);
				$occupation = get_post_meta($id, 'occupation', true);
				$twitterProfileHandle = get_post_meta($id, 'twitter_handle', true);
				$permalink = get_the_permalink();
				$profilePhotoID = get_post_meta($id, 'profile_photo', true);
				$profilePhoto = "";
				if ($profilePhotoID) {
					$profilePhoto = wp_get_attachment_image_src($profilePhotoID , 'mugshot');
					$profilePhoto = $profilePhoto[0];
				}

				$employee  = '<div class="grid-third">';
				$employee .= 	'<a href="' . $permalink . '"><img src="' . $profilePhoto . '"></a>';
				$employee .= 	'<h4 class="article-title"><a href="' . $permalink . '">' . $firstName . ' ' . $lastName . '</a></h4>';
				$employee .= 	'<p class="sans">' . $occupation . '</p>';
				$employee .= '</div>';

				$employee_block .= $employee;
			}
		}
		$employee_block .= 	'</div>';
		$employee_block .= '</div>';
		return $employee_block;
	}

	function employee_profile_list_shortcode() {
		return outputEmployeeProfiles();
	}
	add_shortcode( 'employee_profile_list', 'employee_profile_list_shortcode' );
?>