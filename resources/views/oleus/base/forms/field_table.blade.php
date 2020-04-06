<div class="uk-form-row"
     id="{{ $params->get('id') }}_object">
    @if($label = $params->get('label'))
        <label for="{{ $params->get('id') }}"
               class="uk-form-label">
            {!! $label !!}
            @if($label = $params->get('required'))
                <span class="uk-text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="uk-form-controls">
        @php($name = $params->get('name'))
        @php($options = $params->get('options'))
        @php($cols = $options['cols'])
        @php($thead = isset($options['thead']) ? $options['thead'] : NULL)
        @php($tbody = (($_value = $params->get('value')) ? json_decode($_value) : NULL))
        @if($error = $params->get('error'))
            <span class="uk-help-block uk-text-danger uk-text-right uk-margin-small-bottom uk-display-block">
                {!! $error !!}
            </span>
        @endif
        <div class="uk-inline uk-width-1-1">
            <table class="uk-table uk-table-small uk-table-divider">
                @if($thead)
                    <thead class="uk-background-muted">
                        <tr>
                            @for($i = 0; $i < $cols; $i++)
                                <th class="uk-width-1-{{ $cols }} uk-padding-small">
                                    {!! $thead[$i] !!}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                @endif
                <tbody id="field-table-items">
                    @if($tbody)
                        @foreach($tbody as $td)
                            @include('oleus.base.forms.field_table_item', ['cols' => $cols, 'name' => $name, 'td_item' => $td])
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="uk-clearfix uk-text-right">
                {!! _l(trans('forms.button_add_table_line'), 'oleus.fields.item', ['p' => ['type' => 'table', 'action' => 'add'], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded', 'data-cols' => $cols, 'data-name' => $name]]) !!}
            </div>
        </div>
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">
                {!! $help !!}
            </span>
        @endif
    </div>
</div>
