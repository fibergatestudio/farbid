var OleusSettings = window.Laravel;
var $ckEditorObject = {};

(function ($) {
    $('body').delegate('.uk-waves', 'click', function (event) {
        var $button = $(this);
        var $width = $button.width() * 3;
        var $height = $button.height() * 3;
        var $posX = $button.offset().left;
        var $posY = $button.offset().top;
        $button.find('.uk-particle').remove();
        $button.prepend('<span class="uk-particle"></span>');
        if ($width >= $height) {
            $height = $width;
        } else {
            $width = $height;
        }
        var $x = event.pageX - $posX - $width / 2;
        var $y = event.pageY - $posY - $height / 2;
        $button.find('.uk-particle').css({
            width: $width,
            height: $height,
            top: $y + 'px',
            left: $x + 'px'
        }).addClass('uk-animation');
    });

    $('body').delegate('button[name="delete"]', 'click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var $button = $(this);
        var $form = $button.parents('form');
        var $form_delete = $form.next();
        var $form_delete_data = $form_delete.serialize();
        if ($form.length) {
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
                    if ($button.hasClass('use-ajax')) {
                        $.ajax({
                            url: $form_delete.attr('action'),
                            method: 'DELETE',
                            data: $form_delete_data,
                            headers: {
                                'X-CSRF-TOKEN': window.Laravel.csrfToken
                            },
                            success: function ($result) {
                                if ($result) {
                                    for (var $i = 0; $i < $result.length; ++$i) {
                                        command_action($result[$i]);
                                    }
                                }
                            },
                        });
                    } else {
                        if ($form_delete.length && $form_delete.get(0).tagName == 'FORM') $form_delete.submit();
                    }
                }
            });
        }
    });

    $('body').delegate('#discount-timer input[type="checkbox"], #discount-timer input[type="radio"]', 'change', function (event) {
        var $input = $(this);
        var $typeInput = $input.attr('type');
        var $statusInput = $input.is(':checked');
        var $boxDiscountTimer = $('#discount-timer');
        var $boxUseDiscountTimer = $boxDiscountTimer.find('.use-discount-timer');
        var $boxEnterNewPrice = $boxDiscountTimer.find('.enter-new-price');
        var $inputFinishdate = $('input[name="discount[finish_date]"]');
        var $inputAction = $('input[name="discount[action]"][value="0"]');
        var $inputNewPrice = $('input[name="discount[new_price]"]');
        if ($typeInput == 'checkbox' && $statusInput == true) {
            $boxUseDiscountTimer.removeAttr('hidden');
        } else if ($typeInput == 'checkbox') {
            $inputFinishdate.val('');
            $inputAction.prop('checked', true);
            $inputNewPrice.val('');
            $boxUseDiscountTimer.attr('hidden', 'hidden');
            $boxEnterNewPrice.attr('hidden', 'hidden');
        } else if ($typeInput == 'radio' && $input.val() == 2) {
            $boxEnterNewPrice.removeAttr('hidden');
        } else if ($typeInput == 'radio') {
            $boxEnterNewPrice.attr('hidden', 'hidden');
            $inputNewPrice.val('');
        }
    });

    $(document).ready(function () {
        useCodeMirror($);
        useDatePicker($);
        useDateTimePicker($);
        usePhoneMask($);
        useSelect2($);
        customScroll($);
        useEasyAutocomplete($);
        useCkEditor($);
        useFieldUpload($);
    });

    $(document).ajaxComplete(function (event, request, settings) {
        useCodeMirror($);
        useDatePicker($);
        useDateTimePicker($);
        usePhoneMask($);
        useSelect2($);
        customScroll($);
        useCkEditor($);
        useFieldUpload($);
    });
})(jQuery);

function useCkEditor($) {
    $('.uk-ckEditor').each(function () {
        var $idField = null,
            $optionsEditor = {};
        if ($idField = $(this).attr('id')) {
            if ($(this).hasClass('editor-short')) {
                CKEDITOR.config.customConfig = '/js/CkConfigShort.js';
                $optionsEditor = {
                    height: 150
                };
            } else {
                CKEDITOR.config.customConfig = '/js/CkConfigFull.js';
                $optionsEditor = {
                    height: 250
                };
            }
            if (!$('#cke_' + $idField).length) {
                $ckEditorObject[$idField] = CKEDITOR.replace($idField, $optionsEditor);
                $ckEditorObject[$idField].on('change', function (ck) {
                    $('#' + $idField).val(ck.editor.getData());
                });
                CKEDITOR.config.contentsCss = '/components/uikit/dist/css/uikit.min.css';
                CKEDITOR.config.startupOutlineBlocks = true;
            }
        }
    });
};

function useCodeMirror($) {
    $('textarea.uk-codeMirror').each(function () {
        var $id = $(this).attr('id');
        CodeMirror.fromTextArea(document.getElementById($id), {
            lineNumbers: true,
            styleActiveLine: true,
            name: 'javascript',
            theme: 'idea',
            extraKeys: {
                "F11": function (cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function (cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                }
            }
        });
    });
}

function useSelect2($) {
    $('select.uk-select2').select2({
        width: '100%'
    });
};

function useEasyAutocomplete($) {
    $('input.uk-autocomplete').each(function () {
        var input = $(this),
            parent = input.parents('.uk-form-controls-autocomplete'),
            inputValue = parent.find('input[type="hidden"]');
        if (input.data('url')) {
            input.easyAutocomplete({
                url: input.data('url'),
                ajaxSettings: {
                    dataType: 'json',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    data: {
                        dataType: 'json'
                    }
                },
                getValue: input.data('value'),
                requestDelay: 500,
                template: {
                    type: 'custom',
                    method: function (value, item) {
                        return item.view !== undefined ? (value + ' - <span style="font-size: 0.9em; color: #aaa; font-style: italic;">' + item.view + '</span>') : value;
                    }
                },
                list: {
                    onChooseEvent: function () {
                        var item = input.getSelectedItemData();
                        inputValue.val(item.data).trigger("change");
                    },
                    onLoadEvent: function () {
                        inputValue.val('').trigger("change");
                    },
                    maxNumberOfElements: 10,
                    match: {
                        enabled: true
                    }
                },
                preparePostData: function (data) {
                    data.search = input.val();
                    return data;
                }
            });
        }
    });
};

function useDatePicker($) {
    $('input.uk-datepicker').datepicker({});
};

function useDateTimePicker($) {
    var $currentDate = new Date();
    var $startMinute;
    $startMinute = Math.ceil($currentDate.getMinutes() / 10) * 10;
    if ($startMinute == 60) {
        $currentDate.setHours($currentDate.getHours() + 1);
        $currentDate.setMinutes(0);
    } else {
        $currentDate.setMinutes($startMinute);
    }
    $('input.uk-datetimepicker').datepicker({
        timepicker: true,
        minutesStep: 10,
        minDate: $currentDate
    });
};

function usePhoneMask($) {
    $('input.uk-phone-mask').inputmask(' 999 999-99-99');
};

function customScroll($) {
    $.mCustomScrollbar.defaults.scrollButtons.enable = true;
    $.mCustomScrollbar.defaults.axis = "yx";
    $.mCustomScrollbar.defaults.theme = "inset";
    $('.uk-custom-scroll').mCustomScrollbar();
}