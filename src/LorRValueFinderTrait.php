<?php

declare(strict_types=1);

namespace ptlis\PhpCsFixerRules;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ptlis\PhpCsFixerRules\Support\BracketTracker;

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
    public function getStartTokenIndexAndExpressionContext(int $binaryOperatorIndex, Tokens $tokens): array
    {
        $tokenIndex = $binaryOperatorIndex;
        $lastTokenIndex = $binaryOperatorIndex;
        $bracketTracker = new BracketTracker([BracketTracker::TYPE_ROUND, BracketTracker::TYPE_SQUARE]);

        // Iterate backwards through tokens until we have an opening parens, return statement or an opening parens '('
        while (!is_null($tokenIndex) && $token = $tokens[$tokenIndex]) {
            if ($bracketTracker->isBalanced()) {

                if (
                    $token->getId() === T_OPEN_TAG
                    || (is_null($token->getId()) && $token->getContent() === ';')
                    || (is_null($token->getId()) && $token->getContent() === '}')
                ) {
                    return [$lastTokenIndex, ExpressionContext::STAND_ALONE];
                }

                if ($token->getContent() === '(') {
                    return [$lastTokenIndex, ExpressionContext::PARENS];
                }

                if ($token->getId() === T_RETURN) {
                    return [$lastTokenIndex, ExpressionContext::RETURN];
                }
            }

            $bracketTracker->trackToken($token);
            $lastTokenIndex = $tokenIndex;
            $tokenIndex = $tokens->getPrevMeaningfulToken($tokenIndex);
        }

        // TODO: This is a pretty crappy error message - figure out something better
        throw new \RuntimeException('Unable to determine context for expression...');
    }

    public function getExpressionStartTokenIndex(int $binaryOperatorIndex, Tokens $tokens): int
    {
        [$startTokenIndex] = $this->getStartTokenIndexAndExpressionContext($binaryOperatorIndex, $tokens);
        return $startTokenIndex;
    }

    public function getExpressionContext(int $binaryOperatorIndex, Tokens $tokens): string
    {
        [, $expressionContext] = $this->getStartTokenIndexAndExpressionContext($binaryOperatorIndex, $tokens);
        return $expressionContext;
    }

    /**
     * @return int The index of the expressions final token.
     */
    public function getExpressionEndTokenIndex(int $operatorIndex, Tokens $tokens, string $context): int
    {
        $tokenIndex = $operatorIndex;
        $lastTokenIndex = $operatorIndex;
        $bracketTracker = new BracketTracker([BracketTracker::TYPE_ROUND, BracketTracker::TYPE_SQUARE]);

        while ($token = $tokens[$tokenIndex]) {
            if ($bracketTracker->isBalanced()) {
                switch ($context) {
                    case ExpressionContext::PARENS:
                        if ($token->getContent() === ')') {
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
            }

            $bracketTracker->trackToken($token);
            $lastTokenIndex = $tokenIndex;
            $tokenIndex = $tokens->getNextMeaningfulToken($tokenIndex);
        }

        // TODO: This is a pretty crappy error message - figure out something better
        throw new \RuntimeException('Unable to find end token for expression...');
    }
}
