<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Kernel;
use Behat\Behat\Context\Context;
use LogicException;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

final class WebContext extends WebTestCase implements Context
{
    private bool $checkPageValidity = true;

    /** @var KernelBrowser */
    private static KernelBrowser $currentClient;

    /** @var array<string, string> */
    public array $nextHeaders = [];

    /**
     * @BeforeScenario
     */
    public function setup(): void
    {
        $this->checkPageValidity = true;
    }

    /**
     * @When I go to page :path
     * @param string $path
     */
    public function iGoToPage(string $path): void
    {
        $this->getCurrentClient()->request('GET', $path, [], [], $this->getHttpHeader($this->nextHeaders));
        $this->nextHeaders = [];
        if ($this->checkPageValidity) {
            self::assertResponseIsSuccessful();
        }
    }

    /**
     * @When I try to go to page :path
     * @param string $path
     */
    public function iTryToGoToPage(string $path): void
    {
        $this->getCurrentClient()->request('GET', $path, [], [], $this->getHttpHeader($this->nextHeaders));
        $this->nextHeaders = [];
    }

    /**
     * @param array<string, mixed> $options
     * @return KernelInterface
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        self::$class = Kernel::class;
        return parent::createKernel($options);
    }

    /**
     * @Then I stay on page :path
     * @Then I am on page :path
     * @param string $path
     */
    public function iAmOnPage(string $path): void
    {
        Assert::assertEquals(
            $path,
            $this->getCurrentClient()->getResponse()->headers->get('Location')
            ?? $this->getCurrentClient()->getRequest()->getRequestUri()
        );
    }

    /**
     * @Then I am redirected to page :path
     * @param string $path
     */
    public function iAmRedirectedToPage(string $path): void
    {
        $this::assertResponseRedirects($path);
        $this->getCurrentClient()->followRedirect();
    }

    /**
     * @Then I am redirected to external page :path
     * @param string $path
     */
    public function iAmRedirectedToExternalPage(string $path): void
    {
        $this::assertResponseRedirects($path);
    }

    /**
     * @Then I see :text
     * @param string $text
     */
    public function iSee(string $text): void
    {
        self::assertSelectorTextContains('body', $text);
    }

    /**
     * @When /^pages can be invalid$/
     */
    public function pagesCanBeInvalid(): void
    {
        $this->checkPageValidity = false;
    }

    /**
     * @Then page respond
     */
    public function pageRespond(): void
    {
        self::assertResponseIsSuccessful();
    }

    /**
     * @Then http status code is :statusCode
     */
    public function httpStatusCodeIs(int $statusCode): void
    {
        self::assertResponseStatusCodeSame($statusCode);
    }

    /**
     * @Then no email has been sent
     */
    public function noEmailHasBeenSent(): void
    {
        $emailCount = count(self::getMailerEvents());

        if ($emailCount > 0) {
            throw new LogicException($emailCount . ' email(s) has been sent');
        }
    }

    public function getCurrentClient(): KernelBrowser
    {
        if (!self::$booted) {
            self::$currentClient = self::createClient([], ['SCRIPT_FILENAME' => 'app.php']);
        }

        return self::$currentClient;
    }
    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    private function getHttpHeader(array $headers): array
    {
        $httpHeaders = [];
        foreach ($headers as $name => $value) {
            $httpHeaders['HTTP_' . strtoupper(str_replace('-', '_', $name))] = $value;
        }

        return $httpHeaders;
    }
}
