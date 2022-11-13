<?php

declare(strict_types=1);

namespace App\Tests\TwigCs;

use FriendsOfTwig\Twigcs\RegEngine\RulesetBuilder;
use FriendsOfTwig\Twigcs\RegEngine\RulesetConfigurator;
use FriendsOfTwig\Twigcs\Rule;
use FriendsOfTwig\Twigcs\Ruleset\RulesetInterface;
use FriendsOfTwig\Twigcs\Validator\Violation;

final class TwigCsRuleSet implements RulesetInterface
{
    private int $twigMajorVersion;

    public function __construct(int $twigMajorVersion)
    {
        $this->twigMajorVersion = $twigMajorVersion;
    }

    /**
     * @return Rule\RuleInterface[]
     */
    public function getRules(): array
    {
        $configurator = new RulesetConfigurator();
        $configurator->setTwigMajorVersion($this->twigMajorVersion);
        $builder = new RulesetBuilder($configurator);

        return [
            new Rule\LowerCaseVariable(Violation::SEVERITY_ERROR),
            new Rule\RegEngineRule(Violation::SEVERITY_ERROR, $builder->build()),
            new Rule\ForbiddenFunctions(Violation::SEVERITY_ERROR, ['dump']),
            new Rule\TrailingSpace(Violation::SEVERITY_ERROR),
            new Rule\UnusedMacro(Violation::SEVERITY_WARNING),
            new HasTabulation(Violation::SEVERITY_WARNING),
            //Disabled as it does not understand variable set before a block()
            //new Rule\UnusedVariable(Violation::SEVERITY_WARNING),
        ];
    }
}
