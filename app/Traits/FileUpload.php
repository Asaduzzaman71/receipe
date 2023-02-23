<?php
namespace App\Traits;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUpload
{
    public function FileUpload($base64_image, $path = 'undefined'){

        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
            $data = substr($base64_image, strpos($base64_image, ',') + 1);
            $data = base64_decode($data);
            $extension = explode('/', explode(':', substr($base64_image, 0, strpos($base64_image, ';')))[1])[1];
            $imageName = Str::random(36).'.'.$extension;
            $imageNameWithPath =  $path.'/'.$imageName;
            Storage::disk('public')->put($imageNameWithPath, $data);
            return $imageNameWithPath;
        }
    }

    public function VideoUpload($file, $path = 'undefined'){
        $file = $file->store($path,'public');
        return $file;
    }
}
