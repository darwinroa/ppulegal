jQuery(document).ready(function($) {
  var page = 1; // Inicializando el paginado
  var isLoadMore = false;

  // Filtro por letras
  $(document).on('click', '.letter-filter', function() {
    var letter = $(this).data('letter');
    $('.letter-filter').removeClass('active');
    $(this).addClass('active');
    page = 1; // Reinicia la paginación
    isLoadMore = false;
    mdwLawyersAjax(page, letter);
  });

  // Esto se ejecuta cuando se presiona sobre el botón de filtrar
  // De modo que el filtro se realiza tomando los datos seleccionados
  $('#mdw__button-filter-lawyers').on('click', function() {
    page = 1; // Inicializando el paginado cada vez que se desea filtrar
    isLoadMore = false;
    mdwLawyersAjax(page);
  });

  // Esto se ejecuta cuando se presiona sobre el botón de Load More
  // Realizando una petición de más posts.
  // Considerando también los datos seleccionados para el filtro
  $('#mdw__button-loadmore-lawyers').on('click', function() {
    page++;
    isLoadMore = true;
    mdwLawyersAjax(page);
  });

  // Función Ajax para la petición del filtro y el cargar más
  function mdwLawyersAjax(page, letter= null) {
    const practiceArea = $('#areas-practica').val();
    const country = $('#pais').val();
    const rol = $('#roles').val();
    const search = $('#mdw-search-field').val(); // Nuevo campo de búsqueda
    
    $.ajax({
      url: wp_ajax.ajax_url,
      type: 'post',
      data: {
        action: 'mdw_lawyer_ajax_filter',
        nonce: wp_ajax.nonce,
        page,
        practiceArea,
        country,
        rol,
        search,
        letter,
      },
      beforeSend: function() {
        const loaderUrl = wp_ajax.theme_directory_uri + '/assets/img/ri-preloader.svg';
        const loaderIcon = `<div class='mdw-loader-ajax'><img id='mdw__loadmore-icon' height='20' width='20' decoding='async' alt='Loading' data-src='${loaderUrl}' class='ls-is-cached lazyloaded e-font-icon-svg e-fas-spinner eicon-animation-spin' src='${loaderUrl}'></div>`;
        isLoadMore || $('#mdw__lawyers_section .mdw__content_loop-grid').empty();
        $('#mdw__lawyers_section .mdw__content_button-loadmore').append(loaderIcon);
        $('#mdw__lawyers_section .mdw__button_loadmore').hide();
      },
      success: function(response) {
        if (response.success) {
          $('#mdw__lawyers_section .mdw__button-loadmore').show();
          $('.mdw-loader-ajax').remove();
          if (isLoadMore) {
            $('#mdw__lawyers_section .mdw__content_loop-grid').append(response.data);
          } else {
            $('#mdw__lawyers_section .mdw__content_loop-grid').html(response.data);
          }
        } else {
          $('#mdw__lawyers_section .mdw__content_loop-grid').html('<p>Hubo un error en la solicitud.</p>');
        }
      }
    });
  }

  // Evento para hacer búsqueda en tiempo real
  $('#search-field').on('keyup', function() {
    $('#mdw__button-filter-lawyers').click();
  });
});
