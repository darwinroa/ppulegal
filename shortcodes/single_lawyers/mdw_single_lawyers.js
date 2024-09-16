jQuery(document).ready(function($) {
  $(window).on('load', function() {
      const swiper = new Swiper('#mdw__lawyer_post-slider', {
        slidesPerView: 1,
        spaceBetween: 10,
        breakpoints: {
          576: {
            slidesPerView: 1,
            spaceBetween: 20,
          },
          1024: {
              slidesPerView: 2,
          },
          1366: {
              slidesPerView: 3,
          },
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        loop: true,
    });
  });
});
