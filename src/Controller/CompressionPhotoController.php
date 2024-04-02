<?php

namespace App\Controller;

use App\Service\Compressing\CompressingPhotoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class CompressionPhotoController extends AbstractController
{
    public function __construct(
        private readonly CompressingPhotoService $compressingPhotoService
    ){
    }

    #[Route('/compression/photo', name: 'app_compression_photo')]
    public function compressionPhoto(
        Request $request
    ): Response
    {
        $uploadedImage = $request->files->get('image');

        if (!$uploadedImage) {
            return new Response('No image uploaded', 400);
        }

        $imageName = $uploadedImage->getClientOriginalName();

        $relativePath = '../public/photos/compresser/' . $imageName;
        $pathToSave = dirname(__DIR__) . DIRECTORY_SEPARATOR . $relativePath;

        $pathToPhoto = $uploadedImage;

        $this->compressingPhotoService->compressingPhoto($pathToPhoto, $pathToSave);

        return new Response('Photo compressed successfully');
    }
}
