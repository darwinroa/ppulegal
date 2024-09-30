<?php
// Shortcode para mostrar el formulario y los resultados
function ppu_custom_filter_shortcode()
{
  $slug = '';
  if (is_tax('categorias-ppulegal')) {
    $term = get_queried_object(); // Obtener el objeto de la taxonomía actual

    if ($term && !is_wp_error($term)) {
      // Si quieres almacenar el slug
      $slug = $term->slug; // El slug del término actual
    }
  }

  wp_enqueue_script('mdw-ppulegal-script', get_stylesheet_directory_uri() . '/shortcodes/ppulegal/mdw_ppulegal.js', array('jquery'), null, true);
  wp_localize_script('mdw-ppulegal-script', 'wp_ajax', array(
    'ajax_url'            => admin_url('admin-ajax.php'),
    'nonce'               => wp_create_nonce('load_more_nonce'),
    'theme_directory_uri' => get_stylesheet_directory_uri(),
    'language'            => pll_current_language('slug'), // Agrega el idioma actual
    'category'            => $slug, // Agrega el idioma actual
  ));

  ob_start();
?>
  <style>
    /* Contenedor de la cuadrícula */
    .custom-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
      /* 3 columnas con tamaño mínimo */
      gap: 20px;
      /* Espaciado entre tarjetas */
      margin-top: 50px;
    }

    /* Estilo del card */
    .mdw__post-card {
      background-color: #f0f0f0;
      /* Fondo gris claro */
      padding: 20px;
      border-radius: 8px;
      transition: transform 0.2s;
    }

    /* Efecto al pasar el ratón sobre el card */
    .mdw__post-card:hover {
      transform: translateY(-5px);
      /* Levanta el card al pasar el ratón */
    }

    /* Estilo del título */
    .mdw__card_content-title h2 {
      color: #102554;
      /* Color del título */
      margin: 0 0 10px;
    }

    /* Estilo del botón */
    .mdw-button,
    #ppu-filter-form button,
    #ppu-load-more {
      background-color: #102554;
      /* Color de fondo del botón */
      color: #ffffff;
      /* Color del texto del botón */
      padding: 10px 15px;
      border: none;
      border-radius: 4px;
      text-align: center;
      display: inline-block;
      transition: background-color 0.2s, transform 0.2s;
      cursor: pointer;
      font-family: "Libre Franklin", Sans-serif;
      font-size: 18px;
      font-weight: 600;
    }

    /* Efecto al pasar el ratón sobre el botón */
    .mdw-button:hover,
    #ppu-filter-form button:hover,
    #ppu-load-more:hover {
      background-color: #0e1e3d;
      /* Color de fondo al pasar el ratón */
      transform: translateY(-3px);
      /* Levanta el botón al pasar el ratón */
    }

    /* Centrar el botón de cargar más */
    #ppu-load-more {
      margin: 20px auto;
      /* Centrar y dar espacio al botón */
      display: block;
    }

    /* Estilos adicionales para meta y descripción */
    .mdw__card_content-meta {
      font-size: 14px;
      color: #666;
      /* Color gris para el texto de la meta */
      margin-bottom: 10px;
    }

    .mdw__card_content-description p {
      margin: 0;
      color: #333;
      /* Color del texto de la descripción */
    }
  </style>

  <form id="ppu-filter-form">
    <select name="practice_area">
      <option value=""><?php _e('Seleccionar área de práctica', 'textdomain'); ?></option>
      <?php
      $practice_areas = get_terms(['taxonomy' => 'areas-practica', 'hide_empty' => true]);
      foreach ($practice_areas as $area): ?>
        <option value="<?php echo esc_attr($area->slug); ?>">
          <?php echo esc_html($area->name); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="country">
      <option value=""><?php _e('Seleccionar País', 'textdomain'); ?></option>
      <?php
      $countries = get_terms(['taxonomy' => 'pais', 'hide_empty' => true]);
      foreach ($countries as $country): ?>
        <option value="<?php echo esc_attr($country->slug); ?>">
          <?php echo esc_html($country->name); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="year">
      <option value=""><?php _e('Seleccionar año', 'textdomain'); ?></option>
      <?php
      $years = range(date('Y'), 2012);
      foreach ($years as $year): ?>
        <option value="<?php echo esc_attr($year); ?>">
          <?php echo esc_html($year); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <input type="text" name="search" placeholder="<?php _e('Buscar...', 'textdomain'); ?>">

    <button type="submit"><?php _e('Buscar', 'textdomain'); ?></button>
  </form>

  <div id="ppu-filter-results" class="custom-grid"></div>
  <button id="ppu-load-more" style="display:none;"><?php _e('Cargar más', 'textdomain'); ?></button>

  <script>

  </script>
<?php
  return ob_get_clean();
}
add_shortcode('ppu_custom_filter', 'ppu_custom_filter_shortcode');

// Función que maneja la consulta y filtrado de posts
function ppu_filter_posts()
{
  $language = $_POST['language'];
  $args = [
    'post_type' => 'ppulegal',
    'posts_per_page' => 9,
    'paged' => isset($_POST['page']) ? intval($_POST['page']) : 1,
    'tax_query' => [],
    'meta_query' => [],
    's' => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
    'lang'  => $language, // Agrega el idioma actual
  ];

  if (!empty($_POST['practice_area'])) {
    $args['tax_query'][] = [
      'taxonomy' => 'areas-practica',
      'field' => 'slug',
      'terms' => sanitize_text_field($_POST['practice_area']),
    ];
  }

  if (!empty($_POST['country'])) {
    $args['tax_query'][] = [
      'taxonomy' => 'pais',
      'field' => 'slug',
      'terms' => sanitize_text_field($_POST['country']),
    ];
  }

  if (!empty($_POST['category'])) {
    $args['tax_query'][] = [
      'taxonomy' => 'categorias-ppulegal',
      'field' => 'slug',
      'terms' => sanitize_text_field($_POST['category']),
    ];
  }

  if (!empty($_POST['year'])) {
    $year = sanitize_text_field($_POST['year']);
    $args['date_query'] = [
      [
        'year' => $year,
      ],
    ];
  }

  $query = new WP_Query($args);

  if ($query->have_posts()) {
    while ($query->have_posts()) : $query->the_post();
      echo do_shortcode('[elementor-template id="442655"]');
    endwhile;

    wp_reset_postdata();

    if ($query->max_num_pages > 1) {
      echo '<div class="ppu-pagination" data-max-pages="' . $query->max_num_pages . '">' . paginate_links([
        'base' => '%_%',
        'format' => 'page/%#%/',
        'current' => max(1, $_POST['page']),
        'total' => $query->max_num_pages,
        'prev_text' => __('&laquo; Anterior', 'textdomain'),
        'next_text' => __('Siguiente &raquo;', 'textdomain'),
        'mid_size' => 1,
        'end_size' => 1,
        'before_page_number' => '<span class="page-num">',
        'after_page_number' => '</span>',
      ]) . '</div>';
    }
  } else {
    echo '<p>' . __('No se encontraron resultados.', 'textdomain') . '</p>';
  }

  wp_die();
}

add_action('wp_ajax_ppu_filter_posts', 'ppu_filter_posts');
add_action('wp_ajax_nopriv_ppu_filter_posts', 'ppu_filter_posts');
