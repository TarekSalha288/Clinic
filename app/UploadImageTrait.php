<?php

namespace App;
use Illuminate\Support\Facades\Storage;
trait UploadImageTrait
{
    public function ImageUpload($request, $user_id, $folderName)
    {
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');

            $imageName = md5_file($imageFile->getRealPath()) . '.' . $imageFile->getClientOriginalExtension();
            $path = "images/$folderName/$user_id/$imageName";

            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->putFileAs("images/$folderName/$user_id", $imageFile, $imageName);
            }

            $url = Storage::url($path);
            return $url;
        }
    }
}
