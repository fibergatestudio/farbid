<?php

    namespace App\Observers;

    use App\Models\Permission;
    use App\Models\Role;

    class RoleObservers
    {
        public function created(Role $item)
        {
        }

        public function saved(Role $item)
        {
            if($permissions = request()->input('permissions')) {
                $_permissions = Permission::whereIn('name', array_keys($permissions))
                    ->get();
                $item->syncPermissions($_permissions);
            }
        }

        public function deleting(Role $item)
        {
        }
    }