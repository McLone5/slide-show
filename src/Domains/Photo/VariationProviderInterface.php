<?php

declare(strict_types=1);

namespace App\Domains\Photo;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

interface VariationProviderInterface
{
    /**
     * @throws FileNotFoundException
     * @throws NonExistingVariationNameException
     */
    public function getVariationFile(FieldTypes\Value $value, int $contentId, string $fieldIdentifier, string $variationName): File;
}
