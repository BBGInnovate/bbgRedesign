<?php header("Cache-Control: no-cache"); ?>
<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package bbgRedesign
 */

/* ODDI CUSTOM: several variables can be passed into the header */
global $ogImage, $ogTitle, $ogDescription, $ogUrl;
global $pageBodyID, $metaAuthor, $metaAuthorTwitter, $metaKeywords;
global $templateName;

$twitterCardType = "summary_large_image";
if (!isset($ogImage)) {
	$ogImage=DEFAULT_IMAGE;
	$twitterCardType = "summary";
}

if (!isset($ogTitle)) {
	$ogTitle = DEFAULT_TITLE;
}

if (!isset($ogDescription)) {
	$ogDescription = DEFAULT_DESCRIPTION;
}

if (!isset($metaAuthor)) {
	$metaAuthor = DEFAULT_AUTHOR;
}

$metaTwitter = "";
if (isset($metaAuthorTwitter)) {
	$metaAuthorTwitter = str_replace("@","", $metaAuthorTwitter);
	$metaTwitter = '<meta name="twitter:creator" content="@'.$metaAuthorTwitter.'">';
}

if (!isset($metaKeywords)) {
	$metaKeywords = DEFAULT_KEYWORDS;
}

if (!isset($ogUrl)) {
	//only place we override this is on our trending page where it's the permalink to the post instead of the page itself
	$ogUrl = get_permalink();
}

/* remove smart quotes from title */
//$ogTitle = iconv('UTF-8', 'ASCII//TRANSLIT', $ogTitle);
$ogTitle = convertSmartQuotes($ogTitle);

/* remove html tags, smart quotes and trailing ellipses from description */
$ogDescription = wp_strip_all_tags($ogDescription);
//$ogDescription = iconv('UTF-8', 'ASCII//TRANSLIT', $ogDescription);
$ogDescription = convertSmartQuotes($ogDescription);
$ogDescription = str_replace("[&hellip;]", "...", $ogDescription);
$ogDescription = str_replace('"','&quot;',$ogDescription);
$sitewideAlert = get_field('sitewide_alert', 'option');	//off, simple, or complex

$splash_overlay = get_field('splash_page_overlay', 'option');

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

	<head>
<!-- BLOCK SEARCH ENGINES FROM CRAWLING FOR NOW -->
<meta name="robots" content="noindex">
<meta name="googlebot" content="noindex">

<!-- Basic Page Needs
================================================== -->

	<meta charset="utf-8">
	<!-- <meta charset="<?php bloginfo( 'charset' ); ?>">  -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">


<!-- Mobile Specific Metas
================================================== -->
	<meta name="description" content="<?php echo $ogDescription; ?>" />
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-title" content="BBG" />

	<!-- for Facebook -->
	<meta property="og:locale" content="en_US">
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo $ogTitle; ?>" />
	<meta property="og:description" content="<?php echo $ogDescription; ?>" />
	<meta property="og:image" content="<?php echo $ogImage; ?>" />
	<meta property="og:url" content="<?php echo $ogUrl; ?>" />

	<!-- for Twitter -->
	<meta property="twitter:card" content="<?php echo $twitterCardType; ?>">
	<meta name="twitter:site" content="@bbginnovate">
	<?php echo $metaTwitter ?>
	<meta property="twitter:title" content="<?php echo $ogTitle; ?>">
	<meta property="twitter:description" content="<?php echo $ogDescription; ?>">
	<meta property="twitter:image" content="<?php echo $ogImage; ?>">
	<?php /* <meta property="twitter:url" content="<?php echo $ogUrl; ?>"> */ ?>

	<!-- other og:tags -->
	<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />

	<!-- for facebook sharing -->
	<meta property="fb:app_id" content="1288914594517692" />

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<!-- Title, meta description and CSS
================================================== -->

<?php wp_head(); ?>

<script type="text/javascript">
	bbgConfig = {};
	bbgConfig.MAPBOX_API_KEY = '<?php echo MAPBOX_API_KEY; ?>';
	bbgConfig.template_directory_uri = '<?php echo get_template_directory_uri() . "/"; ?>';

	// SET COOKIES FOR SITEWIDE ALERT AND SPLASH OVERLAY
	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires= " + d.toUTCString();
		document.cookie = cname + " = " + cvalue + "; " + expires;
	}
</script>


<!-- IE <9 patch
================================================== -->

	<!--[if lt IE 9]>
	  <script src="<?php echo get_template_directory_uri() ?>/js/vendor/html5shiv.js"></script>
	  <script src="<?php echo get_template_directory_uri() ?>/js/vendor/respond.js"></script>
	  <script src="<?php echo get_template_directory_uri() ?>/js/vendor/selectivizr-min.js"></script>
	<![endif]-->

	<!-- picturefill - polyfill for srcset sizes on older and/or mobile browsers -->
	<script src="<?php echo get_template_directory_uri() ?>/js/vendor/picturefill.min.js"></script>

	<!-- FortAwesome kit of 20 Font Awesome icons -->
	<script src="https://use.fortawesome.com/e3cb8134.js"></script>

	<!-- Original FULL FontAwesome embed -->
	<script src="https://use.fontawesome.com/41d1f06a97.js"></script>

<!-- Favicons
================================================== -->
	<!-- 128x128 -->
	<link rel="shortcut icon" type="image/ico" href="<?php echo get_template_directory_uri() ?>/img/favicons/favicon.ico" />
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri() ?>/img/favicons/favicon.png" />

	<!-- 192x192, as recommended for Android
	http://updates.html5rocks.com/2014/11/Support-for-theme-color-in-Chrome-39-for-Android
	-->
	<link rel="icon" type="image/png" sizes="192x192" href="<?php echo get_template_directory_uri() ?>/img/favicons/favicon-192.png" />

	<!-- 57x57 (precomposed) for iPhone 3GS, pre-2011 iPod Touch and older Android devices -->
	<link rel="apple-touch-icon-precomposed" href="<?php echo get_template_directory_uri() ?>/img/favicons/favicon-57.png">
	<!-- 72x72 (precomposed) for 1st generation iPad, iPad 2 and iPad mini -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_template_directory_uri() ?>/img/favicons/favicon-72.png">
	<!-- 114x114 (precomposed) for iPhone 4, 4S, 5 and post-2011 iPod Touch -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_template_directory_uri() ?>/img/favicons/favicon-114.png">
	<!-- 144x144 (precomposed) for iPad 3rd and 4th generation -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_template_directory_uri() ?>/img/favicons/favicon-144.png">

	<?php if (!is_user_logged_in()): ?>
	<!-- We participate in the US government's analytics program. See the data at analytics.usa.gov. -->
	<script async type="text/javascript" src="https://dap.digitalgov.gov/Universal-Federated-Analytics-Min.js?agency=BBG&pua=ua-33523145-2" id="_fed_an_ua_tag" ></script>
	<?php endif; ?>
	<link href="https://fonts.googleapis.com/css?family=Alegreya+Sans:300,300i,400,400i,500,700,700i" rel="stylesheet">
</head>


<body <?php body_class(); ?>>

<h1 class="header-outliner">Broadcasting Board of Governors</h1>
<div id="page" class="site main-content" role="main">
	<a class="skipnav skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'bbginnovate' ); ?></a>

	<?php
		// DISPLAY SPLASH OVERLAY BEFORE PAGE
		if ($splash_overlay == 'Yes' && (!isset($_COOKIE['splashPageDismissed']))) {
			display_splash_overlay();
		}
	?>

	<header id="masthead" class="site-header bbg-header" role="banner">
		<?php
			// SITE WIDE NOTIFICATIONS
			$moveUSAbannerBecauseOfAlert = '';
			$banner = display_site_wide_banner_if();
			if (!empty($banner)) {
				echo $banner['msg'];
				if ($banner['type'] == "simple") {
					$moveUSAbannerBecauseOfAlert = ' bbg__site-alert--active';
				}
			}
			if ($_SERVER['HTTP_HOST'] == "bbgredesign.voanews.com" && !is_user_logged_in()) {
				display_development_alert_banner("This is the Development Server");
			}
		?>
		
		<!-- OFFICIAL SITE OF USA GOV -->
		<div class="usa-disclaimer<?php echo $moveUSAbannerBecauseOfAlert; ?>">
			<div class="usa-grid">
				<span class="usa-disclaimer-official">
					<img class="usa-flag_icon" alt="U.S. flag signifying that this is a United States federal government website" src="<?php echo get_template_directory_uri() ?>/img/us_flag_small.png">
					An official website of <span class="u--no-wrap">the United States government</span>
				</span>
			</div>
		</div>

		<!-- MENU TOGGLE -->
		<button id="bbg__menu-toggle" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
			<span class="menu-toggle-label">
				<?php esc_html_e( 'Menu', 'bbginnovate' ); ?>
			</span>
		</button>

		<!-- NAVIGATION BAR -->
		<nav id="site-navigation" class="bbg__main-navigation" role="navigation">
			<h1 class="header-outliner">Navigation</h1>
			<a id="nav-logo" href="<?php echo get_home_url(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/USAMG-temp-logo.jpg"></a>
			<?php
				$btnSearch = "<input alt='Search' type='image' class='bbg__main-navigation__search-toggle' src='" . get_template_directory_uri() . "/img/search.png'>";
				$btnSearch = "";

				$searchBox  = '<form id="top-nav-search-form" class="usa-search usa-search-small" action="' . site_url() . '">';
				$searchBox .= 	'<button type="submit" id="nav-search-bu">';
				$searchBox .= 		'<span class="usa-sr-only">Search</span>';
				$searchBox .= 	'</button>';
				$searchBox .= 	'<div role="search">';
				$searchBox .= 		'<label class="usa-sr-only" for="search-field-small">Search small</label>';
				$searchBox .= 		'<input id="search-field-small" type="search" name="s" placeholder="Search ...">';
				$searchBox .= 	'</div>';
				$searchBox .= '</form>';

				wp_nav_menu(array(
					'theme_location' => 'primary',
					'menu_id' => 'primary-menu',
					'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul><div class="bbg__main-navigation__search">' . $searchBox . '</div>',
					'walker' => new bbginnovate_walker_header_usa_menu()
				)); 
			?>
		</nav>
	</header>

	<div id="content" class="site-content">
