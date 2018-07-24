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


<div class="top-return-box">
	<div class="top-return-arrow">
		<a href="#"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
	</div>
</div>

<footer id="footer">
	<div class="outer-container">
		<div id="footer-wrapper">
			<div class="footer-brand">
				<a class="site-brand" href="<?php echo get_home_url(); ?>">
					<img src="<?php echo get_template_directory_uri(); ?>/img/USAGM-BBG-logo-horiz-RGB-hires.png">
				</a>
			</div>

			<div class="footer-nav">
				<a href="/press-room">Press Room</a>
				<a href="/news/network-highlights/newsletter-archives/signup/">Sign Up</a>
			</div>

			<div class="footer-contact">
				<h6>Contact the BBG</h6>
				<p id="footer-address">330 Independence Avenue, SW, Washington, DC 20237</p>
				<p><a href="tel:+01-202-203-4000">(202) 203-4000</a></p> |
				<p><a href="mailto:publicaffairs@bbg.gov">publicaffairs@bbg.gov</a></p>
			</div>
			<div class="footer-social">
				<a href="https://www.facebook.com/BBGgov/"><i class="fab fa-facebook-f"></i></a>
				<a href="https://twitter.com/BBGgov"><i class="fab fa-twitter"></i></a>
				<a href="https://www.youtube.com/user/bbgtunein"><i class="fab fa-youtube"></i></a>
				<a href="https://www.bbg.gov/category/press-release/feed/"><i class="fas fa-rss"></i></a>
			</div>
		</div>
	</div>
	<?php
		if (have_rows('site_setting_footer_links', 'option')) {
			echo '<div class="footer-sub-nav">';
			while(have_rows('site_setting_footer_links', 'option')) {
				the_row();
				$anchorText = get_sub_field('anchor_text');
				$anchorLink = get_sub_field('anchor_link');
				echo '<div class="bbg__footer__sub__required-link"><a href="' . $anchorLink . '">' . $anchorText . '</a></div>';
			}
			echo '</div>';
		}
	?>
</footer>

<?php wp_footer(); ?>

</body>
</html>
