<?php
/**
 * The template for displaying the Contact Us page.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbginnovate
  template name: Contact Us
 */

require 'inc/bbg-functions-assemble.php';
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

// PAGE INFORMATION
$page_content = "";
$page_title = "";
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$page_content = get_the_content();
		$page_title = get_the_title();
		$page_excerpt = get_the_excerpt();
	}
}
wp_reset_postdata();
wp_reset_query();


$contactUsColumn1 = get_field('contact_us_column_1');
$contactUsColumn2 = get_field('contact_us_column_2');

$contactUsColumn1 = do_shortcode($contactUsColumn1);
$contactUsColumn1 = apply_filters('the_content', $contactUsColumn1);
$contactUsColumn2 = do_shortcode($contactUsColumn2);
$contactUsColumn2 = apply_filters('the_content', $contactUsColumn2);

get_header();

?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>
<main id="main" role="main">
	<?php
		$contact_us_structure  = '<section class="outer-container contact-us">';
		$contact_us_structure .= 	'<div class="grid-container">';
		$contact_us_structure .= 		'<h2 class="section-header">' . $page_title . '</h2>';
		$contact_us_structure .= 		'<p>' . $page_content . '</p>';
		$contact_us_structure .= 	'</div>';
		$contact_us_structure .= 	'<div class="grid-half" id="contact-us-col-1">';
		$contact_us_structure .= 		$contactUsColumn1;
		$contact_us_structure .= 	'</div>';
		$contact_us_structure .= 	'<div class="grid-half" id="contact-us-col-2">';
		$contact_us_structure .= 		$contactUsColumn2;
		$contact_us_structure .= 	'</div>';
		$contact_us_structure .= '</section>';
		echo $contact_us_structure;
	?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>