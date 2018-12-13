<?php

	function getJobs() {
		$jobsFilepath = get_template_directory() . "/external-feed-cache/jobcache.json";
		if ( fileExpired( $jobsFilepath, 90 )  ) {  //1440 min = 1 day
			$jobsUrl = 'https://data.usajobs.gov/api/search?Organization=IB00';
			$apiKey = USAJOBS_API_KEY;	//USAJOBS_API_KEY is set in wp-config.php
			$host = 'data.usajobs.gov';
			$ch = curl_init();
			$timeout = 5;
			curl_setopt( $ch, CURLOPT_URL, $jobsUrl );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );

			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Authorization-Key: ' . $apiKey,
				'Host: ' . $host
			));

			$result = curl_exec($ch);
			curl_close( $ch );
			if ( $result !== false && $result != "" ) {
				file_put_contents( $jobsFilepath, $result );
			} else {
				$result = file_get_contents( $jobsFilepath );
			}
		} else {
			$result = file_get_contents( $jobsFilepath );
		}
		$jobs = json_decode( $result, true );
		return $jobs;
	}

	function dCompare( $a, $b ) {
	    $t1 = ($a['endDateTimestamp']);
	    $t2 = ($b['endDateTimestamp']);
	    return $t1 - $t2;
	}

	function outputJoblist() {
		$jobsObj = getJobs();
		$s = "";
		if ( $jobsObj['SearchResult']['SearchResultCount'] == 0 ) {
			$s = "No federal job opportunities are currently available on <a href='https://www.usajobs.gov/'>USAjobs.gov</a>.<br />";
		} else {
			$jobSearchLink = 'https://www.usajobs.gov/Search?keyword=Broadcasting+Board+of+Governors&amp;Location=&amp;AutoCompleteSelected=&amp;search=Search';
			$s = "<p class='bbg__article-sidebar__tagline'>Includes job postings from Voice of America and Office of Cuba Broadcasting. All federal job opportunities are available on <a target='_blank' href='$jobSearchLink'>USAjobs.gov</a></p>";
			$jobs = $jobsObj['SearchResult']['SearchResultItems'];
			//sort by end date, and add formatted end date
			$fixedJobs = array();
			for ( $i = 0; $i < count( $jobs ); $i++ ) {
				$j = &$jobs[$i]['MatchedObjectDescriptor'];
				$dateObj = date_parse($j['PositionEndDate']);
				$endDateTimestamp = mktime( 0, 0, 0, $dateObj['month'], $dateObj['day'], $dateObj['year'] );
				$j['formatted_end_date'] = date( 'm/d/Y', $endDateTimestamp );
				$j['endDateTimestamp'] = $endDateTimestamp;
				$fixedJobs[] = $j;
			}
			$jobs = $fixedJobs;
			usort( $jobs, 'dCompare' );
			$s .= '<table class="usa-table-borderless bbg__jobs__table">';
			$s .= '<thead><tr><th scope="col">Job</th><th scope="col">Closing date</th></tr></thead>';
			$s .= '<tbody>';

			for ( $i = 0; $i < count( $jobs ); $i++ ) {
				$j = $jobs[$i];
				$url = $j['PositionURI'];
				$title = $j['PositionTitle'];
				$endDate = $j['formatted_end_date'];
				$s .= '<tr><td><a target="_blank" href="' . $url . '" class="bbg__jobs-list__title">' . $title . '</a></td><td>' . $endDate . '</td></tr>';

			}
			$s .= '</tbody></table>';
		}
		return $s;
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
		$employee_block .= 	'<h3>Employee spotlight</h3>';
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
				$employee .= 	'<h4><a href="' . $permalink . '">' . $firstName . ' ' . $lastName . '</a></h4>';
				$employee .= 	'<p class="aside">' . $occupation . '</p>';
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