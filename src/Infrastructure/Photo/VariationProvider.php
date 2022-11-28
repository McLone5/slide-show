<?php

namespace App\Infrastructure\Photo;

use App\Domains\Photo\FileStorageInterface;
use App\Domains\Photo\NonExistingVariationNameException;
use App\Domains\Photo\VariationCacheStorageInterface;
use App\Domains\Photo\VariationProviderInterface;
use App\Infrastructure\FieldTypes\Photo\Value;
use Ibexa\Bundle\Core\Imagine\IORepositoryResolver;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\HttpFoundation\File\File;

final class VariationProvider implements VariationProviderInterface
{
    public function __construct(
        private readonly FileStorageInterface $fileStorage,
        private readonly FilterManager $filterManager,
        private readonly VariationCacheStorageInterface $variationCacheStorage,
    ) {
    }

    public function getVariationFile(Value $value, int $contentId, string $fieldIdentifier, string $variationName): File
    {
        $cacheFile = $this->variationCacheStorage->getCacheFile($value, $contentId, $fieldIdentifier, $variationName);
        if ($cacheFile) {
            return $cacheFile;
        }

        $file = $this->fileStorage->getFileFromPathname((string)$value->pathname);
        if ($variationName === IORepositoryResolver::VARIATION_ORIGINAL) {
            return $file;
        }
        try {
            $binaryFile = $this->filterManager->applyFilter(
                $this->getBinaryFile($file, $value),
                $variationName,
                ['quality' => 66]
            );
        } catch (NonExistingFilterException $e) {
            throw new NonExistingVariationNameException($variationName, $e);
        }

        return $this->variationCacheStorage->storeCacheFile($value, $contentId, $fieldIdentifier, $variationName, (string)$binaryFile->getContent());
    }

    private function getBinaryFile(File $file, Value $value): BinaryInterface
    {
        return new Binary(
            $file->getContent(),
            image_type_to_mime_type((int)$value->imageType),
            $file->getExtension(),
        );
    }
}
