<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;

abstract class AbstractContext implements Context
{
    protected WebContext $webContext;

    public function __construct(WebContext $webContext)
    {
        $this->webContext = $webContext;
    }
}
