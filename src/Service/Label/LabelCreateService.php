<?php

declare(strict_types=1);

namespace App\Service\Label;

use App\Factory\LabelFactory;
use App\Repository\LabelRepository;
use App\Response\LabelResponse;
use Symfony\Component\HttpFoundation\Request;

class LabelCreateService
{
    public function __construct(
        private readonly LabelRepository $labelRepository,
        private readonly LabelFactory    $labelFactory,
    )
    {
    }

    public function handle(Request $request): LabelResponse
    {
        return new LabelResponse(
            $this->labelRepository->insert(
                $this->labelFactory->fromRequest($request)
            )
        );
    }
}
