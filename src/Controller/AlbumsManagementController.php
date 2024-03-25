<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Exception\DocError;
use App\Exception\NotFoundException;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/albums')]
class AlbumsManagementController extends AbstractController
{
    /**
     * This request return album depending on the id.
     */
    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the Album by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Album::class, groups: ['albums']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response404")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    public function get_one(Album $album, SerializerInterface $serializer): JsonResponse
    {
        $jsonAlbumList = $serializer->serialize($album, 'json', ['groups' => ['albums']]);
        return new JsonResponse("Albums: $jsonAlbumList", Response::HTTP_OK, [], true);
    }
    /**
     * This request returns albums owned by a specific user.
     */
    #[Route('/users/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the albums owned by the user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Album::class, groups: ['albums']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response404")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    public function getAlbumsByUser(int $id, AlbumRepository $albumRepository, SerializerInterface $serializer): JsonResponse
    {
        // Fetch albums by user ID
        $albums = $albumRepository->findBy(['owner' => $id]);
        if (empty($albums)) {
            throw new NotFoundHttpException("No albums found for user with ID $id");
        }
        // Serialize the albums
        $jsonAlbumList = $serializer->serialize($albums, 'json', ['groups' => ['albums']]);

        return new JsonResponse("Albums: $jsonAlbumList", Response::HTTP_OK, [], true);
    }
    /**
     * This request return all the albums.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the Album by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Album::class, groups: ['albums']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response404")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    #[Route('', methods: ['GET'])]
    public function get_all(AlbumRepository $albumRepository, SerializerInterface $serializer): JsonResponse
    {
        $albumList = $albumRepository->findAll();
        if (empty($albumList)) {
            throw new NotFoundHttpException("No albums found");
        }
        $jsonAlbumList = $serializer->serialize($albumList, 'json', ['groups' => ['albums']]);
        return new JsonResponse("Albums: $jsonAlbumList", Response::HTTP_OK, [], true);
    }

    /**
     * This request returns albums based on the provided name.
     */
    #[Route('/albums', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the albums matching the provided name',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Album::class, groups: ['albums']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    public function getAlbumsByName(Request $request, AlbumRepository $albumRepository, SerializerInterface $serializer): JsonResponse
    {
            // Get the album name from query parameters
            $name = $request->query->get('name');
            if (!$name) {
                throw new BadRequestHttpException('Album name parameter is missing');
            }
            $albums = $albumRepository->findBy(['name' => $name]);
            if (empty($albums)) {
                throw new NotFoundHttpException("No albums found with name: $name");
            }

            $jsonAlbumList = $serializer->serialize($albums, 'json', ['groups' => ['albums']]);

            return new JsonResponse("Albums: $jsonAlbumList", Response::HTTP_OK, [], true);

    }
    /**
     * This request creates a new album.
     */

    #[OA\RequestBody(
        request: "AlbumData",
        description: "Data for creating the album",
        required: true,
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "owner", type: "integer")
                ],
                type: "object"
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the Album by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Album::class, groups: ['albums']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response404")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    #[Route('', methods: ['POST'])]
    public function createAlbum(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $album = $serializer->deserialize($request->getContent(), Album::class, 'json');
        $requestArray = ($request->toArray());
        if (!array_key_exists("owner", $requestArray)) {
            throw new BadRequestException('Owner required', 400);
        }
        $id = ($requestArray["owner"]);
        $ownerEntity = $em->getRepository(AppUser::class)->find($id);
        if (!$album->getName()) {
            throw new NotFoundException("album");
        }
        if (!$ownerEntity) {
            throw new BadRequestException('Owner not found', 400);
        }
        $album->setOwner($ownerEntity);
        $album->setCreatedAtValue();
        $em->persist($album);
        $em->flush();
        $jsonAlbum = $serializer->serialize($album, 'json', ['groups' => 'users']);
        return new JsonResponse($jsonAlbum, Response::HTTP_CREATED, [], true);
    }

    /**
     * This request updates an album.
     */
    #[OA\RequestBody(
        request: "AlbumData",
        description: "Data for creating the album",
        required: true,
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "owner", type: "integer")
                ],
                type: "object"
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Updated the Album by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Album::class, groups: ['albums']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response404")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Album $currentAlbum, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $album = $serializer->deserialize($request->getContent(), Album::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAlbum]);
        $requestArray = ($request->toArray());
        $name = $requestArray["name"];
        $album->setName($name);
        $em->persist($album);
        $em->flush();
        $jsonAlbum = $serializer->serialize($album, 'json', ['groups' => 'users']);
        return new JsonResponse($jsonAlbum, Response::HTTP_CREATED, [], true);
    }

    /**
     * This request updates visibility of an album.
     */
    #[OA\RequestBody(
        request: "AlbumData",
        description: "Data for creating the album",
        required: true,
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "newUserId", type: "integer"),
                    new OA\Property(property: "deleteUserId", type: "integer")
                ],
                type: "object"
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Updated the Album by id',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Album::class, groups: ['albums']))
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response404")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    #[Route('/share/{id}', methods: ['PUT'])]
    public function updateVisibility(Request $request, Album $currentAlbum, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $album = $serializer->deserialize($request->getContent(), Album::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAlbum]);
        $requestArray = ($request->toArray());
        if (array_key_exists("newUserId", $requestArray)) {
            $userId = $requestArray["newUserId"];
            $userEntity = $em->getRepository(AppUser::class)->find($userId);
            $album->addSharedTo($userEntity);
            $em->persist($album);
            $em->flush();
        }
        if (array_key_exists("deleteUserId", $requestArray)) {
            $deleteUser = $requestArray["deleteUserId"];
            $userEntity = $em->getRepository(AppUser::class)->find($deleteUser);
            $album->getSharedTo()->removeElement($userEntity);
            $em->flush();
        }
        $jsonAlbum = $serializer->serialize($album, 'json', ['groups' => 'shared']);
        return new JsonResponse($jsonAlbum, Response::HTTP_CREATED, [], true);
    }

    /**
     * This request deletes an album.
     */
    #[OA\RequestBody(
        request: "AlbumData",
        description: "Data with ids to delete",
        required: true,
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "idsToDelete", type: "array", items: new OA\Items(type: "integer")),
                ],
                type: "object"
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Album successfully deleted',
        content: new OA\JsonContent(
            type: 'string', example: 'Album was deleted'

        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response400")
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response404")
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\MediaType(
            mediaType: "application/json",
            schema: new OA\Schema(ref: "#/components/schemas/Response500")
        )
    )]
    #[OA\Tag(name: 'Albums')]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'album_delete', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $idsToDelete = $content['idsToDelete'];
        var_dump($idsToDelete);
        if (!is_array($idsToDelete)) {
            throw new BadRequestException("Invalid input format, must be an array of ids", 400);
        }
        foreach ($idsToDelete as $id) {
            $album = $em->getRepository(Album::class)->find($id);
            $em->remove($album);
        }
        $em->flush();
        return new JsonResponse("Album Deleted", Response::HTTP_OK, [], true);
    }

}
