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
if (!is_page_template('usagm-intro.php')) {
?>

<div id="redirect__overlay">
    <div id="redirect__dialog">
        <div id="redirect__dialog--close"></div>
        <h2>Confirm External Link</h2>
        <p>
            Are you sure you want to navigate to this external link?
            <div id="redirect__link"></div>
        </p>
        <button id="redirect__button--cancel" type="button">Cancel</button>
        <button id="redirect__button--confirm" type="button">Continue</button>
    </div>
</div>

<footer id="footer">
	<div class="outer-container">
		<div id="grid-container">
			<a class="site-brand" href="<?php echo get_home_url(); ?>">
				<img src="<?php echo get_template_directory_uri(); ?>/img/USAGM-BBG-logo-horiz-White-hires.png">
			</a>
		</div>
	</div>

	<div class="outer-container">
		<div class="grid-container">
			<?php if (have_rows('site_setting_footer_links', 'option')): ?>
				<?php
					while (have_rows('site_setting_footer_links', 'option')) {
						the_row();
						$anchorText = get_sub_field('anchor_text');
						$anchorLink = get_sub_field('anchor_link');

						echo '<a class="footer-link" href="' . $anchorLink . '"';
						if (strpos($anchorLink, 'http') === 0) {
							echo ' target="_blank"';
						}
						echo '>' . $anchorText . '</a>';
					}
				?>
		<?php endif; ?>
		</div>
	</div>
</footer>
<?php } ?>

<?php wp_footer(); ?>
</body>
</html>