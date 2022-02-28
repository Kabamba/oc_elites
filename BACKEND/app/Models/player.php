<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class player extends Model
{
    use HasFactory;

    public function position()
    {
        return $this->belongsTo(position::class);
    }

    public function images()
    {
        return $this->hasMany(player_image::class);
    }
}
