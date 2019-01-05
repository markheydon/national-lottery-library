<?php
/**
 * Helper class to generate numbers for the Lotto game.
 */

namespace MarkHeydon\LotteryGenerator;

/**
 * Helper class to generate numbers for the Lotto game.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
class LottoGenerate
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
        $allDraws = LottoDownload::readLottoDrawHistory();

        // Build the results array header
        $gameName = 'Lotto';
        $latestDrawDate = Utils::getLatestDrawDate($allDraws);

        // Build some generated lines of 'random' numbers and return
        $linesMethod1 = self::generateMostFrequentTogether($allDraws);
        $linesMethod2 = self::generateMostFrequent($allDraws);
        $linesMethod3 = self::generateFullIteration($allDraws);

        $lines = [
            'method1' => $linesMethod1,
            'method2' => $linesMethod2,
            'method3' => $linesMethod3,
        ];
        $lineBalls = [
            'lottoBalls' => 6,
        ];

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
     * Generate a lotto line by finding balls that occurs most frequently across all data.
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
     * Returns array of balls that frequently occur for the specified draws array.
     *
     * @param array $draws The draws array to use.
     * @param bool $together Balls that occur together?
     * @return array Array of balls 'normal' => (5), 'luckyStars' => (2).
     */
    private static function getFrequentlyOccurringBalls(array $draws, bool $together): array
    {
        $lottoBalls = Utils::getFrequentlyOccurringBalls(
            $draws,
            self::getBallNames(),
            6,
            $together
        );

        // Return results array
        $results = [
            'lottoBalls' => $lottoBalls,
        ];
        return $results;
    }

    /**
     * Generate lotto lines by iterating through most frequent machine, ball set and balls within that set.
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
        $machines = self::getMachineNames($draws);
        foreach ($machines as $machine) {
            // Loop through ball sets (for single machine).
            $machineDraws = self::filterDrawsByMachine($draws, $machine);
            $ballSets = self::getBallSets($machineDraws);
            foreach ($ballSets as $ballSet) {
                $filteredDraws = self::filterDrawsByBallSet($machineDraws, $ballSet);
                $lines[] = self::getFrequentlyOccurringBalls($filteredDraws, true);
            }
        }

        return $lines;
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
        $machineCount = Utils::getCount($draws, ['machine']);
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
        $ballSetCount = Utils::getCount($draws, ['ballSet']);
        arsort($ballSetCount);
        reset($ballSetCount);
        return array_keys($ballSetCount);
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
        $filteredDraws = Utils::filterDrawsBy(['machine'], $draws, $machine);
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
        $filteredDraws = Utils::filterDrawsBy(['ballSet'], $draws, $ballSet);
        return $filteredDraws;
    }

    /**
     * Array of ball names.
     *
     * Balls 1 ~ 6 plus the bonus ball.
     *
     * @return array Array of ball names.
     */
    private static function getBallNames(): array
    {
        for ($b = 1; $b <= 6; $b++) {
            $ballNumber = 'ball' . $b;
            $ballNames[] = $ballNumber;
        }
        $ballNames[] = 'bonusBall';
        return $ballNames;
    }
}
