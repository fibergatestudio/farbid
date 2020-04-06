<input type="{{ $params->get('type') }}"
       id="{{ $params->get('id') }}"
       name="{{ $params->get('name') }}"
       value="{{ $params->get('selected') }}"
    {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}>
