<?php

    namespace App\Library;

    use Carbon\Carbon;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use App\Library\IdentifyDevice;
    use Illuminate\Support\Facades\View;

    class Conf
    {
        public static function set($file_name, $values, $rebuild = FALSE)
        {
            $_data = $rebuild ? [] : config($file_name);
            foreach($values as $_key => $_value) {
                $_parts = explode('.', $_key);
                $_element = &$_data;
                foreach($_parts as $_part) $_element = &$_element[$_part];
                if(is_bool($_value)) {
                    $_element = (bool)$_value;
                } else {
                    $_element = (string)$_value;
                }
            }
            $_code = '<?php return ' . var_export($_data, TRUE) . ';';
            Storage::disk('config')->put("{$file_name}.php", $_code);

            return $_data;
        }
    }