<?php

namespace App\Domains\Photo\FieldTypes;

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
        public ?int $imageType = null,
    ) {
        parent::__construct();
    }

    public function __toString()
    {
        return (string)$this->pathname;
    }

    public function getBaseFileName(): string
    {
        return basename((string)$this->pathname);
    }
}
