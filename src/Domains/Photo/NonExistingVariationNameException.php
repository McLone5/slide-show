<?php

declare(strict_types=1);

namespace App\Domains\Photo;

use Exception;
use Throwable;

final class NonExistingVariationNameException extends Exception
{
    public function __construct(string $variationName, ?Throwable $previous = null)
    {
        parent::__construct("Non existing variation name \"$variationName\", try to configure it in config/packages/ibexa.yaml", 0, $previous);
    }
}
