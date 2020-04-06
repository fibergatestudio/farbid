(function ($) {
    $('body').delegate('.uk-form-controls-file .uk-file-remove-button', 'click', function (event) {
        event.preventDefault();
        var $this = $(this),
            $fieldCard = $this.parents('.uk-form-controls-file');
        swal({
            html: Laravel.translate.confirm_file_delete,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#26b17a',
            cancelButtonColor: '#f0506e',
            confirmButtonText: Laravel.translate.yes,
            cancelButtonText: Laravel.translate.no
        }).then(function (result) {
            if (result) {
                $this.removeAttr('disabled').removeClass('load').find('.uk-ajax-spinner').remove();
                $this.parents('.file').remove();
                if ($fieldCard.hasClass('uk-one-file')) $fieldCard.removeClass('loaded-file');
            }
        });
    });
})(jQuery);

function useFieldUpload($) {
    $('.js-upload').each(function () {
        var $boxUpload = $(this);
        var $fieldUpload = $(this).find('.file-upload-field');
        if (!$fieldUpload.hasClass('applied')) {
            var $fieldCard = $fieldUpload.parents('.uk-form-controls-file'),
                $filePreview = $fieldCard.find('.uk-preview'),
                $barUpload = $fieldCard.find('.uk-progress'),
                $buttonUpload = $fieldCard.find('.uk-field button'),
                $settingsUpload = {
                    url: $fieldUpload.data('url'),
                    allow: $fieldUpload.data('allow'),
                    multiple: $fieldUpload.data('multiple'),
                    type: 'post',
                    name: 'file',
                    params: {
                        field: $fieldUpload.data('field'),
                        view: $fieldUpload.data('view'),
                    },
                    beforeSend: function (e) {
                        $buttonUpload.addClass('load').append('<span class="uk-ajax-spinner"><span class="uk-spinner"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" ratio="1"><circle fill="none" stroke="#fff" cx="15" cy="15" r="14"></circle></svg></span></span>');
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
                        $buttonUpload.removeClass('load').find('.uk-ajax-spinner').remove();
                        var $statusResponse = arguments[0].status,
                            $textResponse = arguments[0].responseText;
                        if ($statusResponse == 200) {
                            $filePreview.append($($textResponse));
                            if ($fieldCard.hasClass('uk-one-file')) {
                                $fieldCard.addClass('loaded-file');
                            }
                        } else {
                            UIkit.notification($textResponse, {
                                status: 'danger',
                                pos: 'bottom-right'
                            });
                        }
                    },
                    completeAll: function () {
                        $barUpload.attr('hidden', 'hidden');
                    },
                    fail: function () {
                        $buttonUpload.removeClass('load').find('.uk-ajax-spinner').remove();
                        UIkit.notification(Laravel.translate.upload_file_mime_type, {
                            status: 'danger',
                            pos: 'bottom-right'
                        });
                    }
                };
            $fieldUpload.addClass('applied');
            UIkit.upload($boxUpload, $settingsUpload);
        }
    });
}