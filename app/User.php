<?php

    namespace App;

    use App\Models\Profile;
    use App\Models\Role;
    use App\Models\ShopOrder;
    use App\Models\ShopProductDesires;
    use App\Notifications\MailResetPasswordToken;
    use Carbon\Carbon;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    use Spatie\Permission\Traits\HasRoles;

    class User extends Authenticatable
    {
        use Notifiable;
        use HasRoles;

        protected $guard_name = 'web';
        protected $table = 'users';
        protected $fillable = [
            'name',
            'email',
            'password',
            'active',
            'api_token'
        ];
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * Attributes
         */
        public function getRoleAttribute()
        {
            $_roles = NULL;
            if($_roles = $this->getRoleNames()) {
                $_roles = $_roles->map(function ($_role) {
                    return Role::where('name', $_role)
                        ->first();
                });
            }

            return $_roles;
        }

        public function getViewRoleAttribute()
        {
            $_view_roles = NULL;
            if($_roles = $this->role) {
                $_view_roles = $_roles->map(function ($_role) {
                    return _l(trans($_role->display_name), 'oleus.roles.edit', ['p' => ['id' => $_role->id]]);
                })->toArray();
            }

            return $_view_roles ? implode(',', $_view_roles) : NULL;
        }

        public function getOrderYearsAttribute()
        {
            $_first_order = ShopOrder::where('user_id', $this->id)
                ->orderBy('id')
                ->first();
            if($_first_order) {
                $_min_year = $_first_order->created_at->year;
                $_years = collect([$_min_year]);
                if($_min_year != date('Y')) {
                    for($_i = 2016+1; $_i <= date('Y'); $_i++) {
                        $_years->push($_i);
                    }
                }

                return $_years;
            }
            return collect([]);
        }

        /**
         * Other
         */
        public function _profile()
        {
            return $this->hasOne(Profile::class, 'uid');
        }

        public function _my_desires()
        {
            return $this->hasMany(ShopProductDesires::class, 'user_id');
        }

        public static function _users()
        {
            return User::orderBy('name')
                ->pluck('name', 'id');
        }

        public function _orders($year = NULL, $month = NULL)
        {
            if(is_null($year)) $year = date('Y');
            if(is_null($month)) $month = Session::get('view_user_order_month', date('F'));
            $_months = [
                'January' => 1,
                'February' => 2,
                'March' => 3,
                'April' => 4,
                'May' => 5,
                'June' => 6,
                'July' => 7,
                'August' => 8,
                'September' => 9,
                'October' => 10,
                'November' => 11,
                'December' => 12
            ];

            return ShopOrder::where('user_id', $this->id)
                ->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $_months[$month])
                ->get();
        }

        /**
         * Notification
         */
        public function sendPasswordResetNotification($token)
        {
            $this->notify(new MailResetPasswordToken($token));
        }
    }
