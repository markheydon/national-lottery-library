<?php
/**
 * Helper class to generate numbers for the Lotto game.
 */

declare(strict_types=1);

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
     * The name of the Lotto game.
     *
     * @return string Name of the Lotto game.
     */
    protected static function getNameOfGame(): string
    {
        return 'Lotto';
    }

    /**
     * The number of Lotto balls to return in the results.
     *
     * @return int Num of Lotto balls in results.
     */
    protected static function getNumOfBalls(): int
    {
        return 6;
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
        $allDraws = LottoDownload::readLottoDrawHistory();

        // Build the results array header
        $gameName = static::getNameOfGame();
        $latestDrawDate = Utils::getLatestDrawDate($allDraws);

        // Build some generated lines of 'random' numbers and return
        $linesMethod1 = self::generateMostFrequentTogether($allDraws);
        $linesMethod2 = self::generateMostFrequent($allDraws);
        $linesMethod3 = self::generateFullIteration($allDraws);

        $lines = [
            'full-iteration' => $linesMethod3,
            'most-freq-together' => $linesMethod1,
            'most-freq' => $linesMethod2,
        ];
        $lineBalls = [
            'lottoBalls' => static::getNumOfBalls(),
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
    protected static function generateMostFrequentTogether(array $draws): array
    {
        // return as array to keep consistence with other generate method(s)
        $lines = [];
        $lines[] = self::getFrequentlyOccurringBalls($draws, static::getNumOfBalls(), true);
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
    protected static function generateMostFrequent(array $draws): array
    {
        // return as array to keep consistence with other generate method(s)
        $lines = [];
        $lines[] = self::getFrequentlyOccurringBalls($draws, static::getNumOfBalls(), false);
        return $lines;
    }

    /**
     * Returns array of balls that frequently occur for the specified draws array.
     *
     * @param array $draws The draws array to use.
     * @param int $numOfResults The number of results to return in the balls array.
     * @param bool $together Balls that occur together?
     * @return array Array of balls 'normal' => (5), 'luckyStars' => (2).
     */
    protected static function getFrequentlyOccurringBalls(array $draws, int $numOfResults, bool $together): array
    {
        $lottoBalls = Utils::getFrequentlyOccurringBalls(
            $draws,
            static::getBallNames(),
            $numOfResults,
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
    protected static function generateFullIteration(array $draws): array
    {
        $lines = [];
        $machines = self::getMachineNames($draws);
        foreach ($machines as $machine) {
            // Loop through ball sets (for single machine).
            $machineDraws = self::filterDrawsByMachine($draws, $machine);
            $ballSets = self::getBallSets($machineDraws);
            foreach ($ballSets as $ballSet) {
                $filteredDraws = self::filterDrawsByBallSet($machineDraws, $ballSet);
                $lines[] = self::getFrequentlyOccurringBalls($filteredDraws, static::getNumOfBalls(), true);
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
     * @param int $ballSet Ball set to filter by.
     * @return array Filtered array of draws.
     */
    private static function filterDrawsByBallSet(array $draws, int $ballSet): array
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
    protected static function getBallNames(): array
    {
        $ballNames = [];
        for ($b = 1; $b <= static::getNumOfBalls(); $b++) {
            $ballNumber = 'ball' . $b;
            $ballNames[] = $ballNumber;
        }
        $ballNames[] = 'bonusBall';
        return $ballNames;
    }
}
