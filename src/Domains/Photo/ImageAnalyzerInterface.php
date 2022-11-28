<?php

namespace App\Domains\Photo;

use Symfony\Component\HttpFoundation\File\File;

interface ImageAnalyzerInterface
{
    public function analyzeImage(File $file): FieldTypes\Value;
}
