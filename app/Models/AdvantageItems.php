<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class AdvantageItems extends Model
    {
        protected $table = 'advantage_items',
            $primaryKey = 'id',
            $fillable = [
            'title',
            'advantage_id',
            'sub_title',
            'icon_fid',
            'body',
            'status',
            'hidden_title',
            'sort',
            'access'
        ];
        protected $perPage = 50;

        public function scopeActive($query)
        {
            return $query->where('status', 1);
        }

        public function _icon()
        {
            return $this->hasOne(File::class, 'id', 'icon_fid');
        }
    }
