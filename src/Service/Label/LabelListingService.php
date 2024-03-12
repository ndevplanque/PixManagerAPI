<?php

namespace App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;
use App\Response\LabelListingResponse;
use Symfony\Component\HttpFoundation\Request;

class LabelListingService
{
    private readonly LabelRepository $labelRepository;

    public function __construct(
        LabelRepository $labelRepository,
    )
    {
        $this->labelRepository = $labelRepository;
    }

    public function handle(): LabelListingResponse
    {
        return new LabelListingResponse($this->labelRepository->findAll());
    }
}
