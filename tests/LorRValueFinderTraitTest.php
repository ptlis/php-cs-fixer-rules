<?php

declare(strict_types=1);

namespace ptlis\PhpCsFixerRules\Test;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use ptlis\PhpCsFixerRules\ExpressionContext;
use ptlis\PhpCsFixerRules\LorRValueFinderTrait;

/**
 * @covers \ptlis\PhpCsFixerRules\LorRValueFinderTrait
 */
class LorRValueFinderTraitTest extends TestCase
{
    /**
     * Returns an object that uses the 'L or R value finder' trait.
     */
    public function getInstance(): object
    {
        return new class {
            use LorRValueFinderTrait;
        };
    }

    public function findExpressionContextProvider(): array
    {
        return [
            'stand alone, first statement' => [
                'tokens' => Tokens::fromCode('<?php $foo === null;'),
                'operator_index' => 3,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 1,
                'end_index' => 5,
            ],
            'stand alone, second statement' => [
                'tokens' => Tokens::fromCode('<?php $test; $foo === null;'),
                'operator_index' => 6,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' =>4,
                'end_index' => 8,
            ],
            'stand alone, after block' => [
                'tokens' => Tokens::fromCode('<?php if (true) {} $foo === null;'),
                'operator_index' => 12,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 10,
                'end_index' => 14,
            ],
            'stand alone, after cast' => [
                'tokens' => Tokens::fromCode('<?php (int)$foo === null;'),
                'operator_index' => 5,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 1,
                'end_index' => 6,
            ],
            'stand alone, with assignment' => [
                'tokens' => Tokens::fromCode('<?php $foo = $bar === null;'),
                'operator_index' => 7,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 1,
                'end_index' => 9,
            ],
            'stand alone, with function call' => [
                'tokens' => Tokens::fromCode('<?php myfunc() === null;'),
                'operator_index' => 4,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 1,
                'end_index' => 7,
            ],
            'stand alone, with method call' => [
                'tokens' => Tokens::fromCode('<?php $obj->foo() === null;'),
                'operator_index' => 7,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 1,
                'end_index' => 9,
            ],
            'stand alone, with static method call' => [
                'tokens' => Tokens::fromCode('<?php SomeClass::foo() === null;'),
                'operator_index' => 7,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 1,
                'end_index' => 9,
            ],
            'stand alone, array dereference' => [
                'tokens' => Tokens::fromCode('<?php $foo[\'bar\'] === null;'),
                'operator_index' => 7,
                'context' => ExpressionContext::STAND_ALONE,
                'start_index' => 1,
                'end_index' => 8,
            ],
            'parens, in if statement' => [
                'tokens' => Tokens::fromCode('<?php if ($bar === null) {}'),
                'operator_index' => 6,
                'context' => ExpressionContext::PARENS,
                'start_index' => 4,
                'end_index' => 8,
            ],
            'parens, in while statement' => [
                'tokens' => Tokens::fromCode('<?php while ($bar === null) {}'),
                'operator_index' => 6,
                'context' => ExpressionContext::PARENS,
                'start_index' => 4,
                'end_index' => 8,
            ],
            'parens, in otherwise stand-alone statement' => [
                'tokens' => Tokens::fromCode('<?php ($bar === null);'),
                'operator_index' => 4,
                'context' => ExpressionContext::PARENS,
                'start_index' => 2,
                'end_index' => 6,
            ],
            'parens, as arguments in function call' => [
                'tokens' => Tokens::fromCode('<?php somefunc($bar === null);'),
                'operator_index' => 5,
                'context' => ExpressionContext::PARENS,
                'start_index' => 3,
                'end_index' => 7,
            ],
            'return' => [
                'tokens' => Tokens::fromCode('<?php return $bar === null;'),
                'operator_index' => 5,
                'context' => ExpressionContext::RETURN,
                'start_index' => 3,
                'end_index' => 7,
            ],
        ];
    }

    /**
     * @dataProvider findExpressionContextProvider
     */
    public function testGetExpressionContext(
        Tokens $tokens,
        int $operatorIndex,
        string $expectedContext
    ): void {
        $fixer = $this->getInstance();

        $this->assertEquals($expectedContext, $fixer->getExpressionContext($operatorIndex, $tokens));
    }

    /**
     * @dataProvider findExpressionContextProvider
     */
    public function testGetExpressionStartTokenIndex(
        Tokens $tokens,
        int $operatorIndex,
        string $expectedContext,
        int $expectedStartIndex
    ): void {
        $fixer = $this->getInstance();

        $this->assertEquals($expectedStartIndex, $fixer->getExpressionStartTokenIndex($operatorIndex, $tokens));
    }

    /**
     * @dataProvider findExpressionContextProvider
     */
    public function testGetExpressionEndTokenIndex(
        Tokens $tokens,
        int $operatorIndex,
        string $expectedContext,
        int $startIndex,
        int $expectedEndIndex
    ): void
    {
        $fixer = $this->getInstance();

        $this->assertEquals($expectedEndIndex, $fixer->getExpressionEndTokenIndex($operatorIndex, $tokens, $expectedContext));
    }
}
