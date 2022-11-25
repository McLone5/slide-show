<?php

declare(strict_types=1);

namespace App\Infrastructure\Photo;

use App\Domains\Photo\FileStorageInterface;
use App\Domains\Photo\ImageAnalyzerInterface;
use App\Infrastructure\FieldTypes\Photo\Value;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File;

final class ImageAnalyzer implements ImageAnalyzerInterface
{
    public function __construct(
        private readonly FileStorageInterface $fileStorage,
    ) {
    }

    public function analyzeImage(File $file): Value
    {
        $imageSize = getimagesize($file->getPathname()) ?: throw new RuntimeException('Unable to get image size');
        [$width, $height, $imageType] = $imageSize;

        return new Value(
            $this->fileStorage->getPathnameFromPath($file),
            null,
            (int)$file->getSize(),
            md5_file($file->getPathname()) ?: null,
            $width,
            $height,
            $imageType
        );
    }
}
