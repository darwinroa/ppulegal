<?php

/**
 * Retorna el HTML del filtro por letras
 */
function mdw_html_letter_filter($form_ID)
{
  $alphabet = range('A', 'Z'); // Genera un array con las letras de la A a la Z
  $allText = pll_current_language() == 'es' ? 'Todos' : 'All';
  $html = "<div class='mdw__letter-filter'>
    <button type='button' class='letter-filter' data-letter=''>$allText</button>"; // Botón para mostrar todos los abogados

  foreach ($alphabet as $letter) {
    $html .= "<button type='button' class='letter-filter' data-letter='$letter'>$letter</button>";
  }

  $html .= "</div>";
  return $html;
}
