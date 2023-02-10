<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategory;
class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
    ];

    public function subCategories(){
        $this->hasMany(SubCategory::class);
    }
}
