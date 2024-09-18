<?php

/**
 * Retorna el HTML del campo de búsqueda
 * Requiere un ID para el campo de búsqueda
 */
function mdw_html_search_field()
{
  $placeHolderText = pll_current_language() == 'es' ? 'Buscar por nombre' : 'Search by name';
  return "
    <div class='mdw__search_field'>
      <input type='text' id='mdw-search-field' name='search' placeholder='$placeHolderText...'>
    </div>
    ";
}
