<?php
/**
 * Base Test Case for Generate lottery unit tests.
 */

declare(strict_types=1);

namespace MarkHeydonTests\LotteryGenerator;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Generate classes.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
abstract class GenerateTestCase extends TestCase
{
    /**
     * Generated results to use in tests.
     *
     * @return array Generated results to use in tests.
     */
    abstract protected function generate(): array;

    /**
     * Tests format of generated array is correct.
     *
     * Should be:
     *
     * 'gameName' => (string), // The name of the lottery game.
     * 'latestDrawDate' => (DateTime), // The date of the latest draw from the history file used.
     * 'numOfMethods' => (int), // The number of methods in the generated results.
     * 'lineBalls' => (array), // Array of names of the balls returned.
     * 'lines' => (array), // Array of lines generated.
     *
     * The 'lineBalls' array should be like the following:
     *
     * $lineBalls = [
     *   'ballTypeA' => 5,
     *   'ballTypeB' => 2,
     * ];
     *
     * And lines should be like the following:
     *
     * $lines = [
     *   'method1' => [
     *     'ballTypeA' => [1, 2, 3, 4, 5],
     *     'ballTypeB' => [1, 2],
     *   ],
     * ];
     */
    public function testGenerateIsSane()
    {
        $var = $this->generate();

        $this->assertArrayHasKey('gameName', $var);
        $this->assertNotEmpty($var['gameName']);

        $this->assertArrayHasKey('latestDrawDate', $var);
        $dateCheck = $var['latestDrawDate'];
        $this->assertInstanceOf(\DateTime::class, $dateCheck);

        $this->assertArrayHasKey('numOfMethods', $var);
        $this->assertGreaterThanOrEqual(1, $var['numOfMethods']);

        $this->assertArrayHasKey('lineBalls', $var);
        $this->assertIsArray($var['lineBalls']);
        foreach ($var['lineBalls'] as $key => $value) {
            $this->assertIsString($key);
            $this->assertGreaterThanOrEqual(1, $value);
        }

        $this->assertArrayHasKey('lines', $var);
        $this->assertIsArray($var['lines']);
    }

    /**
     * Tests that generated results the right number of lines of results.
     *
     * Has to, in theory, generate at least the same number of lines as there are methods. Additionally,
     * each method has to have at least one line.
     */
    public function testGenerateReturnsRightNumOfMethods()
    {
        $generated = $this->generate();
        $var = $generated['lines'];
        $numOfMethods = $generated['numOfMethods'];
        $this->assertGreaterThanOrEqual($numOfMethods, count($var));
        foreach ($var as $methodName => $method) {
            $count = count($method);
            $this->assertGreaterThanOrEqual(
                1,
                $count,
                'Method \'' . $methodName . '\' has count of ' . $count
            );
        }
    }

    /**
     * Tests that generated results returns the right number of line types.
     *
     * Actually also tests the right number of balls per line type were generated.
     */
    public function testGenerateReturnsRightReturnTypes()
    {
        $generated = $this->generate();
        $lineBalls = $generated['lineBalls'];
        $lineBallTypes = array_keys($lineBalls);
        $lines = $generated['lines'];

        foreach ($lines as $method) {
            foreach ($method as $line) {
                foreach ($line as $ballType => $balls) {
                    $this->assertContains($ballType, $lineBallTypes, 'Invalid ball type');
                    $this->assertSame($lineBalls[$ballType], count($balls), 'Invalid number of balls for type');
                }
            }
        }
    }

    /**
     * Tests that generated lines aren't duplicated across all lines.
     *
     * I.e. each line should be unique within a method.
     */
    public function testGenerateResultsAreUnique()
    {
        $generated = $this->generate();
        $var = $generated['lines'];
        foreach ($var as $method) {
            $unique = array_unique($method, SORT_REGULAR);
            $this->assertSame($method, $unique);
        }
    }

    /**
     * Tests to make sure there are no duplicates in the lines generated.
     */
    public function testGenerateResultsDontOverlap()
    {
        $generated = $this->generate();
        $var = $generated['lines'];
        foreach ($var as $method) {
            foreach ($method as $line) {
                $unique = array_unique($line, SORT_REGULAR);
                $this->assertSame($line, $unique);
            }
        }
    }
}
