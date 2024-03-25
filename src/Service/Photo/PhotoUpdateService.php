<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\Photo;
use App\Repository\AlbumRepository;
use App\Repository\LabelRepository;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PhotoUpdateService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly AlbumRepository $albumRepository,
        private readonly LabelRepository $labelRepository,
    )
    {
    }

    public function handle(Request $request, Photo $photo): PhotoResponse
    {
        $payload = $request->toArray();

        if (array_key_exists('name', $payload)) {
            $photo->setName($payload['name']);
        }

        if (array_key_exists('addLabels', $payload)) {
            for ($i = 0; $i < count($payload['addLabels']); $i++) {
                $photo->addLabel(
                    $this->labelRepository->findOrInsert($payload['addLabels'][$i])
                );
            }
        }

        if (array_key_exists('removeLabels', $payload)) {
            $photo->removeLabelsByName($payload['removeLabels']);
        }

        if (array_key_exists('albumId', $payload)) {
            $album = $this->albumRepository->find((int)$payload['albumId']);

            if ($album === null) {
                throw new HttpException(404, "Album #{$payload['albumId']} not found!");
            }

            if ($album->getOwner() !== $photo->getAlbum()->getOwner()) {
                throw new HttpException(403, "Album #{$payload['albumId']} belongs to someone else!");
            }

            $photo->setAlbum($album);
        }

        return new PhotoResponse($this->photoRepository->update($photo));
    }
}
