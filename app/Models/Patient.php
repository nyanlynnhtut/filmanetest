<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Patient extends Model
{
    use HasFactory;

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
 
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    // public function frontImage(): MorphOne
    // {
    //     return $this->morphOne(Image::class, 'imageable')->wherePivot('type', 'front');
    // }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
