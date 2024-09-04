<?php
if (!function_exists('mdw_lawyers_function')) {
  add_shortcode('mdw_lawyers', 'mdw_lawyers_function');

  function mdw_lawyers_function()
  {
    wp_enqueue_style('mdw-lawyer-style', get_stylesheet_directory_uri() . '/shortcodes/lawyers/mdw_lawyers.css', array(), '1.0');
    wp_enqueue_script('mdw-lawyer-script', get_stylesheet_directory_uri() . '/shortcodes/lawyers/mdw_lawyers.js', array('jquery'), null, true);
    wp_localize_script('mdw-lawyer-script', 'wp_ajax', array(
      'ajax_url'            => admin_url('admin-ajax.php'),
      'nonce'               => wp_create_nonce('load_more_nonce'),
      'theme_directory_uri' => get_stylesheet_directory_uri(),
    ));

    /**
     * Aquí se optiene el Loop inicial al momento de cargar la web
     */
    $post_per_page = 16;
    $args = array(
      'post_type'       => 'bd-abogados',
      'posts_per_page'  => $post_per_page,
      'orderby'         => 'title',
      'order'           => 'ASC',
    );
    $query_loop = mdw_query_lawyers_loop($args); // Obtiene el html del grid de todos los abogados

    /**
     * Array con las taxonomías necesarias para el filtro
     */
    $taxonomies = array(
      array(
        'slug' => 'areas-practica',
        'name' => 'Áreas de Práctica'
      ),
      array(
        'slug' => 'pais',
        'name' => 'País'
      ),
      array(
        'slug' => 'roles',
        'name' => 'Rol'
      )
    );
    $form_ID = 'filter-lawyers';
    $filterHTML = mdw_html_filter_form($taxonomies, $form_ID);
    ob_start();
    $html = '';
    $html .= "
      <div id='mdw__lawyers_section' class='mdw__lawyers_section'>
        $filterHTML
        <div class='mdw__content_loop'>
          <div class='mdw__content_loop-grid'>
            $query_loop
          </div>
        </div>
      </div>
    ";
    $html .= ob_get_clean();
    return $html;
  }
}

/**
 * Retorna el HTML del loop para la sección de Abogados
 * $args son los argumentos necesarios para el loop
 */
function mdw_query_lawyers_loop($args)
{
  $query = new WP_Query($args);
  $html = "";
  if ($query->have_posts()) :
    ob_start();
    while ($query->have_posts()) : $query->the_post();
      $html .= do_shortcode('[elementor-template id="116"]');
    endwhile;
    wp_reset_postdata(); // Resetea los datos del post
    $html .= ob_get_clean();
  else : $html .= "<div class='mdw__without-results'>No more results</div>";
  endif;
  return $html;
}

/**
 * Función para la respuesta del Ajax
 */
if (!function_exists('mdw_lawyer_ajax_filter')) {
  add_action('wp_ajax_nopriv_mdw_lawyer_ajax_filter', 'mdw_lawyer_ajax_filter');
  add_action('wp_ajax_mdw_lawyer_ajax_filter', 'mdw_lawyer_ajax_filter');

  function mdw_lawyer_ajax_filter()
  {
    check_ajax_referer('load_more_nonce', 'nonce');
    $page = $_POST['page'];
    $practice_area = isset($_POST['practiceArea']) ? sanitize_text_field($_POST['practiceArea']) : '';
    $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
    $roles = isset($_POST['rol']) ? sanitize_text_field($_POST['rol']) : '';

    /**
     * Construyendo los argumentos necesarios para el Query
     */
    $tax_query = array('relation' => 'AND');
    if ($practice_area) {
      $tax_query[] =  array(
        'taxonomy' => 'areas-practica',
        'field' => 'term_id',
        'terms' => intval($practice_area)
      );
    }
    if ($country) {
      $tax_query[] =  array(
        'taxonomy' => 'pais',
        'field' => 'term_id',
        'terms' => intval($country)
      );
    }
    if ($roles) {
      $tax_query[] =  array(
        'taxonomy' => 'roles',
        'field' => 'term_id',
        'terms' => intval($roles)
      );
    }
    $post_per_page = 16;
    $args = array(
      'post_type' => 'bd-abogados',
      'orderby' => 'title',
      'order' => 'ASC',
      'posts_per_page' => $post_per_page,
      'tax_query' => $tax_query,
      'paged' => $page
    );
    $query_loop = mdw_query_lawyers_loop($args);
    $html = $query_loop;

    wp_send_json_success($html);
    wp_die();
  }
}
