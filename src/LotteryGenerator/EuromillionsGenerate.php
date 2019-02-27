<?php
/**
 * Helper class to generate numbers for the EuroMillions game.
 */

declare(strict_types=1);

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
     * The name of the EuroMillions game.
     *
     * @return string Name of the EuroMillions game.
     */
    protected static function getNameOfGame(): string
    {
        return 'EuroMillions';
    }

    /**
     * Should results include Lucky Stars?
     *
     * @return bool True if results should include Lucky Stars.
     */
    private static function isEuroMillionsGame(): bool
    {
        // The current logic is that the base class is EuroMillions.
        return (static::getNameOfGame() === self::getNameOfGame());
    }

    /**
     * Generate 'random' Lotto numbers.
     *
     * @since 1.0.0
     *
     * @return array Array of lines containing generated numbers.
     */
    public static function generate(): array
    {
        $allDraws = EuromillionsDownload::readEuromillionsDrawHistory();

        // Build the results array header
        $gameName = static::getNameOfGame();
        $latestDrawDate = Utils::getLatestDrawDate($allDraws);

        // Build some generated lines of 'random' numbers and return
        $linesMethod1 = self::generateMostFrequentTogether($allDraws);
        $linesMethod2 = self::generateMostFrequent($allDraws);
        $linesMethod3 = self::generateFullIteration($allDraws);

        // Order of methods differs for Hotpicks.
        $lines = [];
        if (static::isEuroMillionsGame()) {
            $lines['most-freq'] = $linesMethod2;
            $lines['most-freq-together'] = $linesMethod1;
            $lines['full-iteration'] = $linesMethod3;
        } else {
            $lines['most-freq-together'] = $linesMethod1;
            $lines['full-iteration'] = $linesMethod3;
            $lines['most-freq'] = $linesMethod2;
        }

        // Meta data for results structure.
        $lineBalls = [
            'mainNumbers' => 5,
        ];
        if (static::isEuroMillionsGame()) {
            $lineBalls['luckyStars'] = 2;
        }

        // Build the results array and return
        $results = [
            'gameName' => $gameName,
            'latestDrawDate' => $latestDrawDate,
            'numOfMethods' => count($lines),
            'lineBalls' => $lineBalls,
            'lines' => $lines,
        ];
        return $results;
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
        $results = [];
        $normalBalls = Utils::getFrequentlyOccurringBalls(
            $draws,
            self::getNormalBallNames(),
            5,
            $together,
            static::isEuroMillionsGame()
        );
        $results['mainNumbers'] = $normalBalls;
        if (static::isEuroMillionsGame()) {
            $luckyStars = Utils::getFrequentlyOccurringBalls(
                $draws,
                self::getLuckyStarNames(),
                2,
                $together
            );
            $results['luckyStars'] = $luckyStars;
        }

        // Return results array
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

    /**
     * Generate Euro Million lines by iterating through most frequent day and balls within each day.
     *
     * Will run through however many history draws there are available and generate as many lines as possible
     * depending on the site of the data.
     *
     * @since 1.0.0
     *
     * @param array $draws The draws array to use.
     * @return array Array of lines generated.
     */
    private static function generateFullIteration(array $draws): array
    {
        $lines = [];
        $days = self::getDrawDays($draws);
        foreach ($days as $day) {
            $dayDraws = self::filterDrawsByDay($draws, $day);
            $lines[] = self::getFrequentlyOccurringBalls($dayDraws, true);
        }
        return $lines;
    }

    /**
     * Return list of days used in the supplied draws array.
     *
     * @param array $draws The draws array.
     * @return array Array of day names.
     */
    private static function getDrawDays(array $draws): array
    {
        $machineCount = Utils::getCount($draws, ['drawDay']);
        arsort($machineCount);
        reset($machineCount);
        return array_keys($machineCount);
    }

    /**
     * Filter the supplied draws array by the specified draw day.
     *
     * @param array $draws The draws array.
     * @param string $day The day to filter the draws array by.
     * @return array The filtered draws array.
     */
    private static function filterDrawsByDay(array $draws, string $day): array
    {
        $dayDraws = Utils::filterDrawsBy(['drawDay'], $draws, $day);
        return $dayDraws;
    }
}
