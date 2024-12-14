<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-27 23:42:53
 */

namespace App\Traits;

use Illuminate\Support\Str;

trait MultimediaTrait
{
    private const IMAGE_PATH_TMP = '/images/tmp/';
    private const IMAGE_PATH_FULL = '/images/full/';
    private const IMAGE_PATH_SMALL = '/images/small/';

    private function saveImage(string $token): string
    {
        $imageTmp = public_path(self::IMAGE_PATH_TMP.$token.'.jpg');
        $filename = Str::uuid()->toString().'.jpg';

        $image = $this->imageManager->read($imageTmp);
        $image->save(public_path(self::IMAGE_PATH_FULL.$filename));
        $image->resize(150, 150);
        $image->save(public_path(self::IMAGE_PATH_SMALL.$filename));
        @unlink($imageTmp);

        return $filename;
    }

    private function saveImageTmp(string $path, string $random): string
    {
        $filename = $random.'.jpg';

        $image = $this->imageManager->read($path);
        $image->save(public_path(self::IMAGE_PATH_TMP).$filename, quality: 70);

        return url(self::IMAGE_PATH_TMP.$filename);
    }

    protected function deleteImage(string $photo): void
    {
        @unlink(public_path(self::IMAGE_PATH_FULL.$photo));
        @unlink(public_path(self::IMAGE_PATH_SMALL.$photo));
    }
}
