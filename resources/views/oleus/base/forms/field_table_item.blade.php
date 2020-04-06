@php($index = uniqid())
@php($td = isset($td_item) ? $td_item : NULL)
<tr id="table-item-{{ $index }}">
    @for($i = 0; $i < $cols; $i++)
        <td>
            {!!
                field_render("{$name}.{$index}.{$i}", [
                    'value'      => $td && isset($td[$i]) ? $td[$i] : NULL,
                    'type'       => 'textarea',
                    'attributes' => [
                        'rows' => 2
                    ]
                ])
            !!}
        </td>
    @endfor
</tr>