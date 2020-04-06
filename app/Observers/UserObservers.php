<?php

    namespace App\Observers;

    use App\Models\Profile;
    use App\User;
    use Carbon\Carbon;

    class UserObservers
    {
        public function created(User $item)
        {
        }

        public function saved(User $item)
        {
            if(request()->has('role')) $item->syncRoles(request()->input('role'));
            if(request()->has('first_name')) {
                $_save = request()->only([
                    'avatar_fid',
                    'last_name',
                    'first_name',
                    'phone',
                    'sex',
                    'birthday',
                    'comment',
                    'city_delivery',
                    'address_delivery',
                ]);
                $_save['uid'] = $item->id;
                $_save['birthday'] = ($_birthday = request()->input('birthday')) ? Carbon::parse($_birthday) : NULL;
                if(request()->has('avatar_fid') && ($avatar_fid = request()->input('avatar_fid'))) {
                    $_avatar_fid = array_shift($avatar_fid);
                    $_save['avatar_fid'] = (int)$_avatar_fid['id'];
                }
                Profile::updateOrCreate([
                    'uid' => $item->id
                ], $_save);
            }
        }

        public function deleting(User $item)
        {
        }
    }