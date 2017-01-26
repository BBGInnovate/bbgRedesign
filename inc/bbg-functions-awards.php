<?php
	function getAwardInfo($awardPostID, $isAwardDetailPage) {
		//$isAwardDetailPage=true means we're on the landing page for a post that has been categorized as an award
		//$isAwardDetailPage=false means that we're this information on the sidebar of a post where a user picked a related award in the sidebar

		$award = "";
		$awardTitle = get_post_meta( $awardPostID, 'standardpost_award_title', true );
		$awardYear = get_post_meta( $awardPostID, 'standardpost_award_year', true );
		$awardRecipient = get_post_meta( $awardPostID, 'standardpost_award_recipient', true );
		$awardWinningWork = get_post_meta( $awardPostID, 'standardpost_award_winning_work', true );
		$awardWinner = get_post_meta( $awardPostID, 'standardpost_award_winner', true );
		$awardLink = get_post_meta( $awardPostID, 'standardpost_award_link', true );

		$awardOrganization = get_field( 'standardpost_award_organization', $awardPostID, true);
		$awardOrganization = $awardOrganization -> name;
		$awardLogo = get_post_meta( $awardPostID, 'standardpost_award_logo', true );
		$awardOrgUrl = get_post_meta( $awardPostID, 'standardpost_award_org_url', true );
		$awardDescription = get_post_meta( $awardPostID, 'standardpost_award_description', true );
		$awardPermalink = get_permalink($awardPostID);

		$award .='<div class="bbg__sidebar__primary">';
			$awardLogoImage = "";

			if ( $awardLogo ){
				$awardLogoImage = wp_get_attachment_image_src( $awardLogo , 'small-thumb-uncropped');
				$awardLogoImage = $awardLogoImage[0];
				// $awardLogoImage = '<img src="' . $awardLogoImage . '" class="bbg__sidebar__primary-image"/>';
				$awardLogoImage = '<img src="' . $awardLogoImage . '" class="bbg__profile-excerpt__photo"/>';
			}
			// Award-winning work with link to work if it exists
			if ( $awardPermalink && $awardPermalink != "" ){
				$award .= '<h4 class="bbg__sidebar__primary-headline"><a href="' . $awardPermalink .'">' . $awardWinningWork . '</a></h4>';
			} else {
				$award .= '<h4 class="bbg__sidebar__primary-headline">' . $awardWinningWork . '</h4>';
			}

			$award .= '<p><strong>Winner: </strong>' . $awardWinner . '<br/>';
			$award .= '<strong>Network: </strong>' . $awardRecipient . '</p>';

			$award .= '<p><strong>Award: </strong>' . $awardYear . ' ' . $awardTitle . '<br/>';
			if ($awardOrgUrl != "") {
				$award .= '<strong>Presented by: </strong><a href="' . $awardOrgUrl .'">' . $awardOrganization . '</a><br/>';
			} else {
				$award .= '<strong>Presented by: </strong>' . $awardOrganization . '<br/>';
			}
			if ( $awardLink && $awardLink != "" ){
				$award .= '<h4 class="bbg__sidebar__primary-headline"><a href="' . $awardLink .'">Visit the website</a></h4>';
			} 
			
		$award .= '</div>';
		return $award;
	}
?>