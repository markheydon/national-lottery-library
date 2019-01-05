<?php
/**
 * Various helper/utility methods.
 */

namespace MarkHeydon\LotteryGenerator;

/**
 * Various helper/utility methods.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
class Utils
{
    /**
     * Helper method to convert a csv file to an associative array.
     *
     * @since 1.0.0
     *
     * @param string $filename Full filename to the csv file to process.
     * @param string $delimiter Optional delimiter to use if not standard ','.
     * @return array|bool Associative array or false if there was an issue parsing.
     */
    public static function csvToArray($filename = '', $delimiter = ','): array
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
     * Calculate the most frequently occurring element values from the specified draws array.
     *
     * Looks through all the draws and counts the number of times an element value occurs
     * and return the highest count value. Calculation routine should pick the first one
     * (found) when there are multiple. Optionally excludes the specified ball values.
     *
     * @since 1.0.0
     *
     * @param array $draws The draws array.
     * @param array $elements Array of names in $draw to use as ball values.
     * @param array $except Optional array of ball values to ignore from the count.
     *
     * @return int|string Value of the most frequently occurring or 0 if draws array is empty.
     */
    public static function calculateFrequentElementValues(
        array $draws,
        array $elements,
        array $except = []
    ): int {
        $count = Utils::getCount($draws, $elements, $except);
        arsort($count);
        reset($count);

        return key($count) ?? 0;
    }

    /**
     * Returns an array of counters for the specified array element in the supplied draws array.
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param array $elements Element names within the draws array.
     * @param array $except Optional array of ball values to ignore from the count.
     *
     * @return array Array of elements with count of their occurrence in the draws array.
     */
    public static function getCount(
        array $draws,
        array $elements,
        array $except = []
    ): array {
        $count = [];
        foreach ($draws as $draw) {
            foreach ($elements as $element) {
                $elementValue = $draw[$element];
                if (!in_array($elementValue, $except)) {
                    if (!isset($count[$elementValue])) {
                        $count[$elementValue] = 1;
                    } else {
                        $count[$elementValue]++;
                    }
                }
            }
        }
        return $count;
    }

    /**
     * Filter the specified draws array by the specified element value.
     *
     * @since 1.0.0
     *
     * @param array $elements Elements to check in filtering.
     * @param array $draws Array of draws.
     * @param int|string $elementValue Element value number to filter by.
     *
     * @return array Filtered array of draws.
     */
    public static function filterDrawsBy(array $elements, array $draws, $elementValue): array
    {
        $filteredDraws = array_filter($draws, function ($draw) use ($elements, $elementValue) {
            $result = false;
            foreach ($elements as $element) {
                $result = $result || ($draw[$element] == $elementValue);
            }
            return $result;
        });
        return $filteredDraws;
    }

    /**
     * Returns array of balls that frequently occur for the specified draws array.
     *
     * In together mode, the draws used to work out the frequently occurring ball number
     * will be filtered using the last frequent number in an attempt to find the numbers
     * that often come up together.
     *
     * @param array $draws The draws array to use.
     * @param array $ballNames The draw array index names for the balls.
     * @param int $numOfResults The number of results to return in the balls array.
     * @param bool $together Balls that occur together?
     *
     * @return array Array of balls.
     */
    public static function getFrequentlyOccurringBalls(
        array $draws,
        array $ballNames,
        int $numOfResults,
        bool $together
    ): array {
        $results = [];
        $myDraws = $draws;
        for ($n = 1; $n <= $numOfResults; $n++) {
            $freqBall = Utils::calculateFrequentElementValues($myDraws, $ballNames, $results);
            $results[] = $freqBall;
            // in together mode, filter to draws that contain the last
            // frequently ball number found
            if ($n > 1 && $together) {
                $myDraws = self::filterDrawsBy($ballNames, $draws, $freqBall);
            }
        }

        // Sort the results and return
        asort($results);
        return $results;
    }

    /**
     * Returns the latest (more recent) draw date from a draws array.
     *
     * @param array $draws The draws array to use.
     *
     * @return \DateTime Latest draw date from the supplied draws array.
     * @throws \RuntimeException Supplied draws array cannot be empty.
     */
    public static function getLatestDrawDate(array $draws): \DateTime
    {
        if (count($draws) < 1) {
            throw new \RuntimeException('Supplied draws empty cannot be empty');
        }

        // make array of just the drawDate values as DateTime objects
        $drawDates = array_map(function ($draw) {
            if (!key_exists('drawDate', $draw)) {
                throw new \RuntimeException('Invalid draws array');
            }
            return new \DateTime($draw['drawDate']);
        }, $draws);

        // sort and return the top one
        rsort($drawDates);
        $first = $drawDates[0];
        return $first;
    }
}