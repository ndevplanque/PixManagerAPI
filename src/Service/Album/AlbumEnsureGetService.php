<?php

namespace App\Service\Album;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Repository\AlbumRepository;

class AlbumEnsureGetService
{
    public function __construct(
        private readonly AlbumRepository $albumRepository,
    )
    {
    }

    public function handle(AppUser $user): Album
    {
        $album = $user->getOwnedAlbums()->first();

        if (!$album) {
            $album = $this->albumRepository->insert(
                $user->newAlbum('Unnamed')
            );
        }

        return $album;
    }
}
