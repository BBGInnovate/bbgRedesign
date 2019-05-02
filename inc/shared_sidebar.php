<?php
/**
 * Include sidebar
 * show single downloads, quotations, external links, internal links, & photos
 * @var [boolean]
 */

$includeSidebar = get_post_meta(get_the_ID(), 'sidebar_include', true);
$sidebar = "";
$sidebarDownloads = "";

if ($includeSidebar) {
	$sidebarTitle = get_post_meta(get_the_ID(), 'sidebar_title', true);
	$sidebarDescription = get_post_meta(get_the_ID(), 'sidebar_description', true);

	$sidebar_markup = '';
	if ($sidebarTitle != '') {
		$sidebar_markup .= '<h3 class="sidebar-section-header">' . $sidebarTitle . '</h3>';
	}

	if ($sidebarDescription != "") {
		$sidebar_markup .= '<p class="sans">' . $sidebarDescription . '</p>';
	}

	if (have_rows('sidebar_items')):
		while (have_rows('sidebar_items')) : the_row();
			if (get_row_layout() == 'sidebar_download_file') {
				$sidebarDownloadTitle = get_sub_field('sidebar_download_title');
				$sidebarDownloadThumbnail = get_sub_field('sidebar_download_thumbnail');
				$sidebarDownloadLinkObj = get_sub_field('sidebar_download_link');
				$sidebarDownloadDescription = get_sub_field('sidebar_download_description', false);

				$fileID = $sidebarDownloadLinkObj['ID'];
				$sidebarDownloadLink = $sidebarDownloadLinkObj['url'];
				$file = get_attached_file($fileID);
				$ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
				$filesize = formatBytes(filesize($file));

				// GET FILE NAME IF NOT ENTERED IN CUSTOM FIELD
				if (empty($sidebarDownloadTitle)) {
					echo $sidebarDownloadTitle;
					$sidebarDownloadTitle = $sidebarDownloadLinkObj['post_title'];
				}

				$sidebarImage = '';
				if ($sidebarDownloadThumbnail && $sidebarDownloadThumbnail != "") {
					$sidebarImage = '<img src="' . $sidebarDownloadThumbnail . '" alt="Thumbnail image for download" style="margin-bottom: 0.5rem;">';
				}

				$sidebar_download  = '<article>';
				$sidebar_download .= 	'<a target="_blank" href="' . $sidebarDownloadLink . '">' . $sidebarImage . '</a>';
				$sidebar_download .= 	'<h3 class="sidebar-article-title"><a target="_blank" href="' . $sidebarDownloadLink . '">' . $sidebarDownloadTitle . '</a></h3>';
				$sidebar_download .= 	'<span class="bbg__file-size"><br>(' . $ext . ', ' . $filesize . ')</span>';
				
				if ($sidebarDownloadDescription && $sidebarDownloadDescription != "") {
					$sidebar_download .= '<p class="sans">';
					$sidebar_download .= 	$sidebarDownloadDescription;
					$sidebar_download .= '</p>';
				}
				$sidebar_download .= '</article>';

				$sidebar_markup .= $sidebar_download;
			} else if (get_row_layout() == 'sidebar_quote') {
				$sidebarQuotationText = get_sub_field('sidebar_quotation_text', false);
				$sidebarQuotationSpeaker = get_sub_field('sidebar_quotation_speaker');
				$sidebarQuotationSpeakerTitle = get_sub_field('sidebar_quotation_speaker_title');

				$quote_markup  = '<article class="bbg__quotation">';
				$quote_markup .= 	'<p>&quot;' . $sidebarQuotationText . '&quot;</p>';
				$quote_markup .= 	'<p class="sans">';
				$quote_markup .= 		'&mdash;' . $sidebarQuotationSpeaker . '<br>';
				$quote_markup .= 		$sidebarQuotationSpeakerTitle;
				$quote_markup .= 	'</p>';
				$quote_markup .= '</article>';

				$sidebar_markup .= $quote_markup;
			} else if (get_row_layout() == 'sidebar_external_link') {
				$sidebarLinkTitle = get_sub_field('sidebar_link_title', false);
				$sidebarLinkLink = get_sub_field('sidebar_link_link');
				$sidebarLinkImage = get_sub_field('sidebar_link_image');
				$sidebarLinkDescription = get_sub_field('sidebar_link_description', false);

				$external_links  = '<aside>';
				if ($sidebarLinkImage && $sidebarLinkImage != "") {
					$external_links .= '<a target="blank" href="' . $sidebarLinkLink . '">';
					$external_links .= 		'<img class="sans" src="' . $sidebarLinkImage['sizes']['medium'] . '" alt="Image link">';
					$external_links .= '</a>';
				}
				$external_links .= 		'<h4 class="sidebar-article-title">';
				$external_links .= 			'<a target="blank" href="' . $sidebarLinkLink . '">' . $sidebarLinkTitle . '</a>';
				$external_links .= 		'</h4>';

				if ($sidebarLinkDescription != "") {
					$external_links .= '<p class="sans">';
					$external_links .= 		$sidebarLinkDescription;
					$external_links .= '</p>';
				}
				$external_links .= '</aside>';

				$sidebar_markup .= $external_links;
			} else if (get_row_layout() == 'sidebar_internal_link') {
				$sidebarInternalTitle = get_sub_field('sidebar_internal_title', false);
				$sidebarInternalLocation = get_sub_field('sidebar_internal_location');
				$sidebarInternalDescription = get_sub_field('sidebar_internal_description', false);

				$internal_links  = '<aside>';
				$internal_links .= 	'<h4 class="sidebar-article-title">';
				$internal_links .= 		'<a href="' . get_permalink($sidebarInternalLocation -> ID) . '">';
				if ($sidebarInternalTitle && $sidebarInternalTitle != "") {
					$internal_links .= $sidebarInternalTitle;
				} else {
					$internal_links .= $sidebarInternalLocation -> post_title;
				}
				$internal_links .= 		'</a>';
				$internal_links .= 	'</h4>';
				if (!empty($sidebarInternalDescription)) {
					$internal_links .= 	$sidebarInternalDescription;
				}
				$internal_links .= '</aside>';
				$sidebar_markup .= $internal_links;
			} else if (get_row_layout() == 'sidebar_photo') {
				$sidebarPhotoImage = get_sub_field('sidebar_photo_image');
				$sidebarPhotoTitle = get_sub_field('sidebar_photo_title', false);
				$sidebarPhotoCaption = get_sub_field('sidebar_photo_caption', false);

				$sidebarImage = "";
				if ($sidebarPhotoImage && $sidebarPhotoImage != "") {
					$sidebarPhotoImageSrc = $sidebarPhotoImage['sizes']['medium'];
					$sidebarImage = '<img src="' . $sidebarPhotoImageSrc . '" alt="Sidebar image">';
				}

				/*
				helpful for debugging
				var_dump($sidebarPhotoImage);
				foreach ($sidebarPhotoImage as $key=>$value) {
					echo "$key -> $value<BR>";
					if ($key == 'sizes') {
						var_dump($value);
					}
				}
				var_dump($sidebarPhotoImage['sizes']);
				*/

				$sidebarImageTitle = '';
				if ($sidebarPhotoTitle && $sidebarPhotoTitle != "") {
					$sidebarImageTitle = '<p class="sans" style="margin: 0;"><strong>' . $sidebarPhotoTitle . '</strong></p>';
				}

				$sidebarDescription = "";
				if ($sidebarPhotoCaption && $sidebarPhotoCaption != ""){
					$sidebarDescription = '<p class="sans">' . $sidebarPhotoCaption . '</p>';
				}

				$sidebar_markup .= '<article>' . $sidebarImage . $sidebarImageTitle . $sidebarDescription . '</article>';
			} else if (get_row_layout() == 'sidebar_accordion') {
				$accordion = '';
				$accordionTitle = get_sub_field('sidebar_accordion_title');
				if ($accordionTitle != "") {
					$accordion .= '<h3 class="sidebar-section-header">' . $accordionTitle . '</h3>';
				}
				if(have_rows('sidebar_accordion_items')) {
					$accordion .= '<style>';
					$accordion .= 	'div.usa-accordion-content {padding:1.5rem !important;}';
					$accordion .= '</style>';

					$accordion .= '<div class="usa-accordion bbg__committee-list">';
					$accordion .= 		'<ul class="unstyled-list">';
					$i = 0;
					while (have_rows('sidebar_accordion_items')) : the_row();
						$i++;
						$itemLabel = get_sub_field('sidebar_accordion_item_label');
						$itemText = get_sub_field('sidebar_accordion_item_text');

						$accordion .= 	'<li>';
						$accordion .= 		'<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $i . '">' . $itemLabel . '</button>';
						$accordion .= 		'<div id="collapsible-faq-' . $i . '" aria-hidden="true" class="usa-accordion-content">';
						$accordion .= 			$itemText;
						$accordion .= 		'</div>';
						$accordion .= 	'</li>';
					endwhile;
					$accordion .= 	'</ul>';
					$accordion .= '</div>';

					$sidebar_markup .= $accordion;
				}
			} else if (get_row_layout() == 'sidebar_related_award') {
				$relatedPosts = get_sub_field('sidebar_related_award_post');
				
				if (is_array($relatedPosts) && count($relatedPosts) > 0) {
					$label = "About the Award";
					if (count($relatedPosts) > 1) {
						$label .= "s";
					}

					$sidebar_markup .= '<h3 class="sidebar-section-header">' . $label . '</h3>';
					$sidebar_markup .= '<article class="sans">';
					$counter = 0;
					foreach ($relatedPosts as $relatedPost) {
						$counter++;
						if ($counter > 1) {
							$sidebar_markup .= "<br />";
						}
						$sidebar_markup .= getAwardInfo($relatedPost -> ID, false);
					}
					$sidebar_markup .=  '</article>';
				}
			} else if (get_row_layout() == 'sidebar_twitter_widget') {
				$widgetID = get_sub_field('sidebar_twitter_widget_id');
				$widgetHashtag = get_sub_field('sidebar_twitter_widget_hashtag');
				$widgetAuthor = get_sub_field('sidebar_twitter_widget_author');

				if ($widgetHashtag || $widgetAuthor) {
					if ($widgetAuthor) {
						$widgetLink = "https://twitter.com/$widgetAuthor";
						$widgetLinkLabel = '@' . $widgetAuthor;
					} else {
						$widgetLink = "https://twitter.com/hashtag/$widgetHashtag";
						$widgetLinkLabel = '#' . $widgetHashtag;
					}
					$sidebar_markup .= '<h3 class="sidebar-section-header">Follow on Twitter</h3>';
					$sidebar_markup .= '<ul class="bbg__article-share unstyled-list">';
					$sidebar_markup .= 	'<li class="bbg__article-share__link twitter">';
					$sidebar_markup .= 		'<a href="' . $widgetLink . '" title="Follow on Twitter"><span class="bbg__article-share__icon twitter"></span><span class="">' . $widgetLinkLabel . '</span></a>';
					$sidebar_markup .= 	'</li>';
					$sidebar_markup .= '</ul>';

					if ($widgetAuthor) {
						$sidebar_markup .= '<a data-tweet-limit="2" data-show-replies="false" data-chrome="noheader nofooter noborders transparent noscrollbar" data-dnt="true" data-theme="light" class="twitter-timeline" href="https://twitter.com/' . $widgetAuthor . '">Tweets by ' . $widgetAuthor . '</a> <script type="text/javascript" async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
					}
					else if ($widgetHashtag) {
						$sidebar_markup .= '<a data-chrome="noheader" class="twitter-timeline"  href="https://twitter.com/hashtag/' . $widgetHashtag . '" data-widget-id="' . $widgetID . '">#' . $widgetHashtag . ' Tweets</a>';
						$sidebar_markup .= '<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?';
						$sidebar_markup .= "'http':'https'";
						$sidebar_markup .= ';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
					}
				}
			} else if (get_row_layout() == 'sidebar_accordion_dynamic') {
				$taglist = get_sub_field('sidebar_accordion_dynamic_tags');
				$categoryRestriction = get_sub_field('sidebar_accordion_dynamic_categories');
				$accordionTitle = get_sub_field('sidebar_accordion_dynamic_title');
				$sectionDescription = get_sub_field('sidebar_accordion_dynamic_description');
				$maxItems = get_sub_field('sidebar_accordion_dynamic_max_items_per_container');
				$tagMap = array();
				$tagIDs = array();
				$catIDs = array();

				foreach ($taglist as $tag) {
					$tagIDs[] = $tag -> term_id;
					$tagMap[$tag -> term_id]= true;
				}

				if ($categoryRestriction) {
					foreach ($categoryRestriction as $cat) {
						$catIDs[] = $cat -> term_id;
					}
				}
				if (!count($tagIDs)) {
					$sidebar_markup .= 'Dynamic accordion requires at least one tag.<BR>';
				} else {
					if ($accordionTitle != "") {
						$sidebar_markup .= '<h3 class="sidebar-section-header">' . $accordionTitle . '</h3>';
					}

					if ($sectionDescription) {
						$sidebar_markup .= '<p class="sans">' . $sectionDescription . '</p>';
					}
					$sidebar_markup .= '<style>div.usa-accordion-content {padding:1.5rem !important;}</style>';

					$sidebar_markup .= '<div class="usa-accordion bbg__committee-list">';
					$sidebar_markup .= 	'<ul class="unstyled-list">';
					$qParams = array(
						'post_type' => array('post'),
						'posts_per_page' => 999,
						'orderby' => 'post_date',
						'order' => 'desc',
						'tag__in' => $tagIDs
					);
					if (count($catIDs)) {
						$qParams['category__and'] = $catIDs;
					}

					$postsByTag = array();
					$custom_query = new WP_Query($qParams);

					//create a 2 dimensional data structure called "postsByTag".
					//the first key is the tagID, and then each entry is an array of id/title/link
					while ( $custom_query->have_posts() )  {
						$custom_query->the_post();
						$id = get_the_ID();
						$posttags = get_the_tags();
						$permalink = get_the_permalink();
						$title = get_the_title();
						if ($posttags) {
							foreach($posttags as $tag) {
								$term_id = $tag -> term_id;
								if (isset($tagMap[$term_id])) {
									if (!isset($postsByTag[$term_id])) {
										$postsByTag[$term_id] = array();
									}
									$postsByTag[$term_id][] = array(
										'id' => $id,
										'title' => $title,
										'link' => $permalink
									);
								}
							}
						}
					}
					wp_reset_postdata();

					$i = 0;
					foreach ($taglist as $tag) {
						$i++;
						$itemLabel = $tag -> name;
						$itemID = $tag -> term_id;
						$itemLabel = str_replace("Region: ", "", $itemLabel);

						if (isset($postsByTag[$itemID])) {
							$sidebar_markup .= '<li>';
							$sidebar_markup .= '<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $i . '">' . $itemLabel . '</button>';
							$sidebar_markup .= '<div id="collapsible-faq-' . $i . '" aria-hidden="true" class="usa-accordion-content">';
							$j=0;
							foreach($postsByTag[$itemID] as $article) {
								$j++;
								if ($maxItems == 0 || $j <= $maxItems) {
									if ($j > 1) {
										$sidebar_markup .= "<BR><BR>";
									}
									$link = $article['link'];
									$id = $article['id'];
									$title = $article['title'];
									$sidebar_markup .= "<a href='$link'>$title</a>";
								}
							}

							$sidebar_markup .= '</div>';
							$sidebar_markup .= '</li>';
						}
					}
				}
			} else if (get_row_layout() == 'sidebar_taxonomy_display') {
				$sectionTitle = get_sub_field('sidebar_taxonomy_display_title');
				$sectionDescription = get_sub_field('sidebar_taxonomy_display_description');
				$categoryRestriction = get_sub_field('sidebar_taxonomy_display_categories');
				$numItems = get_sub_field('sidebar_taxonomy_display_number_of_items');
				$taglist = get_sub_field('sidebar_taxonomy_display_tags');
				$pastOrFuture = get_sub_field('sidebar_taxonomy_display_past_or_future');

				$tagIDs = array();
				$catIDs = array();

				if ($taglist) {
					foreach ($taglist as $tag) {
						$tagIDs[] = $tag->term_id;
					}
				}


				if ($categoryRestriction) {
					foreach ($categoryRestriction as $cat) {
						$catIDs[] = $cat->term_id;
					}
				}

				if (!count($tagIDs) && !count($catIDs)) {
					$s .= "Sidebar taxonomy display requires at least one tag or category<BR>";
				}
				else {
					//We allow users to enter a zero to indicate no limit.  Wordpress needs a -1 for this.
					if ($numItems == 0) {
						$numItems = -1;
					}
					$qParams=array(
						'post_type' => array('post'),
						'posts_per_page' => $numItems,
						'orderby' => 'post_date'
					);
					if ($pastOrFuture == "past") {
						$qParams['order'] = 'desc';
					} else {
						$qParams['order'] = 'asc';
						$qParams['post_status'] = 'future';
					}

					if (count($tagIDs)) {
						$qParams['tag__and'] = $tagIDs;
					}
					if (count($catIDs)) {
						$qParams['category__and'] = $catIDs;
					}

					$custom_query = new WP_Query($qParams);
					if ($custom_query -> found_posts || $sectionDescription) {
						$sidebar_markup .= '<h3 class="sidebar-section-header">' . $sectionTitle . '</h3>';
						if ($sectionDescription) {
							$sidebar_markup .= '<p class="sans">' . $sectionDescription . '</p>';
						}
						if ($custom_query -> found_posts) {
							$i = 0;
							$sidebar_markup .= '<p class="sans">';
							while ( $custom_query->have_posts() )  {
								$custom_query->the_post();
								$i++;
								if ($i > 1) {
									$sidebar_markup .= "<BR><BR>";
								}
								$id = get_the_ID();
								if ($pastOrFuture == "past") {
									$permalink = get_the_permalink();
								} else {
									/**** wordpress doesn't return a nice permalink for scheduled posts, so we have a workaround ***/
									global $post;
									$my_post = clone $post;
									$my_post->post_status = 'published';
									$my_post->post_name = sanitize_title($my_post->post_name ? $my_post->post_name : $my_post->post_title, $my_post->ID);
									$permalink = get_permalink($my_post);
								}

								$title = get_the_title();
								$sidebar_markup .= "<a style='text-decoration:none;' href='$permalink'>$title</a>";
							}
							$sidebar_markup .= '</p>';
						}
						$sidebar_markup .= '<BR>';
					}
					wp_reset_postdata();
				}
			}
		endwhile;
	endif;
	$sidebar .= $sidebar_markup;
}

/**
 * Sidebar drop-down for multiple downloads (2-col pages)
 * @var [boolean]
 */

$listsInclude = get_field('sidebar_dropdown_include', '', true);

if ($listsInclude) {
	$dropdownTitle = get_field('sidebar_dropdown_title');

	if (have_rows('sidebar_dropdown_content')) {

		$s = '';
		if ($dropdownTitle && $dropdownTitle != "") {
			$s = '<h3 class="sidebar-section-header">' . $dropdownTitle . '</h3>';
		}

		while (have_rows('sidebar_dropdown_content')) : the_row();
			if (get_row_layout() == 'file_downloads') {
				$sidebarDownloadsTitle = get_sub_field('sidebar_downloads_title');
				$sidebarDownloadsDefault = get_sub_field('sidebar_downloads_default');
				$sidebarDownloadsRows = get_sub_field('sidebar_downloads' );
				$sidebarDownloadsTotal = count( $sidebarDownloadsRows);

				$download_select  = '<div class="sidebar-section">';
				$download_select .= 	'<h3 class="sidebar-section-header">' . $sidebarDownloadsTitle . '</h3>';

				if ($sidebarDownloadsTotal >= 2) {
					$download_select .= '<form style="max-width: 100%;">';
					$download_select .= 	'<select name="file_download_list" id="file_download_list" style="display: inline-block; max-width: 100%;">';
					$download_select .= 		'<option>' . $sidebarDownloadsDefault . '</option>';

					foreach ($sidebarDownloadsRows as $row) {
						$sidebarDownloadsLinkName = $row['sidebar_download_title'];
						$sidebarDownloadsLinkObj = $row['sidebar_download_file'];
						$fileLink = $sidebarDownloadsLinkObj['url'];
						$fileID = $sidebarDownloadsLinkObj['ID'];
						$file = get_attached_file( $fileID );
						$ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
						$size = formatBytes(filesize($file));

						if ($sidebarDownloadsLinkName == "" || !$sidebarDownloadsLinkName) {
							$name = $sidebarDownloadsLinkObj['title'];
							$sidebarDownloadsLinkName = $name;
						}

						$download_select .= 		'<option value="' . $fileLink . '">';
						$download_select .= 			$sidebarDownloadsLinkName;
						$download_select .= 			' <span class="bbg__file-size">(' . $ext . ', ' . $size . ')</span>';
						$download_select .= 		'</option>';
					}

					$download_select .= 		'</select>';
					$download_select .= 	'</form>';
					$download_select .= 	'<button class="usa-button downloadFile" id="downloadFile" style="width: 100%;">Download</button>';
					$download_select .= '</div>';
					$s .= $download_select;
				}
				else {
					$sidebarDownloadsTitle = get_sub_field('sidebar_download_title');
					$sidebarDownloadsRows = get_sub_field('sidebar_downloads');

					$download_list = '';
					foreach ($sidebarDownloadsRows as $row) {
						$sidebarDownloadsLinkName = $row['sidebar_download_title'];
						$sidebarDownloadsLinkObj = $row['sidebar_download_file'];
						$fileLink = $sidebarDownloadsLinkObj['url'];
						$fileID = $sidebarDownloadsLinkObj['ID'];
						$file = get_attached_file($fileID);
						$ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
						$size = formatBytes(filesize($file));

						if ($sidebarDownloadsLinkName == "" | !$sidebarDownloadsLinkName) {
							$name = $sidebarDownloadsLinkObj['title'];
							$sidebarDownloadsLinkName = $name;
						}

						$download_list .= '<h4 class="sidebar-article-title">';
						$download_list .= 	'<a target="_blank" href="' . $fileLink . '">' . $sidebarDownloadsLinkName . '</a>';
						$download_list .= 	'<span class="bbg__file-size">(' . $ext . ', ' . $size . ')</span>';
						$download_list .= '<h4>';
					}
					$s .= $download_list;
				}
			}
			elseif (get_row_layout() == 'sidebar_dropdown_internal_links') {
				$sidebarInternalTitle = get_sub_field('sidebar_internal_title');
				$sidebarInternalDefault = get_sub_field('sidebar_internal_default');
				$sidebarInternalRows = get_sub_field('sidebar_internal_objects');

				$sidebar_internal_links  = '<div class="sidebar-section">';

				if (count($sidebarInternalRows) < 5) {
					$sidebar_internal_links .= '<h3 class="sidebar-section-header">' . $sidebarInternalTitle . '</h3>';

					foreach( $sidebarInternalRows as $link ) {
						$sidebarInternalLinkName = $link['internal_links_title'];
						$sidebarInternalLinkObj = $link['internal_links_url'];
						$url = get_permalink($sidebarInternalLinkObj->ID);

						if ( $sidebarInternalLinkName == "" | !$sidebarInternalLinkName ) {
							$title = $sidebarInternalLinkObj->post_title;
							$sidebarInternalLinkName = $title;
						}
						$sidebar_internal_links .= '<h4 class="sidebar-article-title">';
						$sidebar_internal_links .= 	'<a href="' . $url . '">' . $sidebarInternalLinkName . '</a>';
						$sidebar_internal_links .= '</h4>';
					}
					$s .= $sidebar_internal_links;
				} else {
					$sidebar_form  = '<form>';
					$sidebar_form .= 	'<label for="options" style="display: inline-block; font-size: 2rem; font-weight: bold; margin-top: 0;">' . $sidebarInternalTitle . '</label>';
					$sidebar_form .= 	'<select name="internal_links_list" class="internal_links_list" style="display: inline-block;">';
					$sidebar_form .= 		'<option>Select a link</option>';

					foreach( $sidebarInternalRows as $link ) {
						$sidebarInternalLinkName = $link['internal_links_title'];
						$sidebarInternalLinkObj = $link['internal_links_url'];
						$url = get_permalink($sidebarInternalLinkObj->ID);

						if ($sidebarInternalLinkName == "" | !$sidebarInternalLinkName) {
							$title = $sidebarInternalLinkObj->post_title;
							$sidebarInternalLinkName = $title;
						}
						$sidebar_form .= 	'<option value="' . $url . '">' . $sidebarInternalLinkName . '</option>';
					}
					$sidebar_form .= 	'</select>';
					$sidebar_form .= '</form>';
					$sidebar_form .= '<button class="usa-button internalLink" style="width: 100%;">Go</button>';
					$s .= $sidebar_form;
				}
				$s .= '</div>';
			}
		endwhile;
		$sidebarDownloads = $s;
	}
}

?>