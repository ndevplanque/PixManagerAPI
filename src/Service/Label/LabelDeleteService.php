<?php

namespace App\Service\Label;

use App\Repository\LabelRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class LabelDeleteService
{
    private readonly LabelRepository $labelRepository;

    public function __construct(LabelRepository $labelRepository)
    {
        $this->labelRepository = $labelRepository;
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request): void
    {
        $payload = $request->toArray();

        $this->labelRepository->delete(
            $this->labelRepository->findOneBy(['name' => $payload['name']])
        );
    }
}
