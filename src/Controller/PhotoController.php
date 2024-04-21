<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Repository\FileRepository;
use App\Service\Album\AlbumEnsureGetService;
use App\Service\Photo\PhotoCreateService;
use App\Service\Photo\PhotoDeleteService;
use App\Service\Photo\PhotoListingByAlbumService;
use App\Service\Photo\PhotoListingByLabelService;
use App\Service\Photo\PhotoListingByUserService;
use App\Service\Photo\PhotoUpdateService;
use App\Utils\JsonHelper;
use App\Utils\RequestHelper;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class PhotoController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper     $jsonHelper,
        private readonly FileRepository $fileRepository,
        private readonly RequestHelper  $requestHelper,
    )
    {
    }
    /**
     * List the photos of the requester.
     * '/api/photos?include_shared=true' to include photos shared by others.
     * '/api/photos?search=blablabla' to sort by most accurate (compare photo name, labels names, and album name).
     */
    #[OA\Get(
        path: '/api/photos',
        description: 'List the photos of the requester',
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of photos',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Photo')
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response400'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos', name: 'listPhoto', methods: ['GET'])]
    public function listPhoto(
        Request                   $request,
        PhotoListingByUserService $photoListingByUserService,
    ): JsonResponse
    {
        $listingByUserResponse = $photoListingByUserService->handle($request);

        return $this->jsonHelper->send(
            json_encode($listingByUserResponse)
        );
    }
    #[OA\Get(
        path: '/api/photos/albums/{id}',
        description: 'List the photos of a specific album',
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of photos in an album',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Photo')
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response400'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos/albums/{id}', name: 'listPhotosByAlbumId', methods: ['GET'])]
    /**
     * List the photos for the specific album of the requester.
     * '/api/photos/albums/{id}?include_shared=true' to include photos shared by others.
     * '/api/photos/albums/{id}?search=blablabla' to sort by most accurate (compare photo name, labels names, and album name).
     */
    public function listPhotosByAlbumId(
        Request                    $request,
        Album                      $album,
        PhotoListingByAlbumService $photoListingByAlbumService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBe($album->getOwner());

        $listingByAlbumResponse = $photoListingByAlbumService->handle($request, $album);

        return $this->jsonHelper->send(
            json_encode($listingByAlbumResponse->jsonSerialize())
        );
    }
    /**
     * This request return a list the photos associated with a specific label.
     */
    #[OA\Get(
        path: '/api/photos/labels/{name}',
        description: 'List the photos associated with a specific label',
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of photos for a specific label',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Photo')
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response400'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos/labels/{name}', name: 'listPhotosByLabelName', methods: ['GET'])]
    public function listPhotosByLabelName(
        Request                    $request,
        string                     $name,
        PhotoListingByLabelService $photoListingByLabelService,
    ): JsonResponse
    {
        $user = $this->requestHelper->getUser($request);

        $listingByLabelResponse = $photoListingByLabelService->handle($user, $name);

        return $this->jsonHelper->send(
            json_encode($listingByLabelResponse->jsonSerialize())
        );
    }
    /**
     * This request downloads the file associated with a photo.
     */
    #[OA\Get(
        path: '/api/photos/file/{id}',
        description: 'Download the file associated with a photo',
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the photo file',
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized access',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedResponse'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos/file/{id}', name: 'getPhotoFile', methods: ['GET'])]
    public function getPhotoFile(
        Request $request,
        Photo   $photo,
    ): BinaryFileResponse
    {
        $this->requestHelper->getUser($request)->shouldHaveAccessToPhoto($photo);

        return new BinaryFileResponse(
            file: $this->fileRepository->getStoragePath($photo),
            headers: ['Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $photo->getName(),
            )]
        );
    }

    /**
     * This request creates a new photo.
     */
    #[OA\Post(
        path: '/api/photos',
        description: 'Create a new photo',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    ref: '#/components/schemas/Photo'
                )
            )
        ),
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Photo created successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Photo'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response400'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos', name: "createPhoto", methods: ['POST'])]
    public function createPhoto(
        Request               $request,
        AlbumEnsureGetService $albumEnsureGetService,
        PhotoCreateService    $photoCreateService,
    ): JsonResponse
    {
        $user = $this->requestHelper->getUser($request);

        $album = $albumEnsureGetService->handle($user);

        $photoResponse = $photoCreateService->handle($request, $album);

        return $this->jsonHelper->created(
            json_encode($photoResponse->jsonSerialize())
        );
    }

    /**
     * This request creates a photo in a specific album.
     */
    #[OA\Post(
        path: '/api/photos/albums/{id}',
        description: 'Create a photo in a specific album',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    ref: '#/components/schemas/Photo'
                )
            )
        ),
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Photo created successfully in an album',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Photo'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized access',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedResponse'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos/albums/{id}', name: "createPhotoInAlbum", methods: ['POST'])]
    public function createPhotoInAlbum(
        Request            $request,
        Album              $album,
        PhotoCreateService $photoCreateService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldHaveAccessToAlbum($album);

        $photoResponse = $photoCreateService->handle($request, $album);

        return $this->jsonHelper->created(
            json_encode($photoResponse->jsonSerialize())
        );
    }

    /**
     * This request updates an existing photo.
     */
    #[OA\Put(
        path: '/api/photos/{id}',
        description: 'Update an existing photo',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    ref: '#/components/schemas/Photo'
                )
            )
        ),
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Photo updated successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Photo'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized access',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedResponse'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos/{id}', name: "updatePhoto", methods: ['PUT'])]
    public function updatePhoto(
        Request            $request,
        Photo              $photo,
        PhotoUpdateService $photoUpdateService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBe($photo->getOwner());

        $photoResponse = $photoUpdateService->handle($request, $photo);

        return $this->jsonHelper->created(
            json_encode($photoResponse->jsonSerialize())
        );
    }

    /**
     * This request deletes a specific photo.
     */
    #[OA\Delete(
        path: '/api/photos/{id}',
        description: 'Delete a specific photo',
        tags: ['Photos'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Photo deleted successfully',
                content: new OA\JsonContent(
                    type: 'string',
                    example: ''
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response400'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    #[Route('/api/photos/{id}', name: 'deletePhoto', methods: ['DELETE'])]
    public function deletePhoto(
        Request            $request,
        Photo              $photo,
        PhotoDeleteService $photoDeleteService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBeOneOf([
            $photo->getOwner(),
            $photo->getAlbum()->getOwner(),
        ]);

        $photoDeleteService->handle($photo);

        return $this->jsonHelper->noContent();
    }
}
