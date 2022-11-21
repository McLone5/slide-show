<?php

namespace App\Domains\Photo;

use App\Infrastructure\FieldTypes\Photo\Value;
use Symfony\Component\HttpFoundation\File\File;

interface ImageAnalyzerInterface
{
    public function analyzeImage(File $file): Value;
}
