<?php
/**
 * Leverages the USDS side menu.  Not currently in use.
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 * template name: Side Menu
 */

get_header(); ?>

<main id="main" role="main">
	<?php
		while (have_posts()) {
			the_post();
			get_template_part('template-parts/content', 'page');
		}
	?>
</main>

<aside id="menu-content" class="widget-area sidenav" role="complementary">
    <!-- Adding main navigation to the sidebar -->
    <?php
    	wp_nav_menu(
    		array('theme_location' => 'menu-side',
    		'container' => 'nav',
    		'container_class' => '',
    		'menu_id' => 'primary-menu',
    		'menu_class' => 'menu usa-sidenav-list',
    		'walker' => new bbginnovate_walker_nav_menu())
    	);
    ?>
</aside>

<?php //get_footer(); ?>
