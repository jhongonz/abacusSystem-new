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
        $imageTmp = public_path(sprintf('%s%s.jpg', self::IMAGE_PATH_TMP, $token));
        $filename = sprintf('%s.jpg', Str::uuid()->toString());

        $image = $this->imageManager->read($imageTmp);
        $image->save(public_path(sprintf('%s%s', self::IMAGE_PATH_FULL, $filename)));
        $image->resize(150, 150);
        $image->save(public_path(sprintf('%s%s', self::IMAGE_PATH_SMALL, $filename)));
        @unlink($imageTmp);

        return $filename;
    }

    private function saveImageTmp(string $path, string $random): string
    {
        $filename = $random.'.jpg';

        $image = $this->imageManager->read($path);
        $image->save(sprintf('%s%s', public_path(self::IMAGE_PATH_TMP), $filename));

        return url(self::IMAGE_PATH_TMP.$filename);
    }
}
