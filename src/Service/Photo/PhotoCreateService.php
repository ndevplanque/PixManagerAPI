<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\Album;
use App\Factory\PhotoFactory;
use App\Repository\FileRepository;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use App\Utils\RequestHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class PhotoCreateService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly PhotoFactory    $photoFactory,
        private readonly FileRepository  $fileRepository,
        private readonly RequestHelper   $requestHelper,
    )
    {
    }

    public function handle(Request $request, Album $album): PhotoResponse
    {
        $photo = $this->photoRepository->insert(
            $this->photoFactory->fromRequestAndAlbum($request, $album)
        );

        try {
            $this->fileRepository->insert(
                $photo,
                $this->requestHelper->getUploadedFile($request)
            );
        } catch (Throwable $e) {
            $this->photoRepository->delete($photo);
            throw new HttpException(500, 'Failed to save photo! Please try again.');
        }

        return new PhotoResponse($photo);
    }
}
