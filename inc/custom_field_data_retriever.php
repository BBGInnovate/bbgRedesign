<?php
function get_marquee_data() {
	$marquee_heading = get_sub_field('marquee_heading');
	$marquee_link = get_sub_field('marquee_link');
	$marquee_content = get_sub_field('marquee_content');
	$marquee_content = apply_filters( 'the_content', $marquee_content );
	$marquee_content = str_replace( ']]>', ']]&gt;', $marquee_content );

	$marquee_package = array('heading' => $marquee_heading, 'link' => $marquee_link, 'content' => $marquee_content);
	return $marquee_package;
}

function get_umbrella_main_data() {
	$section_heading = get_sub_field('umbrella_section_heading');
	$section_heading_link = get_sub_field('umbrella_section_heading_link');
	$force_content_labels = get_sub_field('umbrella_force_content_labels');
	$section_intro_text = get_sub_field('umbrella_section_intro_text');
	$section_intro_text = apply_filters( 'the_content', $section_intro_text );
	$section_intro_text = str_replace( ']]>', ']]&gt;', $section_intro_text );

	$umbrella_package = array('header' => $section_heading, 'header_link' => $section_heading_link, 'forced_label' => $force_content_labels, 'intro_text' => $section_intro_text);
	return $umbrella_package;
}
?>