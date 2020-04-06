var $ajaxLoad = window.Laravel.ajaxLoad;

(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'LOCALE-CODE': window.Laravel.locale,
            'LOCATION-CODE': window.Laravel.location
        }
    });

    var $body = $('body');

    $body.delegate('.use-ajax', 'click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if ($ajaxLoad === false) {
            var $this = $(this),
                $el = $this.get(0),
                $ajaxHref = '',
                $ajaxData = '';
            if ($el.tagName == 'A' && !$(this).hasClass('load')) {
                event.preventDefault();
                $ajaxHref = $this.data('path') != undefined && $this.data('path') ? $this.data('path') : $this.attr('href');
                console.log($ajaxHref);
                $ajaxData = $this.data();
            } else if ($el.tagName == 'BUTTON' && $this.attr('type') == 'submit') {
                event.preventDefault();
                var $formSubmit = $this.parents('form');
                $ajaxHref = $formSubmit.attr('action');
                $ajaxData = $formSubmit.serialize();
            } else if ($el.tagName == 'BUTTON' && $this.attr('type') == 'button') {
                event.preventDefault();
                $ajaxHref = $this.data('path');
                $ajaxData = $this.data();
            } else if ($el.tagName == 'INPUT' && ($this.attr('type') == 'checkbox' || $this.attr('type') == 'radio') && $this.hasClass('form-submit')) {
                var $formSubmit = $this.parents('form');
                $ajaxHref = $formSubmit.attr('action');
                $ajaxData = $formSubmit.serialize();
            }
            setTimeout(function () {
                if ($ajaxHref && $ajaxData) _ajax_post($this, $ajaxHref, $ajaxData);
            }, 300);
        }
    });

    $body.delegate('.use-ajax', 'change', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if ($ajaxLoad === false) {
            var $this = $(this),
                $el = $this.get(0),
                $ajaxHref = '',
                $ajaxData = '';
            if ($el.tagName == 'SELECT') {
                $ajaxHref = $this.data('href');
                if ($this.hasClass('uk-select2')) {
                    $this.on('select2:select', function (e) {
                        $ajaxData = {};
                        $ajaxData.option = $this.val();
                    });
                } else if ($this.val()) {
                    $ajaxData = $this.data();
                    $ajaxData.option = $this.val();
                }
            }
            setTimeout(function () {
                if ($ajaxHref && $ajaxData) _ajax_post($this, $ajaxHref, $ajaxData);
            }, 300);
        }
    });

    function _ajax_post($this, $ajaxHref, $ajaxData) {
        var $viewLoad = $this.data('view_load');
        $viewLoad = ($viewLoad == undefined || $viewLoad == 1) ? true : false;
        $.ajax({
            url: $ajaxHref,
            method: 'POST',
            data: $ajaxData,
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            },
            beforeSend: function () {
                $ajaxLoad = true;
                $('body').addClass('ajax-load');
                if ($viewLoad) {
                    $this.attr('disabled', 'disabled').addClass('load').append('');
                } else {
                    $this.attr('disabled', 'disabled').addClass('load');
                }
            },
            success: function ($result) {
                $ajaxLoad = false;
                $('body').removeClass('ajax-load');
                if ($viewLoad) {
                    $this.removeAttr('disabled').removeClass('load').find('.uk-ajax-spinner').remove();
                } else {
                    $this.removeAttr('disabled').removeClass('load')
                }
                if ($result) {
                    for (var $i = 0; $i < $result.length; ++$i) {
                        command_action($result[$i]);
                    }
                }
            },
            error: function ($result) {
                $ajaxLoad = false;
                $('body').removeClass('ajax-load');
                if ($viewLoad) {
                    $this.removeAttr('disabled').removeClass('load').find('.uk-ajax-spinner').remove();
                } else {
                    $this.removeAttr('disabled').removeClass('load')
                }
            }
        });
    }

})(jQuery);

function command_action($item) {
    if ($item.command == 'text') command_text($item.target, $item.data);
    if ($item.command == 'html') command_html($item.target, $item.data);
    if ($item.command == 'replaceWith') command_replace_with($item.target, $item.data);
    if ($item.command == 'append') command_append($item.target, $item.data);
    if ($item.command == 'prepend') command_prepend($item.target, $item.data);
    if ($item.command == 'addClass') command_addClass($item.target, $item.data);
    if ($item.command == 'removeClass') command_removeClass($item.target, $item.data);
    if ($item.command == 'remove') command_remove($item.target);
    if ($item.command == 'attr') command_attr($item.target, $item.attr, $item.data);
    if ($item.command == 'removeAttr') command_removeAttr($item.target, $item.attr);
    if ($item.command == 'val') command_val($item.target, $item.data);
    if ($item.command == 'data') command_data($item.target, $item.set, $item.data);
    if ($item.command == 'select2') command_select2();
    if ($item.command == 'ckEditor') command_ckEditor();
    if ($item.command == 'fileUpload') command_fieldUpload();
    if ($item.command == 'easyAutocomplete') command_easyAutocomplete();
    if ($item.command == 'swal') command_swal($item.options);
    if ($item.command == 'magnific') command_magnific();
    if ($item.command == 'addbasket') command_addbasket();
    /**
     * UiKit
     */
    if ($item.command == 'modal') command_modal($item.data, ($item.options || {}));
    if ($item.command == 'modal_close') command_modal_close(($item.target || '#ajax-modal'));
    if ($item.command == 'notice') command_notification($item.text, ($item.status || 'primary'), ($item.position || 'bottom-right'));
    /**
     * USE
     */
    if ($item.command == 'eval') {
        eval($item.code);
    }
    /**
     * Form
     */
    if ($item.command == 'clearForm') command_clearForm($item.form);
    /**
     * SEO
     */
    if ($item.command == 'change_url') command_change_url($item.url);
    if ($item.command == 'change_title') command_change_title($item.title);
    if ($item.command == 'redirect') command_redirect($item.url, ($item.time || 0));
    if ($item.command == 'reload') command_reload(($item.time || 0));
    if ($item.command == 'analytics_gtag') command_analytics_gtag(($item.data || {}));
    if ($item.command == 'analytics_fbq') command_analytics_fbq(($item.data || {}));
    if ($item.command == 'ecommerce') command_ecommerce(($item.event || null), ($item.data || null));
}

function command_change_url($url) {
    history.pushState(null, null, $url);
}

function command_change_title($title) {
    document.title = $title;
}

function command_redirect($url, $time) {
    setTimeout(function () {
        window.location.href = $url;
    }, $time);
}

function command_reload($time) {
    jQuery('body').addClass('reload-page');
    setTimeout(function () {
        location.reload();
    }, $time);
}

function command_html($target, $html) {
    jQuery($target).html($html);
}

function command_replace_with($target, $html) {
    jQuery($target).replaceWith($html);
}

function command_text($target, $html) {
    jQuery($target).text($html);
}

function command_val($target, $data) {
    jQuery($target).val($data);
}

function command_data($target, $attr, $data) {
    jQuery($target).data($attr, $data);
}

function command_append($target, $html) {
    jQuery($target).append($html);
}

function command_prepend($target, $html) {
    jQuery($target).prepend($html);
}

function command_attr($target, $attr, $data) {
    jQuery($target).attr($attr, $data);
}

function command_remove($target) {
    if (jQuery($target).length) {
        jQuery($target).remove();
    }
}

function command_removeAttr($target, $attr) {
    $($target).removeAttr($attr);
}

function command_addClass($target, $class) {
    jQuery($target).addClass($class);
}

function command_removeClass($target, $class) {
    jQuery($target).removeClass($class);
}

function command_select2() {
    useSelect2($);
}

function command_ckEditor() {
    useCkEditor($);
}

function command_fieldUpload() {
    useFieldUpload($);
}

function command_easyAutocomplete() {
    useEasyAutocomplete($);
}

function command_swal($options) {
    $options = $options || {};
    swal($options)
}

function command_clearForm($form_id) {
    jQuery('#' + $form_id).find('input[type="text"], input[type="email"], input[type="password"], textarea').val('');
    jQuery('#' + $form_id).find('input[type="checkbox"]').prop('checked', false);
    jQuery('#' + $form_id).find('select').find('option').prop('selected', false);
}

function command_notification($text, $status, $position) {
    UIkit.notification($text, {
        status: $status,
        pos: $position
    });
}

function command_modal($content, $options) {
    $options.bgClose = $options.bgClose == undefined ? true : $options.bgClose;
    $options.clsPage = 'uk-modal-page view-ajax-modal';

    var $_id = $options.id == undefined ? 'ajax-modal' : $options.id;
    var $_class = $options.class == undefined ? '' : ' ' + $options.class;
    var $d = UIkit.modal(("<div class=\"uk-modal\" id=\"" + $_id + "\"><div class=\"uk-modal-dialog" + $_class + "\">" + $content + "</div></div>"), $options);
    $d.show();
    jQuery('body').delegate('#' + $_id, 'hidden', function (event) {
        if (event.target === event.currentTarget) $d.$destroy(true);
    });
}

function command_modal_close($target) {
    UIkit.modal($target).hide();
}

function command_magnific() {
    setTimeout(function () {
        $.magnificPopup.open({
            items: {
                src: '<div class="modal-success"><div class="content-success">Заявка на подписку отправлена!</div></div>'
            },
            type: 'inline'
        });
    }, 500);
    setTimeout(function () {
        $.magnificPopup.close({
            items: {src: '#newslater-popup'}, type: 'inline'
        }, 0);
    }, 300);
    setTimeout(function () {
        $.magnificPopup.close({});
    }, 4000);
}

function command_addbasket() {
    $.magnificPopup.open({
        items: {
            src: '<div class="modal-success"><div class="content-success">Товар добавлен в корзину!</div></div>'
        },
        type: 'inline'
    });
    setTimeout(function () {
        $.magnificPopup.close({});
    }, 3000);
}

/**
 * Analytics
 **/

function command_analytics_gtag($gtag) {
    if (typeof (gtag) === 'function') gtag('event', ($gtag.event || 'SEND_FORM'), {
        event_category: ($gtag.category || null),
        event_action: ($gtag.event_action || 'SEND')
    });
    return true;
}

function command_analytics_fbq($fbq) {
    if (typeof (fbq) === 'function') fbq('track', ($fbq.event || 'SEND_FORM'));
    return true;
}

function command_ecommerce($event, $data) {
    if (window.commerce !== undefined) window.commerce.event($event, $data);
}