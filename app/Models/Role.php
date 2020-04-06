<?php

    namespace App\Models;

    use Illuminate\Support\Facades\DB;

    class Role extends \Spatie\Permission\Models\Role
    {
        public static $defaultGuardName = 'web';
        protected $perPage = 50;
        protected $table = 'roles';

        public function getCountUsersAttribute()
        {
            return DB::table('model_has_roles')
                ->where('role_id', $this->id)
                ->count();
        }

        public static function getAll()
        {
            $all_roles = self::all();
            if($all_roles->isNotEmpty()) {
                $_roles = $all_roles->map(function ($_role) {
                    $_role->display_name = trans($_role->display_name);

                    return $_role;
                });
                $all_roles = $_roles;
            }

            return $all_roles;
        }
    }
