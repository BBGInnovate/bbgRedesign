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

function getBackgroundImagePartsFromPost($postArg, $imageAlignment = null) {
    if (empty($postArg)) {
        return array();
    }

    $classArray = array('class' => 'cards__backdrop--image');
    if ($imageAlignment != null) {
        $classArray['class'] .= (' image-position-' . $imageAlignment);
    }
    $wpPost = $postArg[0];
    $post = array();
    if ($wpPost->post_type == 'experts') {
        $profile_photo_id = get_post_meta($wpPost->ID, 'profile_photo', true);
        $post['image'] = wp_get_attachment_image($profile_photo_id, 'large-thumb', '', $classArray);
    } else {
        $post['image'] = get_the_post_thumbnail($wpPost, 'large-thumb', $classArray);
    }

    $post['url'] = get_the_permalink($wpPost);

    return $post;
}

function getImageTagFromImage($image, $dimensions = array(), $attr = array()) {
    if (empty($image)) {
        return '';
    }
    $result = '';

    if (isset($attr['class'])) {
        $attr['class'] .= ' cards__backdrop--image';
    } else {
        $attr['class'] = 'cards__backdrop--image';
    }

    if (!empty($dimensions['width']) && !empty($dimensions['height'])) {
        $result = wp_get_attachment_image($image['id'], array($dimensions['width'], $dimensions['height']), '', $attr);
    } else {
        $result = wp_get_attachment_image($image['id'], 'large-thumb', '', $attr);
    }

    return $result;
}

function getImageSrcFromImage($image, $dimensions = array()) {
    if (empty($image)) {
        return '';
    }
    $result = '';

    if (!empty($dimensions['width']) && !empty($dimensions['height'])) {
        $result = wp_get_attachment_image_src($image['id'], array($dimensions['width'], $dimensions['height']));
    } else {
        $result = wp_get_attachment_image_src($image['id'], 'large-thumb');
    }

    return $result[0];
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
    $result['text'] = $tag['text'] ?? '';
    $result['url'] = $tag['url'] ?? '';

    return $result;
}

function getWatermarkParts($watermark) {
    $result = array();

    $brandPageUrlBase = get_home_url() . '/networks';

    switch($watermark) {
        case 'usagm':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_usagm--watermark.png';
            $result['url'] = get_home_url();
            break;

        case 'voa':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_voa--watermark.png';
            $result['url'] = $brandPageUrlBase . '/voa/';
            break;

        case 'rferl':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_rferl--watermark.png';
            $result['url'] = $brandPageUrlBase . '/rferl/';
            break;

        case 'ocb':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_ocb--watermark.png';
            $result['url'] = $brandPageUrlBase . '/ocb/';
            break;

        case 'rfa':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_rfa--watermark.png';
            $result['url'] = $brandPageUrlBase . '/rfa/';
            break;

        case 'mbn':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_mbn--watermark.png';
            $result['url'] = $brandPageUrlBase . '/mbn/';
            break;

        case 'otf':
            $result['image'] = get_home_url() . '/wp-content/themes/bbgRedesign/img/logo_otf--watermark.png';
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

    if (!isset($color) || empty($color)) {
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

function parseTextGroup($titleGroup) {
    $result = array();

    $result['text'] = $titleGroup['text'] ?? '';
    $result['url'] = $titleGroup['url'] ?? '';
    $result['size'] = $titleGroup['size'] ?? '';
    $result['family'] = $titleGroup['family'] ?? '';
    $result['color'] = getColorParts($titleGroup['color'] ?? '', false);
    $result['alignment'] = $titleGroup['alignment'] ?? '';
    $result['vertical_alignment'] = $titleGroup['vertical_alignment'] ?? '';

    return $result;
}

function parseBackgroundGroup($backgroundGroup) {
    $result = array();

    $dimensions = array();

    $result['dimensions'] = parseDimensionsGroup($backgroundGroup['dimensions'] ?? '');
    $dimensions = $result['dimensions'];

    $hoverImage = getImageSrcFromImage($backgroundGroup['hover_image'] ?? '', $dimensions);

    $attr = array();

    if (array_key_exists('image_alignment', $backgroundGroup)) {
        $imageAlignment = $backgroundGroup['image_alignment'];
        $attr['class'] = 'image-position-' . $imageAlignment;
    }

    $result['image'] = getImageTagFromImage($backgroundGroup['image'] ?? '', $dimensions, $attr);
    $result['hover_image'] = getImageTagFromImage($backgroundGroup['hover_image'] ?? '', $dimensions, array('class' => 'hidden'));

    $result['url'] = $backgroundGroup['url'] ?? '';

    $result['color'] = getColorParts($backgroundGroup['color'] ?? '', true);

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
                    $card['title'] = parseTextGroup($titleGroup);
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

    if (!empty($postGroup['override_title']) && $postGroup['override_title'] == true && !empty($postGroup['title_override'])) {
        $card['title']['text'] = $postGroup['title_override'];
    }

    $imageAlignment = $postGroup['image_alignment'] ?? null;
    $card['background'] = getBackgroundImagePartsFromPost($postField, $imageAlignment);

    $card['date'] = getDateFromPost($postField);
    if ($postGroup['include_excerpt'] == true) {
        $card['excerpt'] = getExcerptFromPost($postField);
    }
    $card['include_date'] = get_sub_field('include_date');

    return $card;
}

function getRowsDataHeader($postId = null) {
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
                    $card['title'] = parseTextGroup($titleGroup);

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

    $card['video']['url'] = get_sub_field('url') . '?rel=0';

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

    $card['flexText'] = parseTextGroup(get_sub_field('text'));
    $card['background'] = parseBackgroundGroup(get_sub_field('background'));

    return $card;
}

function getOperationsMapData() {
    $data = '';
    $data .= getNetworkExcerptJS();
    $data .= getMapData();
    $data .= getMapScripts();
    $data .= getMapMarkup();

    return $data;
}

function getRowsDataWidget() {
    $card = array();

    $widgetType = get_sub_field('type');
    switch($widgetType) {
        case 'operations_map':
            $card['data'] = getOperationsMapData();
            break;
        default:
            $card['data'] = '';
            break;
    }

    return $card;
}

function getCardsRowsData($postId) {

    $cardsRows = array();
    if (have_rows('cards_rows', $postId)) {
        while (have_rows('cards_rows', $postId)) {
            the_row();

            $cardsRow = array();
            $cardsRow['cards_heading'] = get_sub_field('cards_heading');
            $cardsRow['cards_heading_url'] = get_sub_field('cards_heading_url');
            $cardsRow['cards_heading_intro'] = get_sub_field('cards_heading_intro');
            $cardsRow['cards_margin_top'] = get_sub_field('cards_margin_top');
            $cardsRow['cards_margin_bottom'] = get_sub_field('cards_margin_bottom');
            $cardsRow['cards_gutter_size'] = get_sub_field('cards_gutter_size');
            $layout = get_sub_field('cards_layout');

            $cardsRow['cards_layout'] = explode('-', $layout);
            $cardsRow['cards_height'] = array_pop($cardsRow['cards_layout']);

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
                        case 'widget':
                            $card = getRowsDataWidget();
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
            $result .= '                    <video style="width: 100%;" muted autoplay playsinline>';
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
            if (!empty($card['background']['url'])) {
                $result .= '            <a href="' . $card['background']['url'] . '">';
                $result .= '                ' . $card['background']['image'] . ($card['background']['hover_image'] ?? '');
                $result .= '            </a>';
            } else {
                $result .= '                ' . $card['background']['image'] . ($card['background']['hover_image'] ?? '');
            }

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

                $verticalAlignment = '';
                if (!empty($title['vertical_alignment'])) {
                    $verticalAlignment .= ' align-vertical-' . $title['vertical_alignment'];
                }

                $alignment = '';
                if (!empty($title['alignment'])) {
                    $alignment .= ' align-' . $title['alignment'];
                }

                $size = '';
                if (!empty($title['size'])) {
                    $size .= ' font-size-' . $title['size'];
                }

                $color = ' ' . $title['color'];

                $result .= '                <h3 class="' . $verticalAlignment . $alignment . $color . $size . '">';
                if (!empty($title['url'])) {
                    $result .= '                    <a class="' . $color .'" href="' . $title['url'] . '">' . $title['text'] . '</a>';
                } else {
                    $result .= '                    ' . $title['text'];
                }
                $result .= '                </h3>';
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
    $result .= '                <h3>';
    $result .= '                    <a class="' . $color . '" href="' . $title['url'] . '">' . $title['text'] . '</a>';
    $result .= '                </h3>';
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
            if (!empty($card['flexText']['text'])) {
                $flexText = $card['flexText'];

                $color = ' ' . $flexText['color'];

                $alignment = '';
                if (!empty($flexText['alignment'])) {
                    $alignment .= ' align-' . $flexText['alignment'];
                }

                $size = '';
                if (!empty($flexText['size'])) {
                    $size .= ' font-size-' . $flexText['size'];
                }

                $family = '';
                if (!empty($flexText['family'])) {
                    $family .= ' font-family-' . $flexText['family'];
                }

                $result .= '        <div class="cards__flex-text ' . ($card['background']['color'] ?? '') . '">';
                $result .= '            <p class="' . $color . $alignment . $size . $family . '">';
                $result .= '                ' . $flexText['text'];
                $result .= '            </p>';
                $result .= '        </div>';
            }

            break;

        case 'widget':
            $result .= $card['data'];
            break;

        default:
            break;
    }

    return $result;
}

function getCardsLayout($cardsRows) {
    $result = '<div class="cards--container">';

    foreach($cardsRows as $cardsRow) {
        $gutterSize = $cardsRow['cards_gutter_size'];
        $layouts = $cardsRow['cards_layout'];
        $marginTop = $cardsRow['cards_margin_top'];
        $marginBottom = $cardsRow['cards_margin_top'];
        $layoutsSum = array_sum($layouts);
        $result .= '<div class="inner-container gutter-' . $gutterSize .'">';
        $cardsHeading = $cardsRow['cards_heading'];
        $cardsHeadingUrl = $cardsRow['cards_heading_url'];
        $cardsHeadingIntro = $cardsRow['cards_heading_intro'];
        if (!empty($cardsHeading)) {
            if (!empty($cardsHeadingUrl)) {
                $result .= '<h2><a href="' . $cardsRow['cards_heading_url'] . '">' . $cardsRow['cards_heading'] . '</a></h2>';
            } else {
                $result .= '<h2>' . $cardsRow['cards_heading'] . '</h2>';
            }
        }
        if (!empty($cardsHeadingIntro)) {
            $result .= '    <p class="lead-in">' . $cardsHeadingIntro . '</p>';
        }
        foreach ($cardsRow['cards_content'] as $card) {
            if (count($layouts) > 0) {
                $verticalAlignment = '';
                if (!empty($card['title']['vertical_alignment'])) {
                    $verticalAlignment .= ' align-vertical-' . $card['title']['vertical_alignment'];
                }
                $result .= '<div class="cards cards--layout-' . $card['type'] . ' cards--size-' . array_shift($layouts) . '-' . $layoutsSum . '-' . $cardsRow['cards_height'] . '-' . $gutterSize . ' margin-top-' . $marginTop . '">';
                $result .= '    <div class="cards__fixed' . ($card['type'] == 'flex_text' ? ' cards__fixed--hidden' : '') . '">';
                $result .= '        <div class="cards__wrapper ' . ($card['background']['color'] ?? '') .  '">';
                $result .= '        <div class="cards__backdrop">';
                $result .=                  createBackground($card);
                $result .=                  createWatermark($card);
                $result .= '            </div>';
                if ($card['type'] != 'widget') {
                    $result .= '        <div class="cards__header ' . $verticalAlignment . '">';
                    $result .=              createDate($card);
                    $result .=              createHeaderTitle($card);
                    $result .= '        </div>';
                }
                $result .=              createFooter($card);
                $result .= '        </div>';
                $result .= '    </div>';
                $result .= '    <div class="cards__flexible">';
                $result .=          createExcerpt($card);
                $result .=          createFlexText($card);
                $result .= '    </div>';
                $result .= '</div>';
            }
        }
        $result .= '</div>';
    }

    $result .= '</div>';

	$result = do_shortcode($result);
    return $result;
}

function getCardsRows($postId = null) {
    $cardsRowsData = getCardsRowsData($postId);
    $cardsLayout = getCardsLayout($cardsRowsData);
    return $cardsLayout;
}

?>