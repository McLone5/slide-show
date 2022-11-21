<?php

declare(strict_types=1);

namespace App\Infrastructure\Photo;

use App\Domains\Photo\FileStorageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

final class FileStorage implements FileStorageInterface
{
    public function __construct(
        private readonly string $photoFolderPath,
    ) {
    }

    public function getFileFromPathname(string $pathname): File
    {
        return new File($this->photoFolderPath . '/' . $pathname);
    }

    public function getPathnameFromPath(File $file): string
    {
        // rtrim fix a long-running issue, which might be fixed by https://github.com/symfony/symfony/pull/47424
        return rtrim(
            (new Filesystem())->makePathRelative($file->getPathname(), $this->photoFolderPath),
            '/'
        );
    }
}
