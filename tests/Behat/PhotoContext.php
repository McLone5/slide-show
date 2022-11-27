<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringStartsWith;

final class PhotoContext extends AbstractContext
{
    /**
     * @Then I get a photo
     */
    public function iGetAPhoto(): void
    {
        $contentType = (string)$this->webContext->getCurrentClient()->getResponse()->headers->get('Content-Type');
        assertStringStartsWith('image/', $contentType);
    }

    /**
     * @Then /^I get a photo with dimensions (\d+)x(\d+)$/
     */
    public function iGetAPhotoWithDimensions640x(int $expectedWidth, int $expectedHeight): void
    {
        $this->iGetAPhoto();

        $binaryFileResponse = $this->webContext->getCurrentClient()->getResponse();
        if (!$binaryFileResponse instanceof BinaryFileResponse) {
            throw new RuntimeException('Unexpected response type');
        }

        [$actualWidth, $actualHeight] = getimagesize($binaryFileResponse->getFile()->getPathname())
            ?: throw new RuntimeException('Unable to get image size')
        ;

        assertEquals([$expectedWidth, $expectedHeight], [$actualWidth, $actualHeight]);
    }
}
