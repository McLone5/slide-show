<?php

namespace App\Infrastructure\FieldTypes\Photo;

use Ibexa\Core\FieldType\Value as BaseValue;

final class Value extends BaseValue
{
    public function __construct(
        public ?string $pathname = null,
        public ?string $alternativeText = null,
        public ?int $fileSize = null,
        public ?string $hash = null,
        public ?int $width = null,
        public ?int $height = null,
    ) {
        parent::__construct();
    }

    public function __toString()
    {
        return (string)$this->pathname;
    }
}
