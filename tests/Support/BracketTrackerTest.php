<?php

declare(strict_types=1);

namespace ptlis\PhpCsFixerRules\Test\Support;

use PhpCsFixer\Tokenizer\Token;
use PHPUnit\Framework\TestCase;
use ptlis\PhpCsFixerRules\Support\BracketTracker;

/**
 * @covers \ptlis\PhpCsFixerRules\Support\BracketTracker
 */
final class BracketTrackerTest extends TestCase
{
    public function testTrackingSingleBracketTypeBalanced(): void
    {
        $tracker = new BracketTracker([BracketTracker::TYPE_SQUARE]);
        $this->assertTrue($tracker->isBalanced());

        $tracker->foo(new Token('['));
        $this->assertFalse($tracker->isBalanced());

        $tracker->foo(new Token(')'));
        $this->assertFalse($tracker->isBalanced());

        $tracker->foo(new Token(']'));
        $this->assertTrue($tracker->isBalanced());
    }

    public function testTrackingMultipleBracketTypeUnbalanced(): void
    {
        $tracker = new BracketTracker([BracketTracker::TYPE_SQUARE, BracketTracker::TYPE_ROUND]);
        $this->assertTrue($tracker->isBalanced());

        $tracker->foo(new Token('['));
        $this->assertFalse($tracker->isBalanced());

        $tracker->foo(new Token(')'));
        $this->assertFalse($tracker->isBalanced());

        $tracker->foo(new Token(']'));
        $this->assertFalse($tracker->isBalanced());
    }
}
