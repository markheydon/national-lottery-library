<?php
/**
 * Unit tests for Utils class.
 */

declare(strict_types=1);

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\Utils;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Utils class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UtilsTest extends TestCase
{
    /**
     * Tests that csv parser returns error on file not found.
     */
    public function testCsvToArrayNoFile()
    {
        $this->expectError();
        Utils::csvToArray('FILE_NOT_FOUND');
    }

    /**
     * Tests the csv parser works with a supplied test file.
     */
    public function testCsvToArrayTestFile()
    {
        // create a temp test file
        $header = "col1,col2" . PHP_EOL;
        $line1 = "val1,val2" . PHP_EOL;
        $line2 = "val1,val2" . PHP_EOL;

        // TODO: #13 Fix tempnam() call on Windows platforms.
        $tempFile = tempnam('/tmp', 'test-csv-');
        $temp = fopen($tempFile, 'w');
        fwrite($temp, $header);
        fwrite($temp, $line1);
        fwrite($temp, $line2);

        // test csv parser
        $var = Utils::csvToArray($tempFile);
        $this->assertCount(2, $var); // two lines
        foreach ($var as $item) {
            $this->assertTrue(isset($item['col1']));
            $this->assertTrue(isset($item['col2']));

            $this->assertSame('val1', $item['col1']);
            $this->assertSame('val2', $item['col2']);
        }

        // remove the temp file
        fclose($temp);
        unlink($tempFile);
    }

    /**
     * Tests simple text counter.
     */
    public function testGetCountSimpleText()
    {
        $testDraws = [
            [
                'whatever' => 'something',
            ],
            [
                'whatever' => 'something',
            ],
        ];
        $res = Utils::getCount($testDraws, ['whatever']);
        $this->assertSame(['something' => 2], $res);
    }

    /**
     * Tests simple int counter.
     */
    public function testGetCountSimpleInt()
    {
        $testDraws = [
            [
                'whatever' => 123,
            ],
            [
                'whatever' => 123,
            ],
        ];
        $res = Utils::getCount($testDraws, ['whatever']);
        $this->assertSame([123 => 2], $res);
    }

    /**
     * Tests the frequent ball calculation returns first frequent number
     * when only one frequently occurs.
     */
    public function testCalcFreqBallSinglePossibleResult()
    {
        $testDraws = [
            [
                'ball1' => 1,
                'ball2' => 2,
                'ball3' => 3,
            ],
            [
                'ball1' => 1,
                'ball2' => 4,
                'ball3' => 5,
            ],
        ];
        $ballNames = ['ball1', 'ball2', 'ball3'];

        $res = Utils::calculateFrequentElementValues($testDraws, $ballNames);
        $this->assertSame(1, $res);
    }

    /**
     * Tests the frequent ball calculation returns first frequent number
     * when several frequently occur.
     */
    public function testCalcFreqBallMultiplePossibleResult()
    {
        $testDraws = [
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 3,
            ],
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 4,
            ],
        ];
        $ballNames = ['ball1', 'ball2', 'ball3'];

        $res = Utils::calculateFrequentElementValues($testDraws, $ballNames);
        $this->assertSame(2, $res);
    }

    /**
     * Tests nothing frequently occurs.
     *
     * I.e. only one line of draws so nothing will be frequently occurring.
     */
    public function testCalcFreqBallNothingFrequentlyOccurring()
    {
        $testDraws = [
            [
                'ball1' => 12,
                'ball2' => 2,
                'ball3' => 3,
            ],
        ];
        $ballNames = ['ball1', 'ball2', 'ball3'];

        $res = Utils::calculateFrequentElementValues($testDraws, $ballNames);
        $this->assertSame(12, $res);
    }

    /**
     * Tests nothing frequently occurs because there are no draws passed in.
     *
     * I.e. empty draws array passed in
     */
    public function testCalcFreqBallDrawsArrayEmpty()
    {
        $testDraws = [];
        $ballNames = ['ball1', 'ball2', 'ball3'];

        $res = Utils::calculateFrequentElementValues($testDraws, $ballNames, [2]);
        $this->assertSame(0, $res);
    }

    /**
     * Tests the frequent ball calculation returns first frequent number
     * when the optional exclusion parameter is in use.
     */
    public function testCalcFreqBallSingleResultWithExcept()
    {
        $testDraws = [
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 3,
            ],
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 4,
            ],
        ];
        $ballNames = ['ball1', 'ball2', 'ball3'];

        $res = Utils::calculateFrequentElementValues($testDraws, $ballNames, [2]);
        $this->assertSame(1, $res);
    }

    /**
     * Tests filtering by a text value.
     */
    public function testFilterDrawsByTextSimple()
    {
        $testDraws = [
            [
                'whatever' => 1,
            ],
            [
                'whatever' => 2,
            ],
        ];
        $this->assertCount(2, $testDraws);

        $res = Utils::filterDrawsBy(['whatever'], $testDraws, 2);
        $this->assertCount(1, $res);
        $this->assertEqualsCanonicalizing([['whatever' => 2]], $res);
    }

    /**
     * Tests filtering by a text value.
     */
    public function testFilterDrawsByIntComplex()
    {
        $testDraws = [
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 3,
            ],
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 4,
            ],
        ];
        $ballNames = ['ball1', 'ball2', 'ball3'];
        $this->assertCount(2, $testDraws);

        $res = Utils::filterDrawsBy($ballNames, $testDraws, 2);
        $this->assertCount(2, $res);
        $this->assertSame($testDraws, $res);

        $res = Utils::filterDrawsBy($ballNames, $testDraws, 3);
        $this->assertCount(1, $res);
        $this->assertEqualsCanonicalizing([['ball1' => 2, 'ball2' => 1, 'ball3' => 3]], $res);
    }

    /**
     * Tests to make sure returns the right number of results
     */
    public function testGetFrequentlyOccurringBallsCount()
    {
        $testDraws = [
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 4,
            ],
            [
                'ball1' => 2,
                'ball2' => 1,
                'ball3' => 3,
            ],
        ];
        $ballNames = ['ball1', 'ball2', 'ball3'];

        // should return 2, 1 and 4
        $res = Utils::getFrequentlyOccurringBalls($testDraws, $ballNames, 3, false);
        $this->assertCount(3, $res);
        $this->assertEqualsCanonicalizing([1, 2, 4], $res);
    }

    /**
     * Tests latest draw routine throws error on empty array supplied.
     */
    public function testLatestDrawsDateEmpty()
    {
        $this->expectException(\RuntimeException::class);
        Utils::getLatestDrawDate([]);
    }

    /**
     * Tests latest draw routine throws error on invalid array supplied.
     */
    public function testLatestDrawsDateInvalid()
    {
        $draws = [
            [
                'invalid' => '01-JAN-2019',
            ]
        ];
        $this->expectException(\RuntimeException::class);
        Utils::getLatestDrawDate($draws);
    }

    /**
     * Tests returning latest Draw date from draws array.
     *
     * @throws \Exception
     */
    public function testLatestDrawsDate()
    {
        $draws = [
            [
                'drawDate' => '01-Jan-2018',
            ],
            [
                'drawDate' => '01-Jan-2019',
            ],
        ];
        $latest = Utils::getLatestDrawDate($draws);
        $expected = new \DateTime('01-Jan-2019');
        $this->assertSame($expected->getTimestamp(), $latest->getTimestamp());
    }
}
