<?php

declare(strict_types=1);

namespace App\Infrastructure\Photo;

use App\Domains\Photo\VariationCacheStorageInterface;
use App\Domains\Photo\FieldTypes\Value;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

final class VariationCacheStorage implements VariationCacheStorageInterface
{
    public function __construct(
        private readonly string $photoVariationCachePath,
    ) {
    }

    public function getCacheFile(Value $value, int $contentId, string $fieldIdentifier, string $variationName): ?File
    {
        $filePathname = $this->getCacheFilePathname($value, $contentId, $fieldIdentifier, $variationName);
        if (file_exists($filePathname)) {
            return new File($filePathname);
        }

        return null;
    }

    public function storeCacheFile(Value $value, int $contentId, string $fieldIdentifier, string $variationName, string $content): File
    {
        $filePathname = $this->getCacheFilePathname($value, $contentId, $fieldIdentifier, $variationName);

        $fileSystem = new Filesystem();
        $fileSystem->mkdir(dirname($filePathname));

        file_put_contents($filePathname, $content);

        return new File($filePathname);
    }

    private function getCacheFilePathname(Value $value, int $contentId, string $fieldIdentifier, string $variationName): string
    {
        return sprintf(
            '%s/%s/%s/%s/%s-%s',
            $this->photoVariationCachePath,
            $contentId,
            $fieldIdentifier,
            $variationName,
            substr((string)$value->hash, 0, 5),
            basename((string)$value->pathname)
        );
    }
}
