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
      'language'            => pll_current_language('slug'), // Agrega el idioma actual
    ));

    /**
     * Aquí se optiene el Loop inicial al momento de cargar la web.
     * Lo que se hace es realizar un query por cada rol de abogado
     * Esto con la intención de poder agrupar a los abogados en 
     * órden de prioridad por Rol y Alfabéticamente por su apellido
     */
    $post_per_page = -1;
    $settintgs = get_page_by_path('settings', OBJECT, 'ppu-legal-settgins');
    $settintgsID = $settintgs->ID;
    $roles = get_field('prioridad_roles', $settintgsID);
    $query_loop = '';
    foreach ($roles as $rol) {
      $args = array(
        'post_type'       => 'bd-abogados',
        'posts_per_page'  => $post_per_page,
        'orderby'         => 'title',
        'order'           => 'ASC',
        'lang'            => pll_current_language('slug'), // Agrega el idioma actual
        'tax_query'       => array(
          array(
            'taxonomy'  => 'roles',
            'field'     => 'slug',
            'terms'     => pll_current_language() == 'es' ? $rol['slug'] : $rol['slug_en'],
          )
        )
      );
      $query_loop .= mdw_query_lawyers_loop($args); // Obtiene el html del grid de todos los abogados
    }

    /**
     * Array con las taxonomías necesarias para el filtro
     */
    $taxonomies = array(
      array(
        'slug' => 'areas-practica',
        'name' => pll_current_language() == 'es' ? 'Áreas de práctica' : 'Practice area'
      ),
      array(
        'slug' => 'pais',
        'name' => pll_current_language() == 'es' ? 'País' : 'Country'
      ),
      array(
        'slug'  => 'roles',
        'name'  => pll_current_language() == 'es' ? 'Rol' : 'Role',
        'order' => $roles,
      )
    );
    // HTML para el filtrado de los abogados
    $form_ID = 'filter-lawyers';
    $filterHTML = mdw_html_filter_form($taxonomies, $form_ID);

    // HTML para el botón de cargar más
    $button_ID = 'loadmore-members';
    $loadMoreButton = mdw_html_loadmore_button($button_ID);
    ob_start();
    $html = '';
    $html .= "
      <div id='mdw__lawyers_section' class='mdw__lawyers_section'>
        $filterHTML
        <div class='mdw__content_loop'>
          <div class='mdw__content_loop-grid'>
            $query_loop
          </div>
          $loadMoreButton
        </div>
      </div>
    ";
    $html .= ob_get_clean();
    return $html;
  }
}

if (!function_exists('mdw_lawyers_practice_area_function')) {
  add_shortcode('mdw_lawyers_practice_area', 'mdw_lawyers_practice_area_function');

  function mdw_lawyers_practice_area_function()
  {
    wp_enqueue_style('mdw-lawyer-style', get_stylesheet_directory_uri() . '/shortcodes/lawyers/mdw_lawyers.css', array(), '1.0');

    /**
     * Aquí se optiene el Loop inicial al momento de cargar la web.
     * Lo que se hace es realizar un query por cada rol de abogado
     * Esto con la intención de poder agrupar a los abogados en 
     * órden de prioridad por Rol y Alfabéticamente por su apellido
     */
    $post_per_page = -1;
    $settintgs = get_page_by_path('settings', OBJECT, 'ppu-legal-settgins');
    $settintgsID = $settintgs->ID;
    $roles = get_field('prioridad_roles', $settintgsID);
    $query_loop = '';
    $currentPostId = get_the_ID();
    $taxonomy = 'areas-practica';
    $practiceAreaTerms = get_the_terms($currentPostId, $taxonomy);

    $practiceAreaSlugs = [];
    if ($practiceAreaTerms && !is_wp_error($practiceAreaTerms)) {
      foreach ($practiceAreaTerms as $term) {
        $practiceAreaSlugs[] = $term->slug; // Guarda los slugs de los términos
      }
    }

    foreach ($roles as $rol) {
      $args = array(
        'post_type'       => 'bd-abogados',
        'posts_per_page'  => $post_per_page,
        'orderby'         => 'title',
        'order'           => 'ASC',
        'lang'            => pll_current_language('slug'), // Agrega el idioma actual
        'tax_query'       => array(
          array(
            'taxonomy'  => 'roles',
            'field'     => 'slug',
            'terms'     => pll_current_language() == 'es' ? $rol['slug'] : $rol['slug_en'],
          ),
          array(
            'taxonomy' => 'areas-practica', // Aquí agregas la taxonomía de área de práctica
            'field'    => 'slug',
            'terms'    => $practiceAreaSlugs, // Usa los slugs que obtuviste antes
            'operator' => 'IN', // Puedes usar 'IN' para que coincidan con cualquiera de los términos
          )
        )
      );
      $query_loop .= mdw_query_lawyers_loop($args); // Obtiene el html del grid de todos los abogados
    }
    ob_start();
    $html = '';
    $html .= "
      <div id='mdw__lawyers_section' class='mdw__lawyers_section'>
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
function mdw_query_lawyers_loop($args, $letter = '')
{
  $query = new WP_Query($args);
  $html = "";
  if ($query->have_posts()) :
    ob_start();
    while ($query->have_posts()) : $query->the_post();
      $lastname = get_field('apellido');
      if ($letter) {
        $letter === $lastname[0] ?
          $html .= do_shortcode('[elementor-template id="309"]') : '';
      } else $html .= do_shortcode('[elementor-template id="309"]');
    endwhile;
    wp_reset_postdata(); // Resetea los datos del post
    $html .= ob_get_clean();
  else : $html .= "";
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
    $language = $_POST['language'];
    $practice_area = isset($_POST['practiceArea']) ? sanitize_text_field($_POST['practiceArea']) : '';
    $country = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
    $rol = isset($_POST['rol']) ? sanitize_text_field($_POST['rol']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $letter = isset($_POST['letter']) ? sanitize_text_field($_POST['letter']) : '';

    /**
     * Construyendo los argumentos necesarios para el Query
     */
    $tax_query = array('relation' => 'AND');
    if ($practice_area) {
      $tax_query[] = array(
        'taxonomy' => 'areas-practica',
        'field' => 'term_id',
        'terms' => intval($practice_area)
      );
    }
    if ($country) {
      $tax_query[] = array(
        'taxonomy' => 'pais',
        'field' => 'term_id',
        'terms' => intval($country)
      );
    }
    if ($rol) {
      $tax_query[] = array(
        'taxonomy' => 'roles',
        'field' => 'term_id',
        'terms' => intval($rol)
      );
    }

    $post_per_page = -1;

    /**
     * Si dentro de las opciones del fitro se ha seleccionado un rol, 
     * entonces se realiza el query con los $args que siguen.
     * Pero, si no incluye ningún rol, entonces se realiza un query por cada rol que existe
     * El listado de cada rol se obtiene de las configuraciones del proyecto
     * donde se están cargando el nombre de cada Rol según su prioridad. 
     */
    if ($rol) {
      $args = array(
        'post_type'       => 'bd-abogados',
        'posts_per_page'  => $post_per_page,
        'orderby'         => 'title',
        'order'           => 'ASC',
        'lang'            => $language, // Agrega el idioma actual
        'tax_query'       => $tax_query,
        'paged'           => $page,
        's'               => $letter ? '' : $search,
      );

      $query_loop = mdw_query_lawyers_loop($args, $letter);
    } else {
      $settintgs = get_page_by_path('settings', OBJECT, 'ppu-legal-settgins');
      $settintgsID = $settintgs->ID;
      $roles = get_field('prioridad_roles', $settintgsID);
      $query_loop = '';
      foreach ($roles as $rol) {
        $args = array(
          'post_type'       => 'bd-abogados',
          'posts_per_page'  => $post_per_page,
          'orderby'         => 'title',
          'order'           => 'ASC',
          'lang'            => $language, // Agrega el idioma actual
          'tax_query'       => array_merge($tax_query, array(
            array(
              'taxonomy'  => 'roles',
              'field'     => 'slug',
              'terms'     => $language == 'es' ? $rol['slug'] : $rol['slug_en'],
            )
          )),
          'paged'           => $page,
          's'               => $letter ? '' : $search,
        );
        $query_loop .= mdw_query_lawyers_loop($args, $letter); // Obtiene el html del grid de todos los abogados
      }
    }
    $html = $query_loop;

    wp_send_json_success($html);
    wp_die();
  }
}


/**
 * Shortcode para imprimir el rol en masculino o femenino según sea el caso
 */
add_shortcode('mdw_gender_rol', 'mdw_gender_rol_func');
function mdw_gender_rol_func()
{
  $gender = get_field('genero');
  $roles = get_the_terms(get_the_ID(), 'roles');

  if (!empty($roles) && !is_wp_error($roles)) {
    foreach ($roles as $rol) {
      $rolFemale = get_field('rol_femenino', 'roles_' . $rol->term_id);
      $rolGender = $gender['value'] === 'h' ? $rol->name : $rolFemale;
      return $rolGender;
    }
  }
}


function filter_posts_where_title_only($where, $wp_query)
{
  if (isset($wp_query->query_vars['s']) && !empty($wp_query->query_vars['s'])) {
    global $wpdb;
    $search = esc_sql($wp_query->query_vars['s']);
    $where .= " AND {$wpdb->posts}.post_title LIKE '%{$search}%'";
  }
  return $where;
}

add_filter('posts_where', 'filter_posts_where_title_only', 10, 2);

function remove_filter_posts_where()
{
  remove_filter('posts_where', 'filter_posts_where_title_only', 10, 2);
}

add_action('wp', 'remove_filter_posts_where');
