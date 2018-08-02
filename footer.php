<?php
/**
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * US Web Design Standards (alpha) includes 3 footers alternatives.
 * @link https://playbook.cio.gov/designstandards/footers/
 *
 * @package bbgRedesign
 */

?>

<footer id="footer">
	<div class="outer-container">
		<div id="footer-wrapper">
			<div class="footer-brand">
			<?php if (is_page_template('usagm-intro.php')) { ?>
				<a class="site-brand" href="<?php echo get_home_url(); ?>/home">
			<?php } else { ?>
				<a class="site-brand" href="<?php echo get_home_url(); ?>">
			<?php } ?>
					<img src="<?php echo get_template_directory_uri(); ?>/img/USAGM-BBG-logo-horiz-White-hires.png">
				</a>
			</div>
			<?php
				wp_nav_menu(); 
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>