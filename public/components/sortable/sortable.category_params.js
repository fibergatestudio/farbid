var OleusSettings = window.OleusSettings;

(function ($) {

    $(document).ready(function () {
        if ($('#list-category-params').length) {
            var $_sortable_list = $('#list-category-params .sortable-params-list .uk-card');
            var $_sortable_list_applicable = $('#list-category-params .sortable-params-list.applicable .uk-card');
            var $_sortable_group = $('#list-category-params');
            var $_options_sortable = {
                group: 'sortable-group'
            };
            UIkit.sortable($_sortable_list, $_options_sortable);
            $_sortable_list.on('stop', function () {
                checked_params();
                $_sortable_group.find('.uk-item').each(function () {
                    var $_i = $(this);
                    if ($_i.parents('.sortable-params-list').hasClass('applicable')) {
                        $_i.find('input.uk-input-sort').val($_i.index());
                        $_i.find('input.uk-input-applicable').val(1);
                    } else {
                        $_i.find('input.uk-input-sort').val(0);
                        $_i.find('input.uk-input-applicable').val(0);
                    }
                });
            });

            function checked_params() {
                var $ajaxData = [];
                var $_relation_category_param = $('#relation-category-param');
                $_sortable_list_applicable.find('.uk-item').each(function () {
                    var $_i = $(this);
                    $ajaxData.push($_i.find('input.uk-input-param-id').val());
                });
                $.ajax({
                    url: $_relation_category_param.data('href'),
                    method: 'POST',
                    data: {
                        params: $ajaxData
                    },
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    beforeSend: function () {
                        $ajaxLoad = true;
                        $('body').addClass('ajax-load');
                        $_relation_category_param.addClass('load').append('<span class="uk-ajax-spinner"><span class="uk-spinner"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" ratio="1"><circle fill="none" stroke="#1e87f0" cx="15" cy="15" r="14"></circle></svg></span></span>');
                    },
                    success: function ($result) {
                        $ajaxLoad = false;
                        $('body').removeClass('ajax-load');
                        $_relation_category_param.removeClass('load').find('.uk-ajax-spinner').remove();
                        if ($result) {
                            for (var $i = 0; $i < $result.length; ++$i) {
                                command_action($result[$i]);
                            }
                        }
                    },
                    error: function ($result) {
                        $ajaxLoad = false;
                        $('body').removeClass('ajax-load');
                        $_relation_category_param.removeClass('load').find('.uk-ajax-spinner').remove();
                    }
                });
            }
        }
    });

})(jQuery);