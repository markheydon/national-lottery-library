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
     * Generate Lotto numbers.
     *
     * @return array Array of lines containing generated numbers.
     */
    public static function generate()
    {
        $lines = [];

        // @todo: Download results periodically -- only updated weekly I think?
        // Currently using a lotto-draw-history.csv file but should download and/or utilize a database.
        $allDraws = self::readLottoDrawHistory();

        // Generate lines using most frequent machine first
        $freqMachines = self::calculateMachinesFrequency($allDraws);
        foreach ($freqMachines as $freqMachine) {
            $results = [];

            // Look for most frequently used ball set (for single machine).
            $filteredDraws = self::filterDrawsByMachine($allDraws, $freqMachine);
            $freqBall = self::calculateFrequentBall($filteredDraws);
            // Want 6 numbers in total
            $results[] = $freqBall;
            for ($n = 1; $n < 6; $n++) {
                $filteredDraws = self::filterDrawsByBall($filteredDraws, $freqBall);
                $freqBall = self::calculateFrequentBall($filteredDraws, $results);
                $results[] = $freqBall;
            }

            // sort results and add to generated lines
            asort($results);
            $lines[] = $results;
        }

        return $lines;
    }

    /**
     * Filter the specified draws array by the specified ball number (value).
     *
     * @param array $draws Array of draws.
     * @param int $ball Ball value number to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByBall(array $draws, int $ball)
    {
        $filteredDraws = array_filter($draws, function ($draw) use ($ball) {
            $result = $draw['ball1'] == $ball || $draw['ball2'] == $ball || $draw['ball3'] == $ball ||
                $draw['ball4'] == $ball || $draw['ball5'] == $ball || $draw['ball6'] == $ball;
            return $result;
        });
        return $filteredDraws;
    }

    /**
     * Filter the specified array by the specified machine name.
     *
     * @param array $draws Array of draws.
     * @param string $machine Machine name to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByMachine(array $draws, string $machine)
    {
        $filteredDraws = array_filter($draws, function ($draw) use ($machine) {
            return $draw['machine'] === $machine;
        });
        return $filteredDraws;
    }

    /**
     * Returns a list of machine names sorted by more frequent first.
     *
     * @param array $draws Array of draws.
     * @return array Array of machine names with most frequent first.
     */
    private static function calculateMachinesFrequency(array $draws)
    {
        $machineCount = [];
        foreach ($draws as $draw) {
            $machine = $draw['machine'];
            if (!isset($machineCount[$machine])) {
                $machineCount[$machine] = 1;
            } else {
                $machineCount[$machine]++;
            }
        }
        if (count($machineCount) < 1) {
            return [];
        }
        arsort($machineCount);
        reset($machineCount);
        return array_keys($machineCount);
    }

    /**
     * Calculate the most frequently occurring ball value from the specified draws array.
     *
     * Looks through all the draws and counts the number of times a ball value occurs
     * and return the highest count value.  Optionally excludes the specified ball values.
     *
     * @param array $draws The draws array.
     * @param array $except Optional array of ball values to ignore from the count.
     * @return int Ball value of the most frequently occurring or 0 if draws array is empty.
     */
    private static function calculateFrequentBall(array $draws, array $except = [])
    {
        $ballCount = [];
        foreach ($draws as $draw) {
            for ($b = 1; $b <= 6; $b++) {
                $ballNumber = 'ball' . $b;
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
        if (count($ballCount) < 1) {
            return 0;
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
     * @param string $raffles String from CSV history file for field 'Raffles'.
     * @return array Array of strings of all the raffles numbers from the specified string.
     */
    private static function parseRafflesString(string $raffles)
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
     * @return array The draws array.
     */
    private static function readLottoDrawHistory()
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

            $allDraws[] = [
                'drawNumber' => $drawNumber,
                'drawDate' => $drawDate,
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
     * @param string $filename Full filename to the csv file to process.
     * @param string $delimiter Optional delimiter to use if not standard ','.
     * @return array|bool Associative array or false if there was an issue parsing.
     */
    private static function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

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
}