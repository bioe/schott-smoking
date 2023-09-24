<?php

namespace App\Traits;

use Storage;

trait UploadTrait
{
    public function saveFile($file, $path)
    {
        $name = ucwords(str_replace(" ", "-", $file->getClientOriginalName()));
        $type = "";
        // convert to lowercase when update to database, prevent
        $lowercase_name = $name;
        if ($name) {
            $lowercase_name = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
        }

        // replace "+" / other special char with "-"
        $lowercase_name = str_replace(["+"], "-", $lowercase_name);

        $save_path = $path . "/" . $lowercase_name;

        $image_mime = ['image/png', 'image/jpeg', 'image/jpg'];
        if (in_array($file->getMimeType(), $image_mime)) {
            imgSanitize($file, 1000, 1000, 80);
            $type = IMAGE;
        } else {
            $type = VIDEO;
        }

        if (Storage::disk('public')->put($save_path, file_get_contents($file))) {
            return ['path' => $save_path, 'name' => $lowercase_name, "type" => $type];
        }
        return ['path' => "", 'name' => "", "type" => ""];
    }

    public function deleteFile($filePath)
    {
        Storage::disk('public')->delete($filePath);
    }
}
