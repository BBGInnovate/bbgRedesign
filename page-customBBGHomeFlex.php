<?php
/**
 * The custom home page for U.S. Agency for Global Media
 *
 * @package bbgRedesign
  template name: Custom BBG Home - Flex
 */

// FUNCTION THAT BUILD SECTIONS
require 'inc/custom-field-data.php';
require 'inc/custom-field-parts.php';
require 'inc/custom-field-modules.php';

require 'inc/bbg-functions-home.php';
require 'inc/bbg-functions-assemble.php';

get_header();

?>

<main id="main" class="site-content" role="main">
    <section class="outer-container">
        <div class="grid-container">
            <div class="nest-container">
                <?php
                    echo getCardsRows('option');
                ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>