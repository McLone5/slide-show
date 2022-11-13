<?php

declare(strict_types=1);

namespace App\Tests\TwigCs;

use FriendsOfTwig\Twigcs\Rule\AbstractRule;
use FriendsOfTwig\Twigcs\Rule\RuleInterface;
use FriendsOfTwig\Twigcs\TwigPort\TokenStream;
use FriendsOfTwig\Twigcs\Validator\Violation;

final class HasTabulation extends AbstractRule implements RuleInterface
{
    /**
     * @param TokenStream $tokens
     * @return Violation[]
     */
    public function check(TokenStream $tokens): array
    {
        $fileContent = (string)file_get_contents($tokens->getSourceContext()->getPath());

        if (str_contains($fileContent, "\t")) {
            return [
                $this->createViolation(
                    $tokens->getSourceContext()->getPath(),
                    0,
                    0,
                    'A file should not have the tabulation character.'
                )
            ];
        }

        return [];
    }
}
