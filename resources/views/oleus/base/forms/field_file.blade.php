@php
    $files = ($_files = session($params->get('old'))) ? json_decode($_files) : (($_files = $params->get('values')) ? $_files : NULL);
@endphp
<div class="uk-form-row"
     id="{{ $params->get('id') }}_object">
    @if($error = $params->get('error'))
        <span
            class="uk-help-block uk-text-danger uk-text-right uk-margin-small-bottom uk-display-block">{!! $error !!}</span>
    @endif
    @if($label = $params->get('label'))
        <label for="{{ $params->get('id') }}"
               class="uk-form-label">{!! $label !!}
            @if($label = $params->get('required'))
                <span class="uk-text-danger">*</span>
            @endif
        </label>
    @endif
    <div
        class="uk-form-controls uk-form-controls-file {{ $params->get('multiple') ? 'uk-multiple-file' : 'uk-one-file' }}{{ !$params->get('multiple') && $files ? ' loaded-file' : '' }}">
        <div class="uk-width-1-1">
            <input type="hidden"
                   name="{{ $params->get('name') }}">
            <div class="uk-preview">
                {{--<div class="uk-grid uk-grid-collapse uk-child-width-1-3">--}}
                {{--</div>--}}
                @if($files)
                    @foreach($files as $file)
                        {!! preview_file_render($file, $params->get('name'), $params->get('view')) !!}
                    @endforeach
                @endif
            </div>
            <div class="uk-field uk-text-right">
                <div class="js-upload uk-placeholder uk-text-center uk-border-rounded"
                     id="{{ $params->get('id') }}">
                    <span uk-icon="icon: ui_cloud_upload"
                          class="uk-text-muted"></span>
                    <span class="uk-text-middle">
                        @lang('forms.file_dropdown_start')
                    </span>
                    @php($_upload_allow = $params->get('upload_allow'))
                    <div data-url="{{ $params->get('ajax_url') }}"
                         data-allow="{{ $_upload_allow }}"
                         data-field="{{ $params->get('name') }}"
                         data-multiple="{{ $params->get('multiple') ? 1 : 0 }}"
                         data-view="{{ $params->get('view') }}"
                         class="uk-field file-upload-field{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                         uk-form-custom>
                        <input type="file"{{ $params->get('multiple') ? ' multiple' : '' }}>
                        <span class="uk-link uk-text-lowercase">
                            @lang('forms.file_dropdown_finish')
                        </span>
                    </div>
                    @php($_upload_allow_view = str_replace('*.(', '.', str_replace(')', '', str_replace('|', ' .', $_upload_allow))))
                    <div class="uk-text-small uk-text-muted">
                        @lang('forms.file_allow_mime_type', ['mime_type' => $_upload_allow_view])
                        @if($help = $params->get('help'))
                            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
                        @endif
                    </div>
                </div>
            </div>
            <progress class="uk-progress"
                      value="0"
                      max="100"
                      hidden></progress>
        </div>
    </div>
</div>
