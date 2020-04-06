(function ($) {
    useFieldUpload($);
    $(document).ready(function () {
        $('body').delegate('#form-basket-checkout-login-user', 'touch click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $form = $(this).data('form');
            var $path = $(this).data('path');
            var $data = {};
            $data.form_id = $form;
            $data.email = $('#' + $form + '-email').val();
            $data.password = $('#' + $form + '-password').val();
            $.ajax({
                url: $path,
                method: 'POST',
                data: $data,
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                beforeSend: function () {
                    $ajaxLoad = true;
                    $('body').addClass('ajax-load');
                    $(this).attr('disabled', 'disabled').addClass('load');
                },
                success: function ($result) {
                    $ajaxLoad = false;
                    $('body').removeClass('ajax-load');
                    $(this).removeAttr('disabled').removeClass('load');
                    if ($result) {
                        for (var $i = 0; $i < $result.length; ++$i) {
                            command_action($result[$i]);
                        }
                    }
                },
                error: function ($result) {
                    $ajaxLoad = false;
                    $('body').removeClass('ajax-load');
                    $(this).removeAttr('disabled').removeClass('load')
                }
            });
        });
    });
})(jQuery);

function useFieldUpload($) {
    $('.js-upload').each(function () {
        var $boxUpload = $(this);
        var $fieldUpload = $(this).find('.file-upload-field');
        if (!$fieldUpload.hasClass('applied')) {
            var $fieldCard = $fieldUpload.parents('.user-photo'),
                $barUpload = $fieldCard.find('.uk-progress'),
                $settingsUpload = {
                    url: $fieldUpload.data('url'),
                    allow: $fieldUpload.data('allow'),
                    multiple: $fieldUpload.data('multiple'),
                    type: 'post',
                    name: 'file',
                    params: {
                        field: $fieldUpload.data('field'),
                        view: $fieldUpload.data('view')
                    },
                    beforeSend: function (e) {
                    },
                    loadStart: function (e) {
                        $barUpload.removeAttr('hidden');
                        $barUpload.attr('max', e.total);
                        $barUpload.val(e.loaded);
                    },
                    progress: function (e) {
                        $barUpload.attr('max', e.total);
                        $barUpload.val(e.loaded);
                    },
                    loadEnd: function (e) {
                        $barUpload.attr('max', e.total);
                        $barUpload.val(e.loaded);
                    },
                    complete: function () {
                        var $statusResponse = arguments[0].status,
                            $textResponse = arguments[0].responseText;
                        if ($statusResponse == 200) {
                            $_response = JSON.parse($textResponse);
                            $('#form-account-edit-avatar_fid').val($_response.file_id);
                            $('.user-photo .js-upload').css('background-image', 'url(' + $_response.path + ')');
                            $fieldCard.addClass('loaded-file');
                        } else {
                            command_modal($textResponse, {id: 'modal-alert', class: 'alert-danger uk-border-rounded'});
                        }
                    },
                    completeAll: function () {
                        $barUpload.attr('hidden', 'hidden');
                    },
                    fail: function () {
                        var $modal = '<button class="uk-modal-close-default" type="button" uk-close></button>' +
                            '<div class="uk-modal-body uk-padding-small">' + Laravel.translate.upload_file_mime_type + '</div>';
                        command_modal($modal, {id: 'modal-alert', class: 'alert-danger uk-border-rounded'});
                    }
                };
            $fieldUpload.addClass('applied');
            UIkit.upload($boxUpload, $settingsUpload);
        }
    });
}