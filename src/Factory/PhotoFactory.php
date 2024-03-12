<?php

namespace App\Factory;

use App\Entity\Album;
use App\Entity\Label;
use App\Entity\Photo;
use App\Repository\LabelRepository;
use App\Validator\PayloadValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PhotoFactory
{
    private readonly LabelRepository $labelRepository;
    private readonly PayloadValidator $payloadValidator;

    public function __construct(
        LabelRepository  $labelRepository,
        PayloadValidator $payloadValidator,
    )
    {
        $this->labelRepository = $labelRepository;
        $this->payloadValidator = $payloadValidator;
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
            $label = $this->labelRepository->findOneBy(['name' => $payload['labels'][$i]]);
            if ($label !== null) {
                $photo->addLabel($label);
            } else {
                $created = $this->labelRepository->insert(new Label($payload['labels'][$i]));
                $photo->addLabel($created);
            }
        }

        return $photo;
    }
}
