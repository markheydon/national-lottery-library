<?php
/**
 * Helper class to generate numbers for the Lotto game.
 *
 * @package MarkHeydon
 * @subpackage MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */

namespace MarkHeydon\LotteryGenerator;

class GenerateLotto
{
    /**
     * Generate 'random' Lotto numbers.
     *
     * @since 1.0.0
     *
     * @return array Array of lines containing generated numbers.
     */
    public static function generate(): array
    {
        // @todo: Download results periodically -- only updated weekly I think?
        // Currently using a lotto-draw-history.csv file but should download and/or utilize a database.
        $allDraws = self::readLottoDrawHistory();

        // Build some generated lines of 'random' numbers and return
        $lines = self::generateMethod2($allDraws);
        $lines = array_merge($lines,self::generateMethod1($allDraws));
        return $lines;
    }

    /**
     * Filter the specified draws array by the specified ball number (value).
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param int $ball Ball value number to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByBall(array $draws, int $ball): array
    {
        $filteredDraws = array_filter($draws, function ($draw) use ($ball) {
            $result = $draw['ball1'] == $ball || $draw['ball2'] == $ball || $draw['ball3'] == $ball ||
                $draw['ball4'] == $ball || $draw['ball5'] == $ball || $draw['ball6'] == $ball ||
                $draw['bonusBall'] == $ball;
            return $result;
        });
        return $filteredDraws;
    }

    /**
     * Filter the specified array by the specified machine name.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param string $machine Machine name to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByMachine(array $draws, string $machine): array
    {
        $filteredDraws = array_filter($draws, function ($draw) use ($machine) {
            return $draw['machine'] === $machine;
        });
        return $filteredDraws;
    }

    /**
     * Filter the specified array by the specified ball set.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param string $ballSet Ball set to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByBallSet(array $draws, string $ballSet): array
    {
        $filteredDraws = array_filter($draws, function ($draw) use ($ballSet) {
            return $draw['ballSet'] === $ballSet;
        });
        return $filteredDraws;
    }

    /**
     * Returns a list of machine names sorted by most frequent first.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @return array Array of machine names with most frequent first.
     */
    private static function getMachineNames(array $draws): array
    {
        $machineCount = self::getCount($draws, 'machine');
        arsort($machineCount);
        reset($machineCount);
        return array_keys($machineCount);
    }

    /**
     * Returns a list of ball sets sorted by most frequent first.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @return array Array of ball sets with most frequent first.
     */
    private static function getBallSets(array $draws): array
    {
        $ballSetCount = self::getCount($draws, 'ballSet');
        arsort($ballSetCount);
        reset($ballSetCount);
        return array_keys($ballSetCount);
    }

    /**
     * Returns an array of counters for the specified array element in the supplied draws array.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param string $element Element name within the draws array.
     * @return array Array of elements with count of their occurrence in the draws array.
     */
    private static function getCount(array $draws, string $element): array
    {
        $count = [];
        foreach ($draws as $draw) {
            $machine = $draw[$element];
            if (!isset($count[$machine])) {
                $count[$machine] = 1;
            } else {
                $count[$machine]++;
            }
        }
        return $count;
    }

    /**
     * Returns array of balls that frequently occur together for the specified draws array.
     *
     * @param array $draws The draws array to use.
     * @return array Array of balls.
     */
    private static function getFrequentBalls(array $draws): array
    {
        // Want 6 numbers in total
        $results = [];
        $freqBall = self::calculateFrequentBall($draws);
        $results[] = $freqBall;
        for ($n = 1; $n < 6; $n++) {
            $draws = self::filterDrawsByBall($draws, $freqBall);
            $freqBall = self::calculateFrequentBall($draws, $results);
            $results[] = $freqBall;
        }

        // Sort the results and return
        asort($results);
        return $results;
    }

    /**
     * Calculate the most frequently occurring ball value from the specified draws array.
     *
     * Looks through all the draws and counts the number of times a ball value occurs
     * and return the highest count value.  Optionally excludes the specified ball values.
     *
     * @since 1.0.0
     *
     * @param array $draws The draws array.
     * @param array $except Optional array of ball values to ignore from the count.
     * @return int Ball value of the most frequently occurring or 0 if draws array is empty.
     */
    private static function calculateFrequentBall(array $draws, array $except = []): int
    {
        $ballCount = [];
        foreach ($draws as $draw) {
            for ($b = 1; $b <= 7; $b++) {
                if ($b === 7) {
                    $ballNumber = 'bonusBall';
                } else {
                    $ballNumber = 'ball' . $b;
                }
                $ballValue = $draw[$ballNumber];
                if (!in_array($ballValue, $except)) {
                    if (!isset($ballCount[$ballValue])) {
                        $ballCount[$ballValue] = 1;
                    } else {
                        $ballCount[$ballValue]++;
                    }
                }
            }
        }
        arsort($ballCount);
        reset($ballCount);
        return (int)key($ballCount) ?? 0;
    }

    /**
     * Returns array of raffle numbers from the specified string.
     *
     * CSV history file seems to be in the format 'RAF;RAF,RAF,RAF'.
     *
     * @since 1.0.0
     *
     * @param string $raffles String from CSV history file for field 'Raffles'.
     * @return array Array of strings of all the raffles numbers from the specified string.
     */
    private static function parseRafflesString(string $raffles): array
    {
        if (empty($raffles)) {
            return [];
        }
        $split = explode(';', $raffles);
        $first = $split[0];
        $others = explode(',', $split[1]);
        $result = array_merge([$first], $others);
        return $result;
    }

    /**
     * Uses the lotto-draw-history.csv file in the data directory to return a draws array.
     *
     * @since 1.0.0
     *
     * @return array The draws array.
     */
    private static function readLottoDrawHistory(): array
    {
        $filename = 'lotto-draw-history.csv';
        $filepath = __DIR__ . '/../../data/' . $filename;
        $results = self::csvToArray($filepath);

        $allDraws = [];
        foreach ($results as $draw) {
            $drawDate = $draw['DrawDate'];
            $ball1 = $draw['Ball 1'];
            $ball2 = $draw['Ball 2'];
            $ball3 = $draw['Ball 3'];
            $ball4 = $draw['Ball 4'];
            $ball5 = $draw['Ball 5'];
            $ball6 = $draw['Ball 6'];
            $bonusBall = $draw['Bonus Ball'];
            $ballSet = $draw['Ball Set'];
            $machine = $draw['Machine'];
            $raffles = self::parseRafflesString($draw['Raffles']);
            $drawNumber = $draw['DrawNumber'];
            $dayOfDraw = date('l', strtotime($drawDate));

            $allDraws[] = [
                'drawNumber' => $drawNumber,
                'drawDate' => $drawDate,
                'drawDay' => $dayOfDraw,
                'ball1' => $ball1,
                'ball2' => $ball2,
                'ball3' => $ball3,
                'ball4' => $ball4,
                'ball5' => $ball5,
                'ball6' => $ball6,
                'bonusBall' => $bonusBall,
                'ballSet' => $ballSet,
                'machine' => $machine,
                'raffles' => $raffles,
            ];
        }

        return $allDraws;
    }

    /**
     * Helper method to convert a csv file to an associative array.
     *
     * @since 1.0.0
     *
     * @param string $filename Full filename to the csv file to process.
     * @param string $delimiter Optional delimiter to use if not standard ','.
     * @return array|bool Associative array or false if there was an issue parsing.
     */
    private static function csvToArray($filename = '', $delimiter = ','): array
    {
        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * Generate lotto lines by iterating through most frequent machine, ball set and balls within that set.
     *
     * Will run through however many history draws there are available and generate as many lines as possible
     * depending on the site of the data.
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    private static function generateMethod1(array $draws): array
    {
        $lines = [];
        $machines = self::getMachineNames($draws);
        foreach ($machines as $machine) {
            // Loop through ball sets (for single machine).
            $machineDraws = self::filterDrawsByMachine($draws, $machine);
            $ballSets = self::getBallSets($machineDraws);
            foreach ($ballSets as $ballSet) {
                $filteredDraws = self::filterDrawsByBallSet($machineDraws, $ballSet);
                $lines[] = self::getFrequentBalls($filteredDraws);
            }
        }

        return $lines;
    }

    /**
     * Generate lotto lines by finding balls that occurs most frequently across all data.
     *
     * Will run through however many history draws there are available and generate as many lines as possible
     * depending on the site of the data.
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    private static function generateMethod2(array $draws): array
    {
        $lines = [];
        $lines[] = self::getFrequentBalls($draws);
        return $lines;
    }
}