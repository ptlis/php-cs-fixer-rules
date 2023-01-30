<?php

declare(strict_types=1);

namespace ptlis\PhpCsFixerRules;

final class ExpressionContext
{
    /** Used when the expression is a 'stand alone' statement. E.g. `$bar !== null;` */
    public const STAND_ALONE = 'stand_alone';

    /** Used when the expression is in round brackets. E.g. `if ($foo === $bar) {}` */
    public const PARENS = 'parens';

    /** Used when the expression is in a return statement. E.g. `return $foo['bar'] === null;` */
    public const RETURN = 'return';
}
