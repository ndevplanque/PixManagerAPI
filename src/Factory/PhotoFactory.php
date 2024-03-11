<?php

namespace App\Factory;

use App\Entity\Album;
use App\Entity\Photo;
use App\Repository\LabelRepository;
use App\Validator\PayloadValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PhotoFactory
{
    private readonly LabelRepository $labelRepository;
    private readonly PayloadValidator $payloadValidator;

    public function __construct(LabelRepository $labelRepository)
    {
        $this->labelRepository = $labelRepository;
        $this->payloadValidator = new PayloadValidator();
    }

    public function fromAlbumAndRequest(Album $album, Request $request): Photo
    {
        $payload = $request->toArray();
        $this->validate($payload);

        $photo = $album->newPhoto();
        $photo->setName($payload['name']);

        for ($i = 0; $i < count($payload['labels']); $i++) {
            $label = $this->labelRepository->findOneBy(['name' => $payload['labels'][$i]]);
            if ($label === null) {
                $label = $this->labelRepository->insert($payload['labels'][$i]);
            }
            $photo->addLabel($label);
        }

        return $photo;
    }

    /**
     * @throws HttpException
     */
    private function validate(array $payload): void
    {
        $this->payloadValidator->hasKeys($payload, [
            'name',
            'labels',
        ]);
    }
}
