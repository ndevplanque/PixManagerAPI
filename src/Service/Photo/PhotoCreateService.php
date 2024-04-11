<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\Album;
use App\Factory\PhotoFactory;
use App\Repository\FileRepository;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use App\Utils\RequestHelper;
use Psr\Log\LoggerInterface;
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
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request, Album $album): PhotoResponse
    {
        $photo = $this->photoRepository->insert(
            $this->photoFactory->fromRequestAndAlbum($request, $album)
        );

        try {
            $uploadedFile = $this->requestHelper->getUploadedFile($request);
        } catch (Throwable $e) {
            throw new HttpException(400, 'File was not sent.');
        }

        try {
            $this->fileRepository->insert($photo, $uploadedFile);
        } catch (Throwable $e) {
            $this->photoRepository->delete($photo);

            $error = $uploadedFile->getErrorMessage();

            if (str_contains($error, 'exceeds your upload_max_filesize ini directive')) {
                $error = 'Image is too big';
            }

            throw new HttpException(500, $error);
        }

        return new PhotoResponse($photo);
    }
}
