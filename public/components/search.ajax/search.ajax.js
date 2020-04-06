var $ajaxLoad = window.Laravel.ajaxLoad;
var $timeOutAjax;

(function ($) {
    $(document).ready(function () {
        $('body').delegate('.uk-search-input.ajax', 'keyup input', function (event) {
            event.preventDefault();
            var $this = $(this);
            var $resultItemsBox = $('.uk-search-results-items');
            $resultItemsBox.find('.result-list').html('');
            clearTimeout($timeOutAjax);
            $timeOutAjax = setTimeout(function () {
                if ($this.val().length >= 3) {
                    var _data = {
                        string: $this.val()
                    };
                    if ($ajaxLoad === false) {
                        $.ajax({
                            url: $this.data('path'),
                            method: "POST",
                            data: _data,
                            headers: {
                                'X-CSRF-TOKEN': window.Laravel.csrfToken,
                                'LOCALE-CODE': window.Laravel.locale,
                                'LOCATION-CODE': window.Laravel.location
                            },
                            beforeSend: function () {
                                $('body').addClass('ajax-load');
                                $this.attr('disabled', 'disabled').addClass('load');
                                $resultItemsBox.find('.result-list').html('');
                                $('.history-btn').css('display', 'none');
                            },
                            success: function (data) {
                                $ajaxLoad = false;
                                $('body').removeClass('ajax-load');
                                $this.removeAttr('disabled').removeClass('load').focus();
                                $resultItemsBox.find('.result-list').html(data.content);
                                $('body').addClass('open-search');
                                $('.history-btn').css('display', 'none');
                                if(data.count_result) {
                                    command_analytics_fbq({event: 'PRODUCT_SEARCH'});
                                }
                            },
                            error: function ($result) {
                                $ajaxLoad = false;
                                $('body').removeClass('ajax-load');
                                $this.removeAttr('disabled').removeClass('load');
                                $resultItemsBox.find('.result-list').html('');
                            }
                        });
                    }
                } else {
                    $resultItemsBox.find('.result-list').html('');
                }
            }, 1000);
            $('body').removeClass('open-search');
            $resultItemsBox.on('touch click', '.result-maybe', function (event) {
                event.preventDefault();
                $this.val($(this).text()).trigger('keyup');
            });
            $(document).on('touch click', function (event) {
                if (!$(event.target).closest("#search-container").length) {
                    $resultItemsBox.find('.result-list').html('');
                    $('.history-btn').css('display', 'block');
                    $('body').removeClass('open-search');
                }
            });
        });
        $('body').delegate('.uk-search-results-items .shop-product-link, .history-btn', 'touch click', function (event) {
            var $product = $(this).data('product');
            $.ajax({
                url: '/ajax/search-history/',
                method: "POST",
                data: {
                    product_id: $product
                },
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'LOCALE-CODE': window.Laravel.locale,
                    'LOCATION-CODE': window.Laravel.location
                },
                beforeSend: function () {
                },
                success: function (data) {
                    if (!$('body').hasClass('open-search-history')) {
                        $('body').addClass('open-search-history');
                    } else {
                        $('body').removeClass('open-search-history');
                    }
                },
                error: function ($result) {
                }
            });

        });
        $('body').on('submit', '#search-container form', function(event){
            var $this = $(this);
            var $input = $this.find('.uk-search-input');
            if(!$input.val()){
                event.preventDefault();
                event.stopPropagation();
                $input.focus();
            }
        });
    });
})(jQuery);