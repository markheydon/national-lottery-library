<?php
/**
 * Helper class to generate numbers for the EuroMillions game.
 *
 * @package MarkHeydon
 * @subpackage MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */

namespace MarkHeydon\LotteryGenerator;

class EuromillionsGenerate
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
        $allDraws = self::readEuromillionsDrawHistory();

        // Build some generated lines of 'random' numbers and return
        $linesMethod1 = self::generateMostFrequent($allDraws);
        $linesMethod2 = self::generateMostFrequentTogether($allDraws);

        $lines = [
            'method1' => $linesMethod1,
            'method2' => $linesMethod2,
        ];
        return $lines;
    }

    /**
     * Uses the Lotto draw history file in the data directory to return a draws array.
     *
     * @since 1.0.0
     *
     * @return array The draws array.
     */
    private static function readEuromillionsDrawHistory(): array
    {
        $results = Utils::csvToArray(EuromillionsDownload::filePath());

        $allDraws = [];
        foreach ($results as $draw) {
            $drawDate = $draw['DrawDate'];
            $ball1 = $draw['Ball 1'];
            $ball2 = $draw['Ball 2'];
            $ball3 = $draw['Ball 3'];
            $ball4 = $draw['Ball 4'];
            $ball5 = $draw['Ball 5'];
            $luckyStar1 = $draw['Lucky Star 1'];
            $luckyStar2 = $draw['Lucky Star 2'];
            $raffles = explode(',', $draw['UK Millionaire Maker']);
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
                'luckyStar1' => $luckyStar1,
                'luckyStar2' => $luckyStar2,
                'raffles' => $raffles,
            ];
        }

        return $allDraws;
    }

    /**
     * Generate a euromillions line by finding balls that occurs most frequently across all data.
     *
     * @since 1.0.0
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    private static function generateMostFrequent(array $draws): array
    {
        // return as array to keep consistence with other generate method(s)
        $lines = [];
        $lines[] = self::getFrequentlyOccurringBalls($draws, false);
        return $lines;
    }

    /**
     * Generate a lotto line by finding balls that occurs most frequently across all data together.
     *
     * I.e. looks for numbers that occur within the same lines together, not across the whole data set.
     *
     * @since 1.0.0
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    private static function generateMostFrequentTogether(array $draws): array
    {
        // return as array to keep consistence with other generate method(s)
        $lines = [];
        $lines[] = self::getFrequentlyOccurringBalls($draws, true);
        return $lines;
    }



    /**
     * Returns array of balls that frequently occur for the specified draws array.
     *
     * @param array $draws The draws array to use.
     * @param bool $together Balls that occur together?
     * @return array Array of balls 'normal' => (5), 'luckyStars' => (2).
     */
    private static function getFrequentlyOccurringBalls(array $draws, bool $together): array
    {
        // Want 5 normal balls
        $normalBalls = [];
        $freqBall = self::calculateFrequentNormalBall($draws);
        $normalBalls[] = $freqBall;
        for ($n = 1; $n < 5; $n++) {
            if ($together) {
                $draws = self::filterDrawsByNormalBall($draws, $freqBall);
            }
            $freqBall = self::calculateFrequentNormalBall($draws, $normalBalls);
            $normalBalls[] = $freqBall;
        }
        asort($normalBalls);

        // And 2 lucky stars
        $luckyStars = [];
        $freqBall = self::calculateFrequentLuckyStar($draws);
        $luckyStars[] = $freqBall;
        $freqBall = self::calculateFrequentLuckyStar($draws, $luckyStars);
        $luckyStars[] = $freqBall;
        asort($luckyStars);

        // Return results array
        $results = [
            'normal' => $normalBalls,
            'luckyStars' => $luckyStars,
        ];
        return $results;
    }

    /**
     * Calculate the most frequently occurring ball normal value from the specified draws array.
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
    private static function calculateFrequentNormalBall(array $draws, array $except = []): int
    {
        $ballCount = [];
        foreach ($draws as $draw) {
            for ($b = 1; $b <= 5; $b++) {
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
        arsort($ballCount);
        reset($ballCount);
        return (int)key($ballCount) ?? 0;
    }

    /**
     * Calculate the most frequently occurring ball lucky star from the specified draws array.
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
    private static function calculateFrequentLuckyStar(array $draws, array $except = []): int
    {
        $ballCount = [];
        foreach ($draws as $draw) {
            for ($b = 1; $b <= 2; $b++) {
                $ballNumber = 'luckyStar' . $b;
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
     * Filter the specified draws array by the specified ball number (value).
     *
     * @since 1.0.0
     *
     * @param array $draws Array of draws.
     * @param int $ball Ball value number to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByNormalBall(array $draws, int $ball): array
    {
        $filteredDraws = array_filter($draws, function ($draw) use ($ball) {
            $result = $draw['ball1'] == $ball || $draw['ball2'] == $ball || $draw['ball3'] == $ball ||
                $draw['ball4'] == $ball || $draw['ball5'] == $ball;
            return $result;
        });
        return $filteredDraws;
    }

}