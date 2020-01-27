<?php
/**
 * The template for displaying search results pages.
 * Uses Google Custom Search as the primary, and the WordPress native search as fallback.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package bbgRedesign
 */

get_header();

?>

<main id="main" role="main">
    <div class="outer-container">
        <div class="grid-container">

<?php
    $searchQuery = get_search_query();

    $searchPageUrl = site_url() . "/?s=" . urlencode($searchQuery);

    echo    '<header style="margin-bottom: 1em;">';
    if (empty($searchQuery)) {
        echo    '<h2 class="section-header">' . get_the_title() . '</h2>';
    } else {
        echo    '<h2 class="section-header">Search Results for: ';
        echo        '<span>' . $searchQuery . '</span>';
        echo    '</h2>';
    }
    echo        '<form class="bbg__form__search" action="' . site_url() . '" method="GET">';
    echo            '<input type="text" name="s" value="' . $searchQuery . '"/>';
    echo            '<input type="submit" value="Search">';
    echo        '</form>';
    echo    '</header>';

    if (empty($searchQuery)) {
        echo '<div class="entry-summary bbg-search-results__excerpt-content">';
        echo     '<p>Please enter a search term</p>';
        echo '</div>';
    } else {
        $googleResults = array();

        if (defined('GOOGLE_SITE_SEARCH_API_KEY') && defined('GOOGLE_SITE_SEARCH_CX')) {
            $start = '';
            if (isset($_GET['start'])) {
                $start = $_GET['start'];
            }

            $apiUrl = getGoogleSearchUrl($searchQuery, $start);

            $googleResults = fetchGoogleResults($apiUrl);
        }

        if (isset($googleResults) && !isset($googleResults['error'])) {
            $prevLink = '';
            $nextLink = '';
            $prevIndex = 0;
            $nextIndex = 0;
            $firstResultLabel = 1;

            if (!empty($start)) {
                $firstResultLabel = $start;
                $prevIndex = $start - 10;
                if ($prevIndex <= 1) {
                    $prevUrl = $searchPageUrl;
                } else {
                    $prevUrl = $searchPageUrl . '&start=' . $prevIndex;
                }
                $prevLink = '<a href="' . $prevUrl . '">Previous</a>';
            }

            $totalResults = 0;

            if (isset($googleResults['searchInformation']['totalResults'])) {
                $totalResults = $googleResults['searchInformation']['totalResults'];
            }

            if ($totalResults == 0) {
                $firstResultLabel = 0;
            }

            $endResultLabel = min($totalResults, $firstResultLabel + 9);

            $about = '';
            if ($totalResults > $endResultLabel) {
                $about = 'about';
            }
            echo '<div class="entry-summary bbg-search-results__excerpt-content" style="margin-bottom: 1em;">';
            echo     '<em>Showing results ' . $firstResultLabel . '-' . $endResultLabel . ' of ' . $about . ' ' . $totalResults. '</em>';
            echo '</div>';

            if (isset($googleResults['queries']['nextPage']) && isset($googleResults['queries']['nextPage'][0])) {
                $nextUrl = $searchPageUrl . '&start=' . $googleResults['queries']['nextPage'][0]['startIndex'];
                $nextLink = '<a href="' . $nextUrl . '">Next</a>';
            }

            foreach ($googleResults['items'] as $result) {
                $title = $result['title'];
                $link = $result['link'];
                $snippet = $result['snippet'];
                $htmlSnippet = $result['htmlSnippet'];

                /* If the title ends in BBG, don't include that */
                $pos = strpos($title, ' - BBG');
                if ($pos && $pos == (strlen($title) - 6)) {
                    $title = substr($title, 0, strlen($title) - 6);
                }

                $pos = strpos($snippet, "...");
                $date = substr($snippet, 0, $pos);
                $restStartPosition = $pos + 3;
                $restOfSnippet = substr($snippet, $pos + 3);
                $description = $restOfSnippet;

                if (isset($result['pagemap']) && isset($result['pagemap']['metatags']) && (count($result['pagemap']['metatags'][0]) > 0) && isset($result['pagemap']['metatags'][0]['og:description'])) {
                    $description = $result['pagemap']['metatags'][0]['og:description'];
                }

                $description = str_replace("&qout;", '"', $description);    //fixes a legacy bug
                $description = str_replace("&quot;", '"', $description);

                $description = preg_replace('/\[nggallery.*\]/', '', $description);

                echo '<article id="post-<?php the_ID(); ?>" ' . get_post_class("bbg__article") . '>';
                echo     '<header class="entry-header bbg-blog__excerpt-header">';
                echo         '<h3 class="article-title"><a href="' . $link . '" rel="bookmark">' . $title . '</a></h3>';
                echo     '</header><!-- .bbg-blog__excerpt-header -->';
                echo     '<div class="entry-summary bbg-search-results__excerpt-content">';
                echo         '<p>';
                echo             $description;
                echo         '</p>';
                echo     '</div><!-- .entry-summary -->';
                echo '</article>';
            }

            if ($prevLink != '' || $nextLink != '') {
                echo '<nav class="navigation posts-navigation" role="navigation">';
                echo     '<h2 class="screen-reader-text">Posts navigation</h2>';
                echo     '<div class="nav-links">';
                if ($prevLink != '') {
                    echo     '<div class="nav-previous">' . $prevLink . '</div>';
                }
                if ($nextLink != '') {
                    echo     '<div class="nav-next">' . $nextLink . '</div>';
                }
                echo     '</div>';
                echo '</nav>';
            }
        } else {
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    get_template_part('template-parts/content', 'search');
                }

                the_posts_navigation();
            } else {
                get_template_part('template-parts/content', 'none');
            }
        }
    }
?>
        </div>
    </div>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>

<?php
    function getGoogleSearchUrl($searchQuery, $start) {
        $apiKey = GOOGLE_SITE_SEARCH_API_KEY;
        $cx = GOOGLE_SITE_SEARCH_CX;

        $apiUrl = 'https://www.googleapis.com/customsearch/v1/siterestrict?';
        $apiUrl .= '&key=' . $apiKey;
        $apiUrl .= '&cx=' . $cx; // Context restricts search to usagm.gov
        $apiUrl .= '&q=' . urlencode($searchQuery);
        if (!empty($start)) {
            $apiUrl .= '&start=' . $start;
        }

        return $apiUrl;
    }

    function fetchGoogleResults($apiUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        curl_close($ch);

        $results = json_decode($output, true);

        return $results;
    }
?>
