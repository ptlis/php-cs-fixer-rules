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

    public function findExpressionFromOperatorIndex(int $operatorIndex, Tokens $tokens): array
    {
        $context = $this->findExpressionContext($operatorIndex, $tokens);




    }

    /**
     * Provides a method to figure out the context of an expression when provided with the binary operator.
     *
     * @return array{int, string} The index at which the expression starts & the context.
     */
    public function getStartTokenIndexExpressionContext(int $binaryOperatorIndex, Tokens $tokens): array
    {
        $tokenIndex = $binaryOperatorIndex;
        $lastTokenIndex = $binaryOperatorIndex;

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
                return [$lastTokenIndex, ExpressionContext::STAND_ALONE];
            }

            if (
                $token->getContent() === '('
                && !$roundBracketCount
                && !$squareBracketCount
            ) {
                return [$lastTokenIndex, ExpressionContext::PARENS];
            }

            if ($token->getId() === T_RETURN) {
                return [$lastTokenIndex, ExpressionContext::RETURN];
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

            $lastTokenIndex = $tokenIndex;
            $tokenIndex = $tokens->getPrevMeaningfulToken($tokenIndex);
        }

        // TODO: This is a pretty crappy error message - figure out something better
        throw new \RuntimeException('Unable to determine context for expression...');
    }

    public function getExpressionStartTokenIndex(int $binaryOperatorIndex, Tokens $tokens): int
    {
        [$startTokenIndex] = $this->getStartTokenIndexExpressionContext($binaryOperatorIndex, $tokens);
        return $startTokenIndex;
    }

    public function getExpressionContext(int $binaryOperatorIndex, Tokens $tokens): string
    {
        [, $expressionContext] = $this->getStartTokenIndexExpressionContext($binaryOperatorIndex, $tokens);
        return $expressionContext;
    }

    /**
     * @return int The index of the expressions final token.
     */
    public function getExpressionEndTokenIndex(int $operatorIndex, Tokens $tokens, string $context): int
    {
        $tokenIndex = $operatorIndex;
        $lastTokenIndex = $operatorIndex;

        // Track opening / closing brackets - must be balanced for expression to be complete
        $roundBracketCount = 0;
        $squareBracketCount = 0;

        while ($token = $tokens[$tokenIndex]) {
            switch ($context) {
                case ExpressionContext::PARENS:
                    if (!$roundBracketCount && !$squareBracketCount && $token->getContent() === ')') {
                        return $lastTokenIndex;
                    }
                    break;
                case ExpressionContext::RETURN:
                case ExpressionContext::STAND_ALONE:
                    if ($token->getContent() === ';') {
                        return $lastTokenIndex;
                    }
                    break;
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

            $lastTokenIndex = $tokenIndex;
            $tokenIndex = $tokens->getNextMeaningfulToken($tokenIndex);
        }

        // TODO: This is a pretty crappy error message - figure out something better
        throw new \RuntimeException('Unable to find end token for expression...');
    }
}
