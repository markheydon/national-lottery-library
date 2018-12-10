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
        // check for 3 methods,
        // and that each method has at least one result
        $var = GenerateLotto::generate();
        $this->assertCount(3, $var);
        foreach ($var as $methodName => $method) {
            $this->assertTrue(count($method) > 0,
                'Method \'' . $methodName . '\' has count of ' . count($method));
        }

    }

    /**
     * Tests that the generated lines all contain 6 numbers.
     *
     * @since 1.0.0
     */
    public function testGenerateLinesContainsSix()
    {
        $var = GenerateLotto::generate();
        foreach ($var as $method) {
            foreach ($method as $line) {
                $this->assertCount(6, $line);
            }
        }

    }

    /**
     * Tests to make sure there are no duplicates in the lines generated.
     *
     * @since 1.0.0
     */
    public function testGenerateResultsDontOverlap()
    {
        $var = GenerateLotto::generate();
        foreach ($var as $method) {
            foreach ($method as $line) {
                $unique = array_unique($line);
                $this->assertSame($line, $unique);
            }
        }

    }
}
