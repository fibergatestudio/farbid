<?php

    namespace App\Models;

    use App\User;
    use Illuminate\Database\Eloquent\Model;

    class Profile extends Model
    {
        protected $table = 'profiles';
        protected $guarded = [];
        public $timestamps = FALSE;
        protected $dates = [
            'birthday'
        ];

        public function getFullNameAttribute()
        {
            $_name = NULL;
            if($this->last_name) $_name[] = $this->last_name;
            if($this->first_name) $_name[] = $this->first_name;

            return $_name ? implode(' ', $_name) : $this->_user->name;
        }

        public function _user()
        {
            return $this->belongsTo(User::class, 'uid');
        }

        public function _avatar()
        {
            return $this->hasOne(File::class, 'id', 'avatar_fid');
        }

        public function _avatar_asset($preset = NULL, $options = [])
        {
            return ($this->exists && $this->_avatar) ? image_render($this->_avatar, $preset, $options) : image_render(NULL, $preset, $options);
        }
    }
