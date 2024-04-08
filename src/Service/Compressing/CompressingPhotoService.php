<?php

namespace App\Service\Compressing;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CompressingPhotoService
{
    /**
     * Compresse une photo.
     *
     * Cette fonction lit une photo à partir d'un chemin donné, la compresse
     * et l'enregistre dans un nouveau chemin avec une qualité de 20%.
     *
     * @param string $pathToPhoto Le chemin vers la photo à compresser.
     * @param string $pathToSave Le chemin où la photo compressée sera enregistrée.
     * @return void
     */
    public function compressingPhoto($pathToPhoto, $pathToSave): void
    {
        $manager = new ImageManager(Driver::class);

        $photo = $manager->read($pathToPhoto);

        $photo->save($pathToSave,20);
    }
}