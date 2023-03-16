<?php

namespace App\Models;
use App\Models\TutorialStep;
use App\Models\TutorialImage;
use App\Models\Bookmark;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function tutorialSteps(){
        return  $this->hasMany(TutorialStep::class);
    }
    public function tutorialImages(){
       return  $this->hasMany(TutorialImage::class);
    }
    public function bookmarks(){
       return  $this->hasMany(Bookmark::class);
    }
    public function category(){
       return  $this->belongsTo(Category::class);
    }
}
