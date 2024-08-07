<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtra extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
