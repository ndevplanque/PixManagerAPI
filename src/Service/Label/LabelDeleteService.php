<?php

namespace App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;
use Exception;

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
    public function handle(Label $label): void
    {
        $this->labelRepository->delete($label);
    }
}
