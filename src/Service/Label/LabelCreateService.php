<?php

namespace App\Service\Label;

use App\Entity\Label;
use App\Factory\LabelFactory;
use App\Repository\LabelRepository;
use Symfony\Component\HttpFoundation\Request;

class LabelCreateService
{
    public function __construct(
       private readonly LabelRepository $labelRepository,
       private readonly LabelFactory    $labelFactory,
    )
    {
    }

    public function handle(Request $request): Label
    {
        return $this->labelRepository->insert(
            $this->labelFactory->fromRequest($request)
        );
    }
}
