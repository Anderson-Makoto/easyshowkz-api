<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Phone extends Model
{
    use HasFactory, SoftDeletes;

    public function user(): Builder
    {
        return $this->belongsTo(User::class);
    }
}
