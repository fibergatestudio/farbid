var OleusSettings = window.Laravel;
var $filterAjaxLoad = false;

(function ($) {

    $(document).ready(function (event, request, settings) {
        _filter_slider();
    });

    $(document).ajaxComplete(function (event, request, settings) {
        _filter_slider();
    });

    function _filter_slider() {
        var $filterForm = $('body').find('.shop-category-filter-card');
        $filterForm.find('.input-slider').each(function () {
            var $slider = $(this);
            var $sliderValues = $slider.prev('.input-slider-values');
            var $sliderButton = $sliderValues.find('button');
            var $sliderInputMin = $slider.find('.input-min');
            var $sliderInputMax = $slider.find('.input-max');
            $slider.slider({
                range: true,
                min: $slider.data('min'),
                max: $slider.data('max'),
                step: $slider.data('step'),
                values: [
                    $slider.data('selected-min'), $slider.data('selected-max')
                ],
                slide: function (event, ui) {
                    $sliderButton.show();
                    // $sliderValues.find('.value-min').text(new Intl.NumberFormat('ru-RU').format(ui.values[0]));
                    // $sliderValues.find('.value-max').text(new Intl.NumberFormat('ru-RU').format(ui.values[1]));
                    var $formationParamUrl = $sliderButton.data('back_path');
                    var $setData = $sliderButton.data('name') + '[min]=' + ui.values[0] + '&' + $sliderButton.data('name') + '[max]=' + ui.values[1];
                    if ($slider.data('min') != ui.values[0] || $slider.data('max') != ui.values[1]) {
                        if ($sliderButton.data('use_query')) {
                            $formationParamUrl = $formationParamUrl + '&' + $setData;
                        } else {
                            $formationParamUrl = $formationParamUrl + '?' + $setData;
                        }
                    }
                    $sliderInputMin.val(ui.values[0]);
                    $sliderInputMax.val(ui.values[1]);
                    $sliderButton.attr('data-path', $formationParamUrl);
                    $(ui.handle).find('.tooltip').text(ui.value);
                },
                create: function (event, ui) {
                    var $min_tooltip = $('<div class="tooltip">' + $slider.data('selected-min') + '</div>'),
                        $max_tooltip = $('<div class="tooltip">' + $slider.data('selected-max') + '</div>');
                    $(event.target).find('.ui-slider-handle.min-handle').append($min_tooltip);
                    $(event.target).find('.ui-slider-handle.max-handle').append($max_tooltip);
                },
                change: function (event, ui) {
                    var $min = ui.values[0],
                        $max = ui.values[1];
                    if ($min == $slider.data('min') && $max == $slider.data('max')) {
                        $min = $max = null;
                    }
                    $(event.target).find('input[data-value="min"]').val($min);
                    $(event.target).find('input[data-value="max"]').val($max).trigger('change');
                    $sliderInputMin.val($min);
                    $sliderInputMax.val($max);
                }
            });
        });
    }

    // $filterForm.submit(function (event) {
    //     event.preventDefault();
    //     event.stopPropagation();
    // });

    // $('body').delegate('#shop-category-filter button.uk-filter-param', 'click', function (event) {
    //     var $param = $(this);
    //     var $paramInput = $param.prev();
    //     var $paramInputStatus = $paramInput.prop('checked');
    //     var $paramParent = $param.parent();
    //     var $paramParentLi = $paramParent.parent();
    //     var $checkboxData;
    //     // $paramParentLi.addClass('show-more');
    //     $paramInput.prop('checked', ($paramInputStatus ? false : true));
    //     $checkboxData = $filterForm.serialize() + '&category=' + $filterForm.data('category');
    //     _ajax_filter_post($paramParent, $checkboxData);
    // });
    //
    // function _get_form_data($form) {
    //     var $neededArray = $form.serializeArray();
    //     var $indexedObject = {};
    //     $.map($neededArray, function (n, i) {
    //         $indexedObject[n['name']] = n['value'];
    //     });
    //
    //     return $indexedObject;
    // }

    // function _ajax_filter_post($obj, $ajaxData) {
    //     $.ajax({
    //         url: '/ajax/shop-filter/',
    //         method: 'POST',
    //         dataType: 'JSON',
    //         data: $ajaxData,
    //         headers: {
    //             'X-CSRF-TOKEN': window.Laravel.csrfToken
    //         },
    //         beforeSend: function () {
    //             $filterAjaxLoad = true;
    //             $('body').addClass('ajax-load');
    //             $obj.addClass('uk-disabled');
    //             // load
    //         },
    //         success: function ($result) {
    //             $filterAjaxLoad = false;
    //             $('body').removeClass('ajax-load');
    //             $obj.removeClass('uk-disabled');
    //             console.log($result);
    //         },
    //         error: function ($result) {
    //             $filterAjaxLoad = false;
    //             $('body').removeClass('ajax-load');
    //             $obj.removeClass('uk-disabled');
    //         }
    //     });
    // }
})(jQuery);