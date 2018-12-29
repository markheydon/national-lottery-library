<?php
/**
 * Helper class to generate numbers for the EuroMillions game.
 */

namespace MarkHeydon\LotteryGenerator;

/**
 * Helper class to generate numbers for the EuroMillions game.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
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
        $allDraws = EuromillionsDownload::readEuromillionsDrawHistory();

        // Build some generated lines of 'random' numbers and return
        $linesMethod1 = self::generateMostFrequentTogether($allDraws);
        $linesMethod2 = self::generateMostFrequent($allDraws);

        $lines = [
            'method1' => $linesMethod1,
            'method2' => $linesMethod2,
        ];
        return $lines;
    }

    /**
     * Generate a EuroMillions line by finding balls that occurs most frequently across all data.
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
        $normalBalls = Utils::getFrequentlyOccurringBalls(
            $draws, self::getNormalBallNames(), 5, $together);
        $luckyStars = Utils::getFrequentlyOccurringBalls(
            $draws, self::getLuckyStarNames(), 2, $together);

        // Return results array
        $results = [
            'normal' => $normalBalls,
            'luckyStars' => $luckyStars,
        ];
        return $results;
    }

    /**
     * Array of normal ball names.
     *
     * @return array Array of normal ball names.
     */
    private static function getNormalBallNames(): array
    {
        $ballNames = [];
        for ($b = 1; $b <= 5; $b++) {
            $ballNumber = 'ball' . $b;
            $ballNames[] = $ballNumber;
        }
        return $ballNames;
    }

    /**
     * Array of Lucky Star ball names.
     *
     * @return array Array of Lucky Star ball names.
     */
    private static function getLuckyStarNames(): array
    {
        $ballNames = [];
        for ($b = 1; $b <= 2; $b++) {
            $ballNumber = 'luckyStar' . $b;
            $ballNames[] = $ballNumber;
        }
        return $ballNames;
    }
}