<?php

namespace App\Service\Compressing;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CompressingPhotoService
{
    public function compressingPhoto($pathToPhoto, $pathToSave): void
    {
        $manager = new ImageManager(Driver::class);

        if (!file_exists($pathToSave)) {
            touch($pathToSave, 777, true);
        }

        $photo = $manager->read($pathToPhoto);

        $photo->save($pathToSave,20);
    }
}