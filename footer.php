<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * Note that we're leveraging a repeater custom field in the footer.  It's in the 'homepage options'
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * US Web Design Standards (alpha) includes 3 footers alternatives.
 * @link https://playbook.cio.gov/designstandards/footers/
 *
 * @package bbgRedesign
 */

?>


<!-- <div class="top-return-box">
	<div class="top-return-arrow">
		<a href="#"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
	</div>
</div> -->

<footer id="footer">
	<div class="outer-container">
		<div id="footer-wrapper">
			<div class="footer-brand">
				<a class="site-brand" href="<?php echo get_home_url(); ?>">
					<img src="<?php echo get_template_directory_uri(); ?>/img/USAGM-BBG-logo-horiz-White-hires.png">
				</a>
			</div>
			<?php
wp_nav_menu(array(
	'theme_location' => 'primary',
	'menu_id' => 'primary-menu',
	'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul><div class="bbg__main-navigation__search">' . $searchBox . '</div>',
	'walker' => new bbginnovate_walker_header_usa_menu()
)); 
			?>
		</div>
	</div>
	<?php
		// if (have_rows('site_setting_footer_links', 'option')) {
		// 	echo '<div class="footer-sub-nav">';
		// 	while(have_rows('site_setting_footer_links', 'option')) {
		// 		the_row();
		// 		$anchorText = get_sub_field('anchor_text');
		// 		$anchorLink = get_sub_field('anchor_link');
		// 		echo '<div class="bbg__footer__sub__required-link"><a href="' . $anchorLink . '">' . $anchorText . '</a></div>';
		// 	}
		// 	echo '</div>';
		// }
	?>
</footer>

<?php wp_footer(); ?>

</body>
</html>
