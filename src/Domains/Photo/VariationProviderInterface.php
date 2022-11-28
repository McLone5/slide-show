<?php

declare(strict_types=1);

namespace App\Domains\Photo;

use App\Infrastructure\FieldTypes\Photo\Value;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

interface VariationProviderInterface
{
    /**
     * @throws FileNotFoundException
     * @throws NonExistingVariationNameException
     */
    public function getVariationFile(Value $value, int $contentId, string $fieldIdentifier, string $variationName): File;
}
