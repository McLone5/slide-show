<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Photo\FileStorageInterface;
use App\Infrastructure\FieldTypes\Photo\Value;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Helper\TranslationHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class PhotoAccess
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly TranslationHelper $translationHelper,
        private readonly PermissionResolver $permissionResolver,
        private readonly FileStorageInterface $fileStorage,
    ) {
    }

    /**
     * @throws BadStateException
     * @throws InvalidArgumentException
     */
    #[Route('/photo/{contentId}/{fieldIdentifier}/{baseFilename}')]
    public function __invoke(int $contentId, string $fieldIdentifier, string $baseFilename): BinaryFileResponse
    {
        try {
            $content = $this->contentService->loadContent($contentId);
        } catch (NotFoundException|UnauthorizedException) {
            throw new NotFoundHttpException('Content not found');
        }
        if (!$this->permissionResolver->canUser('content', 'read', $content)) {
            throw new NotFoundHttpException('Current user cannot read content');
        }

        $fieldValue = $this->translationHelper->getTranslatedField($content, $fieldIdentifier)->value ?? null;
        if (!$fieldValue instanceof Value) {
            throw new NotFoundHttpException('Field not found');
        }
        if (!$fieldValue->imageType) {
            throw new NotFoundHttpException('Missing image type');
        }

        try {
            $file = $this->fileStorage->getFileFromPathname((string)$fieldValue->pathname);
        } catch (FileNotFoundException) {
            throw new NotFoundHttpException('File not found');
        }

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $baseFilename);
        $response->headers->set('Content-Type', image_type_to_mime_type($fieldValue->imageType));

        return $response;
    }
}
