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

if (!function_exists('getHoursMinutes')) {
    function getHoursMinutes($seconds, $short = true)
    {
        if (0 == $seconds) {
            return "0";
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        $result = '';
        if ($hours > 0) {
            $result .= $hours . ' hours ';
            if ($short) {
                return $result;
            }
        }
        if ($minutes > 0) {
            $result .= $minutes . ' minutes ';
            if ($short) {
                return $result;
            }
        }
        if ($seconds > 0) {
            $result .= $seconds . ' seconds';
            if ($short) {
                return $result;
            }
        }

        return $result;
    }
}

if (!function_exists('frontHexToNumber')) {
    //Groove CardRead
    function frontHexToNumber($hex)
    {
        /*Eg. Full Card 0009527380 145 24660
         * Incoming
         * 0C00916054A9 - Correct
         * 90C00916054A - Wrong
         */
        if (strlen($hex) != 12) {
            //invalid card_id
            return null;
        }

        // $second_char = substr($hex, 1, 1);
        // if (!preg_match("/[a-z]/i", $second_char)) {
        //     //Incoming is wrong
        //     //Second character not alphabet re-arrage first character to last character
        //     //Sometime Arduino will mess up the character position
        //     $firstChar = substr($hex, 0, 1);
        //     $substring = substr($hex, 1);
        //     $hex = $substring . $firstChar;
        // }

        //UART Protocal
        //always start from index 2, get 8 characters
        $front_hex = substr($hex, 2, strlen($hex) - 4);
        //convert from hex to decimal
        $front_number = hexdec($front_hex); //Front number
        $behind_hex = dechex($front_number); //Convert to hex

        $three = hexdec(substr($behind_hex, 0, 2)); //Split first 2 hex and convert to decimal
        if (strlen($three) == 1) {
            $three = "00" . $three;
        }
        //if only 1 char add two zero
        if (strlen($three) == 2) {
            $three = "0" . $three;
        }
        //if only 2 char add one zero
        $four = hexdec(substr($behind_hex, 2, 4)); //Split last 4 hex and convert to decimal

        //Add 0 between the space
        //Last 8 digit (0)145(0)24660
        $real_card_id = "0" . $three . "0" . $four;
        return $real_card_id;
    }
}

if (!function_exists('behindHexToNumber')) {
    //From Shopee RFID RDM6300
    function behindHexToNumber($hex, $shift)
    {
        /*Eg.
         * Incoming
         * 0006413739 097 56747 = 0B0061DDAB1C - Correct
         * 0006423272 098 00744 = 0B006202E838 - Correct
         */
        if (strlen($hex) != 12) {
            //invalid card_id
            return null;
        }

        // $second_char = substr($hex, 1, 1);
        // if (!preg_match("/[a-z]/i", $second_char)) {
        //     //Second character not alphabet re-arrage first character to last character
        //     //Sometime Arduino will mess up the character position
        //     $firstChar = substr($hex, 0, 1);
        //     $substring = substr($hex, 1);
        //     $hex = $substring . $firstChar;
        // }

        //Re-arrange front char to back base on number of shift.
        //Arduino will sometime return wrong hex, with wrong char position
        if ($shift > 0) {
            $frontChars = substr($hex, 0, $shift);
            $substring = substr($hex, $shift);
            $hex = $substring . $frontChars;
        }


        //UART Protocal
        //always start from index 4, get 8 characters
        $behind_hex = substr($hex, 4, strlen($hex) - 4);
        $three = hexdec(substr($behind_hex, 0, 2)); //Split first 2 hex and convert to decimal

        //if not three char append extra zero
        if (strlen($three) != 3) {
            for ($i = 0; $i <= (3 - strlen($three)); $i++) {
                $three = "0" . $three;
            }
        }

        $five = hexdec(substr($behind_hex, 2, 4)); //Split last 4 hex and convert to decimal
        //if not five char append extra zero
        if (strlen($five) != 5) {
            for ($i = 0; $i <= (5 - strlen($five)); $i++) {
                $five = "0" . $five;
            }
        }

        //Add 0 between the space
        //Last 8 digit (0)145(0)24660
        $real_card_id = "0" . $three . "0" . $five;
        return $real_card_id;
    }
}
