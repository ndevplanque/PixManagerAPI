<?php

namespace App\Service\Label;

use App\Repository\LabelRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LabelDeleteService
{
    public function __construct(
        private readonly LabelRepository $labelRepository,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request): void
    {
        $payload = $request->toArray();

        $label = $this->labelRepository->findOneBy(['name' => $payload['name']]);

        if ($label === null) {
            throw new HttpException(404, "Label {$payload['name']} not found!");
        }

        $this->labelRepository->delete($label);
    }
}
