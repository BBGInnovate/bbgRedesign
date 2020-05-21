<?php

function usagm_experts_list() {
    $entity_value = '';
    if (!empty($_GET['entity'])) {
        $entity_value = htmlspecialchars($_GET['entity']);
        $entity_filter = array(
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => $entity_value,
        );
    } else {
        $entity_filter = '';
    }

    $expertise_value = '';
    if (!empty($_GET['expertise'])) {
        $expertise_value = htmlspecialchars($_GET['expertise']);
        $expertise_filter = array(
            'taxonomy' => 'expertise',
            'field'    => 'slug',
            'terms'    => $expertise_value,
        );
    } else {
        $expertise_filter = '';
    }

    $language_value = '';
    if (!empty($_GET['language'])) {
        $language_value = htmlspecialchars($_GET['language']);
        $language_filter = array(
            'taxonomy' => 'language',
            'field'    => 'slug',
            'terms'    => $language_value,
        );
    } else {
        $language_filter = '';
    }

    $location_value = '';
    if (!empty($_GET['location'])) {
        $location_value = htmlspecialchars($_GET['location']);
        $location_filter = array(
            'taxonomy' => 'location',
            'field'    => 'slug',
            'terms'    => $location_value,
        );
    } else {
        $location_filter = '';
    }

    $usagm_experts_args = array (
        'posts_per_page' => -1,
        'post_type' => 'experts',
        'tax_query' => array(
            $entity_filter,
            $expertise_filter,
            $language_filter,
            $location_filter,
        )
    );
    $usagm_experts_query = new WP_Query($usagm_experts_args);
    $usagm_experts_array = $usagm_experts_query->posts;

    $expert_id_list = array();
    $all_profiles = '';

    $experts_markup  = '<h5>Filter by:</h5>';
    if (!empty($entity_filter) || !empty($expertise_filter || !empty($language_filter) || !empty($location_filter))) {
        $experts_markup .= '<br><span class="date-meta"><a href="/experts">Remove all filters</a></span>';
    }

    $experts_markup .= '<form class="bbg__form__experts-filter" action="" method="GET">';
    $experts_markup .= '<div class="inner-container">';
    $experts_markup .=     '<label class="grid-five">Entity<br />';
    $experts_markup .=         '<select name="entity">';
    $experts_markup .=             '<option value="">ALL</option>';
    $experts_markup .=             '<option value="voa">VOA</option>';
    $experts_markup .=             '<option value="ocb">OCB</option>';
    $experts_markup .=             '<option value="rferl">RFE/RL</option>';
    $experts_markup .=             '<option value="rfa">RFA</option>';
    $experts_markup .=             '<option value="mbn">MBN</option>';
    $experts_markup .=             '<option value="otf">OTF</option>';
    $experts_markup .=         '</select>';
    $experts_markup .=     '</label>';

    $expertise_args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'echo' => 0,
        'show_option_all' => 'ALL',
        'name' => 'expertise',
        'hide_empty' => 0,
        'value_field' => 'slug',
        'taxonomy' => 'expertise'
    );
    $experts_markup .= '<label class="grid-five">Expertise<br />';
    $experts_markup .= wp_dropdown_categories($expertise_args);
    $experts_markup .= '</label>';

    $language_args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'echo' => 0,
        'show_option_all' => 'ALL',
        'name' => 'language',
        'hide_empty' => 0,
        'value_field' => 'slug',
        'id' => '',
        'taxonomy' => 'language'
    );
    $experts_markup .= '<label class="grid-five">Language<br />';
    $experts_markup .= wp_dropdown_categories($language_args);
    $experts_markup .= '</label>';

    $location_args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'echo' => 0,
        'show_option_all' => 'ALL',
        'name' => 'location',
        'hide_empty' => 0,
        'value_field' => 'slug',
        'id' => '',
        'taxonomy' => 'location'
    );
    $experts_markup .= '<label class="grid-five">Location<br />';
    $experts_markup .= wp_dropdown_categories($location_args);
    $experts_markup .= '</label>';

    $experts_markup .=     '<input type="submit" name="submit" value="Filter" />';
    $experts_markup .= '</div>';
    $experts_markup .= '</form>';

    foreach ($usagm_experts_array as $expert_id) {
        $first_name = get_post_meta($expert_id->ID, 'first_name', true);
        $last_name = get_post_meta($expert_id->ID, 'last_name', true);
        $occupation = get_post_meta($expert_id->ID, 'occupation', true);
        $expertise = getTermsStringFromPost($expert_id->ID, 'expertise');
        $languages = getTermsStringFromPost($expert_id->ID, 'language');
        $locations = getTermsStringFromPost($expert_id->ID, 'location');
        $profile_photo_id = get_post_meta($expert_id->ID, 'profile_photo', true);
        $profile_name = $first_name . ' ' . $last_name;
        $profile_link = get_the_permalink($expert_id->ID);

        if ($profile_photo_id) {
            $profile_photo = wp_get_attachment_image_src($profile_photo_id , 'thumbnail');
            $profile_photo = $profile_photo[0];
        } else {
            $profile_photo = get_template_directory_uri() . '/img/photo-not-available-300x300.png';
        }

        $expert_profile  = '<div class="grid-half profile-clears">';
        if (!empty($profile_photo)) {
            $expert_profile .= '<a href="' . $profile_link . '">';
            $expert_profile .=     '<img src="' . $profile_photo . '" class="bbg__profile-excerpt__photo" alt="Photo of ' . $profile_name . '">';
            $expert_profile .= '</a>';
        }
        $expert_profile .=     '<h3 class="article-title">';
        $expert_profile .=         '<a href="' . $profile_link . '">' . $profile_name . '</a>';
        $expert_profile .=     '</h3>';
        $expert_profile .=     '<p>';
        $expert_profile .=         '<span class="bbg__profile-excerpt__occupation">' . $occupation . '</span>';
        if (!empty($expertise)) {
            $expert_profile .=  '<strong>Expertise:</strong> ' . $expertise;
        }
        if (!empty($languages)) {
            $expert_profile .=  '<br><strong>Languages:</strong> ' . $languages;
        }
        if (!empty($locations)) {
            $expert_profile .=  '<br><strong>Locations:</strong> ' . $locations;
        }
        $expert_profile .=     '</p>';
        $expert_profile .= '</div>';

        $all_profiles .= $expert_profile;
    }

    $experts_markup .= '<div class="nest-container">';
    $experts_markup .=     $all_profiles;
    $experts_markup .= '</div>';

    $experts_markup .= '
        <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $("select[name=entity] option[value=\"' . $entity_value . '\"").attr("selected", "selected");
                $("select[name=expertise] option[value=\"' . $expertise_value . '\"").attr("selected", "selected");
                $("select[name=language] option[value=\"' . $language_value . '\"").attr("selected", "selected");
                $("select[name=location] option[value=\"' . $location_value . '\"").attr("selected", "selected");
            });
        })(jQuery);
        </script>
    ';

    return $experts_markup;
}

/* Get the excerpts for sidebar items that are Press Releases under the Threats to Press page */
function getSidebarPressReleaseExcerpts($profile_id) {
    $pressReleaseExcerpts = '';

    $parentId = wp_get_post_parent_id($profile_id);

    $threatsToPressPage = get_page_by_path('/news-and-information/threats-to-press');
    if ($parentId == $threatsToPressPage->ID) {
        if (have_rows('sidebar_items', $profile_id)) {
            while (have_rows('sidebar_items', $profile_id)) {
                the_row();
                if (get_row_layout() == 'sidebar_internal_link') {
                    $sidebarInternalLocation = get_sub_field('sidebar_internal_location');
                    $id = $sidebarInternalLocation->ID;
                    $excerpt = $sidebarInternalLocation->post_excerpt;

                    $categories = wp_list_pluck(get_the_category($id), 'slug');
                    if (in_array('press-release', $categories) && in_array('threats-to-press', $categories)) {
                        $pressReleaseExcerpts .= '<p>' . $excerpt . '</p>';
                    }
                }
            }
            reset_rows('sidebar_items', $profile_id);
        }
    }

    $pressReleaseExcerpts = do_shortcode($pressReleaseExcerpts);

    return $pressReleaseExcerpts;
}

?>