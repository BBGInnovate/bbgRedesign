<?php
function create_rss_markup($rss_url, $id, $page_name = NULL) {
	// CREATE RSS ITEMS
	$entityJson = getFeed($rss_url, $id);
	$rssItems = array();
	$itemContainer = false;
	$languageDirection = '';

	if ($entityJson != false) {
		if (property_exists($entityJson, 'channel') && property_exists($entityJson->channel, 'item')) {
			$itemContainer = $entityJson->channel;
		} else {
			$itemContainer = $entityJson;
		}
	} else {
		$itemContainer = $entityJson;
	}

	if ($itemContainer) {
		if (property_exists($itemContainer, 'language')) {
			if ($itemContainer->language == "ar"){
				$languageDirection = " rtl";
			}
		}
		foreach ($itemContainer->item as $e) {
			$title = $e->title;
			$url = $e->link;
			$description = $e->description;
			$enclosureUrl = '';
			if (property_exists($e, 'enclosure') && property_exists($e->enclosure, '@attributes') && property_exists($e->enclosure->{'@attributes'}, 'url')) {
				$enclosureUrl = ($e->enclosure->{'@attributes'}->url );
			}
			$rssItems[] = array(
				'title' => $title,
				'url' => $url,
				'description' => $description,
				'image' => $enclosureUrl
			);
		}
	}
	
	// CREATE RSS MARKUP
	if ($rssItems) {
		$rss_markup  = '<aside class="inner-container side-recent-stories">';
		$rss_markup .= 	'<h3 class="sidebar-section-header">Recent stories';
		if (isset($page_name)) {
			$rss_markup .= 	' from ' . $page_name;
		}
		$rss_markup .= 	'</h3>';
		$maxRelatedStories = 3;
		for ($i = 0; $i < min($maxRelatedStories, count($rssItems)); $i++) {
			$cur_rss_item = $rssItems[$i];
			$short_copy = wp_trim_words($cur_rss_item['description'], 15, ' ...');

			$rss_markup .= '<div class="nest-container post-group">';
			$rss_markup .= 	'<div class="inner-container">';
			$rss_markup .= 		'<div class="post-image">';
			if ($cur_rss_item['image'] != '') {
				$rss_markup .= 		'<a href="' . $cur_rss_item['url'] . '">';
				$rss_markup .= 			'<img src="' . $cur_rss_item['image'] . '" alt="">';
				$rss_markup .= 		'</a>';
			}
			$rss_markup .= 		'</div>';
			$rss_markup .= 		'<div class="post-copy">';
			$rss_markup .= 			'<h4 class="sidebar-article-title"><a href="' . $cur_rss_item['url'] . '">' . $cur_rss_item['title'] . '</a></h4>';
			$rss_markup .= 			'<p class="sans">' . $short_copy . '</p>';
			$rss_markup .= 		'</div>';
			$rss_markup .= 	'</div>';
			$rss_markup .= '</div>';
		}
		$rss_markup .= '</aside>';
		return $rss_markup;
	}
}