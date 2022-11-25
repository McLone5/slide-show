<?php

namespace App\Infrastructure\FieldTypes\Photo;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use JsonException;

final class PhotoConverter implements Converter
{
    /**
     * Converts data from $value to $storageFieldValue.
     *
     * @param FieldValue        $value
     * @param StorageFieldValue $storageFieldValue
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue): void
    {
        $data = $value->data ?? $value->externalData;
        if (!$data) {
            $storageFieldValue->dataText = '';
            return;
        }

        $storageFieldValue->dataText = json_encode([
            'pathname' => $data['pathname'] ?? null,
            'alternativeText' => $data['alternativeText'] ?? null,
            'fileSize' => $data['fileSize'] ?? null,
            'hash' => $data['hash'] ?? null,
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'imageType' => $data['imageType'] ?? null,
        ], JSON_THROW_ON_ERROR);

        $storageFieldValue->sortKeyString = $data['pathname'] ?? '';
    }

    /**
     * Converts data from $value to $fieldValue.
     *
     * @param StorageFieldValue $value
     * @param FieldValue        $fieldValue
     * @throws JsonException
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue): void
    {
        if (!$value->dataText) {
            $fieldValue->data = null;
            return;
        }

        $storageValue = json_decode($value->dataText, true, 512, JSON_THROW_ON_ERROR);
        $fieldValue->data = [
            'pathname' => $storageValue['pathname'] ?? null,
            'alternativeText' => $storageValue['alternativeText'] ?? null,
            'fileSize' => $storageValue['fileSize'] ?? null,
            'hash' => $storageValue['hash'] ?? null,
            'width' => $storageValue['width'] ?? null,
            'height' => $storageValue['height'] ?? null,
            'imageType' => $storageValue['imageType'] ?? null,
        ];
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef): void
    {
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef): void
    {
    }

    public function getIndexColumn(): string
    {
        return 'sort_key_string';
    }
}
