<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class staf extends Model
{
    use HasFactory;

    public function attribution()
    {
        return $this->belongsTo(attribution::class);
    }

    public function images()
    {
        return $this->hasMany(staf_image::class);
    }
}
