<?php
	function getAccordion() {
		$accordion = "";
		if(have_rows('accordion_items')):
			$accordion .= '<style>
			div.usa-accordion-content {
				padding:1.5rem !important;
			}
			</style>';
			$accordion .= '<div class="usa-accordion bbg__committee-list">';
			$accordion .= 	'<ul class="usa-unstyled-list">';
			$i = 0;
			while (have_rows('accordion_items')) : the_row();
				$i++;
				$itemLabel = get_sub_field('accordion_item_label');
				$itemText = get_sub_field('accordion_item_text');
				$accordion .= '<li>';
				$accordion .= 	'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $i . '">' . $itemLabel . '</button>';
				$accordion .= 	'<div id="collapsible-faq-' . $i . '" aria-hidden="true" class="usa-accordion-content">' . $itemText . '</div>';
				$accordion .= '</li>';
			endwhile;
			$accordion .= 	'</ul>';
			$accordion .= '</div>';
		endif;
		return $accordion;
	}

	function getInterviewees() {
			$interviewees_label = get_field('interviews_label');
			$interviewees_list = "";

			while (have_rows('interview_names')) : the_row();
				// BUILD LIST IN ARRAY AND ASSEMBLE BELOW
				$list_items = array();

				if (get_row_layout() == 'interview_names_internal') {
					$profileObjects = get_sub_field( 'interviewee_internal' );

					foreach ($profileObjects as $profile) {
						$url = get_permalink($profile -> ID); // Use WP object ID to get permalink for link
						$name = $profile -> post_title; // WP object title
						$title = $profile -> occupation; // custom field

						$internal_interviews  = '<li>';
						$internal_interviews .= 	'<h6>';
						$internal_interviews .= 		'<a href="' . $url . '">' . $name . '</a>';
						$internal_interviews .= 	'</h6>';
						$internal_interviews .= 	'<span class="bbg__profile-excerpt__occupation">' . $title . '</span>';
						$internal_interviews .= '</li>';
						array_push($list_items, $internal_interviews);
					}

				} elseif (get_row_layout() == 'interview_names_external') {
					$extInterviewees = get_sub_field( 'interviewee_external' );

					foreach ($extInterviewees as $extName) {
						$externalName = $extName['interviewee_name'];
						$externalTitle = $extName['interviewee_title'];
						$externalURL = $extName['interviewee_url'];

						$external_interviews  = '<li>';
						$external_interviews .= 	'<h6>';
						$external_interviews .= 		'<a href="' . $externalURL . '">' . $externalName . '</a>';
						$external_interviews .= 	'</h6>';
						$external_interviews .= 	'<span class="bbg__profile-excerpt__occupation">' . $externalTitle . '</span>';
						$external_interviews .= '</li>';
						array_push($list_items, $external_interviews);
					}
				}

				$interviewees_list .= '<div>';
				$interviewees_list .= 	'<h3 class="sidebar-section-header">' . $interviewees_label . '</h3>';
				$interviewees_list .= 	'<ul class="unstyled-list">';
				foreach ($list_items as $interview_item) {
					$interviewees_list .= 		$interview_item;
				}
				$interviewees_list .= 	'</ul>';
				$interviewees_list .= '</div>';
			endwhile;

		return $interviewees_list;
	}
?>