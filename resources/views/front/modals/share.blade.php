<div id="modal-share"
     class="modal-share uk-flex-top"
     uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <h2 class="uk-modal-title uk-text-uppercase uk-text-center">
                {{ __('Отправить') }}
            </h2>
        </div>
        <div class="uk-modal-body uk-padding-remove-vertical uk-text-center">
            <button class="uk-modal-close-default"
                    type="button"
                    uk-close></button>
            <div class=""></div>
            <div class="messenger uk-flex uk-flex-center uk-text-center">
                @if($_contacts['current']['telegram'])
                    <a class="uk-display-block"
                       href="{{$_contacts['current']['telegram']}}">
                        <div
                            class="icon telegram uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                            <i class="icon-telegram sprites-m uk-display-block"></i>
                        </div>
                        <div class="name uk-text-uppercase">
                            telegram
                        </div>
                    </a>
                @endif
                @if($_contacts['current']['viber'])
                    <a class="uk-display-block"
                       href="{{$_contacts['current']['viber']}}">
                        <div
                            class="icon viber uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                            <i class="icon-viber sprites-m uk-display-block"></i>
                        </div>
                        <div class="name uk-text-uppercase">
                            viber
                        </div>
                    </a>
                @endif
                @if($_contacts['current']['whatsapp'])
                    <a class="uk-display-block"
                       href="{{$_contacts['current']['whatsapp']}}">
                        <div
                            class="icon whatsapp uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                            <i class="icon-whatsapp sprites-m uk-display-block"></i>
                        </div>
                        <div class="name uk-text-uppercase">
                            whatsapp
                        </div>
                    </a>
                @endif
            </div>
            <div class="url-share"
                 id="url-share">
                {{ config('app.url') . _u($item->_alias->alias, [], TRUE) }}
            </div>
            <button class="uk-button btn-share"
                    type="button"
                    onclick="UIkit.notification({message: 'Ссылка скопирована в буфер обмена', pos: 'bottom-left'})"
                    data-clipboard-action="copy"
                    data-clipboard-target="#url-share">
                {{ __('Скопировать ссылку') }}
            </button>
        </div>
    </div>
</div>
