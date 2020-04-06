<?php

    return [
        'variable'       => [
            'model' => 'App\Models\Variable',
            'ceil'  => 'key'
        ],
        'block'          => [
            'model' => 'App\Models\Block',
            'ceil'  => 'id',
        ],
        'advantage'      => [
            'model' => 'App\Models\Advantage',
            'ceil'  => 'id',
        ],
        'service_prices' => [
            'model' => 'App\Models\ServicePrice',
            'ceil'  => 'service_id',
            'lists' => TRUE
        ],
        'banner'         => [
            'model' => 'App\Models\Banner',
            'ceil'  => 'id'
        ]
    ];