jQuery(document).ready(function($) {
  var page = 1;
  var maxPages = 1;

  function loadPosts() {
    $.ajax({
      url: wp_ajax.ajax_url,
      
      type: 'post',
      data: {
        action: 'ppu_filter_posts',
        practice_area: $('select[name="practice_area"]').val(),
        country: $('select[name="country"]').val(),
        year: $('select[name="year"]').val(),
        search: $('input[name="search"]').val(),
        page: page,
        nonce: wp_ajax.nonce,
        language: wp_ajax.language,
        category: wp_ajax.category,
      },
      success: function(response) {
        if (page === 1) {
          $('#ppu-filter-results').html(response);
        } else {
          $('#ppu-filter-results').append(response);
        }

        // Obtener maxPages del atributo data-max-pages
        maxPages = parseInt($('.ppu-pagination').data('max-pages')) || 1;



        // Ocultar el botón si ya no hay más posts o no hay respuesta
        if (page >= maxPages || response.trim() === '') {

          $('#ppu-load-more').hide();
        } else {
          $('#ppu-load-more').show();
        }

        // Eliminar la paginación después de cargar los posts
        $('.ppu-pagination').remove();
      }
    });
  }

  // Enviar formulario de filtro
  $('#ppu-filter-form').on('submit', function(e) {
    e.preventDefault();
    page = 1; // Reiniciar a la primera página
    loadPosts();
  });

  // Cargar más artículos al hacer clic en el botón de "Cargar más"
  $('#ppu-load-more').on('click', function() {
    page++; // Incrementar página
    loadPosts();
  });

  // Carga inicial de artículos
  loadPosts();
});