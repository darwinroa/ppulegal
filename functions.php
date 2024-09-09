<?php

/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles()
{

  wp_enqueue_style(
    'hello-elementor-child-style',
    get_stylesheet_directory_uri() . '/style.css',
    [
      'hello-elementor-theme-style',
    ],
    HELLO_ELEMENTOR_CHILD_VERSION
  );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);

///////////////////////////////////////////////////////////////////////
////////////////////////////SHORTCODES/////////////////////////////////
///////////////////////////////////////////////////////////////////////
require 'shortcodes/lawyers/mdw_lawyers.php'; // Abogados

///////////////////////////////////////////////////////////////////////
///////////////////////UTILIDADES GENRALES/////////////////////////////
///////////////////////////////////////////////////////////////////////
require 'utilities/mdw_html_filter.php'; // Filters HTML
require 'utilities/mdw_html_search_input.php'; // Search input filter
require 'utilities/mdw_letter_html_filter.php'; // Letter filter
require 'utilities/mdw_load_more_button.php'; // Button Load More