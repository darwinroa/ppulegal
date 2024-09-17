<?php

/**
 * Shorcodes para modificar textos en función del idioma
 * Ejemplo de uso
 * [mdw_handle_text text_es="Contacto Personal" text_en="Personal Contact"]
 */
add_shortcode('mdw_handle_text', 'mdw_handle_text_function');

function mdw_handle_text_function($atts)
{
  // Definir los atributos aceptados y sus valores predeterminados
  $attributes = shortcode_atts(
    array(
      'text_es'  => '',  // Texto para el idioma español
      'text_en'  => '',  // Texto para el idioma inglés
      'field_slug'   => false,  // Slug del campo personalizado
    ),
    $atts
  );

  $text = '';  // Variable para almacenar el texto final
  $currentLanguage = pll_current_language();  // Obtener el idioma actual
  $fieldSlug = $attributes['field_slug'];
  $textES = $attributes['text_es'];
  $textEN = $attributes['text_en'];
  // Verificar si se proporciona un campo personalizado y si tiene valor
  if ($fieldSlug) {
    $field = get_field($fieldSlug);
    if (!empty($field) && !is_wp_error($field)) {
      $text .= $currentLanguage == 'es' ? $textES : $textEN;
    }
  } else {
    // Si no hay campo personalizado, mostrar el texto según el idioma actual
    $text .= $currentLanguage == 'es' ? $attributes['text_es'] : $attributes['text_en'];
  }

  return $text;  // Devolver el texto final
}
