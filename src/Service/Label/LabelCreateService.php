<?php

namespace App\Service\Label;

use App\Entity\Label;
use App\Factory\LabelFactory;
use App\Repository\LabelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LabelCreateService
{
    private readonly LabelRepository $labelRepository;
    private readonly LabelFactory $labelFactory;

    public function __construct(
        LabelRepository $labelRepository,
        LabelFactory    $labelFactory,
    )
    {
        $this->labelRepository = $labelRepository;
        $this->labelFactory = $labelFactory;
    }

    public function handle(Request $request): Label
    {
        return $this->labelRepository->insert(
            $this->labelFactory->fromRequest($request)
        );
    }
}