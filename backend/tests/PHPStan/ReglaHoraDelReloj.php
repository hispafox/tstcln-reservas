<?php

declare(strict_types=1);

namespace App\Tests\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<New_>
 */
final class ReglaHoraDelReloj implements Rule
{
    public function getNodeType(): string
    {
        return New_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->class instanceof Name || $node->getArgs() !== []) {
            return [];
        }

        if (!in_array($scope->resolveName($node->class), [\DateTimeImmutable::class, \DateTime::class], true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('La hora se pide al reloj: inyecta ClockInterface y llama a now().')
                ->identifier('reservas.horaDelSistema')
                ->build(),
        ];
    }
}
