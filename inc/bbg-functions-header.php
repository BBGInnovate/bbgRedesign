<?php
function display_site_wide_banner() {
	$q = get_field('sitewide_alert_complex', 'option');	//off, simple, or complex

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

	$site_alert  = '<div id="dismissableBanner" class="bbg__site-alert--complex">';
	$site_alert .= 	'<div class="usa-grid-full">';
	$site_alert .= 		'<div class="bbg__banner-table">';
	$site_alert .= 			'<div class="bbg__banner-table__cell--left" style="background-image: url(' . $calloutImageURL . ');"></div>';
	$site_alert .= 			'<div class="bbg__banner-table__cell--middle">';
	$site_alert .= 				'<h3>' . $bannerTitleText . '</h3>';
	$site_alert .= 				'<h6>' . $bannerSubtitle . '</h6>';
	$site_alert .= 				'<div class="banner_readmore">';
	$site_alert .= 					'<h6><a href="';
	$site_alert .= 						$bannerPromoLink;
	$site_alert .= 						'">';
	$site_alert .= 						$bannerPromoReadCTA;
	$site_alert .= 					'</a></h6>';
	$site_alert .= 				'</div>';
	$site_alert .= 			'</div>';
	$site_alert .= 			'<div class="bbg__banner-table__cell--right">';
	$site_alert .= 				'<i id="dismissBanner" style="color:#CCC; cursor:pointer;" aria-role="button" class="fa fa-times-circle"></i>';
	$site_alert .= '</div></div></div></div>';
	$site_alert .= '<script type="text/javascript">';
	$site_alert .= 	'jQuery(document).ready(function() {';
	$site_alert .= 		'jQuery("#dismissBanner").click(function(e) {';
	$site_alert .= 			'setCookie( "richBannerDismissed", 1, 7);';
	$site_alert .= 			'jQuery("#dismissableBanner").hide();';
	$site_alert .= 		'});';
	$site_alert .= 	'});';
	$site_alert .= '</script>';
	echo $site_alert;
}

function display_splash_overlay() {
	$story_link_items = get_field('splash_story_link', 'options');

	$splash  = '<div id="splash-bg">';
	$splash .= 	'<div id="text-box" class="bbg__quotation">';
	$splash .= 		'<a id="close-splash" class="ck-set" href="javascript:void(0)">';
	$splash .= 			'<i id="dismissBanner" style="color:#CCC; cursor:pointer;" aria-role="button" class="fa fa-times-circle"></i>';
	$splash .= 		'</a>';
	$splash .= 		'<h2 class="bbg__quotation-text--large">';
	$splash .= 			get_field('splash_quote', 'option');
	$splash .= 		'</h2>';
	$splash .= 		'<a class="ck-set" href="';
	$splash .= 			$story_link_items->guid;
	$splash .= 			'"><p>';
	$splash .= 			get_field('splash_link_text', 'option');
	$splash .= 		'</p></a>';
	$splash .= 	'</div>';
	$splash .= '</div>';
	$splash .= '<script type="text/javascript">';
	$splash .= 	'jQuery(document).ready(function() {';
	$splash .= 		'jQuery(".ck-set").click(function(e) {';
	$splash .= 			'setCookie("splashPageDismissed", 1, 7);';
	$splash .= 			'jQuery("#splash-bg").hide();';
	$splash .= 		'});';
	$splash .= 	'});';
	$splash .= '</script>';
	echo $splash;
}
?>