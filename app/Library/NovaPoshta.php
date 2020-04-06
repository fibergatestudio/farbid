<?php

    namespace App\Library;

    class NovaPoshta
    {
        private $api_key;
        private $url = 'https://api.novaposhta.ua/v2.0/json/';
        private $hide_area = [
            '71508128-9b87-11de-822f-000c2965ae0e'
        ];

        public function __construct()
        {
            $this->api_key = config('os_shop.np.key');
        }

        public function send_request($_request)
        {
            $_data = json_encode($_request);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($_data)
                ]
            );
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_data);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $_response = curl_exec($ch);
            curl_close($ch);

            return $_response;
        }

        public function get_area($code_area = NULL)
        {
            $_data = [
                'modelName'    => 'Address',
                'calledMethod' => 'getAreas',
                'apiKey'       => $this->api_key
            ];
            $_items = NULL;
            $_result = json_decode($this->send_request($_data));
            if ($_result) {
                foreach ($_result->data as $_area) {
                    if (is_null($code_area) && !in_array($_area->Ref, $this->hide_area)) {
                        $_items[$_area->Ref] = trim(preg_replace('/\(.*?\)/is', '', $_area->Description));
                    } elseif ($code_area && $code_area == $_area->Ref) {
                        $_items = trim(preg_replace('/\(.*?\)/is', '', $_area->Description));
                        break;
                    }
                }
            }

            return $_items;
        }

        public function get_city($_area, $code_city = NULL)
        {
            $_data = [
                'modelName'    => 'Address',
                'calledMethod' => 'getCities',
                'apiKey'       => $this->api_key
            ];
            $_items = NULL;
            $_result = json_decode($this->send_request($_data));
            if ($_result) {
                foreach ($_result->data as $_city) {
                    if (is_null($code_city) && $_city->Area == $_area) {
                        $_items[$_city->Ref] = trim($_city->SettlementTypeDescription) . ' ' . trim(preg_replace('/\(.*?\)/is', '', $_city->Description));
                    } elseif ($code_city && $code_city == $_city->Ref) {
                        $_items = trim($_city->SettlementTypeDescription) . ' ' . trim(preg_replace('/\(.*?\)/is', '', $_city->Description));
                        break;
                    }
                }
            }

            return $_items;
        }

        public function get_warehouses($_city, $code_warehouses = NULL)
        {
            $_data = [
                'modelName'        => 'Address',
                'calledMethod'     => 'getWarehouses',
                'apiKey'           => $this->api_key,
                'methodProperties' => [
                    'CityRef' => $_city
                ]
            ];
            $_items = NULL;
            $_result = json_decode($this->send_request($_data));
            if ($_result) {
                foreach ($_result->data as $_warehouse) {
                    if (is_null($code_warehouses)) {
                        $_items[$_warehouse->Ref] = trim($_warehouse->Description);
                    } elseif ($code_warehouses && $code_warehouses == $_warehouse->Ref) {
                        $_items = trim($_warehouse->Description);
                        break;
                    }
                }
            }

            return $_items;
        }

        public function get_street($_city, $code_street = NULL)
        {
            $_data = [
                'modelName'        => 'Address',
                'calledMethod'     => 'getStreet',
                'apiKey'           => $this->api_key,
                'methodProperties' => [
                    'CityRef' => $_city
                ]
            ];
            $_items = NULL;
            $_result = json_decode($this->send_request($_data));
            if ($_result) {
                foreach ($_result->data as $_warehouse) {
                    if (is_null($code_street)) {
                        $_items[$_warehouse->Ref] = '"' . trim($_warehouse->StreetsType) . ' ' . trim($_warehouse->Description) . '"';
                    } elseif ($code_street && $code_street == $_warehouse->Ref) {
                        $_items = trim($_warehouse->StreetsType) . ' ' . trim($_warehouse->Description);
                    }
                }
            }

            return $_items;
        }

        public function formation_address($request = [])
        {
            $_data = array_merge([
                'area'       => NULL,
                'city'       => NULL,
                'warehouses' => NULL,
                'street'     => NULL,
            ], $request);

            $_response = NULL;
            if ($_data['area']) {
                $_response['area'] =
                $_response['list'][] = 'область ' . self::get_area($_data['area']);
                if ($_data['area']) {
                    $_response['city'] =
                    $_response['list'][] = self::get_city($_data['area'], $_data['city']);
                    if ($_data['warehouses']) {
                        $_response['warehouses'] =
                        $_response['list'][] = self::get_warehouses($_data['city'], $_data['warehouses']);
                    }
                    if ($_data['street']) {
                        $_response['street'] =
                        $_response['list'][] = self::get_street($_data['city'], $_data['street']);
                    }
                }
            }

            if ($_response) $_response['output'] = implode(', ', $_response['list']);

            return $_response;
        }
    }