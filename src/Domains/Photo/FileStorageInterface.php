<?php

namespace App\Domains\Photo;

use Symfony\Component\HttpFoundation\File\File;

interface FileStorageInterface
{
    public function getFileFromPathname(string $pathname): File;
    public function getPathnameFromPath(File $file): string;
}
