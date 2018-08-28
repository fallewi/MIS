/**
 * Foundation
 */
jQuery(document).foundation();

/**
 * Slick Slider settings
 */
jQuery('#slick-best').slick({
  speed: 1000,
  arrows: true,
  nextArrow: '<div class="best__slick_next hide-for-small-only"></div>',
  prevArrow: '<div class="best__slick_prev hide-for-small-only"></div>',
  // pauseOnHover: true,
  touchThreshold: 15,
  infinite: true,
  dots: true,
});

jQuery('#slick-featured').slick({
  speed: 500,
  nextArrow: '<div class="featured__slick_next hide-for-small-only"></div>',
  prevArrow: '<div class="featured__slick_prev hide-for-small-only"></div>',
  infinite: true,
  pauseOnHover: true,
  // slidesToScroll: 1,
  // swipeToSlide: true,
  touchThreshold: 15,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 1,
      }
    },
    {
      breakpoint: 900,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 700,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 500,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }]
});

jQuery('#slick-recent').slick({
  speed: 1000,
  arrows: true,
  nextArrow: '<div class="best__slick_next hide-for-small-only"></div>',
  prevArrow: '<div class="best__slick_prev hide-for-small-only"></div>',
  // pauseOnHover: true,
  touchThreshold: 15,
  infinite: true,
  dots: true,
});

jQuery('.slick-gallery').slick({
  speed: 1000,
  arrows: true,
  nextArrow: '<div class="gallery__slick_next"></div>',
  prevArrow: '<div class="gallery__slick_prev"></div>',
  slidesToShow: 1,
  infinite: true,
  slidesToScroll: 1,
  swipeToSlide: true,
  autoplay: true
});

//
// Share popup
//
jQuery(document).ready(function($) {
  var social_link = $('.entry__share a:not(.email-share a)');
    social_link.live('click', function(){
        newwindow=window.open($(this).attr('href'),'','height=450,width=700');
        if (window.focus) {newwindow.focus()}
        return false;
    });
});
