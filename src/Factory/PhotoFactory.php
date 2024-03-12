<?php

namespace App\Factory;

use App\Entity\Album;
use App\Entity\Photo;
use App\Repository\LabelRepository;
use App\Validator\PayloadValidator;
use Symfony\Component\HttpFoundation\Request;

class PhotoFactory
{
    public function __construct(
        private readonly LabelRepository  $labelRepository,
        private readonly PayloadValidator $payloadValidator,
    )
    {
    }

    public function fromRequestAndAlbum(Request $request, Album $album): Photo
    {
        $payload = $request->toArray();

        $this->payloadValidator->hasKeys($payload, [
            'name',
            'labels',
        ]);

        $photo = $album->newPhoto($payload['name']);

        for ($i = 0; $i < count($payload['labels']); $i++) {
            $photo->addLabel(
                $this->labelRepository->findOrInsert($payload['labels'][$i])
            );
        }

        return $photo;
    }
}
