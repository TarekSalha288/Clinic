<?php

namespace App;
use Illuminate\Support\Facades\Storage;
trait UploadImageTrait
{
    public function ImageUpload($request, $user_id, $folderName, $field = 'image')
    {
        if ($request->hasFile($field)) {
            $imageFile = $request->file($field);

            $imageName = md5_file($imageFile->getRealPath()) . '.' . $imageFile->getClientOriginalExtension();
            $path = "uploads/$folderName/$user_id/$imageName";

            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->putFileAs("uploads/$folderName/$user_id", $imageFile, $imageName);
            }

            $url = Storage::url($path);
            return $url;
        }
    }
}
