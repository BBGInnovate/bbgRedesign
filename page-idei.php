<?php
/**
 * Custom landing page for the "Who we are" and "Our Work" sections
 *
 * template name: IDEI
 *
 * @author Gigi Frias <gfrias@bbg.gov>
 * @package bbgRedesign
 */

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$id = get_the_ID();
		$page_title = get_the_title();
		$page_content = do_shortcode(get_the_content());
		$page_content = apply_filters('the_content', $page_content);
		$ogDescription = get_the_excerpt();
	}
}
wp_reset_postdata();
wp_reset_query();

// GET THE CARD DATA
$cards_markup = '';
$card_field_data = get_field('journalist_flip_card');
if (!empty($card_field_data)) {
	foreach ($card_field_data as $cur_card) {
		$single_card = array(
			'image' => $cur_card['front_image'],
			'name' => $cur_card['full_name'],
			'organization' => $cur_card['organization']
		);
		$cards_markup .= build_card($single_card);
	}
}

// BUILD THE CARD MARKUP
function build_card($card_data) {
	$card_block  = '<div class="flip-card">';
	$card_block .= 	'<div class="flip-card-inner">';
	$card_block .= 		'<div class="flip-card-front">';
	$card_block .= 			'<img src="' . $card_data['image'] . '" alt="' . $card_data['name'] . '" style="width:180px;height:252px;">';
	$card_block .= 		'</div>';
	$card_block .= 		'<div class="flip-card-back">';
	$card_block .= 			'<div class="card-back-content">';
	$card_block .= 				'<p>' . $card_data['name'] . '</p>';
	$card_block .= 				'<p>' . $card_data['organization'] . '</p>';
	$card_block .= 			'</div>';
	$card_block .= 		'</div>';
	$card_block .= 	'</div>';
	$card_block .= '</div>';
	return $card_block;
}

get_header();
?>

<main id="main" role="main">
	<div id="idei" class="outer-container">
		<div class="grid-container">
			<h2 class="section-header"><?php echo $page_title; ?></h2>

			<?php
				if (!empty($cards_markup)) {
					echo $cards_markup;
				}
			?>
		</div>
	</div>
</main>

<?php get_footer(); ?>