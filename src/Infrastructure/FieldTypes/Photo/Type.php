<?php

namespace App\Infrastructure\FieldTypes\Photo;

use App\Domains\Photo\FileStorageInterface;
use App\Domains\Photo\ImageAnalyzerInterface;
use DomainException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\Value as BaseValue;
use LogicException;

final class Type extends FieldType
{
    public function __construct(
        private readonly ImageAnalyzerInterface $imageAnalyzer,
        private readonly FileStorageInterface $fileStorage,
    ) {
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'mlphoto';
    }

    /**
     * @param Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return $value->alternativeText ?? (string)$value->pathname;
    }

    /**
     * @return Value
     */
    public function getEmptyValue(): Value
    {
        return new Value();
    }

    /**
     * @param mixed $inputValue
     */
    protected function createValueFromInput($inputValue): Value
    {
        if (is_string($inputValue)) {
            $inputValue = $this->buildValueFromString($inputValue);
        }

        if (is_array($inputValue)) {
            $inputValue = $this->buildValueFromArray($inputValue);
        }

        if (!$inputValue instanceof Value) {
            throw new LogicException('inputValue type not yet implemented');
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @param Value $value
     * @throws InvalidArgumentException If the value does not match the expected structure.
     *
     */
    protected function checkValueStructure(BaseValue $value): void
    {
        if ($value->pathname) {
            if (!$value->hash) {
                throw new InvalidArgumentType('hash', '!null');
            }
            if (!$value->fileSize) {
                throw new InvalidArgumentType('fileSize', '!null');
            }
            if (!$value->width) {
                throw new InvalidArgumentType('width', '!null');
            }
            if (!$value->height) {
                throw new InvalidArgumentType('height', '!null');
            }
            if (!$value->imageType) {
                throw new InvalidArgumentType('imageType', '!null');
            }
        }
    }

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration): array
    {
        return [];
    }

    protected function getSortInfo(BaseValue $value): bool
    {
        return false;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return Value $value
     */
    public function fromHash($hash): Value
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return $this->createValueFromInput($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param Value $value
     *
     * @return array<string, mixed>|null
     */
    public function toHash(SPIValue $value): ?array
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return [
            'pathname' => $value->pathname,
            'alternativeText' => $value->alternativeText,
            'fileSize' => $value->fileSize,
            'hash' => $value->hash,
            'width' => $value->width,
            'height' => $value->height,
            'imageType' => $value->imageType,
        ];
    }

    /**
     * Converts a $value to a persistence value.
     *
     * @param Value $value
     *
     * @return FieldValue
     */
    public function toPersistenceValue(SPIValue $value): FieldValue
    {
        // Store original data as external (to indicate they need to be stored)
        return new FieldValue(
            [
                'data' => null,
                'externalData' => $this->toHash($value),
                'sortKey' => $this->getSortInfo($value),
            ]
        );
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * @param FieldValue $fieldValue
     *
     * @return Value
     */
    public function fromPersistenceValue(FieldValue $fieldValue): Value
    {
        if ($fieldValue->data === null) {
            return $this->getEmptyValue();
        }

        return $this->fromHash($fieldValue->data);
    }

    public function valuesEqual(SPIValue $value1, SPIValue $value2): bool
    {
        if (!$value1 instanceof Value || !$value2 instanceof Value) {
            throw new DomainException('Bad value type for either $value1 or $value2');
        }

        $hashValue1 = $this->toHash($value1);
        $hashValue2 = $this->toHash($value2);

        return $hashValue1 === $hashValue2;
    }

    private function buildValueFromString(string $inputValue): Value
    {
        if (!$inputValue) {
            return $this->getEmptyValue();
        }

        return $this->imageAnalyzer->analyzeImage($this->fileStorage->getFileFromPathname($inputValue));
    }

    /**
     * @param array<int|string, mixed> $inputValue
     */
    private function buildValueFromArray(array $inputValue): Value
    {
        $pathname = $inputValue['pathname'] ?? null;
        $alternativeText = $inputValue['alternativeText'] ?? null;
        $fileSize = $inputValue['fileSize'] ?? null;
        $hash = $inputValue['hash'] ?? null;
        $width = $inputValue['width'] ?? null;
        $height = $inputValue['height'] ?? null;
        $imageType = $inputValue['imageType'] ?? null;

        return new Value(
            $pathname ? (string)$pathname : null,
            $alternativeText ? (string)$alternativeText : null,
            $fileSize ? (int)$fileSize : null,
            $hash ? (string)$hash : null,
            $width ? (int)$width : null,
            $height ? (int)$height : null,
            $imageType ? (int)$imageType : null,
        );
    }
}
