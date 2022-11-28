<?php

namespace App\Domains\Photo;

use App\Infrastructure\FieldTypes\Photo\Value;
use Symfony\Component\HttpFoundation\File\File;

interface VariationCacheStorageInterface
{
    public function getCacheFile(Value $value, int $contentId, string $fieldIdentifier, string $variationName): ?File;

    public function storeCacheFile(Value $value, int $contentId, string $fieldIdentifier, string $variationName, string $content): File;
}
