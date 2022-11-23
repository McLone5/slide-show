<?php

declare(strict_types=1);

namespace App\Tests\Behat;

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
}
