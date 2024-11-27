<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
     
    public function getImageUrlAttribute()
    {
        return $this->image 
            ? url('storage/' . $this->image) 
            : url('images/default-brand.png'); // Replace with your default image path if necessary
    }
}

