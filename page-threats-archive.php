<?php
/**
 * The template for displaying the Threats to Press archive page.
 * @package bbginnovate
  template name: Threats to Press Archive
 */
require 'inc/bbg-functions-assemble.php';

$qParams = array(
	'post_type'=> 'threat_to_press',
	'post_status' => 'publish',
	'orderby' => 'post_date',
	'order' => 'desc',
	'posts_per_page' => -1,
	'date_query' => array(
        array(
        	'after' => '2016-01-01 00:00:00',
            'before' => '2016-12-31 23:59:59'
        )
    )
);

$custom_query = new WP_Query($qParams);

$threats = array();
if ($custom_query->have_posts()) {
	while ($custom_query->have_posts()) {
		$custom_query->the_post();
		$id = get_the_ID();
		$country = get_post_meta( $id, 'threats_to_press_country', true );
		$targetNames = get_post_meta( $id, 'threats_to_press_target_names', true );
		$networks = get_post_meta( $id, 'threats_to_press_network', true );
		$coordinates = get_post_meta( $id, 'threats_to_press_coordinates', true );
		$status = get_post_meta( $id, 'threats_to_press_status', true );
		$link = get_post_meta( $id, 'threats_to_press_link', true );

		$threat_post_data = array(
			'country' => $country,
			'name' => $targetNames,
			'date' => get_the_date(),
			'year' => get_the_date('Y'),
			'niceDate' => get_the_date('M d, Y'),
			'status' => $status,
			'description' => get_the_excerpt(),
			'mugshot' => '',
			'network' => $networks,
			'link' => $link,
			'latitude' => $coordinates['lat'],
			'longitude' => $coordinates['lng'],
			'headline' => get_the_title()
		);
		$threats[] = $threat_post_data;
	}
}
wp_reset_postdata();
wp_reset_query();


$pageContent = "";
$pageTitle = "";
$pageExcerpt = "";
$id = 0;
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$pageContent = get_the_content();
		$pageTitle = get_the_title();
		$pageExcerpt = get_the_excerpt();
		$pageContent = apply_filters( 'the_content', $pageContent );
		$pageContent = str_replace( ']]>', ']]&gt;', $pageContent );
		$id = get_the_ID();
	endwhile;
endif;
wp_reset_postdata();
wp_reset_query();

get_header();
?>

<?php
	$featured_media_result = get_feature_media_data();
	if ($featured_media_result != "") {
		echo $featured_media_result;
	}
?>

<main id="main" role="main">
	<div class="outer-container">
		<div class="grid-container">
			<h2 class="section-header">Threats to Press</h2>
		</div>
	</div>

	<?php if (count($threats)) : ?>
		<div class="outer-container">
			<div class="grid-container">
				<h3 class="section-subheader"><?php echo $pageTitle; ?></h3>
				<?php
					foreach ($threats as $threat_post) {
						$imgSrc = '';
						foreach ($threat_post['network'] as $abbreviation) {
							$imgSrc = get_template_directory_uri() . '/img/logo_' . $abbreviation . '--circle-200.png'; //
						}

						echo '<div>';
						echo 	'<header class="bbg__article-icons-container">';
						echo 		'<div class="bbg__article-icon" style="background-position: left 0.25rem; background-image: url(' . $imgSrc . ');"></div>';
						echo 		'<h4 class="article-title">' . $threat_post['headline'] . '</h4>';
						echo 	'</header>';
						echo 	'<div class="sans">';
						echo 		'<time>' . $threat_post['niceDate'] . '</time>';
						echo 	'</div>';
						echo 	'<p>' . $threat_post['description'] . '</p>';
						echo '</div>';
					}
				?>
			</div>
		</div>
		<?php endif; ?>
</main>

<?php get_footer(); ?>