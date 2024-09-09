<?php

/**
 * Retorna el HTML del campo de búsqueda
 * Requiere un ID para el campo de búsqueda
 */
function mdw_html_search_field($id)
{
  return "
    <div class='mdw__search_field'>
      <input type='text' id='mdw-search-field' name='search' placeholder='Buscar por nombre...'>
    </div>
    ";
}
