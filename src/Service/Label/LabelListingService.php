<?php

namespace App\Service\Label;

use App\Repository\LabelRepository;
use App\Response\LabelListingResponse;

class LabelListingService
{
    public function __construct(
        private readonly LabelRepository $labelRepository,
    )
    {
    }

    public function handle(): LabelListingResponse
    {
        return new LabelListingResponse($this->labelRepository->findAll());
    }
}
