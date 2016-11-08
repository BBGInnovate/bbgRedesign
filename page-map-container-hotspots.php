<?php
/**
 * The template for containing maps created using our ammap.js Vector maps
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bbgRedesign
 * template name: Map Container Hotspot
 */

$pageContent="";
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		$pageContent = get_the_content();
		$pageContent = apply_filters('the_content', $pageContent);
		$pageContent = str_replace(']]>', ']]&gt;', $pageContent);
	endwhile;
endif;
wp_reset_postdata();
wp_reset_query();

get_header();
echo getNetworkExcerptJS();
 ?>

<style>
.legendBox {
	width: 15px;
	height: 15px;
	display:inline-table;
	background: #000000;
}
.entity-buttons button { 
	border-top:3px;
	border-left:3px;
	border-right:3px;
	border-bottom:0px;
	border-style: solid;
}
</style>

	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/vendor/ammap.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/mapdata-worldLow.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/map-hotspot.js'></script>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="usa-grid-full">
				<div class="usa-grid">
					<header class="page-header">
						
						<h5 class="bbg__label--mobile large"><a href="<?php echo $parent_link; ?>">Section Title<?php //echo $parent->post_title; ?></a></h5>
						<?php echo "<h1 class='entry-title'>Hot Spots</h1>"; ?>
					</header><!-- .page-header -->
					<h3 id="site-intro" class="usa-font-lead">Curabitur convallis eleifend ipsum et malesuada. Donec in egestas mauris, et tincidunt sapien. Lorem ipsum dolor sit amet, consectetur adipiscing elit.  <!--<a href="/who-we-are/" class="bbg__read-more">LEARN MORE »</a>--></h3>
				</div><!-- div.usa-grid -->
			</div><!-- div.usa-grid-full -->
			<section class="usa-section">
				<div class="usa-grid">
					<div class="btn-group entity-buttons u--show-medium-large" role="group" aria-label="..." style="clear: none;">
						<button type="button" title="ALL" class=" btn-default all" value="all"><span class="bbg__map__button-text">ALL</span></button><!--
						--><button type="button" title="CHINA" class=" btn-default china" value="china"><span class="bbg__map__button-text">CHINA</span></button><!--
						--><!--
						--><button type="button" title="CUBA" class=" btn-default cuba" value="cuba"><span class="bbg__map__button-text">CUBA</span></button><!--
						--><button type="button" title="IRAN" class=" btn-default iran" value="iran"><span class="bbg__map__button-text">IRAN</span></button><!--
						--><button type="button" title="RUSSIA" class=" btn-default russia" value="russia"><span class="bbg__map__button-text">RUSSIA</span></button><button type="button" title="COUNTERING VIOLENT EXTREMISM" class=" btn-default cve" value="cve"><span class="bbg__map__button-text">COUNTERING VIOLENT EXTREMISM</span></button>
					</div>
					<div align="center" id="mapFilters" class="u--hide-medium-large">
						<BR>
						<select id="hotSpotPicker">
							<option value="all">All</option>
							<option value="china">China</option>
							<option value="cve">CVE</option>
							<option value="cuba">Cuba</option>
							<option value="iran">Iran</option>
							<option value="russia">Russia</option>
						</select>
					</div>

					<div class="bbg__map-area__container " style="postion: relative;">
						<div id="chartdiv"></div>
					</div><BR>

					<div align="center" class="u--hide-medium-large">
						<div class="legendBox china"></div> China
						<div class="legendBox cve"></div> Countering Violent Extremism
						<div class="legendBox cuba"></div> Cuba
						<div class="legendBox iran"></div> Iran
						<div class="legendBox russia"></div> Russia
					</div><BR>
					<!--
					<div align="center" id="mapFilters" class="u--show-medium-large">
						<input type="radio" checked name="hotspot" id="hs_all" value="all" /><label for="hs_all"> All</label>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="hotspot" id="hs_china" value="china" /><label for="hs_china"> China</label>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="hotspot" id="hs_cve" value="cve" /><label for="hs_cve"> CVE</label>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="hotspot" id="hs_cuba" value="cuba" /><label for="hs_cuba"> Cuba</label>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="hotspot" id="hs_iran" value="iran" /><label for="hs_iran"> Iran</label>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="hotspot" id="hs_russia" value="russia" /><label for="hs_russia"> Russia</label>
					</div>
					-->
					
					
				</div>
			</section>

			<section id="" class="usa-section usa-grid" style="margin-bottom: 2rem;">
				<?php echo $pageContent; ?>
			</section>
			</div><!-- .usa-grid-full -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php /*get_sidebar();*/ ?>
<?php get_footer(); ?>