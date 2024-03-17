<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\AppUserController;


#[Route('/api/albums')]
class AlbumsManagementController extends AbstractController
{
    #[Route('/{id}', methods: ['GET'])]
    public function get_one(Album $album, SerializerInterface $serializer): JsonResponse
    {
        $jsonAlbumList = $serializer->serialize($album, 'json', ['groups' => ['albums']]);
        return new JsonResponse("Albums: $jsonAlbumList", Response::HTTP_OK, [], true);
    }

    #[Route('/', name: 'album_list_all', methods: ['GET'])]
    public function get_all(AlbumRepository $albumRepository, SerializerInterface $serializer): JsonResponse
    {
        $albumList = $albumRepository->findAll();
        $jsonAlbumList = $serializer->serialize($albumList, 'json', ['groups' => ['albums']]);
        return new JsonResponse("Albums: $jsonAlbumList", Response::HTTP_OK, [], true);
    }

    #[Route('/', methods: ['POST'])]
    public function createAlbum(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $album = $serializer->deserialize($request->getContent(), Album::class, 'json');
        $requestArray = ($request->toArray());

        $id = ($requestArray["owner"]);
        $ownerEntity = $em->getRepository(AppUser::class)->find($id);
        if (!$album->getName()) {
            return new JsonResponse(['error' => 'Album name is required.'], Response::HTTP_BAD_REQUEST);
        }
        if (!$ownerEntity) {
            return new JsonResponse(['error' => 'Owner not found.'], Response::HTTP_NOT_FOUND);
        }
        $album->setOwner($ownerEntity);
        $album->setCreatedAtValue();
        $em->persist($album);
        $em->flush();
        $jsonAlbum = $serializer->serialize($album, 'json', ['groups' => 'users']);
        return new JsonResponse($jsonAlbum, Response::HTTP_CREATED, [], true);
    }

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

    #[Route('/{id}', name: 'album_delete', methods: ['DELETE'])]
    public function delete(Album $album, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($album);
        $em->flush();
        return new JsonResponse("Album Deleted", Response::HTTP_OK, [], true);
    }

}
