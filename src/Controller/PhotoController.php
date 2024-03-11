<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Factory\PhotoFactory;
use App\Utils\JsonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PhotoController extends AbstractController
{
    private readonly PhotoFactory $photoFactory;
    private readonly JsonHelper $jsonHelper;

    public function __construct(PhotoFactory $photoFactory)
    {
        $this->photoFactory = $photoFactory;
        $this->jsonHelper = new JsonHelper();
    }

    #[Route('/api/albums/{id}/photos', name: 'getPhotos', methods: ['GET'])]
    public function getPhotos(
        Album               $album,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $user = $album->getOwner();

        // todo: security check -> requester should be $user

        $photos = $album->getPhotos()->getValues();
        $json = $serializer->serialize($photos, 'json', ['groups' => ['photos']]);
        return $this->jsonHelper->send($json);
    }

    #[Route('/api/albums/{id}/photos', name: "createPhoto", methods: ['POST'])]
    public function createPhoto(
        Album                  $album,
        Request                $request,
        SerializerInterface    $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface  $urlGenerator,
    ): JsonResponse
    {
        $user = $album->getOwner();

        // todo: security check -> requester should be $user

        $photo = $this->photoFactory->fromAlbumAndRequest($album, $request);

        $entityManager->persist($photo);
        $entityManager->flush();

        $json = $serializer->serialize($photo, 'json', ['groups' => 'photos']);

        // si on voulait rediriger vers une page détaillant la photo
        $location = null; // $urlGenerator->generate('detailPhoto', ['id' => $photo->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->jsonHelper->created($json, $location);
    }

    #[Route('/api/photos/{id}', name: 'deletePhoto', methods: ['DELETE'])]
    public function deletePhoto(
        Photo                  $photo,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $entityManager->remove($photo);
        $entityManager->flush();

        return $this->jsonHelper->noContent();
    }
}
