<?php

/**
 * Retorna el HTML del formulario
 * Requiere 2 variables
 * $taxonomies es un array de arrays donde vada array contiene la key 'slug', 'name' y 'order'
 * Siendo estas keies el slug de la taxonomía y el nombre que quiero que se muestre de la taxonomía
 * La key 'order' puede ser opcional, y solo debe tener como valor DESC.
 */
function mdw_html_filter_form($taxonomies, $form_ID)
{

  // HTML par ael input de búsqueda por nombre del abogado
  $searchInputHTML = mdw_html_search_field('search-field');

  // Añade el filtro por letras
  $letterFilter = mdw_html_letter_filter($form_ID);
  $resetText = pll_current_language() == 'es' ? 'Limpiar' : 'Reset';

  $html = "
  <div class='mdw__content-filter'>
    <form id='mdw__form-$form_ID'>
      $searchInputHTML";
  foreach ($taxonomies as $taxonomy) :
    $order = array_key_exists('order', $taxonomy) ? $taxonomy['order'] : 'ASC';
    if ($terms = mdw_filter_options($taxonomy['slug'], $order)) $html .= mdw_html_filter_select($taxonomy['slug'], $taxonomy['name'], $terms);
  endforeach;
  $html .= $letterFilter;
  $html .= "<button type='reset' id='mdw__button-reset' class='reset-buton'>$resetText</button>";
  $html .= "</form></div>";
  return $html;
}

/**
 * Retorna los valores de todas las taxonomías
 * Se le debe pasar como variable el slug de la taxonomia
 */
function mdw_filter_options($taxonomy, $order)
{
  $terms = get_terms(
    array(
      'taxonomy'    => $taxonomy,
      'orderby'     => 'name',
      'order'       => gettype($order) == 'string' ? $order : 'ASC',
      'hide_empty'  => false
    )
  );

  // Si se tiene un orden en específico se realiza aquí
  if (is_array($order)) {
    // Crear un array para almacenar el orden deseado de los slugs
    $desired_order = array();
    foreach ($order as $item) {
      if (isset($item['slug'])) {
        $desired_order[] = pll_current_language() == 'es' ? $item['slug'] : $item['slug_en'];
      }
    }

    // Reordenar $terms según $order
    usort($terms, function ($a, $b) use ($desired_order) {
      $pos_a = array_search($a->slug, $desired_order);
      $pos_b = array_search($b->slug, $desired_order);

      // Si $pos_a o $pos_b no están en $desired_order, consideramos la posición como -1 (al final)
      if ($pos_a === false) $pos_a = -1;
      if ($pos_b === false) $pos_b = -1;

      return $pos_a - $pos_b;
    });
  }

  return $terms;
}

/**
 * Retorna el html del select
 * Requiere de las variables de slug del cpt, nombre del cpt y los terms
 * El atributo $terms son los terms retornados de la función mdw_filter_options()
 */
function mdw_html_filter_select($cpt_slug, $cpt_name, $terms)
{
  $allText = pll_current_language() == 'es' ? 'Todos' : 'All';
  $html = "<select class='member-select-filter' name='{$cpt_slug}' id='{$cpt_slug}'>
    <option value='' selected disabled>$cpt_name</option>
    <option value=''>$allText</option>";
  foreach ($terms as $term) :
    $html .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
  endforeach;
  $html .= "</select>";
  return $html;
}
