<?php

namespace ptlis\PhpCsFixerRules\Support;

use PhpCsFixer\Tokenizer\Token;

/**
 * Utility class to help ensure that braces are balanced.
 */
final class BracketTracker
{
    public const TYPE_ANGLE = 'angle';
    public const TYPE_CURLY = 'curly';
    public const TYPE_ROUND = 'round';
    public const TYPE_SQUARE = 'square';
    private const OPENING_BRACKETS = [
        self::TYPE_ANGLE => '<',
        self::TYPE_CURLY => '{',
        self::TYPE_ROUND => '(',
        self::TYPE_SQUARE => '[',
    ];
    private const CLOSING_BRACKETS = [
        self::TYPE_ANGLE => '>',
        self::TYPE_CURLY => '}',
        self::TYPE_ROUND => ')',
        self::TYPE_SQUARE => ']',
    ];

    /**
     * @var array<string, int>
     */
    private array $countDelta = [
        self::TYPE_ANGLE => 0,
        self::TYPE_CURLY => 0,
        self::TYPE_ROUND => 0,
        self::TYPE_SQUARE => 0,
    ];
    /**
     * @var array<string>
     */
    public readonly array $types;

    /**
     * @param array<string> $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    public function foo(Token $token): void
    {
        foreach ($this->types as $type) {
            switch ($token->getContent()) {
                case self::OPENING_BRACKETS[$type]:
                    $this->countDelta[$type]++;
                    break;
                case self::CLOSING_BRACKETS[$type]:
                    $this->countDelta[$type]--;
                    break;
            }
        }
    }

    public function isBalanced(): bool
    {
        foreach ($this->countDelta as $delta) {
            if ($delta !== 0) {
                return false;
            }
        }
        return true;
    }
}
