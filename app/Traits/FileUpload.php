<?php
namespace App\Traits;
use Illuminate\Support\Facades\Storage;
trait FileUpload
{
    public function FileUpload($file, $path = 'undefined'){
        $file = $file->store($path,'public');
        return $file;
    }
}
