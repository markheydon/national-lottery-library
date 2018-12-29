<?php
/**
 * Unit tests for LottoGenerate class.
 */

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\LottoGenerate;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for LottoGenerate class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class LottoGenerateTest extends TestCase
{
    /**
     * Tests that generate returns at least three lines of results.
     *
     * Has to, in theory, generate three as generate methods will always return at least one line
     * when there is data available.
     */
    public function testGenerateReturnsArray()
    {
        // check for 3 methods,
        // and that each method has at least one result
        $var = LottoGenerate::generate();
        $this->assertCount(3, $var);
        foreach ($var as $methodName => $method) {
            $this->assertTrue(count($method) > 0,
                'Method \'' . $methodName . '\' has count of ' . count($method));
        }

    }

    /**
     * Tests that the generated lines all contain 6 numbers.
     */
    public function testGenerateLinesContainsSix()
    {
        $var = LottoGenerate::generate();
        foreach ($var as $method) {
            foreach ($method as $line) {
                $this->assertCount(6, $line);
            }
        }

    }

    /**
     * Tests to make sure there are no duplicates in the lines generated.
     */
    public function testGenerateResultsDontOverlap()
    {
        $var = LottoGenerate::generate();
        foreach ($var as $method) {
            foreach ($method as $line) {
                $unique = array_unique($line);
                $this->assertSame($line, $unique);
            }
        }

    }
}
