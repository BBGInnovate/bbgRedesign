<?php
/**
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
   template name: Citing List
 */

/* THIS IS NOT A TEMPLATE. THIS IS A LIST OF ARTICLES FROM THE LINKS FROM page-press-list.php */
if (!empty($_GET['entity'])) {
	$selected_entity = htmlspecialchars($_GET['entity']);
}
echo $selected_entity;

// GET ALL CITED POSTS FROM SPECIFIC NETWORK
// $cited_network_list = new WP_Query(
// 	array(
// 		'post-type' => 'media_clips',
// 		'citing_outlet' => $selected_entity
// 	)
// );
// if ($cited_network_list -> have_posts()) {
// 	$citing_post_list = array();
// 	while ($cited_network_list -> have_posts()) {
// 		$cited_network_list -> the_post();
// 	}
// 	wp_reset_postdata();
// }

get_header();
?>

<p>hello</p>

<?php get_footer(); ?>