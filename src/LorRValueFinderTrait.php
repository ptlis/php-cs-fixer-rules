<?php

declare(strict_types=1);

namespace ptlis\PhpCsFixerRules;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Provides functionality to extract the tokens for the 'L' or 'R' values of an expression. For example in this
 *  example expression `$foo['bar'] === Class::method()` the 'L' value is `$foo['bar']` and the 'R' value is
 *  `Class::method()`.
 */
trait LorRValueFinderTrait
{
    /**
     * Provides a method to figure out the context of an expression when provided with the binary operator.
     */
    public function findExpressionContext(int $binaryOperatorIndex, Tokens $tokens): string
    {
        $tokenIndex = $binaryOperatorIndex;

        // Track opening / closing brackets - must be balanced for expression to be complete
        $roundBracketCount = 0;
        $squareBracketCount = 0;

        // Iterate backwards through tokens until we have an opening parens, return statement or an opening parens '('
        while (!is_null($tokenIndex) && $token = $tokens[$tokenIndex]) {

            if (
                (
                    $token->getId() === T_OPEN_TAG
                    || (is_null($token->getId()) && $token->getContent() === ';')
                    || (is_null($token->getId()) && $token->getContent() === '}')
                )
                && !$roundBracketCount
                && !$squareBracketCount
            ) {
                return ExpressionContext::STAND_ALONE;
            }

            if (
                $token->getContent() === '('
                && !$roundBracketCount
                && !$squareBracketCount
            ) {
                return ExpressionContext::PARENS;
            }

            if ($token->getId() === T_RETURN) {
                return ExpressionContext::RETURN;
            }


            // Track brackets
            switch ($token->getContent()) {
                case '(':
                    $roundBracketCount++;
                    break;
                case ')':
                    $roundBracketCount--;
                    break;
                case '[':
                    $squareBracketCount++;
                    break;
                case ']':
                    $squareBracketCount--;
                    break;
            }

            $tokenIndex = $tokens->getPrevMeaningfulToken($tokenIndex);
        }

        // TODO: This is a pretty crappy error message - figure out something better
        throw new \RuntimeException('Unable to determine context for expression...');
    }
}
