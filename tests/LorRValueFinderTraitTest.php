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
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'stand alone, second statement' => [
                'tokens' => Tokens::fromCode('<?php $test; $foo === null;'),
                'operator_index' => 6,
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'stand alone, after block' => [
                'tokens' => Tokens::fromCode('<?php if (true) {} $foo === null;'),
                'operator_index' => 12,
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'stand alone, after cast' => [
                'tokens' => Tokens::fromCode('<?php (int)$foo === null;'),
                'operator_index' => 5,
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'stand alone, with assignment' => [
                'tokens' => Tokens::fromCode('<?php $foo = $bar === null;'),
                'operator_index' => 7,
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'stand alone, with function call' => [
                'tokens' => Tokens::fromCode('<?php myfunc() === null;'),
                'operator_index' => 7,
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'stand alone, with method call' => [
                'tokens' => Tokens::fromCode('<?php $obj->foo() === null;'),
                'operator_index' => 7,
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'stand alone, with static method call' => [
                'tokens' => Tokens::fromCode('<?php SomeClass::foo() === null;'),
                'operator_index' => 7,
                'expected_context' => ExpressionContext::STAND_ALONE,
            ],
            'parens, in if statement' => [
                'tokens' => Tokens::fromCode('<?php if ($bar === null) {}'),
                'operator_index' => 6,
                'expected_context' => ExpressionContext::PARENS,
            ],
            'parens, in while statement' => [
                'tokens' => Tokens::fromCode('<?php while ($bar === null) {}'),
                'operator_index' => 4,
                'expected_context' => ExpressionContext::PARENS,
            ],
            'parens, in otherwise stand-alone statement' => [
                'tokens' => Tokens::fromCode('<?php ($bar === null);'),
                'operator_index' => 4,
                'expected_context' => ExpressionContext::PARENS,
            ],
            'parens, as arguments in function call' => [
                'tokens' => Tokens::fromCode('<?php somefunc($bar === null);'),
                'operator_index' => 5,
                'expected_context' => ExpressionContext::PARENS,
            ],
            'return' => [
                'tokens' => Tokens::fromCode('<?php return $bar === null;'),
                'operator_index' => 5,
                'expected_context' => ExpressionContext::RETURN,
            ],
        ];
    }

    /**
     * @dataProvider findExpressionContextProvider
     */
    public function testFindExpressionContext(Tokens $tokens, int $operatorIndex, string $expectedContext): void
    {
        $fixer = $this->getInstance();

        $this->assertEquals($expectedContext, $fixer->findExpressionContext($operatorIndex, $tokens));
    }
}
