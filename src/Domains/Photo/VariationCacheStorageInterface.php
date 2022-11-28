<?php

namespace App\Domains\Photo;

use Symfony\Component\HttpFoundation\File\File;

interface VariationCacheStorageInterface
{
    public function getCacheFile(FieldTypes\Value $value, int $contentId, string $fieldIdentifier, string $variationName): ?File;

    public function storeCacheFile(FieldTypes\Value $value, int $contentId, string $fieldIdentifier, string $variationName, string $content): File;
}
