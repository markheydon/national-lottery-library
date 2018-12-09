<?php
/**
 * Unit tests for GenerateLotto class.
 *
 * @since 1.0.0
 */

namespace MarkHeydonTest\LotteryGenerator;

use MarkHeydon\LotteryGenerator\GenerateLotto;
use PHPUnit\Framework\TestCase;

class GenerateLottoTest extends TestCase
{
    /**
     * Tests that generate returns at least two lines of results.
     *
     * Has to, in theory, generate two as both generate methods will always return at least one line
     * when there is data available.
     *
     * @since 1.0.0
     */
    public function testGenerateReturnsArray()
    {
        $var = GenerateLotto::generate();
        $this->assertTrue(count($var) >= 2);
    }

    /**
     * Tests that the generated lines all contain 6 numbers.
     *
     * @since 1.0.0
     */
    public function testGenerateLinesContainsSix()
    {
        $var = GenerateLotto::generate();
        foreach ($var as $line) {
            $this->assertCount(6, $line);
        }
    }
}
