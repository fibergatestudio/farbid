(function ($) {
    $(document).ready(function () {
        usePhoneMask($);
        countdown_clock();
        $(".uk-nav-sub li").click(function (e) {
            $(".uk-nav-sub li").removeClass('show-more');
            $(this).addClass('show-more');
        });

        $(".link-search a").click(function (e) {
            if ($(".link-search").hasClass('open')) {
                $(".link-search").removeClass('open');
            } else {
                $(".link-search").addClass('open');
            }

        });


//				   UIkit.switcher('.color-switcher').show(function(){
//					   index:0;
//					   $('.color-switcher li').removeClass('uk-active');
//				   });
//				     UIkit.switcher('.color-switcher-text').show(function(){
//						 index:0;
//					   $('.color-switcher-text li').removeClass('uk-active');
//				   });


        $(".color-switcher-text").hide();
        // css('opacity','0');
        $(".color-switcher li a").addClass('opacity');

        $('body').delegate('.color-switcher li', 'click', function (event) {
            $(".color-switcher-text").show();
            // css('opacity', '1');
            $(".color-switcher li a").removeClass('opacity');
        });


        if ($(window).width() > 960) {
            $(".text-description-content").addClass('box-hide');
            $('body').delegate('.description-more-link', 'click', function (event) {
                event.preventDefault();
                $(this).parent().find(".text-description-content").removeClass('box-hide');
                $(".text-description-content").slideDown("slow", function () {
                    // Animation complete.
                });
                $(this).css('display', 'none');
            });
        }


        if ($(window).width() < 1200) {
            var $panel = $('.box-offers');

            $panel.on('click', '.link-filter a', function (e) {
                e.preventDefault();
                if ($(".filter-category").hasClass('open')) {
                    $(".filter-category").removeClass('open').slideUp('fast', function () {
                    });
                } else {
                    $(".filter-category").addClass('open').slideDown('fast', function () {
                    });
                }
            });

        }


        $('body').delegate('#search-history-front', 'click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('.result-list-history').slideToggle(300);
        });
        $('body').delegate('#search-history', 'click', function () {
            $('.result-list').slideUp();
            $('.result-list-history').slideDown();
        });
        $('body').delegate('#search-history-close', 'click', function () {
            $('.result-list-history').slideUp();
            $('.result-list').slideDown();
            $('body').removeClass('open-search-history');
        });

        $('body').delegate('a.link-go-to-basket', 'click', function (){
            command_analytics_fbq({event: 'GO_TO_BASKET'});
        });

        if ($(window).width() > 959) {
            $('.blog-item').on("mouseenter mouseleave", function () {
                if ($(this).hasClass("mouseenter")) {
                    $(this).find('.blog-teaser').slideUp();
                    $(this).removeClass('mouseenter');
                }
                else {
                    $(this).find('.blog-teaser').slideDown();
                    $(this).addClass('mouseenter');
                }
                return false;
            });
        }

        $('.button-close').on("click", function () {
            $('nav').removeClass('child child-next');
        });

        $('.uk-navbar-left').on("mouseenter mouseleave", function () {
            $('nav').toggleClass('child');
            $('.button-close').toggleClass('child');
        });

        $('.child-content').on("mouseenter mouseleave", function () {
            $('nav').toggleClass('child-next');
            $('.button-close').toggleClass('child-next');
        });


        $(".btn-modal").click(function () {
            var $id = $(this).data('id');
            if ($(this).hasClass("open")) {
                $('#' + $id).removeClass('open-content').slideUp();
                $(this).removeClass('open');
            }
            else {
                $('.change').removeClass('open-content').slideUp();
                $('.btn-modal').removeClass('open');
                $('#' + $id).addClass('open-content').slideDown();
                $(this).addClass('open');
            }
            return false;
        });

        $('body').delegate('.user-account-open', 'click', function () {
            if ($(this).hasClass("open")) {
            }
            else {
                $('.box-input').slideDown();
                $('.user-account-open').css('display', 'none');
            }
            return false;
        });


        $('body').delegate('#forms-shop-basket input[name="delivery"]', 'change', function (event) {
            var $_input = $(this);
            $('.delivery-box:not([data-index="' + $_input.val() + '"])').slideUp(500, function () {
                $('#delivery-' + $_input.val() + '-address').slideDown(500);
            });
            var $np_box = $('.delivery-box');
            if ($_input.val() == 1) {
                $np_box.find('select[name="delivery_area"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('select[name="delivery_city"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('select[name="delivery_warehouses"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('input[name="delivery_address_city"]').attr('disabled', 'disabled').val('').removeClass('uk-form-danger');
                $np_box.find('input[name="delivery_address_address"]').attr('disabled', 'disabled').val('').removeClass('uk-form-danger');
            } else if ($_input.val() == 2) {
                $np_box.find('select[name="delivery_area"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('select[name="delivery_city"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('select[name="delivery_warehouses"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('input[name="delivery_address_city"]').removeAttr('disabled').removeClass('uk-form-danger');
                $np_box.find('input[name="delivery_address_address"]').removeAttr('disabled').removeClass('uk-form-danger');
            } else {
                $np_box.find('select[name="delivery_area"]').removeAttr('disabled', 'disabled').removeClass('uk-form-danger');
                $np_box.find('select[name="delivery_city"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('select[name="delivery_warehouses"]').attr('disabled', 'disabled').val(0).removeClass('uk-form-danger');
                $np_box.find('input[name="delivery_address_city"]').attr('disabled', 'disabled').val('').removeClass('uk-form-danger');
                $np_box.find('input[name="delivery_address_address"]').attr('disabled', 'disabled').val('').removeClass('uk-form-danger');
            }
        });

        $('body').delegate('.form-action-link button', 'click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $this = $(this);
            var $form_account = $('body').find('.form-action-user');
            var $class = $(this).data('class');
            $form_account.slideUp(400, function () {
                $form_account.removeClass('log_in sing_in');
                $this.prevAll().removeClass('open');
                $this.nextAll().removeClass('open');
                $('input[name="new_user"]').val(0);
                if ($this.hasClass("open")) {
                    $this.removeClass('open');
                } else {
                    $form_account.addClass($class).slideDown();
                    $this.addClass('open');
                    if ($class == 'sing_in') $('input[name="new_user"]').val(1);
                }
            });
            return false;
        });


        // if ($(window).width() < 960) {
        // 	UIkit.grid('.masonry-group', {
        // 		options: {
        // 			masonry: false
        // 		}
        // 	});e
        // }


//        $('body').delegate('#forms-shop-basket input[name="delivery"]', 'change', function (event) {
//            var $delivery_placeholder = $(this).data('placeholder');
//            var $delivery_field = $(this).data('field');
//            var $delivery_text = $('#delivery-text');
//            if ($delivery_field) {
//                $delivery_text.find('input').attr('placeholder', $delivery_placeholder).val('').removeAttr('disabled');
//                $delivery_text.addClass('open');
//            } else {
//                $delivery_text.find('input').attr('placeholder', $delivery_placeholder).val($delivery_placeholder).attr('disabled', 'disabled');
//                $delivery_text.removeClass('open');
//            }
//        });


        if ($(window).width() > 1199) {
            $(function () {
                $('body').delegate('.uk-item span, .uk-item a', 'click', function (e) {
                    $(this).parent().find(".uk-navbar-dropdown").first().addClass("uk-open");
                });
            });
        }


//		 UIkit.util.on('#offcanvas', 'hide', function () {
        //          transition-delay: '.5s';
//         });

        if ($(window).width() < 1199) {
//			    $('.uk-item.level').prepend("<button class='uk-button uk-button-default uk-position-relative link-drop' type='button'></button>");


//			  var $newdiv1 = $( "<button class='uk-button uk-button-default uk-position-relative link-drop' type='button'></button>" ),
//                  existingdiv1 = document.getElementsByClassName( "page-scroll" )[0],
//				  existingdiv2 = document.getElementsByClassName( "uk-navbar-dropdown" )[0];

//              $( ".uk-item.level" ).append(existingdiv1, [ $newdiv1, existingdiv2] );


//			  $( ".uk-item.level" ).append( "<button class='uk-button uk-button-default uk-position-relative link-drop' type='button'></button>" );

//			  UIkit.dropdown('.uk-navbar-dropdown', function(){
//				  toggle: '.link-drop'
//			  });

//		$('body').delegate('.link-drop', 'click', function (e) {
//			var $open = $(this).parent().find(".uk-navbar-dropdown").first();
//			    if ($open.hasClass("uk-open")) {
//				  $open.removeClass("uk-open");
//		  } else {
//				  $open.addClass("uk-open");
//			  }

//        });
        }


        if ($(window).width() < 1199) {
//		  $('body').delegate('.uk-item a', 'click', function (e) {
//			    if ($(this).parent().hasClass("level3")) {
//
//			  } else {
//				  e.preventDefault();
//			  }

//        });
        }

//		 $(function () {
//
//            $("#offcanvas-catalog").on( "mouseleave", function() {
//				setTimeout(function () {
//             var $offcanvas = UIkit.offcanvas('.uk-offcanvas.uk-open');
//             if ($offcanvas) $offcanvas.hide();
//			  }, 2000);
//			 });
//        });

//		 $('.uk-item').on( "hover", function() {
//         height = $('.uk-navbar-dropdown').height();
//		  var $elm = height;
//				$('.uk-navbar-container').css('height', $elm);
//        });


    });


    $(document).ajaxComplete(function (event, request, settings) {
        usePhoneMask($);
        countdown_clock();
    });

})(jQuery);

function exist($target) {
    var $ = jQuery;
    return $($target).length ? true : false;
}

function usePhoneMask($) {
    $('input.phone-mask').inputmask('+38 (999) 999 9999');
};

/*countdown-clock Js Start*/

function countdown_clock() {

    if ($('.countdown-clock').length) {
        $('.countdown-clock').each(function (event) {
            var $timer = $(this);
            var $timeCount = $timer.data('time');
            var $timeId = $timer.data('id');
            var $timeType = $timer.data('type');
            var $timeUTC = $timer.data('utc');

            $timer.downCount({
                date: $timeCount,
                offset: $timeUTC
            }, function () {
                $.ajax({
                    url: '/ajax/count-down/',
                    method: 'POST',
                    data: {
                        id: $timeId,
                        type: $timeType
                    },
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    beforeSend: function () {

                    },
                    success: function ($result) {
                        if ($result.command == 'alert') {
                            location.reload(true);
                        } else if ($result.command == 'reload') {
                            location.reload(true);
                        } else if ($result.command == 'redirect') {
                            window.location.href = $result.data;
                        }
                    },
                    error: function ($result) {

                    }
                });
                return false;
            });
        });
    }
}

/*countdown-clock Js End*/


function init_maps() {
    if (exist('#contact-maps')) {
        var m = jQuery('#contact-maps');
        m.gmap3({
            center: [
                m.data('lat'),
                m.data('lon')
            ],
            zoom: 16,
            mapTypeId: "dark",
            // mapTypeControlOptions: {
            //     mapTypeIds: [google.maps.MapTypeId.ROADMAP, "dark"]
            // }
        })
        // .marker({
        //     address: "пр.Богдана Хмельницкого 152",
        //     position: [
        //         m.data('lat'),
        //         m.data('lon')
        //     ]
        // })
            .styledmaptype(
                "dark",
                [
                    {
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#242f3e"
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#746855"
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.stroke",
                        "stylers": [
                            {
                                "color": "#242f3e"
                            }
                        ]
                    },
                    {
                        "featureType": "administrative.locality",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#d59563"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#d59563"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#263c3f"
                            }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#6b9a76"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#38414e"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#212a37"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#9ca5b3"
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#746855"
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#1f2835"
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#f3d19c"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#2f3948"
                            }
                        ]
                    },
                    {
                        "featureType": "transit.station",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#d59563"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#17263c"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#515c6d"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.stroke",
                        "stylers": [
                            {
                                "color": "#17263c"
                            }
                        ]
                    }
                ],
                {name: "FARBID"}
            );
    }
}
init_maps();