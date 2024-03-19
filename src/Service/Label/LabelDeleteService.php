<?php

declare(strict_types=1);

namespace App\Service\Label;

use App\Repository\LabelRepository;
use Exception;
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
    public function handle(string $labelName): void
    {
        $label = $this->labelRepository->findOneBy(['name' => $labelName]);

        if ($label === null) {
            throw new HttpException(404, "Label $labelName not found!");
        }

        $this->labelRepository->delete($label);
    }
}
