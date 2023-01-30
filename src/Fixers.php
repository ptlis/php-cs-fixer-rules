<?php

declare(strict_types=1);

namespace ptlis\PhpCsFixerRules;

use DirectoryIterator;
use Generator;
use IteratorAggregate;
use PhpCsFixer\Fixer\FixerInterface;

/**
 * @implements \IteratorAggregate<FixerInterface>
 */
class Fixers implements IteratorAggregate
{
    /**
     * @return Generator<FixerInterface>
     */
    public function getIterator(): Generator
    {
        $classNames = [];

        foreach (new DirectoryIterator(__DIR__ . '/Fixers') as $fileInfo) {
            $fileName = $fileInfo->getBasename('.php');

            if (in_array($fileName, ['.', '..', 'AbstractFixer'], true)) {
                continue;
            }
            $classNames[] = __NAMESPACE__ . '\\Fixer\\' . $fileName;
        }

        sort($classNames);

        foreach ($classNames as $className) {
            $fixer = new $className();
            assert($fixer instanceof FixerInterface);

            yield $fixer;
        }
    }
}
