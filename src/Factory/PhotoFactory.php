<?php

namespace App\Factory;

use App\Entity\Album;
use App\Entity\Photo;
use App\Repository\LabelRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PhotoFactory
{
    public function __construct(
        private readonly LabelRepository  $labelRepository,
    )
    {
    }

    public function fromRequestAndAlbum(Request $request, Album $album): Photo
    {
        $labels = json_decode(
            $request->request->get('labels')
        );

        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        //todo: get the owner from jwt instead
        $owner = $album->getOwner();

        $photo = $owner->newPhoto($file->getClientOriginalName(), $album);

        for ($i = 0; $i < count($labels); $i++) {
            $photo->addLabel(
                $this->labelRepository->findOrInsert($labels[$i])
            );
        }

        return $photo;
    }
}
