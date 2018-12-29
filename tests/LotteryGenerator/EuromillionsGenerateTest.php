<?php
/**
 * Unit tests for LottoGenerate class.
 */

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\EuromillionsGenerate;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for LottoGenerate class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class EuromillionsGenerateTest extends TestCase
{
    /**
     * Tests that generate returns at least two lines of results.
     *
     * Has to, in theory, generate two as generate methods will always return at least one line
     * when there is data available.
     */
    public function testGenerateReturnsArray()
    {
        // check for 2 methods,
        // and that each method has at least one result
        $var = EuromillionsGenerate::generate();
        $this->assertCount(2, $var);
        foreach ($var as $methodName => $method) {
            $this->assertTrue(count($method) > 0,
                'Method \'' . $methodName . '\' has count of ' . count($method));
        }

    }

    /**
     * Tests that the generated lines contain 'normal' and 'luckyStars'.
     */
    public function testGenerateLinesContainsNormalAndLuckyStars()
    {
        $var = EuromillionsGenerate::generate();
        foreach ($var as $method) {
            foreach ($method as $line) {
                $this->assertTrue(isset($line['normal']));
                $this->assertTrue(isset($line['luckyStars']));
            }
        }
    }

    /**
     * Tests that the generated lines all contain 5 numbers and 2 lucky stars.
     */
    public function testGenerateLinesContainsSix()
    {
        $var = EuromillionsGenerate::generate();
        foreach ($var as $method) {
            foreach ($method as $line) {
                $this->assertCount(5, $line['normal']);
                $this->assertCount(2, $line['luckyStars']);
            }
        }
    }

    /**
     * Tests to make sure there are no duplicates in the lines generated.
     */
    public function testGenerateResultsDontOverlap()
    {
        $var = EuromillionsGenerate::generate();
        foreach ($var as $method) {
            foreach ($method as $line) {
                $unique = array_unique($line['normal']);
                $this->assertEquals($line['normal'], $unique, '', 0.0, 10, true);

                $unique = array_unique($line['luckyStars']);
                $this->assertEquals($line['luckyStars'], $unique, '', 0.0, 10, true);
            }
        }
    }
}
