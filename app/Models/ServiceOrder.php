<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class ServiceOrder extends Model
    {
        protected $table = 'service_orders';
        protected $fillable = [
            'name',
            'email',
            'phone',
            'comment',
            'services',
            'urgently',
            'status',
        ];

        public function getServiceItemsAttribute()
        {
            if($this->services) {
                $_data = unserialize($this->services);
                $_response = NULL;
                foreach($_data as $_service) {
                    if($_item = Service::find($_service['id'])) {
                        $_response[] = _l($_item->title, $_item->_alias->alias, ['a' => ['target' => '_blank']]);
                    } else {
                        $_response[] = $_service['title'];
                    }
                }

                return implode(', ', $_response);
            }

            return NULL;
        }
    }
