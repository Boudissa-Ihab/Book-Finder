<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserBook extends Pivot
{
    protected $fillable = [
        'user_id',
        'book_id',
    ];
}
