<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    use Searchable;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createUser()
    {
        return $this->belongsTo(User::class, 'generate_user_id');
    }
}
