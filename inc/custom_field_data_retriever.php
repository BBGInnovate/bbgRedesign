<?php
function get_marquee_data() {
	$marquee_heading = get_sub_field('marquee_heading');
	$marquee_link = get_sub_field('marquee_link');
	$marquee_content = get_sub_field('marquee_content');

	$marquee_data = array(
		'heading' => $marquee_heading, 
		'link' => $marquee_link, 
		'content' => $marquee_content
	);
	return $marquee_data;
}

function get_ribbon_data() {
	$label_text = get_sub_field('about_ribbon_label');
	$label_link = get_sub_field('about_ribbon_label_link');
	$headline_text = get_sub_field('about_ribbon_headline');
	$headline_link = get_sub_field('about_ribbon_headline_link');
	$image_url = get_sub_field('about_ribbon_image');
	$summary = get_sub_field('about_ribbon_summary');


	$ribbon_data = array(
		'label' => $label_text,
		'label_link' => $label_link,
		'headline' => $headline_text,
		'headline_link' => $headline_link,
		'image_url' => $image_url,
		'summary' => $summary
	);
}

function get_umbrella_main_data() {
	$section_heading = get_sub_field('umbrella_section_heading');
	$section_heading_link = get_sub_field('umbrella_section_heading_link');
	$force_content_labels = get_sub_field('umbrella_force_content_labels');
	$section_intro_text = get_sub_field('umbrella_section_intro_text');
	$section_content = get_sub_field('umbrella_content');

	$umbrella_data = array(
		'header' => $section_heading, 
		'header_link' => $section_heading_link, 
		'forced_label' => $force_content_labels, 
		'intro_text' => $section_intro_text,
		'content' => $section_content
	);
	return $umbrella_data;
}

function get_umbrella_content_data($content_type, $num_blocks) {
	if (get_row_layout() == 'umbrella_content_internal') {
		$pageObj = get_sub_field('umbrella_content_internal_link');
		$id = $pageObj[0]->ID;
		$link = get_the_permalink($id);
		$title = "";
		$includeTitle = get_sub_field('umbrella_content_internal_include_item_title');
		$titleOverride = get_sub_field('umbrella_content_internal_title');
		$secondaryHeadline = get_post_meta($id, 'headline', true);
		$lawName = get_post_meta($id, 'law_name', true);

		if ($includeTitle) {
			$titleOverride = get_sub_field('umbrella_content_internal_item_title');
			if ($titleOverride != "") {
				$title = $titleOverride;
			} else {
				if ($secondaryHeadline) {
					$title = $secondaryHeadline;	
				} else {
					$title = $pageObj[0]->post_title;	
				}
			}
		}

		$showFeaturedImage = get_sub_field('umbrella_content_internal_include_featured_image');
		$thumbSrc = "";
		if ($showFeaturedImage) {
			$thumbSrc = wp_get_attachment_image_src( get_post_thumbnail_id($id) , 'medium-thumb' );
			if ($thumbSrc) {
				$thumbSrc = $thumbSrc[0];
			}
		}
		
		$showExcerpt = get_sub_field('umbrella_content_internal_include_excerpt');
		$description = "";
		if ($showExcerpt) {
			$description = my_excerpt( $id );
		}

		$internal_data = array(
			'columnTitle' => get_sub_field('umbrella_content_internal_column_title'),
			'itemTitle' => $title,
			'description' => $description,
			'link' => $link, 
			'thumbSrc' => $thumbSrc,
			'gridClass' => $num_blocks,
			'forceContentLabels' => $forceContentLabels,
			'columnType' => 'internal',
			'layout' => get_sub_field('umbrella_content_internal_layout'),
			'subTitle' => $lawName
		);
		return get_umbrella_data($internal_data);
	}
	elseif (get_row_layout() == 'umbrella_content_external') {
		$thumbnail = get_sub_field('umbrella_content_external_thumbnail');
		$thumbnailID = $thumbnail['ID'];
		$thumbSrc = wp_get_attachment_image_src( $thumbnailID , 'medium-thumb' );
		if ($thumbSrc) {
			$thumbSrc = $thumbSrc[0];
		}
		$external_data = array(
			'columnTitle' => get_sub_field('umbrella_content_external_column_title'),
			'itemTitle' => get_sub_field('umbrella_content_external_item_title'),
			'description' => get_sub_field('umbrella_content_external_description'),
			'link' => get_sub_field('umbrella_content_external_link'),
			'thumbSrc' => $thumbSrc,
			'gridClass' => $num_blocks,
			'forceContentLabels' => $forceContentLabels,
			'columnType' => 'external',
			'layout' => get_sub_field('umbrella_content_external_layout'),
			'subTitle' => ''
		);
		return get_umbrella_data($external_data);
	}
	elseif (get_row_layout() == 'umbrella_content_file') {
		$fileObj = get_sub_field('umbrella_content_file_file');
		$description = get_sub_field('umbrella_content_file_description');
		$layout = get_sub_field('umbrella_content_file_layout');

		$thumbnail = get_sub_field('umbrella_content_file_thumbnail');
		$thumbnailID = $thumbnail['ID'];
		$thumbSrc = wp_get_attachment_image_src( $thumbnailID , 'medium-thumb' );
		if ($thumbSrc) {
			$thumbSrc = $thumbSrc[0];
		}
		
		$description = get_sub_field('umbrella_content_file_description');
		
		$fileTitle = get_sub_field('umbrella_content_file_item_title');
		//parse information about the file so we can append file sizeto append to our file title
		$fileID = $fileObj['ID'];
		$fileURL = $fileObj['url'];
		$file = get_attached_file( $fileID );
		$fileExt = strtoupper( pathinfo( $file, PATHINFO_EXTENSION ) ); // set extension to uppercase
		$fileSize = formatBytes( filesize( $file ) ); // file size

		$file_data = array(
			'columnTitle' => get_sub_field('umbrella_content_file_column_title'),
			'itemTitle' => $fileTitle,
			'description' => $description,
			'link' => $fileURL, 
			'thumbSrc' => $thumbSrc,
			'gridClass' => $num_blocks,
			'forceContentLabels' => $forceContentLabels,
			'columnType' => 'file',
			'layout' => $layout,
			'fileExt' => $fileExt,
			'fileSize' => $fileSize,
			'subTitle' => ''
		);
		return get_umbrella_data($file_data);
	}

}

function get_umbrella_data($atts) {
	$itemTitle = $atts['itemTitle'];
	$columnTitle = $atts['columnTitle'];
	$link = $atts['link'];
	$gridClass = $atts['gridClass'];
	$description = $atts['description'];
	$forceContentLabels = $atts['forceContentLabels'];
	$thumbPosition = "center center";
	$subTitle = $atts['subTitle'];
	$thumbSrc = $atts['thumbSrc'];
	$columnType = $atts['columnType'];
	$anchorTarget = "";
	$layout = $atts['layout'];
	$linkSuffix = "";

	if ($columnType == "file") {
		$fileSize = $atts['fileSize'];
		$fileExt = $atts['fileExt'];
		$linkSuffix = ' <span class="bbg__file-size">(' . $fileExt . ', ' . $fileSize . ')</span>';
	}
	if ($columnType == "external" || $columnType == "file") {
		$anchorTarget = " target='_blank' ";
	}

	if ($layout == 'full') {
		$col_header = "";
		if (!empty($link)) {
			$col_header = '<h2>' . $columnTitle . '</h2>';
		} else {
			$col_header  = '<h2>';
			$col_header .= 	'<a href="' . $link . '" ' . $anchorTarget . '>';
			$col_header .= 	 	$columnTitle;
			$col_header .= 	'</a>';
			$col_header .= '<h2>';
		}

		$col_image = "";
		if ($thumbSrc) {
			$col_image  = 	'<a href="' . $link . '" ' . $anchorTarget . ' rel="bookmark" tabindex="-1">';
			$col_image .= 		'<img src="' . $thumbSrc .  '">';
			$col_image .= 	'</a>';
		}

		$umbrella_package = array(
			'layout' => $layout,
			'grid' => $gridClass,
			'header' => $col_header,
			'image_source' => $thumbSrc,
			'image' => $col_image,
			'link' => $link,
			'link_tarket' => $anchorTarget,
			'title' => $itemTitle,
			'link_suffix' => $linkSuffix,
			'subtitle' => $subTitle,
			'description' => $description
		);
		return $umbrella_package;
	}
	else {
		$columnTitle = $itemTitle;
		if ($link != "") {
			$columnTitle = '<a href="' . $link . '" ' . $anchorTarget . '>' . $columnTitle . '</a>';
		}
		$columnTitle = $columnTitle . $linkSuffix;

		$umbrella_package = array(
			'layout' => $layout,
			'grid' => $gridClass,
			'title' => $columnTitle,
			'link' => $link,
			'link_target' => $anchorTarget,
			'image_source' => $thumbSrc,
			'description' => $description
		);
		return $umbrella_package;
	}
}
?>