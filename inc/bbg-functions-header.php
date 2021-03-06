<?php
function display_splash_overlay() {
	$include_logo = get_field('include_agency_logo', 'option');
	$splash_video_url = get_field('splash_video', 'option');
	$splash_title = get_field('splash_title', 'option');
	$splash_text = get_field('splash_text', 'option');
	$splash_byline = get_field('splash_byline', 'option');
	$splash_link = get_field('splash_link', 'option');

	if (is_user_logged_in()) {
		echo '<style>#splash-bg #close-splash {top: 2em;}</style>';
	}
	$splash  = '<div id="splash-bg">';

	$splash .= 		'<a id="close-splash" class="ck-set" href="javascript:void(0)">';
	$splash .= 			'<i id="dismissBanner" class="far fa-times-circle"></i>';
	$splash .= 		'</a>';
	
	if (!empty($splash_video_url)) {
		if (strpos($splash_video_url, 'facebook.com') !== false) {
			$splash .= featured_video($splash_video_url);
		} else {
			$video_data = featured_video($splash_video_url);
			$splash .= 	'<div id="iframe-container">';
			$splash .= 		'<iframe src="' . $video_data['url'] . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
			$splash .= 	'</div>';
		}
	} else {
		echo '<style>#splash-text {margin-top: 6em;}</style>';
	}

	$splash .= 		'<div id="splash-text">';
	if (!empty($splash_title)) {
		$splash .= 		'<p id="splash-title">';
		$splash .= 			$splash_title;
		$splash .= 		'</p>';
	}
	if (!empty($splash_text)) {
		$splash .= 			'<p id="splash-description">';
		$splash .= 				$splash_text;
		$splash .= 			'</p>';
	}

	if (!empty($splash_byline)) {
		$splash .= 			'<p>';
		if (!empty($splash_link)) {
			$splash .= 			'<a id="splash-link" href="' . $splash_link . '">';
		}
		$splash .= 					$splash_byline;
		if (!empty($splash_link)) {
			$splash .= 			'</a>';
		}
		$splash .= 			'</p>';
	}

	if ($include_logo == 'yes') {
		$splash .= 			'<img src="' . get_template_directory_uri() . '/img/USAGM-BBG-logo-horiz-White-hires.png" alt="USAGM Logo">';
	}
	$splash .= 		'</div>';

	$splash .= '</div>';

	$splash .= '<script type="text/javascript">';
	$splash .= 	'jQuery(document).ready(function() {';
	$splash .= 		'var bodyHeight = jQuery(document).height();';
	$splash .= 		'jQuery("#splash-bg").height(bodyHeight);';
	$splash .= 		'jQuery(".ck-set").click(function(e) {';
	$splash .= 			'setCookie("splashPageDismissed", 1, 7);';
	$splash .= 			'jQuery("#splash-bg").hide();';
	$splash .= 			'jQuery("iframe").attr("src", "");';
	$splash .= 		'});';
	$splash .= 		'jQuery("#splash-link").click(function(e) {';
	$splash .= 			'setCookie("splashPageDismissed", 1, 7);';
	$splash .= 			'jQuery("iframe").attr("src", "");';
	$splash .= 		'});';
	$splash .= 	'});';
	$splash .= '</script>';
	echo $splash;
}

function display_site_wide_banner_if() {
	$sitewideAlert = get_field('sitewide_alert', 'option');
	$q = get_field('sitewide_alert_complex', 'option');	//off, simple, or complex
	$alert_package = '';
	if ($sitewideAlert == "complex" && (!isset( $_COOKIE['richBannerDismissed']))) {

		$alertCalloutID = $q -> ID;
		$bannerTitleText = $q -> post_title;

		if (has_post_thumbnail($alertCalloutID)) {
			$calloutImageID = get_post_thumbnail_id( $q );
			$calloutImageURL = wp_get_attachment_image_url($calloutImageID, $size = 'small-thumb', $icon = false);
		}

		$calloutNetwork = get_post_meta( $alertCalloutID, 'callout_network', true );
		$callToAction = get_post_meta( $alertCalloutID, 'callout_call_to_action', true );
		$bannerPromoReadCTA = get_post_meta( $alertCalloutID, 'callout_action_label', true );
		$bannerPromoLink = get_post_meta( $alertCalloutID, 'callout_action_link', true );
		$bannerSubtitle = my_excerpt( $alertCalloutID );
		$bannerPromoImage = wp_get_attachment_image_src( $calloutImageID , 'single-post-thumbnail');

		if ( $calloutNetwork == "" ) {
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

		$alert_complex  = '<div id="dismissableBanner" class="bbg__site-alert--complex">';
		$alert_complex .= 	'<div class="usa-grid-full">';
		$alert_complex .= 		'<div class="bbg__banner-table">';
		$alert_complex .= 			'<div class="bbg__banner-table__cell--left" style="background-image: url(' . $calloutImageURL . ');"></div>';
		$alert_complex .= 			'<div class="bbg__banner-table__cell--middle">';
		$alert_complex .= 				'<h3>' . $bannerTitleText . '</h3>';
		$alert_complex .= 				'<h6>' . $bannerSubtitle . '</h6>';
		$alert_complex .= 				'<div class="banner_readmore">';
		$alert_complex .= 					'<h6><a href="';
		$alert_complex .= 						$bannerPromoLink;
		$alert_complex .= 						'">';
		$alert_complex .= 						$bannerPromoReadCTA;
		$alert_complex .= 					'</a></h6>';
		$alert_complex .= 			'</div></div>';
		$alert_complex .= 			'<div class="bbg__banner-table__cell--right">';
		$alert_complex .= 				'<i id="dismissBanner" style="color:#CCC; cursor:pointer;" aria-role="button" class="fa fa-times-circle"></i>';
		$alert_complex .= '</div></div>';
		$alert_complex .= '<script type="text/javascript">';
		$alert_complex .= 	'jQuery(document).ready(function() {';
		$alert_complex .= 		'jQuery("#dismissBanner").click(function(e) {';
		$alert_complex .= 			'setCookie( "richBannerDismissed", 1, 7);';
		$alert_complex .= 			'jQuery("#dismissableBanner").hide();';
		$alert_complex .= 		'});';
		$alert_complex .= 	'});';
		$alert_complex .= '</script>';
		$alert_complex .= '</div></div>';
		$alert_package = array('msg' => $alert_complex, 'type' => $sitewideAlert);
	}
	else if ($sitewideAlert == "simple") {
		$sitewideAlertText = get_field( 'sitewide_alert_text', 'option' );
		$sitewideAlertLink = get_field( 'sitewide_alert_link', 'option' );
		$sitewideAlertNewWindow = get_field( 'sitewide_alert_new_window', 'option' );
		$moveUSAbannerBecauseOfAlert = " bbg__site-alert--active";

		$alert_simple  = '<div class="bbg__site-alert">';
		if ($sitewideAlertLink != "") {
			$targetStr = "";
			if ($sitewideAlertNewWindow && $sitewideAlertNewWindow != "") {
				$targetStr = " target ='_blank' ";
			}
			$alert_simple .= '<span class="bbg__site-alert__text">';
			$alert_simple .= 	'<a href="';
			$alert_simple .= 		$sitewideAlertLink;
			$alert_simple .= 		'">';
			$alert_simple .= 			$sitewideAlertText;
			$alert_simple .= '</a></span>';
		} else {
			$alert_simple .= '<span class="bbg__site-alert__text">';
			$alert_simple .= 	$sitewideAlertText;
			$alert_simple .= '</span>';
		}
		$alert_simple .= '</div>';
		$alert_package = array('msg' => $alert_simple, 'type' => $sitewideAlert);
	}
	return $alert_package;
}

function display_development_alert_banner($message) {
	$dev_alert  = '<div style="background-color:#FF0000; color:#FFFFFF; z-index:9999; position:fixed; width:100%;" class="usa-disclaimer<?php echo $moveUSAbannerBecauseOfAlert; ?>">';
	$dev_alert .= 	'<div class="usa-grid">';
	$div_alert .= 		'<span class="usa-disclaimer-official">';
	$div_alert .= 			$message;
	$div_alert .= '</div></div></span>';
}
?>