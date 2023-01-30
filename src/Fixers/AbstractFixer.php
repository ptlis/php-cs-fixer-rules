<?php

declare(strict_types=1);

namespace ptlis\PhpCsFixerRules\Fixers;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Preg;
use SplFileInfo;

/**
 * Base class providing common functionality for other fixers.
 */
abstract class AbstractFixer implements FixerInterface
{
    final public static function name(): string
    {
        $name = Preg::replace('/(?<!^)(?=[A-Z])/', '_', substr(static::class, 29, -5));

        return 'ptlis/PhpCsFixerRules/' . strtolower($name);
    }

    final public function getName(): string
    {
        return self::name();
    }

    final public function supports(SplFileInfo $file): bool
    {
        return true;
    }
}
