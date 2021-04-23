<?php
/**
 * Include sidebar
 * show single downloads, quotations, external links, internal links, & photos
 * @var [boolean]
 */

$includeSitewideSidebar = get_field('sidebar_include', 'option');
$includeCurrentSidebar = get_post_meta(get_the_ID(), 'sidebar_include', true);
$includeSidebar = ($includeSitewideSidebar || $includeCurrentSidebar);

$sidebar = '';
$sidebarDownloads = '';

if ($includeSitewideSidebar) {

    $sidebarIncludedTagsIds = get_field('sidebar_included_tags', 'option');

    if (is_array($sidebarIncludedTagsIds) && !empty($sidebarIncludedTagsIds) && !has_term($sidebarIncludedTagsIds, 'post_tag')) {
        $sidebarIncludedPagesOverrideTags = get_field('sidebar_include_override_tags', 'option');
        if (!empty($sidebarIncludedPagesOverrideTags) && in_array(get_the_ID(), $sidebarIncludedPagesOverrideTags)) {
            $sidebar .= getSidebarContent('option');
        }
    } else {
        $sidebar .= getSidebarContent('option');
    }
}

if ($includeCurrentSidebar) {
    $sidebar .= getSidebarContent(get_the_ID());
}

$sidebarDownloads .= getSidebarDropdownContent();

function getSidebarContent($postId) {
    global $post;

    $sidebarTitle = get_field('sidebar_title', $postId, true);
    $sidebarDescription = get_field('sidebar_description', $postId, true);

    $sidebar_markup = '';
    if ($sidebarTitle != '') {
        $sidebar_markup .= '<h3 class="sidebar-section-header">' . $sidebarTitle . '</h3>';
    }

    if ($sidebarDescription != "") {
        $sidebar_markup .= '<p class="sans">' . $sidebarDescription . '</p>';
    }

    if (have_rows('sidebar_items', $postId)) {
        while (have_rows('sidebar_items', $postId)) {
            the_row();
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
                    if (!empty($sidebarDownloadLinkObj['post_title'])) {
                        $sidebarDownloadTitle = $sidebarDownloadLinkObj['post_title'];
                    } else {
                        $sidebarDownloadLinkObj['title'];
                    }
                }

                $sidebar_download  = '<div class="sidebar-related">';
                if (!empty($sidebarDownloadThumbnail)) {
                    $sidebar_download .= '<a target="_blank" href="' . $sidebarDownloadLink . '">';
                    $sidebar_download .=    '<img src="' . $sidebarDownloadThumbnail . '" alt="Thumbnail image for download" style="margin-bottom: 0.5rem;">';
                    $sidebar_download .= '</a>';
                }
                $sidebar_download .=    '<p class="sidebar-article-title"><a target="_blank" href="' . $sidebarDownloadLink . '">' . $sidebarDownloadTitle . '</a>';
                $sidebar_download .=    '<br><span class="date-meta">(' . $ext . ', ' . $filesize . ')</span></p>';

                if ($sidebarDownloadDescription && $sidebarDownloadDescription != "") {
                    $sidebar_download .= '<p class="sans">';
                    $sidebar_download .=    $sidebarDownloadDescription;
                    $sidebar_download .= '</p>';
                }
                $sidebar_download .= '</div>';

                $sidebar_markup .= $sidebar_download;
            } else if (get_row_layout() == 'sidebar_quote') {
                $sidebarQuotationText = get_sub_field('sidebar_quotation_text', false);
                $sidebarQuotationSpeaker = get_sub_field('sidebar_quotation_speaker');
                $sidebarQuotationSpeakerTitle = get_sub_field('sidebar_quotation_speaker_title');

                $quote_markup  = '<div class="bbg__quotation">';
                $quote_markup .=    '<p>&quot;' . $sidebarQuotationText . '&quot;</p>';
                $quote_markup .=    '<p class="sans">';
                $quote_markup .=        '&mdash;' . $sidebarQuotationSpeaker . '<br>';
                $quote_markup .=        $sidebarQuotationSpeakerTitle;
                $quote_markup .=    '</p>';
                $quote_markup .= '</div>';

                $sidebar_markup .= $quote_markup;
            } else if (get_row_layout() == 'sidebar_external_link') {
                $sidebarLinkTitle = get_sub_field('sidebar_link_title', false);
                $sidebarLinkLink = get_sub_field('sidebar_link_link');
                $sidebarLinkImage = get_sub_field('sidebar_link_image');
                $sidebarLinkDescription = get_sub_field('sidebar_link_description', false);

                $external_links  = '<div class="sidebar-related">';
                if ($sidebarLinkImage && $sidebarLinkImage != "") {
                    $external_links .= '<a target="blank" href="' . $sidebarLinkLink . '">';
                    $external_links .=      '<img class="sans" src="' . $sidebarLinkImage['sizes']['medium'] . '" alt="Image link">';
                    $external_links .= '</a>';
                }
                $external_links .=      '<p class="sidebar-article-title">';
                $external_links .=          '<a target="blank" href="' . $sidebarLinkLink . '">' . $sidebarLinkTitle . '</a>';
                $external_links .=      '</p>';

                if ($sidebarLinkDescription != "") {
                    $external_links .= '<p class="sans">';
                    $external_links .=      $sidebarLinkDescription;
                    $external_links .= '</p>';
                }
                $external_links .= '</div>';

                $sidebar_markup .= $external_links;
            } else if (get_row_layout() == 'sidebar_internal_link') {
                $sidebarInternalTitle = get_sub_field('sidebar_internal_title', false);
                $sidebarInternalLocation = get_sub_field('sidebar_internal_location');
                $sidebarInternalDescription = get_sub_field('sidebar_internal_description', false);

                $internal_links  = '<div class="sidebar-related">';
                $internal_links .=  '<h4 class="sidebar-article-title">';
                $internal_links .=      '<a href="' . get_permalink($sidebarInternalLocation -> ID) . '">';
                if ($sidebarInternalTitle && $sidebarInternalTitle != "") {
                    $internal_links .= $sidebarInternalTitle;
                } else {
                    $internal_links .= $sidebarInternalLocation -> post_title;
                }
                $internal_links .=      '</a>';
                $internal_links .=  '</h4>';
                if (!empty($sidebarInternalDescription)) {
                    $internal_links .=  $sidebarInternalDescription;
                }
                $internal_links .= '</div>';
                $sidebar_markup .= $internal_links;
            } else if (get_row_layout() == 'sidebar_posts_for_tag') {
                $sidebarPostsTag = get_sub_field('sidebar_posts_tag', false);
                $sidebarPostsMax = get_sub_field('sidebar_posts_max', false);

                $sidebarPostsMax = intval($sidebarPostsMax);

                $qParams = array(
                    'post_type' => array('post'),
                    'posts_per_page' => $sidebarPostsMax,
                    'orderby' => 'post_date',
                    'order' => 'desc',
                    'tag__in' => array($sidebarPostsTag)
                );

                $custom_query = new WP_Query($qParams);

                $posts_for_tag = '';
                while ($custom_query->have_posts())  {
                    $custom_query->the_post();

                    $posts_for_tag .= '<div class="sidebar-related">';
                    $posts_for_tag .=     '<h4 class="sidebar-article-title">';
                    $posts_for_tag .=         '<a href="' . get_the_permalink() . '">';
                    $posts_for_tag .= get_the_title();
                    $posts_for_tag .=         '</a>';
                    $posts_for_tag .=     '</h4>';
                    $posts_for_tag .= '</div>';
                }
                wp_reset_postdata();

                $sidebar_markup .= $posts_for_tag;
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

                $sidebar_markup .= '<div class="related-photo">' . $sidebarImage . $sidebarImageTitle . $sidebarDescription . '</div>';
            } else if (get_row_layout() == 'sidebar_accordion') {
                $accordion = '';
                $accordionTitle = get_sub_field('sidebar_accordion_title');
                if ($accordionTitle != "") {
                    $accordion .= '<h3 class="sidebar-section-header">' . $accordionTitle . '</h3>';
                }
                if(have_rows('sidebar_accordion_items', $postId)) {
                    $accordion .= '<style>';
                    $accordion .=   'div.usa-accordion-content {padding:1.5rem !important;}';
                    $accordion .= '</style>';

                    $accordion .= '<div class="usa-accordion bbg__committee-list">';
                    $accordion .=       '<ul class="unstyled-list">';
                    $i = 0;
                    while (have_rows('sidebar_accordion_items', $postId)) : the_row();
                        $i++;
                        $itemLabel = get_sub_field('sidebar_accordion_item_label');
                        $itemText = get_sub_field('sidebar_accordion_item_text');

                        $accordion .=   '<li>';
                        $accordion .=       '<button class="usa-button-unstyled" aria-expanded="false" aria-controls="collapsible-faq-' . $i . '">' . $itemLabel . '</button>';
                        $accordion .=       '<div id="collapsible-faq-' . $i . '" aria-hidden="true" class="usa-accordion-content">';
                        $accordion .=           $itemText;
                        $accordion .=       '</div>';
                        $accordion .=   '</li>';
                    endwhile;
                    $accordion .=   '</ul>';
                    $accordion .= '</div>';

                    $sidebar_markup .= $accordion;
                }
            }
            else if (get_row_layout() == 'sidebar_twitter_widget') {
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
                    $sidebar_markup .=  '<li>';
                    $sidebar_markup .=      '<a href="' . $widgetLink . '" title="Follow on Twitter"><span class="bbg__article-share__icon twitter"></span><span class="">' . $widgetLinkLabel . '</span></a>';
                    $sidebar_markup .=  '</li>';
                    $sidebar_markup .= '</ul>';

                    if ($widgetAuthor) {
                        $sidebar_markup .= '<a data-tweet-limit="2" data-show-replies="false" data-chrome="noheader nofooter noborders transparent noscrollbar" data-dnt="true" data-theme="light" class="twitter-timeline" href="https://twitter.com/' . $widgetAuthor . '">Tweets by ' . $widgetAuthor . '</a> <script type="text/javascript" async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
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
                    $sidebar_markup .=  '<ul class="unstyled-list">';
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
                                    $sidebar_markup .= '<br><br>';
                                }
                                $id = get_the_ID();
                                if ($pastOrFuture == "past") {
                                    $permalink = get_the_permalink();
                                } else {
                                    /**** wordpress doesn't return a nice permalink for scheduled posts, so we have a workaround ***/
                                    $my_post = clone $post;
                                    $my_post->post_status = 'published';
                                    $my_post->post_name = sanitize_title($my_post->post_name ? $my_post->post_name : $my_post->post_title, $my_post->ID);
                                    $permalink = get_permalink($my_post);
                                }

                                $title = get_the_title();
                                $sidebar_markup .= '<a style="text-decoration:none;" href="' . $permalink . '">' . $title . '</a>';
                            }
                            $sidebar_markup .= '</p>';
                        }
                        $sidebar_markup .= '<br>';
                    }
                    wp_reset_postdata();
                }
            } else if (get_row_layout() == 'sidebar_related_posts') {
                $relatedPostsTitle = get_sub_field('sidebar_related_posts_title');
                if ($relatedPostsTitle != "") {
                    $sidebar_markup .= '<h3 class="sidebar-section-header">' . $relatedPostsTitle . '</h3>';
                }

                $sidebar_markup .= do_shortcode('[yarpp template="yarpp-template-related-posts"]');
            } else if (get_row_layout() == 'sidebar_free_text') {
                $freeTextTitle = get_sub_field('sidebar_free_text_title');
                if ($freeTextTitle != "") {
                    $sidebar_markup .= '<h3 class="sidebar-section-header">' . $freeTextTitle . '</h3>';
                }

                $sidebar_markup .= get_sub_field('sidebar_free_text_text');
            } else if (get_row_layout() == 'sidebar_languages_served') {
                $sidebar_markup .= '<div class="nest-container">';
                $sidebar_markup .=     '<div class="inner-container">';
                $sidebar_markup .=         '<div class="grid-container">';
                $sidebar_markup .=             '<h3 class="sidebar-section-header">Languages Served</h3>';
                $sidebar_markup .=         '</div>';

                while(have_rows('sidebar_languages_served_languages')) {
                    the_row();

                    $sidebar_markup .=     '<div class="grid-container">';
                    $sidebar_markup .=         '<h6>' . get_sub_field('sidebar_languages_served_language_name') . '</h6>';
                    $sidebar_markup .=     '</div>';

                    if(have_rows('sidebar_languages_served_language_sites')) {
                        while(have_rows('sidebar_languages_served_language_sites')) {
                            the_row();

                            $link = get_sub_field('sidebar_languages_served_language_site_url');
                            $serviceInLanguage = get_sub_field('sidebar_languages_served_language_site_name_in_language');
                            $serviceInEnglish = get_sub_field('sidebar_languages_served_language_site_name_in_english');
                            $network = get_sub_field('sidebar_languages_served_language_site_network');
                            $serviceName = $serviceInLanguage;
                            $entityLogo = getTinyEntityLogo($network);

                            if (!$entityLogo) {
                                $entityLogo = '';
                            }

                            $sidebar_markup .= '<div class="inner-container">';
                            $sidebar_markup .=     '<div class="small-side-content-container">';
                            $sidebar_markup .=         '<img width="20" height="20" style="height:20px !important; width:20px !important; max-width:none; margin-bottom:0;" src="' . $entityLogo . '" alt="Entity logo">';
                            $sidebar_markup .=         '<a title="' . $serviceInEnglish . '" target="_blank" href="' . $link . '" class="bbg__jobs-list__title">' . $serviceName . '</a>';
                            $sidebar_markup .=     '</div>';
                            $sidebar_markup .=     '<div class="small-main-content-container">';
                            $sidebar_markup .=         str_replace(array('https://', 'http://'), '', $link);
                            $sidebar_markup .=     '</div>';
                            $sidebar_markup .= '</div>';
                        }
                    }
                }
                $sidebar_markup .=     '</div>';
                $sidebar_markup .= '</div>';
            }
        }
    }

    return $sidebar_markup;
}

function getSidebarDropdownContent() {
    /**
     * Sidebar drop-down for multiple downloads (2-col pages)
     * @var [boolean]
     */

    global $post;

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
                    $sidebarDownloadsEnableSubsection = !empty(get_sub_field('sidebar_downloads_enable_subsection'));
                    $sidebarDownloadsDefault = get_sub_field('sidebar_downloads_default');
                    $sidebarDownloadsRows = get_sub_field('sidebar_downloads' );
                    $sidebarDownloadsTotal = count( $sidebarDownloadsRows);

                    if ($sidebarDownloadsTotal >= 2) {
                        $download_select  = '<div class="sidebar-section">';
                        if ($sidebarDownloadsEnableSubsection) {
                            $download_select .= '<h3 class="sidebar-section-subheader">' . $sidebarDownloadsTitle . '</h3>';
                        } else {
                            $download_select .= '<h3 class="sidebar-section-header">' . $sidebarDownloadsTitle . '</h3>';
                        }
                        $download_select .= '<form style="max-width: 100%;">';
                        $download_select .=     '<select name="file_download_list" id="file_download_list" class="file_download_list" style="display: inline-block; max-width: 100%;">';
                        $download_select .=         '<option>' . $sidebarDownloadsDefault . '</option>';

                        foreach ($sidebarDownloadsRows as $row) {
                            $sidebarDownloadsLinkName = $row['sidebar_download_title'];
                            $sidebarDownloadsLinkObj = $row['sidebar_download_file'];
                            // echo $sidebarDownloadsLinkObj;
                            $fileLink = $sidebarDownloadsLinkObj['url'];
                            $fileID = $sidebarDownloadsLinkObj['ID'];
                            $file = get_attached_file( $fileID );
                            $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                            $size = formatBytes(filesize($file));

                            if ($sidebarDownloadsLinkName == "" || !$sidebarDownloadsLinkName) {
                                $name = $sidebarDownloadsLinkObj['title'];
                                $sidebarDownloadsLinkName = $name;
                            }

                            $download_select .=         '<option value="' . $fileLink . '">';
                            $download_select .=             $sidebarDownloadsLinkName;
                            $download_select .=             ' <span class="bbg__file-size">(' . $ext . ', ' . $size . ')</span>';
                            $download_select .=         '</option>';
                        }

                        $download_select .=         '</select>';
                        $download_select .=     '</form>';
                        $download_select .=     '<button class="usa-button downloadFile" id="downloadFile" style="width: 100%;">Download</button>';
                        $download_select .= '</div>';
                        $s .= $download_select;
                    }
                    else {
                        $sidebarDownloadsRows = get_sub_field('sidebar_downloads');

                        $download_list = '';
                        if ($sidebarDownloadsEnableSubsection) {
                            $download_list .= '<h3 class="sidebar-section-subheader">' . $sidebarDownloadsTitle . '</h3>';
                        } else {
                            $download_list .= '<h3 class="sidebar-section-header">' . $sidebarDownloadsTitle . '</h3>';
                        }
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
                            $download_list .=   '<a target="_blank" href="' . $fileLink . '">' . $sidebarDownloadsLinkName . '</a>';
                            $download_list .=   '<span class="bbg__file-size">(' . $ext . ', ' . $size . ')</span>';
                            $download_list .= '<h4>';
                        }
                        $s .= $download_list;
                    }
                } else if (get_row_layout() == 'file_downloads_and_external_links') {
                    $downloadsAndExternalLinksTitle = get_sub_field('downloads_and_external_links_title');
                    $downloadsAndExternalLinksDefaultValue = get_sub_field('downloads_and_external_links_default_value');
                    $downloadsAndExternalLinksObjects = get_sub_field('downloads_and_external_links_objects');
                    $downloadsAndExternalLinksObjectsCount = count($downloadsAndExternalLinksObjects);

                    $downloadsAndExternalLinksSelect  = '<div class="sidebar-section">';
                    $downloadsAndExternalLinksSelect .=     '<h3 class="sidebar-section-header">' . $downloadsAndExternalLinksTitle . '</h3>';

                    $downloadsAndExternalLinksSelect .= '<form style="max-width: 100%;">';
                    $downloadsAndExternalLinksSelect .=     '<select name="downloadsAndExternalLinksList" id="downloadsAndExternalLinksList" style="display: inline-block; max-width: 100%;">';
                    $downloadsAndExternalLinksSelect .=         '<option>' . $downloadsAndExternalLinksDefaultValue . '</option>';

                    if (have_rows('downloads_and_external_links_objects')) {
                        while (have_rows('downloads_and_external_links_objects')) : the_row();
                            $downloadsAndExternalLinkTitle = get_sub_field('download_or_external_link_title');
                            if (have_rows('download_or_external_link_content')) {
                                while (have_rows('download_or_external_link_content')) : the_row();
                                    if (get_row_layout() == 'download_or_external_link_content_external_link') {
                                        $downloadsAndExternalLinkUrl = get_sub_field('download_or_external_link_content_external_link_url');

                                        if ($downloadsAndExternalLinkTitle == '' || !$downloadsAndExternalLinkTitle) {
                                            $name = $downloadsAndExternalLinkUrl['title'];
                                            $downloadsAndExternalLinkTitle = $name;
                                        }

                                        $url = $downloadsAndExternalLinkUrl['url'];
                                        $downloadsAndExternalLinksSelect .=     '<option data-file-or-link="link" value="' . $url . '">' . $downloadsAndExternalLinkTitle . '</option>';
                                    } else if (get_row_layout() == 'download_or_external_link_content_file') {
                                        $downloadsAndExternalLinkFile = get_sub_field('download_or_external_link_content_file_object');

                                        $fileLink = $downloadsAndExternalLinkFile['url'];
                                        $fileID = $downloadsAndExternalLinkFile['ID'];
                                        $file = get_attached_file($fileID);
                                        $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                                        $size = formatBytes(filesize($file));

                                        if ($downloadsAndExternalLinkTitle == '' || !$downloadsAndExternalLinkTitle) {
                                            $name = $downloadsAndExternalLinkFile['title'];
                                            $downloadsAndExternalLinkTitle = $name;
                                        }

                                        $downloadsAndExternalLinksSelect .=         '<option data-file-or-link="file" value="' . $fileLink . '">';
                                        $downloadsAndExternalLinksSelect .=             $downloadsAndExternalLinkTitle;
                                        $downloadsAndExternalLinksSelect .=             ' <span class="bbg__file-size">(' . $ext . ', ' . $size . ')</span>';
                                        $downloadsAndExternalLinksSelect .=         '</option>';
                                    } else {
                                        // Shouldn't happen
                                    }
                                endwhile;
                            }
                        endwhile;
                    }

                    $downloadsAndExternalLinksSelect .=         '</select>';
                    $downloadsAndExternalLinksSelect .=     '</form>';
                    $downloadsAndExternalLinksSelect .=     '<button class="usa-button downloadsAndExternalLinks" id="downloadsAndExternalLinks" style="width: 100%;">View</button>';
                    $downloadsAndExternalLinksSelect .= '</div>';
                    $s .= $downloadsAndExternalLinksSelect;
                } elseif (get_row_layout() == 'sidebar_dropdown_internal_links') {
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
                            $sidebar_internal_links .=  '<a href="' . $url . '">' . $sidebarInternalLinkName . '</a>';
                            $sidebar_internal_links .= '</h4>';
                        }
                        $s .= $sidebar_internal_links;
                    } else {
                        $sidebar_form  = '<form>';
                        $sidebar_form .=    '<label for="options" style="display: inline-block; font-size: 2rem; font-weight: bold; margin-top: 0;">' . $sidebarInternalTitle . '</label>';
                        $sidebar_form .=    '<select name="internal_links_list" class="internal_links_list" style="display: inline-block;">';
                        $sidebar_form .=        '<option>Select a link</option>';

                        foreach( $sidebarInternalRows as $link ) {
                            $sidebarInternalLinkName = $link['internal_links_title'];
                            $sidebarInternalLinkObj = $link['internal_links_url'];
                            $url = get_permalink($sidebarInternalLinkObj->ID);

                            if ($sidebarInternalLinkName == "" | !$sidebarInternalLinkName) {
                                $title = $sidebarInternalLinkObj->post_title;
                                $sidebarInternalLinkName = $title;
                            }
                            $sidebar_form .=    '<option value="' . $url . '">' . $sidebarInternalLinkName . '</option>';
                        }
                        $sidebar_form .=    '</select>';
                        $sidebar_form .= '</form>';
                        $sidebar_form .= '<button class="usa-button internalLink" style="width: 100%;">Go</button>';
                        $s .= $sidebar_form;
                    }
                    $s .= '</div>';
                } else if (get_row_layout() == 'sidebar_dropdown_section_heading') {
                    $sectionHeadingText = get_sub_field('section_heading_text');
                    $s .= '<h3 class="sidebar-section-header">' . $sectionHeadingText . '</h3>';
                }
            endwhile;
            return $s;
        }
    }
    return '';
}

?>