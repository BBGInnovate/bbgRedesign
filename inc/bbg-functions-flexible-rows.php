<?php

function getTitlePartsFromPost($wpPost) {
    if (empty($wpPost)) {
        return array();
    }
    $wpPost = $wpPost[0];
    $post = array();

    $post['text'] = $wpPost->post_title;
    $post['url'] = get_the_permalink($wpPost);

    return $post;
}

function getBackgroundImagePartsFromPost($postArg) {
    if (empty($postArg)) {
        return array();
    }

    $wpPost = $postArg[0];
    $post = array();
    if ($wpPost->post_type == 'experts') {
        $profile_photo_id = get_post_meta($wpPost->ID, 'profile_photo', true);
        $post['image'] = wp_get_attachment_image($profile_photo_id, 'large-thumb');
    } else {
        $post['image'] = get_the_post_thumbnail($wpPost, 'large-thumb');
    }

    $post['url'] = get_the_permalink($wpPost);

    return $post;
}

function getImageTagFromImage($image, $dimensions = array()) {
    if (empty($image)) {
        return '';
    }
    $result = '';

    if (!empty($dimension['width']) && !empty($dimension['height'])) {
        $result = wp_get_attachment_image($image['id'], array($dimensions['width'], $dimensions['height']));
    } else {
        $result = wp_get_attachment_image($image['id'], 'large-thumb');
    }

    return $result;
}

function getDateFromPost($postArg) {
    if (empty($postArg)) {
        return array();
    }
    $wpPost = $postArg[0];
    $result = array();

    $result = date_format(date_create($wpPost->post_date), 'F d, Y');

    return $result;
}

function getExcerptFromPost($postArg) {

    if (empty($postArg)) {
        return array();
    }
    $wpPost = $postArg[0];

    $result = '';

    $result = $wpPost->post_excerpt;

    return $result;
}

function parseTagGroup($tag) {
    if (empty($tag)) {
        return array();
    }
    $result = array();
    $result['text'] = $tag['text'];
    $result['url'] = $tag['url'];

    return $result;
}

function getWatermarkParts($watermark) {
    $result = array();

    $brandPageUrlBase = get_home_url() . '/networks';

    switch($watermark) {
        case 'usagm':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_usagm.png';
            $result['url'] = get_home_url();
            break;

        case 'voa':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_voa.png';
            $result['url'] = $brandPageUrlBase . '/voa/';
            break;

        case 'rferl':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_rferl.png';
            $result['url'] = $brandPageUrlBase . '/rferl/';
            break;

        case 'ocb':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_ocb.png';
            $result['url'] = $brandPageUrlBase . '/ocb/';
            break;

        case 'rfa':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_rfa.png';
            $result['url'] = $brandPageUrlBase . '/rfa/';
            break;

        case 'mbn':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_mbn.png';
            $result['url'] = $brandPageUrlBase . '/mbn/';
            break;

        case 'otf':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_otf.png';
            $result['url'] = $brandPageUrlBase . '/otf/';
            break;

        case 'twitter':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/social-icons/png-white/social-icons_twitter-100x100.png';
            $result['url'] = 'https://twitter.com/USAGMgov/';
            break;

        default:
            $result['image'] = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
            $result['url'] = get_home_url();
            break;
    }

    return $result;
}

function getTweetParts($tweet) {
    $result = array();
    $result['username'] = $tweet['username'];
    $result['url'] = $tweet['url'];
    $result['text'] = $tweet['text'];
    $result['color'] = getColorParts($tweet['color'], false);
    $result['alignment'] = $tweet['alignment'];

    return $result;
}

function getColorParts($color, $isBgColor = false) {

    if (empty($color)) {
        $color = 'white';
    }

    $colors = array();

    $prefix = ($isBgColor ? 'card-bg-color-' : 'card-color-');

    $colors['white'] = $prefix . 'white';
    $colors['black'] = $prefix . 'black';

    $colors['primary1'] = $prefix . 'primary1';
    $colors['primary2'] = $prefix . 'primary2';
    $colors['primary3'] = $prefix . 'primary3';
    $colors['primary4'] = $prefix . 'primary4';

    $colors['neutral1'] = $prefix . 'neutral1';
    $colors['neutral2'] = $prefix . 'neutral2';
    $colors['neutral3'] = $prefix . 'neutral3';
    $colors['neutral4'] = $prefix . 'neutral4';

    $colors['secondary1'] = $prefix . 'secondary1';
    $colors['secondary2'] = $prefix . 'secondary2';
    $colors['secondary3'] = $prefix . 'secondary3';

    $result = '';
    if (array_key_exists($color, $colors)) {
        $result = $colors[$color];
    }

    return $result;
}

function getVideoUrl($video) {
    $result = array();

    $url = $video['url'];
    $urlParts = explode('.', $url);
    $filetype = end($urlParts);

    $result['url'] = $url;

    $valid = array(
            'mp4' => true,
            'webm' => true,
            'ogg' => true);

    if (isset($valid[$filetype])) {
        $result['filetype'] = $filetype;
    } else {
        $result['filetype'] = 'mp4';
    }

    return $result;
}

function parseTitleGroup($titleGroup) {
    $result = array();

    $result['text'] = $titleGroup['text'] ?? '';
    $result['url'] = $titleGroup['url'] ?? '';
    $result['color'] = getColorParts($titleGroup['color'] ?? '', false);
    $result['alignment'] = $titleGroup['alignment'] ?? '';

    return $result;
}

function parseBackgroundGroup($backgroundGroup) {
    $result = array();

    $dimensions = array();

    $result['dimensions'] = parseDimensionsGroup($backgroundGroup['dimensions'] ?? '');
    $dimensions = $result['dimensions'];

    $result['image'] = getImageTagFromImage($backgroundGroup['image'] ?? '', $dimensions);

    $result['url'] = $backgroundGroup['url'] ?? '';

    $result['color'] = getColorParts($backgroundGroup['color'] ?? '', true);

    return $result;
}

function parseTextGroup($textGroup) {
    $result = array();

    $result['color'] = getColorParts($textGroup['color'] ?? '', false);

    $result['alignment'] = $textGroup['alignment'] ?? '';

    return $result;
}

function parseDimensionsGroup($dimensionsGroup) {
    $result = array();

    $result['width'] = $dimensionsGroup['width'] ?? '';
    $result['height'] = $dimensionsGroup['height'] ?? '';

    return $result;
}

function getRowsDataGeneral($postId) {
    $card = array();

    $card['tag'] = parseTagGroup(get_sub_field('tag'));
    $card['watermark'] = getWatermarkParts(get_sub_field('watermark'));

    if (have_rows('content', $postId)) {
        while (have_rows('content', $postId)) {
            the_row();

            switch(get_row_layout()) {
                case 'internal':
                    $postGroupResult = parsePostGroup(get_sub_field('post'));

                    foreach ($postGroupResult as $key => $val) {
                        $card[$key] = $val;
                    }

                    break;
                case 'external':
                    $titleGroup = get_sub_field('title');
                    $card['title'] = parseTitleGroup($titleGroup);
                    $backgroundGroup = get_sub_field('background');
                    $card['background'] = parseBackgroundGroup($backgroundGroup);
                    break;
            }
        }
    }

    return $card;
}

function parsePostGroup($postGroup) {
    $card = array();

    $postField = $postGroup['post'];

    $card['title'] = getTitlePartsFromPost($postField);
    $card['title']['color'] = getColorParts($postGroup['color'] ?? '', false);
    $card['title']['alignment'] = $postGroup['alignment'] ?? '';

    $card['background'] = getBackgroundImagePartsFromPost($postField);

    $card['date'] = getDateFromPost($postField);
    if ($postGroup['include_excerpt'] == true) {
        $card['excerpt'] = getExcerptFromPost($postField);
    }
    $card['include_date'] = get_sub_field('include_date');

    return $card;
}

function getRowsDataHeader() {
    $card = array();

    $card['background'] = parseBackgroundGroup(get_sub_field('background'));
    $card['text'] = parseTextGroup(get_sub_field('text'));
    $card['watermark'] = getWatermarkParts(get_sub_field('watermark'));

    if (have_rows('content', $postId)) {
        while (have_rows('content', $postId)) {
            the_row();

            switch(get_row_layout()) {
                case 'internal':
                    $postGroupResult = parsePostGroup(get_sub_field('post'));

                    foreach ($postGroupResult as $key => $val) {
                        if ($key == 'background') {
                            continue;
                        }
                        $card[$key] = $val;
                    }

                    break;
                case 'custom':
                    $titleGroup = get_sub_field('title');
                    $card['title'] = parseTitleGroup($titleGroup);

                    break;
            }
        }
    }

    return $card;
}

function getRowsDataVideoInternal() {
    $card = array();

    $card['video'] = getVideoUrl(get_sub_field('file'));

    $postGroupResult = parsePostGroup(get_sub_field('post'));
    foreach ($postGroupResult as $key => $val) {
        $card[$key] = $val;
    }
    $card['watermark'] = getWatermarkParts(get_sub_field('watermark'));
    $card['tag'] = parseTagGroup(get_sub_field('tag'));

    return $card;
}

function getRowsDataVideoYouTube() {
    $card = array();

    $card['video']['url'] = get_sub_field('url');

    return $card;
}

function getRowsDataSocialMediaTwitter() {
    $card = array();

    $card['background'] = parseBackgroundGroup(get_sub_field('background'));
    $card['text'] = parseTextGroup(get_sub_field('text'));
    $card['tweet'] = getTweetParts(get_sub_field('tweet'));
    $card['watermark'] = getWatermarkParts(get_sub_field('watermark'));
    $card['watermark']['url'] = $card['tweet']['url'];

    return $card;
}

function getRowsDataImage() {
    $card = array();

    $backgroundGroup = get_sub_field('background');
    $card['background'] = parseBackgroundGroup($backgroundGroup);

    return $card;
}

function getRowsDataFlexText() {
    $card = array();

    $card['title'] = parseTitleGroup(get_sub_field('title'));

    return $card;
}

function getCardsRows($postId = null) {
    // $postId = 'options';
    $postId = '';

    $cardsRows = array();
    if (have_rows('cards_rows', $postId)) {
        while (have_rows('cards_rows', $postId)) {
            the_row();

            $cardsRow = array();
            $cardsRow['cards_heading'] = get_sub_field('cards_heading');
            $cardsRow['cards_heading_url'] = get_sub_field('cards_heading_url');
            $cardsRow['cards_margin_top'] = get_sub_field('cards_margin_top');
            $cardsRow['cards_margin_bottom'] = get_sub_field('cards_margin_bottom');
            $cardsRow['cards_height'] = get_sub_field('cards_height');
            $cardsRow['cards_gutter_size'] = get_sub_field('cards_gutter_size');
            $layout = get_sub_field('cards_layout');

            $cardsRow['cards_layout'] = explode('-', $layout);

            if (have_rows('cards_content', $postId)) {
                while (have_rows('cards_content', $postId)) {
                    the_row();
                    $card = array();

                    switch(get_row_layout()) {
                        case 'general':
                            $card = getRowsDataGeneral($postId);
                            break;
                        case 'header':
                            $card = getRowsDataHeader();
                            break;
                        case 'video_internal':
                            $card = getRowsDataVideoInternal();
                            break;
                        case 'video_youtube':
                            $card = getRowsDataVideoYouTube();
                            break;
                        case 'social_media_twitter':
                            $card = getRowsDataSocialMediaTwitter();
                            break;
                        case 'image':
                            $card = getRowsDataImage();
                            break;
                        case 'flex_text':
                            $card = getRowsDataFlexText();
                            break;
                    }

                    $card['type'] = get_row_layout();

                    $cardsRow['cards_content'][] = $card;
                }
            }
            $cardsRows[] = $cardsRow;
        }
    }

    return $cardsRows;
}

// Layout
function createBackground($card) {
    if (empty($card)) {
        return '';
    }

    $result = '';

    switch ($card['type']) {
        case 'video_internal':
            $result .= '                <div style="width:100%;">';
            $result .= '                    <video style="width: 100%;" muted playsinline>';
            $result .= '                        <source src="' . $card['video']['url'] . '" type="video/' . $card['video']['filetype'] . '"/>';
            $result .= '                        Sorry, your browser doesn\'t support embedded videos.';
            $result .= '                    </video>';
            $result .= '                </div>';
            $result .= '                <div class="cards__backdrop-shadow">';
            $result .= '                </div>';

            break;

        case 'video_youtube':
            $result .= '                <div style="width:100%; height: 100%;">';
            $result .= '                    <iframe style="width: 100%; height: 100%;" src="' . $card['video']['url'] . '" frameborder="0" allow="autoplay; encrypted-media;" allowfullscreen>';
            $result .= '                    </iframe>';
            $result .= '                </div>';

            break;

        case 'image':
            $result .= '                <a href="' . $card['background']['url'] . '">';
            $result .= '                    ' . $card['background']['image'];
            $result .= '                </a>';

            break;

        case 'header':
            break;

        default:
            if (!empty($card['background'])) {
                $background = $card['background'];

                if (!empty($background['url']) && !empty($background['image'])) {
                    $result .= '                <a href="' . $background['url'] . '">' . $background['image'] . '</a>';
                    $result .= '                <div class="cards__backdrop-shadow">';
                    $result .= '                </div>';
                } else if (!empty($background['image'])) {
                    $result .= $background['image'];
                } else {
                    //
                }
            }

            break;
    }

    return $result;
}

function createWatermark($card) {
    if (empty($card) || empty($card['watermark'])) {
        return '';
    }

    $watermark = $card['watermark'];

    $result = '';
    if (!empty($watermark['image'])) {
        $result .= '                <div class="cards__backdrop-logo">';
        if (!empty($watermark['url'])) {
            $result .= '                    <a href="' . $watermark['url'] . '">';
        }
        $result .= '                    <img src="' . $watermark['image'] . '"/>';
        if (!empty($watermark['url'])) {
            $result .= '                </a>';
        }
        $result .= '                </div>';
    } else if (!empty($watermark['image'])) {
        $result .= $watermark['image'];
    } else {
        //
    }

    return $result;
}

function createDate($card) {
    if (empty($card) || empty($card['date']) || !array_key_exists('include_date', $card) || !$card['include_date']) {
        return '';
    }

    $date = $card['date'];

    $result = '';
    $result .= '                <div class="cards__date">';
    $result .= '                    ' . $date;
    $result .= '                </div>';

    return $result;
}

function createHeaderTitle($card) {
    if (empty($card)) {
        return '';
    }

    $result = '';

    switch($card['type']) {
        case 'social_media_twitter':
            $tweet = $card['tweet'];

            $color = ' ' . $tweet['color'];

            $result .= '                <h3>';
            $result .= '                    <a class="' . $color .'" href="' . $tweet['url'] . '">' . $tweet['username'] . '</a>';
            $result .= '                </h3>';
            $result .= '                <p class="' . $color .'">';
            $result .= '                    ' . $tweet['text'];
            $result .= '                </p>';

            break;

        case 'header':
            if (!empty($card['type']) && !empty($card['title'])) {
                $title = $card['title'];

                $alignment = '';
                if (!empty($title['alignment'])) {
                    $alignment .= ' align-' . $title['alignment'];
                }

                $color = ' ' . $title['color'];

                $result .= '                <h2 class="' . $alignment . $color . '">';
                if (!empty($title['url'])) {
                    $result .= '                    <a class="' . $color .'" href="' . $title['url'] . '">' . $title['text'] . '</a>';
                } else {
                    $result .= '                    ' . $title['text'];
                }
                $result .= '                </h2>';
            }

            break;

        default:
            // do nothing
            break;
    }

    return $result;
}

function createFooter($card) {
    if (empty($card) || empty($card['type']) || empty($card['title']) || $card['type'] == 'header') {
        return '';
    }

    $tag = $card['tag'];
    $title = $card['title'];
    $color = $title['color'];

    $result = '';
    $result .= '            <div class="cards__footer">';
    if (!empty($tag['text'])) {
        $result .= '                <div class="cards__tag">';
        if (!empty($tag['url'])) {
            $result .= '                <a href="' . $tag['url'] . '">' . $tag['text'] . '</a>';
        } else {
            $result .= '                ' . $tag['text'];
        }
        $result .= '                </div>';
    }
    $result .= '                <h2>';
    $result .= '                    <a class="' . $color . '" href="' . $title['url'] . '">' . $title['text'] . '</a>';
    $result .= '                </h2>';
    $result .= '            </div>';

    return $result;
}

function createExcerpt($card) {
    if (empty($card) || empty($card['excerpt'])) {
        return '';
    }

    $result = '';
    $result .= '        <div class="cards__excerpt">';
    $result .= '            <p>';
    $result .= '                ' . $card['excerpt'];
    $result .= '            </p>';
    $result .= '        </div>';

    return $result;
}

function createFlexText($card) {
    if (empty($card)) {
        return '';
    }

    $result = '';
    switch ($card['type']) {
        case 'flex_text':
            if (!empty($card['title']['text'])) {
                $title = $card['title'];

                $color = ' ' . $title['color'];

                $alignment = '';
                if (!empty($title['alignment'])) {
                    $alignment .= ' align-' . $title['alignment'];
                }

                $result .= '        <div class="cards__flex-text">';
                $result .= '            <p class="' . $color . $alignment. '">';
                $result .= '                ' . $title['text'];
                $result .= '            </p>';
                $result .= '        </div>';
            }

            break;
        default:
            break;
    }

    return $result;
}

function getCardsLayout($cardsRows) {
    $result = '';

    foreach($cardsRows as $cardsRow) {
        $gutterSize = $cardsRow['cards_gutter_size'];
        $layouts = $cardsRow['cards_layout'];
        $marginTop = $cardsRow['cards_margin_top'];
        $marginBottom = $cardsRow['cards_margin_top'];
        $layoutsSum = array_sum($layouts);
        $result .= '<div class="inner-container gutter-' . $gutterSize .'">';
        $cardsHeading = $cardsRow['cards_heading'];
        $cardsHeadingUrl = $cardsRow['cards_heading_url'];
        if (!empty($cardsHeading)) {
            if (!empty($cardsHeadingUrl)) {
                $result .= '<h3><a href="' . $cardsRow['cards_heading_url'] . '">' . $cardsRow['cards_heading'] . '</a></h3>';
            } else {
                $result .= '<h3>' . $cardsRow['cards_heading'] . '</h3>';
            }
        }
        foreach ($cardsRow['cards_content'] as $card) {
            $result .= '<div class="cards cards--layout-' . $card['type'] . ' cards--size-' . array_shift($layouts) . '-' . $layoutsSum . '-' . $cardsRow['cards_height'] . '-' . $gutterSize . ' margin-top-' . $marginTop . '">';
            $result .= '    <div class="cards__fixed">';
            $result .= '        <div class="cards__wrapper ' . ($card['background']['color'] ?? '') . '">';
            if ($card['type'] == 'image') {
                $result .= '        <div class="cards__backdrop cards__backdrop-image">';
            } else {
                $result .= '        <div class="cards__backdrop">';
            }
            $result .=                  createBackground($card);
            $result .=                  createWatermark($card);
            $result .= '            </div>';
            $result .= '            <div class="cards__header">';
            $result .=                  createDate($card);
            $result .=                  createHeaderTitle($card);
            $result .= '            </div>';
            $result .=              createFooter($card);
            $result .= '        </div>';
            $result .= '    </div>';
            $result .= '    <div class="cards__flexible">';
            $result .=          createExcerpt($card);
            $result .=          createFlexText($card);
            $result .= '    </div>';
            $result .= '</div>';
        }
        $result .= '</div>';
    }

	$result = do_shortcode($result);
    return $result;
}

function getFlexibleRowsArray($postId = null) {
	$all_flex_rows = array();

	if (have_rows('flexible_page_rows', $postId)) {
		$includeFlexiblePageRows = get_field('include_flexible_page_rows', $postId);
		if (!empty($includeFlexiblePageRows)) {
			$umbrella_group = array();
			while (have_rows('flexible_page_rows', $postId)) {
				the_row();

				if (get_row_layout() == 'cards') {
					$cardsRows = getCardsRows($postId);
					$cardsLayout = getCardsLayout($cardsRows);
					array_push($all_flex_rows, $cardsLayout);
				}
				elseif (get_row_layout() == 'marquee') {
					$marquee_data_result = get_marquee_data();
					$marquee_parts_result = build_marquee_parts($marquee_data_result);
					$marquee_module = assemble_marquee_module($marquee_parts_result);
					array_push($all_flex_rows, $marquee_module);
				}
				elseif (get_row_layout() == 'ribbon_page') {
					$ribbon_data_result = get_ribbon_data();
					$ribbon_parts_result = build_ribbon_parts($ribbon_data_result);
					$ribbon_module = assemble_ribbon_module($ribbon_parts_result);
					array_push($all_flex_rows, $ribbon_module);
				}
			}
		}
	}

	return $all_flex_rows;
}

function getFlexibleRows($postId = null) {
	$flexRows = getFlexibleRowsArray($postId);

	$result = '';

	foreach ($flexRows as $flexRow) {
		if (is_array($flexRow)) {
			$result .= '<div class="outer-container about-flexible-row">';
			foreach ($flexRow as $row) {
				$result .= $row;
			}
			$result .= '</div>';
		}
		else {
			// echo $flex_row;
			$result .= '<div class="outer-container">';
			$result .= $flexRow;
			$result .= '</div>';
		}
	}

	return $result;
}

?>