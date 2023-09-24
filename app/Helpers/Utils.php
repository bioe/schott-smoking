<?php

if (!function_exists('date_extract_format')) {
    function date_extract_format($d, $null = '')
    {
        // check Day -> (0[1-9]|[1-2][0-9]|3[0-1])
        // check Month -> (0[1-9]|1[0-2])
        // check Year -> [0-9]{4} or \d{4}
        $patterns = array(
            '/\b\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3,8}Z\b/' => 'Y-m-d\TH:i:s.u\Z', // format DATE ISO 8601
            '/\b\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\b/' => 'Y-m-d',
            '/\b\d{4}-(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])\b/' => 'Y-d-m',
            '/\b(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-\d{4}\b/' => 'd-m-Y',
            '/\b(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-\d{4}\b/' => 'm-d-Y',

            '/\b\d{4}\/(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\b/' => 'Y/d/m',
            '/\b\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\b/' => 'Y/m/d',
            '/\b(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}\b/' => 'd/m/Y',
            '/\b(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/\d{4}\b/' => 'm/d/Y',

            '/\b\d{4}\.(0[1-9]|1[0-2])\.(0[1-9]|[1-2][0-9]|3[0-1])\b/' => 'Y.m.d',
            '/\b\d{4}\.(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\b/' => 'Y.d.m',
            '/\b(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.\d{4}\b/' => 'd.m.Y',
            '/\b(0[1-9]|1[0-2])\.(0[1-9]|[1-2][0-9]|3[0-1])\.\d{4}\b/' => 'm.d.Y',

            // for 24-hour | hours seconds
            '/\b(?:2[0-3]|[01][0-9]):[0-5][0-9](:[0-5][0-9])\.\d{3,6}\b/' => 'H:i:s.u',
            '/\b(?:2[0-3]|[01][0-9]):[0-5][0-9](:[0-5][0-9])\b/' => 'H:i:s',
            '/\b(?:2[0-3]|[01][0-9]):[0-5][0-9]\b/' => 'H:i',

            // for 12-hour | hours seconds
            '/\b(?:1[012]|0[0-9]):[0-5][0-9](:[0-5][0-9])\.\d{3,6}\b/' => 'h:i:s.u',
            '/\b(?:1[012]|0[0-9]):[0-5][0-9](:[0-5][0-9])\b/' => 'h:i:s',
            '/\b(?:1[012]|0[0-9]):[0-5][0-9]\b/' => 'h:i',

            '/\.\d{3}\b/' => '.v',
        );
        //$d = preg_replace('/\b\d{2}:\d{2}\b/', 'H:i',$d);
        $d = preg_replace(array_keys($patterns), array_values($patterns), $d);

        return preg_match('/\d/', $d) ? $null : $d;
    }
}


if (!function_exists('imgSanitize')) {
    function imgSanitize($file, $thumbwidth = 2000, $thumbheight = 2000, $quality = 90)
    {
        $extension = $file->extension();
        $path = $file->path();
        // Load image and get image size.
        $img = @imagecreatefromjpeg($path);
        if (!$img) {
            $img = @imagecreatefrompng($path);
        }
        if (!$img) {
            // unable to recognize image
            throw new \Exception("Error Processing Image", 500);
            return;
        }

        $width = imagesx($img);
        $height = imagesy($img);
        if ($width <= $thumbwidth && $height <= $thumbheight) {
            // no need to resize
            imagedestroy($img);
            unset($img);
            return;
        }
        if ($width > $height) {
            $newwidth = $thumbwidth;
            $divisor = $width / $thumbwidth;
            $newheight = floor($height / $divisor);
        } else {
            $newheight = $thumbheight;
            $divisor = $height / $thumbheight;
            $newwidth = floor($width / $divisor);
        }
        // Create a new temporary image.
        $tmpimg = imagecreatetruecolor($newwidth, $newheight);
        // Copy and resize old image into new image.
        imagecopyresampled($tmpimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        //Rotate image from phone orientation
        if (function_exists('exif_read_data')) {
            if (in_array($extension, ["jpg", "jpeg"]) && exif_imagetype($path) === IMAGETYPE_JPEG) {
                $exif = @exif_read_data($path);
                if ($exif && isset($exif['Orientation'])) {
                    $orientation = $exif['Orientation'];
                    if (1 != $orientation) {
                        $deg = 0;
                        switch ($orientation) {
                            case 3:
                                $deg = 180;
                                break;
                            case 6:
                                $deg = 270;
                                break;
                            case 8:
                                $deg = 90;
                                break;
                        }
                        if ($deg) {
                            $tmpimg = imagerotate($tmpimg, $deg, 0);
                        }
                    }
                }
            }
        }

        // Save thumbnail into a file.
        imagejpeg($tmpimg, $path, $quality);
        imagedestroy($tmpimg);
        unset($tmpimg);
    }
}
