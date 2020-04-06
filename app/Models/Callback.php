<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Callback extends Model
    {
        protected $table = 'callbacks';
        protected $fillable = [
            'name',
            'email',
            'phone',
            'comment',
            'type',
            'status',
        ];
    }
