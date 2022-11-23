<?php

namespace App\Domains\Photo;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

interface FileStorageInterface
{
    /**
     * @throws FileNotFoundException
     */
    public function getFileFromPathname(string $pathname): File;
    public function getPathnameFromPath(File $file): string;
}
