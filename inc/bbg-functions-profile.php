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

    $region_value = '';
    if (!empty($_GET['region'])) {
        $region_value = htmlspecialchars($_GET['region']);
        $region_filter = array(
            'taxonomy' => 'region',
            'field'    => 'slug',
            'terms'    => $region_value,
        );
    } else {
        $region_filter = '';
    }

    $subject_area_value = '';
    if (!empty($_GET['subject-area'])) {
        $subject_area_value = htmlspecialchars($_GET['subject-area']);
        $subject_area_filter = array(
            'taxonomy' => 'subject_area',
            'field'    => 'slug',
            'terms'    => $subject_area_value,
        );
    } else {
        $subject_area_filter = '';
    }

    $usagm_experts_args = array (
        'posts_per_page' => -1,
        'post_type' => 'experts',
        'tax_query' => array(
            $entity_filter,
            $region_filter,
            $subject_area_filter
        )
    );
    $usagm_experts_query = new WP_Query($usagm_experts_args);
    $usagm_experts_array = $usagm_experts_query->posts;

    $expert_id_list = array();
    $all_profiles = '';

    $experts_markup  = '<h5>Filter by:</h5>';
    if (!empty($entity_filter) || !empty($region_filter) || !empty($subject_area_filter)) {
        $experts_markup .= '<br><span class="date-meta"><a href="/experts">Remove all filters</a></span>';
    }

    $experts_markup .= '<form class="bbg__form__experts-filter" action="" method="GET">';
    $experts_markup .=     '<select name="entity">';
    $experts_markup .=         '<option value="">ALL</option>';
    $experts_markup .=         '<option value="voa">VOA</option>';
    $experts_markup .=         '<option value="ocb">OCB</option>';
    $experts_markup .=         '<option value="rferl">RFE/RL</option>';
    $experts_markup .=         '<option value="rfa">RFA</option>';
    $experts_markup .=         '<option value="mbn">MBN</option>';
    $experts_markup .=         '<option value="otf">OTF</option>';
    $experts_markup .=     '</select> ';


    $region_args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'echo' => 0,
        'show_option_all' => 'ALL',
        'name' => 'region',
        'hide_empty' => 0,
        'value_field' => 'name',
        'id' => '',
        'taxonomy' => 'region'
    );
    $experts_markup .= wp_dropdown_categories($region_args);

    $subject_area_args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'echo' => 0,
        'show_option_all' => 'ALL',
        'name' => 'subject-area',
        'hide_empty' => 0,
        'value_field' => 'name',
        'taxonomy' => 'subject_area'
    );
    $experts_markup .= wp_dropdown_categories($subject_area_args);

    $experts_markup .=     '<input type="submit" name="submit" value="Filter" />';
    $experts_markup .= '</form>';

    foreach ($usagm_experts_array as $expert_id) {
        $first_name = get_post_meta($expert_id->ID, 'first_name', true);
        $last_name = get_post_meta($expert_id->ID, 'last_name', true);
        $occupation = get_post_meta($expert_id->ID, 'occupation', true);
        $profile_photo_id = get_post_meta($expert_id->ID, 'profile_photo', true);
        $profile_name = $first_name . ' ' . $last_name;
        $profile_link = get_the_permalink($expert_id->ID);

        if  ($profile_photo_id) {
            $profile_photo = wp_get_attachment_image_src($profile_photo_id , 'mugshot');
            $profile_photo = $profile_photo[0];
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
                $("select[name=region] option[value=\"' . $region_value . '\"").attr("selected", "selected");
                $("select[name=subject-area] option[value=\"' . $subject_area_value . '\"").attr("selected", "selected");
            });
        })(jQuery);
        </script>
    ';

    return $experts_markup;
}

?>