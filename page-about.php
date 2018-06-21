<?php
/**
 * Custom landing page for the "Who we are" and "Our Work" sections
 *
 * template name: About
 *
 * @author Gigi Frias <gfrias@bbg.gov>
 * @package bbgRedesign
 */

/* @Check if number of pages is odd or even
*  Return BOOL (true/false) */

// FUNCTION THAT BUILD SECTIONS
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

require 'inc/bbg-functions-assemble.php';

function isOdd($pageTotal) {
	return ($pageTotal % 2) ? true : false;
}

$templateName = "about";

if (have_posts()) :
	while (have_posts()) : the_post();
		$id = get_the_id();
		$pageContent = do_shortcode(get_the_content());
	endwhile;
endif;

wp_reset_postdata();
wp_reset_query();

get_header();
?>

<main id="main" class="site-main" role="main">
<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
	$page_content  = '<div class="outer-container">';
	$page_content .= 	'<div class="grid-container">';
	$page_content .= 		'<p class="lead-in">' . $pageContent . '</p>';
	$page_content .= 	'</div>';
	$page_content .= '</div>';
	echo $page_content;
?>
	<?php
		$rows = get_field('about_flexible_page_rows', $id);
// echo '<pre>';
// print_r($row);
// echo '</pre>';
		foreach ($rows as $row) {
			if ($row['acf_fc_layout'] == 'about_ribbon_page') {
				echo 'ribbon';
			}
			elseif ($row['acf_fc_layout'] == 'about_office') {
				echo 'got a office';
			}
			elseif ($row['acf_fc_layout'] == 'umbrella') {
				$umbrella_row = $row;

				// GATHER UMBERLLA'S MAIN DATA
				$main_umbrella_data = get_umbrella_main_data($umbrella_row);
				$main_umbrella_parts = build_umbrella_main_parts($main_umbrella_data);

				// COUNT BLOCKS TO MAKE GRID CLASS (put this in function to lessen clutter here)
				$cur_umbrella_content = $umbrella_row['umbrella_content'];
				$content_count = count($cur_umbrella_content);
				$content_counter = 0;
				$containerClass = 'grid-half';
				if (isOdd($content_count)) {
					$containerClass = 'grid-third';
				}
				// EACH UMBRELLA ROW'S CONTENT
				$content_blocks = array();
				while ($content_counter < $content_count) {
					// GATHER UMBERLLA'S CONTENT DATA
					$umbrella_content_result = get_umbrella_content_data($cur_umbrella_content[$content_counter]);
					$umbrella_content_chunks = build_umbrella_content_parts($umbrella_content_result, $containerClass);
					array_push($content_blocks, $umbrella_content_chunks);
					$content_counter++;
				}
				$umbrella_markup = assemble_umbrella_content_section($main_umbrella_parts, $content_blocks);
				if (!empty($umbrella_markup)) {
					echo $umbrella_markup;
				}
			}
			elseif($row['acf_fc_layout'] == 'marquee') {
				$marquee_row = $row;
				$marquee_data = get_marquee_data($marquee_row);
				$marquee_parts = build_marquee_parts($marquee_data);
				$marquee_module = assemble_umbrella_marquee($marquee_parts);

				if (!empty($marquee_module)) {
					echo $marquee_module;
				}
			}
		}
	?>

	<!-- NETWORK ENTITY LIST -->
	<?php
		$show_networks = get_field('about_networks_row', $id);
		if (!empty($show_networks)) {
			// ["entity-main" | "entity-side"]
			$entity_placement = "entity-main";
			$entity_data = get_entity_data($entity_placement);
		}
	?>
</main>

<?php get_footer(); ?>