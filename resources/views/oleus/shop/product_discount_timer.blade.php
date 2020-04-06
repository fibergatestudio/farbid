@php($_discount_timer = $entity->_discount_timer)
<div id="discount-timer">
    {!!
         field_render('discount_timer.use_timer', [
            'type'     => 'checkbox',
            'label'    => 'использовать таймер отсчета дисконтной цены',
            'selected' => $_discount_timer->exists ? 1 : 0,
            'attributes' => [
                'autocomplete' => 'off'
            ]
         ])
    !!}
    <div class="use-discount-timer uk-margin-top"
        {{ $_discount_timer->exists ? NULL : 'hidden' }}>
        {!!
            field_render('discount_timer.finish_date', [
                'label' => 'Дата и время окончания',
                'value' => $_discount_timer->exists ? $_discount_timer->finish_date->format('d.m.Y H:i') : NULL,
                'class' => 'uk-datetimepicker',
                'help' => 'До какого времени будет учитываться работа таймера',
                'attributes' => [
                    'autocomplete' => 'off'
                ]
            ])
        !!}
        {!!
            field_render('discount_timer.action', [
                'type'   => 'radio',
                'label'  => 'Действие после окончания',
                'value'  => $_discount_timer->exists ? $_discount_timer->action : 0,
                'values' => [
                    0 => 'снять товар с публикации',
                    1 => 'старая цена становится актуальной',
                    2 => 'указать цену',
                ],
                'attributes' => [
                    'autocomplete' => 'off'
                ]
            ])
        !!}
        <div class="enter-new-price uk-margin-top"
             {{ $_discount_timer->exists && $_discount_timer->action == 2 ? NULL : 'hidden' }}>
            {!!
            field_render('discount_timer.new_price', [
                'type' => 'number',
                'label' => 'Новая цена',
                'value' => $_discount_timer->exists && $_discount_timer->action == 2 ? $_discount_timer->new_price : NULL,
                'attributes' => [
                    'autocomplete' => 'off',
                    'min'  => 0,
                    'step' => 0.01
                ],
            ])
        !!}
        </div>
    </div>
</div>